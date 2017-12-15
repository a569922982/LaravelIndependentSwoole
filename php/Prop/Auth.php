<?php
namespace Prop;

use Prop\Exception\AuthException;

class Auth
{
    public static function setAdminSession($user, $token = false, $name = 'admin', $id = 'Id')
    {
        $tokens = $token ? $token : md5($user[$id] . time());
        redis()->setex($name . ':' . $tokens, 3600 * 12, $user[$id]);
        MysqlRedis::setRedis($name, $user[$id], $user);
        return $tokens;
    }

    public static function getAdminSession($token, $name = 'admin')
    {
        $user_id = redis()->get($name . ':' . $token);
        $user = MysqlRedis::getRedisView('HASH:' . $name . ':' . $user_id);
        if ($user) static::setAdminSession($user, $token);
        return $user;
    }

    public static function clearAdminSession($token, $name = 'admin')
    {
        redis()->delete($name . ':' . $token);
    }

    public function getAdminOrFail($token, $name = 'admin')
    {
        $agent = static::getAdminSession($token, $name);
        if (!$agent) throw new AuthException('auth.no.' . $name);
        return $agent;
    }

    public function getAdmin($token, $name = 'admin')
    {
        $agent = static::getAdminSession($token, $name);
        return $agent;
    }

    public function setCode($code, $id, $name = 'user')
    {
        redis()->setex($name . ':code:' . $id, 60 * 30, $code);
    }

    public function getCode($id, $name = 'user')
    {
        $code = redis()->get($name . ':code:' . $id);
        return $code;
    }
}
