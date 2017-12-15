#!/usr/bin/env php
<?php
$config = require_once __DIR__ . '/../../config/server.php';
define('AppName', $config['HTTP']['NAME']);//运存名称
define('AppListen', $config['HTTP']['LISTEN']); //监听的端口
define('AppFile', $config['HTTP']['FILE']);//文件后缀
define('AppQueueFile', $config['HTTP']['QUEUE']);
function errorLog($e)
{
    return json_encode([
        'return' => 'false',
        'msg' => $e->getMessage(),
        'line' => $e->getLine(),
        'file' => $e->getFile()
    ]);
}

$serv = new swoole_http_server('0.0.0.0', AppListen);
$serv->set($config['HTTP']['CONFIG']);
$serv->on('Start', function ($serv) {
    cli_set_process_title(AppName);
});
$serv->on('WorkerStart', function ($serv, $worker_id) {
    require __DIR__ . '/../../php/index.php';
});
$serv->on('Task', function ($serv, $task_id, $from_id, $data) {
    $setIng = json_decode($data);
    $api = $setIng->action;
    return Prop\Task::$api($setIng);
});
$serv->on('Finish', function ($serv, $task_id, $data) {
    $msg = "Task {$task_id} finish,Result: {$data}" . "[" . date('Y-m-d H:i:s') . " #" . $task_id . "]\n";
    file_put_contents(AppQueueFile, $msg, FILE_APPEND);
});
$serv->on('request', function ($Request, $Response) use ($serv) {
    gl('serv', $serv);
    gl('Request', $Request);
    try {
        $apiUrl = preg_replace(['/\//i', '/\.' . AppFile . '/i'], ['\\', ''], $Request->server['request_uri']);
        $run = (new $apiUrl())->run();
        $runCode = str_split($run, 2048 * 1000);
        foreach ($runCode as $item) $Response->write($item);
    } catch (Exception $e) {
        $Response->end(errorLog($e));
    } catch (Error $e) {
        $Response->end(errorLog($e));
    }
});
$serv->start();