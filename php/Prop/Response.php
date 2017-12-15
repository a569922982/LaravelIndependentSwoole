<?php 
namespace Prop;

class Response {
    
    public static function end($swooleResponse, $response)
    {
        foreach ($response->headers->allPreserveCase() as $key => $value) {
            foreach ($value as $v) {
                $swooleResponse->header($key, $v);
            }
        }

        $swooleResponse->status($response->getStatusCode());

        foreach ($response->headers->getCookies() as $cookie) {
            $swooleResponse->cookie($cookie->getName(), $cookie->getValue(), $cookie->getExpiresTime(), $cookie->getPath(), $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly());
        }
        
        $swooleResponse->end($response->getContent());
    }

}