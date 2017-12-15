<?php
/**
 * Created by Chen.
 * Date: 2016/5/17
 * Time: 21:25
 */
use Illuminate\Support\Str;

if (!function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @param  string $make
     * @param  array $parameters
     * @return mixed|\Illuminate\Container\Container
     */
    function app($make = null, $parameters = [])
    {
        if (is_null($make)) {
            return \Illuminate\Container\Container::getInstance();
        }

        return \Illuminate\Container\Container::getInstance()->make($make, $parameters);
    }
}

if (!function_exists('config')) {
    /**
     * Get / set the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array|string $key
     * @param  mixed $default
     * @return mixed
     */
    function config($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('config');
        }

        if (is_array($key)) {
            return app('config')->set($key);
        }

        return app('config')->get($key, $default);
    }
}

if (!function_exists('validator')) {
    /**
     * Create a new Validator instance.
     *
     * @param  array $data
     * @param  array $rules
     * @param  array $messages
     * @param  array $customAttributes
     * @return \Illuminate\Contracts\Validation\Validator
     */
    function validator(array $data = [], array $rules = [], array $messages = [], array $customAttributes = [])
    {
        $factory = app('validator');

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($data, $rules, $messages, $customAttributes);
    }
}

if (!function_exists('trans')) {
    /**
     * Translate the given message.
     *
     * @param  string $id
     * @param  array $parameters
     * @param  string $domain
     * @param  string $locale
     * @return \Symfony\Component\Translation\TranslatorInterface|string
     */
    function trans($id = null, $parameters = [], $domain = 'messages', $locale = null)
    {
        if (is_null($id)) {
            return app('translator');
        }

        return app('translator')->trans($id, $parameters);
    }
}

if (!function_exists('db')) {
    /**
     * @return \Illuminate\Database\DatabaseManager
     */
    function db()
    {
        return app('db');
    }
}

if (!function_exists('logger')) {
    /**
     * Log a debug message to the logs.
     *
     * @param  string $message
     * @param  array $context
     * @return \Illuminate\Contracts\Logging\Log|null
     */
    function logger($message = null, array $context = [])
    {
        if (is_null($message)) {
            return app('log');
        }

        return app('log')->debug($message, $context);
    }
}

if (!function_exists('request')) {
    /**
     * Get an instance of the current request or an input item from the request.
     *
     * @param  string $key
     * @param  mixed $default
     * @return \Illuminate\Http\Request|string|array
     */
    function request($key = null, $default = null)
    {
        if (extension_loaded('swoole') && isset($GLOBALS['Request']))
            $request = Prop\Request::createWithSwooleRequest($GLOBALS['Request']);
        else $request = app('request');
        if (is_null($key)) {
            return $request;
        }
        return $request->input($key, $default);
    }
}

if (!function_exists('httpUrl')) {
    /**
     * @return string
     */
    function httpUrl()
    {
        return request()->url();
    }
}

if (!function_exists('httpClient')) {
    /**
     * @return \GuzzleHttp\Client
     */
    function httpClient()
    {
        return app('httpClient');
    }
}

if (!function_exists('filesystem')) {
    /**
     * @return \Illuminate\Filesystem\Filesystem
     */
    function filesystem()
    {
        return app('files');
    }
}

if (!function_exists('session')) {
    /**
     * @param string $key
     * @param mixed $value
     * @return \Symfony\Component\HttpFoundation\Session\Session|mixed
     */
    function session($key = null, $value = null)
    {
        if (is_null($key)) {
            return app('session');
        }

        if (is_null($value)) {
            return app('session')->get($key);
        }

        app('session')->set($key, $value);
        return app('session')->save();
    }
}

if (!function_exists('env')) {
    /**
     * Gets the value of an environment variable. Supports boolean, empty and null.
     *
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    function env($key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return value($default);
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;

            case 'false':
            case '(false)':
                return false;

            case 'empty':
            case '(empty)':
                return '';

            case 'null':
            case '(null)':
                return;
        }

        if (strlen($value) > 1 && Str::startsWith($value, '"') && Str::endsWith($value, '"')) {
            return substr($value, 1, -1);
        }

        return $value;
    }
}


if (!function_exists('encrypt')) {
    /**
     * @param $value
     * @param int $expiry
     * @return mixed
     */
    function encrypt($value, $expiry = 0)
    {
        return app('encrypt')->encrypt($value, $expiry);
    }
}

if (!function_exists('decrypt')) {
    /**
     * @param $value
     * @param int $expiry
     * @return mixed
     */
    function decrypt($value, $expiry = 0)
    {
        return app('encrypt')->decrypt($value, $expiry);
    }
}

if (!function_exists('redis')) {
    /**
     * @return Redis
     */
    function redis()
    {
        $redis = new Redis();
        $redis->connect(env('REDIS_HOST'), env('REDIS_PORT'));
        return $redis;
    }
}

if (!function_exists('server')) {
    function server()
    {
        try {
            $server = $GLOBALS['serv'];
        } catch (\Exception $e) {
            $server = false;
        }
        return $server;
    }
}

if (!function_exists('gl')) {
    function gl($name, $value = null)
    {
        if (is_null($value)) return $GLOBALS[$name];
        return $GLOBALS[$name] = $value;
    }
}

if (!function_exists('pay')) {
    function pay($setting = null)
    {
        $config = config('payconfig');
        if (!is_null($setting)) $config = array_merge($config, $setting);
        return new Yansongda\Pay\Pay($config);
    }
}

if (!function_exists('Queue')) {
    function Queue()
    {
        $queue = new \Illuminate\Queue\Capsule\Manager();
        $container = $queue->getContainer();
        $container['config']['database.redis'] = config('database.redis');
        $container->singleton('redis', function ($container) {
            return new \Illuminate\Redis\Database($container['config']['database.redis']);
        });
        $container['config']["queue.connections.redis"] = [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => 'default',
            'retry_after' => 30,
        ];
        $queue->addConnection($container['config']["queue.connections.redis"]);
        $queue->setAsGlobal();
        return $queue;
    }
}


if (!function_exists('weChat')) {
    function weChat($name = 'officialAccount')
    {
        return EasyWeChat\Factory::$name(config('wxconfig.' . $name));
    }
}

if (!function_exists('base_path')) {
    /**
     * Get the path to the base of the install.
     *
     * @param  string $path
     * @return string
     */
    function base_path($path = '')
    {
        return app()->basePath() . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}
if (!function_exists('view')) {
    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string $view
     * @param  array $data
     * @param  array $mergeData
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    function view($view = null, $data = [], $mergeData = [])
    {
        return app('view')->make($view, $data, $mergeData)->render();
    }
}

if (!function_exists('getClientIp')) {
    function getClientIp()
    {
        return request()->getClientIp();
    }
}

if (!function_exists('sendMail')) {
    function sendMail($mailTo, $mailSubject, $mailBody)
    {
        $transport = Swift_SmtpTransport::newInstance(env('MAIL_HOST'), env('MAIL_PORT'))
            ->setUsername(env('MAIL_USERNAME'))
            ->setPassword(env('MAIL_PASSWORD'));
        $message = Swift_Message::newInstance($mailSubject, $mailBody)
            ->setFrom([env('MAIL_USERNAME')])
            ->setTo([$mailTo]);
        $result = Swift_Mailer::newInstance($transport)->send($message);
        if ($result == 0) {
            throw new \Prop\Exception\EmailException('send mail failed!');
        }
    }
}