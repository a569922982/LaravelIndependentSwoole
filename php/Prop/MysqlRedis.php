<?php
namespace Prop;

use Model;

class MysqlRedis
{

    public static function setRedis($name, $id, $array = [])
    {
        if (preg_match('/:/', $name)) $apiName = explode(':', $name)[0];
        else $apiName = $name;
        $hashKey = 'HASH:' . $apiName . ':';
        foreach ($array as $key => $item) if (is_array($array[$key]) || is_object($array[$key])) $array[$key] = json_encode($item);
        redis()->hMset($hashKey . $id, $array);
        return $array;
    }

    public static function addRedis($name, $id, $array = [])
    {
        $listKey = 'LIST:' . $name;
        $RedisTotal = redis()->zCard($listKey) + 1;
        redis()->zAdd($listKey, $RedisTotal, $id);
        static::setRedis($name, $id, $array);
        return $array;
    }

    public static function getRedisView($name)
    {
        $list = redis()->hGetAll($name);
        foreach ($list as $key => $item) if (preg_match('/(\{.*?\}|\[.*?\])/iu', $item)) $list[$key] = json_decode($item, true);
        return $list;
    }

    public static function getRedis($name, $id, $where = false, $idName = 'id')
    {
        if (preg_match('/:/', $name)) $apiName = explode(':', $name)[0];
        else $apiName = $name;
        $hashKey = 'HASH:' . $apiName . ':';
        $apiClass = "\Model\\" . $apiName;
        if (env('APP_SQL_CACHE', true)) {
            if (!redis()->exists($hashKey . $id)) $list = static::getRedisView($hashKey . $id);
        } else {
            $query = $apiClass::where($idName, $id);
            if (is_array($where)) foreach ($where as $item) {
                $count = count($item);
                if ($count == 1) $query = $query->{$item[0]}();
                if ($count == 2) $query = $query->{$item[0]}($item[1]);
                if ($count == 3) $query = $query->{$item[0]}($item[1], $item[2]);
                if ($count == 4) $query = $query->{$item[0]}($item[1], $item[2], $item[3]);
                if ($count == 5) $query = $query->{$item[0]}($item[1], $item[2], $item[3], $item[4]);
            }
            $list = $query->first();
            if ($list) $list = $list->toArray(); else $list = [];
            static::setRedis($name, $id, $list);
        }
        return $list;
    }

    public static function checkRedis($name, $id)
    {
        $hashKey = 'HASH:' . $name . ':';
        if (!redis()->exists($hashKey . $id)) return false;
        else return true;
    }

    public static function clearRedis($name, $id)
    {
        $listKey = 'LIST:' . $name;
        if (preg_match('/:/', $name)) $apiName = explode(':', $name)[0];
        else $apiName = $name;
        $hashKey = 'HASH:' . $apiName . ':';
        if (is_array($id)) foreach ($id as $item) {
            redis()->delete($hashKey . $item);
            redis()->zRem($listKey, $item);
        } else {
            redis()->delete($hashKey . $id);
            redis()->zRem($listKey, $id);
        }
    }

    public static function saveRedis($params)
    {
        if (extension_loaded('swoole') && server()) {
            $params = json_encode($params);
            server()->task($params);
        }
    }

    public static function getRedisWhere($name, $page = false, $where = false, $order = ['id', 'desc'], $idName = 'id', $sql = true, $group = false, $with = false)
    {
        $listKey = 'LIST:' . $name;
        if (preg_match('/:/', $name)) $apiName = explode(':', $name)[0];
        else $apiName = $name;
        $hashKey = 'HASH:' . $apiName . ':';
        $apiClass = "\\Model\\" . $apiName;
        if ($page) {
            $limit = ($page[0] - 1) * $page[1];
            $limits = $page[1];
        } else {
            $limit = 0;
            $limits = -1;
        }
        $list = [];
        if ($sql && env('APP_SQL_CACHE', true)) {
            $MysqlTotal = redis()->get($listKey . ':count');
            $RedisTotal = redis()->zCard($listKey);
            static::saveRedis([
                'action' => 'getRedisCount', 'listKey' => $listKey, 'hashKey' => $hashKey, 'apiClass' => $apiClass,
                'idName' => $idName, 'where' => $where, 'with' => $with, 'name' => $name, 'group' => $group, 'order' => $order
            ]);
            if ($MysqlTotal > 0 && $MysqlTotal == $RedisTotal) {
                if (!$where || preg_match('/:/', $name)) {
//                $array = redis()->sort($listKey, array_merge(['sort' => $order[1]], $page ? ['limit' => [$limit, $limits]] : []));
                    if ($order[1] == 'desc') $array = redis()->zRange($listKey, $limit, $limits);
                    else $array = redis()->zRevRange($listKey, $limit, $limits);
                    if (is_array($array)) foreach ($array as $v) {
                        $arrays = redis()->hGetAll($hashKey . $v);
                        foreach ($arrays as $key => $item)
                            if (preg_match('/(\{.*?\}|\[.*?\])/iu', $item)) $arrays[$key] = json_decode($item, true);
                        $list[] = $arrays;
                    }
                    return ['data' => $list, 'total' => $RedisTotal];
                }
            } else static::saveRedis([
                'action' => 'getRedisList', 'listKey' => $listKey, 'hashKey' => $hashKey, 'apiClass' => $apiClass,
                'idName' => $idName, 'where' => $where, 'with' => $with, 'name' => $name, 'group' => $group, 'order' => $order
            ]);
        }
        $query = $apiClass::orderBy($order[0], $order[1]);
        if (is_array($where)) foreach ($where as $item) {
            $count = count($item);
            if ($count == 1) $query = $query->{$item[0]}();
            if ($count == 2) $query = $query->{$item[0]}($item[1]);
            if ($count == 3) $query = $query->{$item[0]}($item[1], $item[2]);
            if ($count == 4) $query = $query->{$item[0]}($item[1], $item[2], $item[3]);
            if ($count == 5) $query = $query->{$item[0]}($item[1], $item[2], $item[3], $item[4]);
        }
        if ($with) $query = $query->with($with);
        if ($group) $query = $query->groupBy($group);
        $MysqlTotal = $query->count();
        if ($page) $query = $query->skip($limit)->take($limits);
        $list = $query->get()->toArray();
        return ['data' => $list, 'total' => $MysqlTotal];
    }
}
