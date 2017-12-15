#!/usr/bin/env php
<?php
$config = require_once __DIR__ . '/../../config/server.php';
define('AppName', $config['UDP']['NAME']);//运存名称
define('AppListen', $config['UDP']['LISTEN']); //监听的端口
define('AppQueueFile', $config['UDP']['QUEUE']);
function errorLog($e)
{
    return json_encode([
        'return' => 'false',
        'msg' => $e->getMessage(),
        'line' => $e->getLine(),
        'file' => $e->getFile()
    ]);
}

$server = new swoole_server('0.0.0.0', AppListen, SWOOLE_BASE, SWOOLE_SOCK_UDP);
$server->set($config['UDP']['CONFIG']);
$server->on('Start', function ($server) {
    cli_set_process_title(AppName);
});
$server->on('WorkerStart', function ($server, $worker_id) {
    require __DIR__ . '/../../php/index.php';
});
$server->on('Packet', function ($server, $data, $clientInfo) {
    gl('serv', $server);
    gl('data', $data);
    gl('clientInfo', $clientInfo);
    try {
        $apiUrl = json_encode($data);
        $api = $apiUrl->action;
        (new $api())->run();
    } catch (Exception $e) {
        $server->sendto($clientInfo['address'], $clientInfo['port'], errorLog($e));
    } catch (Error $e) {
        $server->sendto($clientInfo['address'], $clientInfo['port'], errorLog($e));
    }
});
$server->on('Task', function ($serv, $task_id, $from_id, $data) {
    $setIng = json_decode($data);
    $api = $setIng->action;
    return Prop\Task::$api($setIng);
});
$server->on('Finish', function ($serv, $task_id, $data) {
    $msg = "Task {$task_id} finish,Result: {$data}" . "[" . date('Y-m-d H:i:s') . " #" . $task_id . "]\n";
    file_put_contents(AppQueueFile, $msg, FILE_APPEND);
});
$server->start();