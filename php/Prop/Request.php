<?php
namespace Prop;

class Request extends \Illuminate\Http\Request
{

    public static function createWithSwooleRequest($swooleRequest)
    {

        $get = $_GET = isset($swooleRequest->get) ? $swooleRequest->get : [];
        $post = $_POST = isset($swooleRequest->post) ? $swooleRequest->post : [];
        $json = $swooleRequest->rawContent() ? json_decode($swooleRequest->rawContent(), true) : [];
        if (!is_array($json)) $json = [];
        $cookie = isset($swooleRequest->cookie) ? $swooleRequest->cookie : [];
        $server = isset($swooleRequest->server) ? $swooleRequest->server : [];
        $header = isset($swooleRequest->header) ? $swooleRequest->header : [];
        $files = isset($swooleRequest->files) ? $swooleRequest->files : [];
        $content = $swooleRequest->rawContent() ?: null;
        return static::create($server['request_uri'], $server['request_method'], array_merge($get, $post, $json), $cookie, $files, $server, $content);
    }

}