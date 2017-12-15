#!/usr/bin/env php
<?php
$config = require_once __DIR__ . '/../../config/server.php';
define('AppName', $config['WEBSOCKET']['NAME']);//运存名称
define('AppListen', $config['WEBSOCKET']['LISTEN']); //监听的端口
define('AppQueueFile', $config['WEBSOCKET']['QUEUE']);
function errorLog($e)
{
    return json_encode([
        'return' => 'false',
        'msg' => $e->getMessage(),
        'line' => $e->getLine(),
        'file' => $e->getFile()
    ]);
}

$server = new swoole_websocket_server('0.0.0.0', AppListen);
$chat = [];
$chatBox = [];
$server->set($config['WEBSOCKET']['CONFIG']);
$server->on('Start', function ($server) {
    cli_set_process_title(AppName);
});
$server->on('WorkerStart', function ($server, $worker_id) {
    require __DIR__ . '/../../php/index.php';
});
$server->on('open', function ($server, $Request) {
    gl('serv', $server);
    gl('Request', $Request);
    $api = preg_replace('/\//i', '\\', $Request->server['request_uri']);
    if (substr($api, -1) != '\\') $api = $api . '\\';
    gl('Class', $api);

    $apiUrl = gl('Class') . 'Open';
    (new $apiUrl())->run();
});
$server->on('message', function ($server, $frame) {
    gl('serv', $server);
    gl('Frame', $frame);
    try {
        $apiUrl = gl('Class') . 'Send';
//        echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
        (new $apiUrl())->run();
    } catch (Exception $e) {
        $server->push($frame->fd, errorLog($e));
    } catch (Error $e) {
        $server->push($frame->fd, errorLog($e));
    }
});
$server->on('close', function ($server, $fd) {
    echo "client {$fd} closed\n";
    gl('serv', $server);
    gl('Fd', $fd);
    $apiUrl = gl('Class') . 'Close';
    (new $apiUrl())->run();
});
//$server->on('request', function ($request, $response) use ($server) {
//    // $server->connections 遍历所有websocket连接用户的fd，给所有用户推送
//    if ($request->get['fd'])
//        $server->push($request->get['fd'], $request->get['message']);
//    $response->end('发送成功');
//});
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