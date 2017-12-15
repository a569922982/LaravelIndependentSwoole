<?php
/**
 * Created by Chen.
 * Date: 2016/6/6
 * Time: 11:17
 */
app('validator')->extend('public_ip', function ($attribute, $value, $parameters) {
    return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)
    !== false && $value != '0.0.0.0' && !preg_match('/127/', $value);
});
app('validator')->extend('port', function ($attribute, $value, $parameters) {
    return filter_var($value, FILTER_VALIDATE_INT) && $value > 0 && $value <= 65535;
});
app('validator')->extend('port_format', function ($attribute, $value, $parameters) {
    return filter_var($value, FILTER_VALIDATE_INT) && $value > 0 && $value <= 65535;
});
app('validator')->extend('strip_tags', function ($attribute, $value, $parameters) {
    return $value == strip_tags($value);
});
app('validator')->extend('email_mobile', function ($attribute, $value, $parameters) {
    return filter_var($value, FILTER_VALIDATE_EMAIL) !== false || preg_match('/^1[34578]\d{9}$/', $value);
});
app('validator')->extend('mobile', function ($attribute, $value, $parameters) {
    return preg_match('/^1[34578]\d{9}$/', $value);
});
app('validator')->extend('qq', function ($attribute, $value, $parameters) {
    return preg_match('/^[1-9]\d{4,12}$/', $value);
});
app('validator')->extend('auth_code', function ($attribute, $value, $parameters) {
    return app('auth')->validateCode($value);
});
app('validator')->extend('certificate_name_duplicate', function ($attribute, $value, $parameters) {
    $user = app('auth')->getUserSession();
    return !\Model\Certificate::where('certificate_name', strtolower($value))
        ->where('user_id', $user->id)
        ->first();
});
app('validator')->extend('openssl_x509_base64', function ($attribute, $value, $parameters) {
    return openssl_x509_parse(base64_decode($value));
});
app('validator')->extend('openssl_pkey_base64', function ($attribute, $value, $parameters) {
    return openssl_pkey_get_private(base64_decode($value));
});
app('validator')->extend('domain', function ($attribute, $value, $parameters) {
    return preg_match('/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i', $value)
    && preg_match('/^.{1,253}$/', $value)
    && preg_match('/^[^\.]{1,63}(\.[^\.]{1,63})*$/', $value);
});
app('validator')->extend('second_domain', function ($attribute, $value, $parameters) {
    return $value == '@' || $value == '*'
    || preg_match('/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i', $value)
    && preg_match('/^.{1,253}$/', $value)
    && preg_match('/^[^\.]{1,63}(\.[^\.]{1,63})*$/', $value);
});
app('validator')->extend('domain_black_list', function ($attribute, $value, $parameters) {
    return !\Model\DomainBlackList::findByDomain($value);
});
app('validator')->extend('domain_preg', function ($attribute, $value, $parameters) {
    $domainArray = explode('.', $value);
    array_shift($domainArray);
    $domain = implode('.', $domainArray);
    return \Model\DomainPreg::findByDomain($domain);
});
app('validator')->extend('domain_exists', function ($attribute, $value, $parameters) {
    return !\Model\Domain::findByAgent($value);
});
app('validator')->extend('password_format', function ($attribute, $value, $parameters) {
    $value = trim($value);
    $match = '/^[\w\W]{6,20}$/i';
    return empty($value) ? false : preg_match($match, $value);

});
app('validator')->extend('salt_format', function ($attribute, $value, $parameters) {
    return preg_match('/^[\d\D]{32}$/i', $value);
});
app('validator')->extend('high_port_source_ips', function ($attribute, $value, $parameters) {
    $checkSourceIps = \Model\HighPort::checkSourceIps($value);
    if (!$checkSourceIps) {
        return false;
        //throw new \Exception('validate.ipPorts.empty');
    }
    foreach ($value as $sourceIp) {
        $checkPublicIp = \Model\HighPort::checkPublicIp($sourceIp['ip']);
        if (!$checkPublicIp) {
            return false;
            //throw new \Exception('validate.ipPorts.error');
        }
        $checkWeight = \Model\HighPort::checkWeight($sourceIp['weight']);
        if (!$checkWeight) {
            return false;
            //throw new \Exception('validate.weight.error');
        }
        $checkStatus = \Model\HighPort::checkStatus($sourceIp['status']);
        if (!$checkStatus) {
            return false;
            //throw new \Exception('validate.status.error');
        }
    }
    return true;
});
app('validator')->extend('agentId', function ($attribute, $value, $parameters) {
    $result = \Model\Config::isValidAgentId($value);
    return isset($result) ? true : false;
});
app('validator')->extend('seconddomain_waf_option', function ($attribute, $value, $parameters) {
    return array_keys($value) == ["1", "2", "3", "4"] ? true : false;
});

app('validator')->extend('attachment', function ($attribute, $value, $parameters) {
    $match = array('txt', 'zip', 'rar', '7z', 'tar.gz', 'tar.bz2', 'tar.xz', 'jpg', 'jpeg', 'png');
    if (is_array($value)) {
        foreach ($value as $item) {
            if (!in_array($item->getClientOriginalExtension(), $match)) {
                return false;
            }
        }
    }
    $type = $value->getClientOriginalExtension();
    return in_array($type, $match);
});
