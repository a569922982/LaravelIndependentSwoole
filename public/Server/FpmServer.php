<?php
$config = require_once __DIR__ . '/../../config/server.php';
define('AppFile', $config['HTTP']['FILE']);//文件后缀
function errorLog($e)
{
    return json_encode([
        'return' => 'false',
        'msg' => $e->getMessage(),
        'line' => $e->getLine(),
        'file' => $e->getFile()
    ]);
}

require __DIR__ . '/../../php/index.php';
try {
    $apiUrl = preg_replace(['/\//i', '/\.' . AppFile . '/i'], ['\\', ''], request()->getPathInfo());
    die((new $apiUrl())->run());
} catch (Exception $e) {
    die(errorLog($e));
} catch (Error $e) {
    die(errorLog($e));
}