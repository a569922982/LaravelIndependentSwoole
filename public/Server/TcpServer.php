#!/usr/bin/env php
<?php
$config = require_once __DIR__ . '/../../config/server.php';
define('AppName', $config['TCP']['NAME']);//运存名称
define('AppListen', $config['TCP']['LISTEN']); //监听的端口
define('AppQueueFile', $config['TCP']['QUEUE']);
function errorLog($e)
{
    return json_encode([
        'return' => 'false',
        'msg' => $e->getMessage(),
        'line' => $e->getLine(),
        'file' => $e->getFile()
    ]);
}

$server = new swoole_server('0.0.0.0', AppListen);
$server->set($config['TCP']['CONFIG']);
$server->on('Start', function ($server) {
    cli_set_process_title(AppName);
});
$server->on('WorkerStart', function ($server, $worker_id) {
    require __DIR__ . '/../../php/index.php';
});
$server->on('connect', function ($server, $fd) {
    gl('serv', $server);
    gl('fd', $fd);
    echo "Client: Connect.\n";
});
$server->on('receive', function ($server, $fd, $from_id, $data) {
    gl('serv', $server);
    gl('fd', $fd);
    gl('data', $data);
    gl('from_id', $from_id);
    try {
        $apiUrl = json_encode($data);
        $api = $apiUrl->action;
        (new $api())->run();
    } catch (Exception $e) {
        $server->send($fd, errorLog($e));
    } catch (Error $e) {
        $server->send($fd, errorLog($e));
    }
});
$server->on('close', function ($server, $fd) {
    echo "client {$fd} closed\n";
    gl('serv', $server);
    gl('Fd', $fd);
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