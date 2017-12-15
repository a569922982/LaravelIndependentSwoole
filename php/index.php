<?php
//自动加载器
require_once __DIR__ . '/../vendor/autoload.php';
date_default_timezone_set('Asia/Shanghai');

$app = new \Illuminate\Container\Container();
\Illuminate\Container\Container::setInstance($app);

$app->instance('path', __DIR__ . '/..');
$app->instance('path.config', app('path') . '/config');
$app->instance('path.storage', app('path') . '/storage');
$app->instance('path.resource', app('path') . '/resources');
$app->instance('path.lang', app('path.resource') . '/lang');

$app->singleton('config', function () {
    return new \Illuminate\Config\Repository();
});
//database
$app->singleton('db.factory', function ($app) {
    return new \Illuminate\Database\Connectors\ConnectionFactory($app);
});
$app->singleton('db', function ($app) {
    return new \Illuminate\Database\DatabaseManager($app, $app['db.factory']);
});
\Illuminate\Database\Eloquent\Model::setConnectionResolver(app('db'));

//translator
$app->singleton('files', function () {
    return new \Illuminate\Filesystem\Filesystem;
});
$app->singleton('translation.loader', function ($app) {
    return new \Illuminate\Translation\FileLoader($app['files'], $app['path.lang']);
});
$app->singleton('translator', function ($app) {
    $loader = $app['translation.loader'];

    // When registering the translator component, we'll need to set the default
    // locale as well as the fallback locale. So, we'll grab the application
    // configuration so we can easily get both of these values from there.
    $locale = $app['config']['app.locale'];

    $trans = new \Illuminate\Translation\Translator($loader, $locale);

    $trans->setFallback($app['config']['app.fallback_locale']);

    return $trans;
});

//validator
$app->singleton('validator', function ($app) {
    $validator = new \Illuminate\Validation\Factory($app['translator'], $app);

    // The validation presence verifier is responsible for determining the existence
    // of values in a given data collection, typically a relational database or
    // other persistent data stores. And it is used to check for uniqueness.
    if (isset($app['validation.presence'])) {
        $validator->setPresenceVerifier($app['validation.presence']);
    }

    return $validator;
});
$app->singleton('validation.presence', function ($app) {
    return new \Illuminate\Validation\DatabasePresenceVerifier($app['db']);
});

//session
$app->singleton('session', function () {
    $session = new \Symfony\Component\HttpFoundation\Session\Session();
    if (!$session->isStarted()) {
        $session->start();
    }
    return $session;
});

//pagination
//\Illuminate\Pagination\Paginator::currentPageResolver(function ($pageName = 'page') {
//    $page = app('request')->input($pageName);
//
//    if (filter_var($page, FILTER_VALIDATE_INT) !== false && (int)$page >= 1) {
//        return $page;
//    }
//
//    return 1;
//});

//log
$app->singleton('log', function ($app) {
    $logger = new \Illuminate\Log\Writer(new \Monolog\Logger(config('env')));
    $level = config('app.logLevel');
    switch (strtolower($app['config']['app.log'])) {
        case 'single':
            $logger->useFiles($app['path.storage'] . '/logs/laravel.log', $level);
            break;
        case 'daily':
            $logger->useDailyFiles(
                $app['path.storage'] . '/logs/laravel.log',
                $app->make('config')->get('app.log_max_files', 5, $level)
            );
            break;
        case 'syslog':
            $logger->useSyslog('laravel', $level);
            break;
        case 'errorlog':
            $logger->useErrorLog($level);
            break;
    }
    return $logger;
});


//http client
$app->singleton('httpClient', function () {
    $client = new \GuzzleHttp\Client();
    return $client;
});

//files
$app->singleton('files', function () {
    return new \Illuminate\Filesystem\Filesystem();
});
//encrypt
$app->singleton('encrypt', function () {
    $key = substr(md5(config('app.key')), 8, 16);
    return new \Illuminate\Encryption\Encrypter($key);
});


//view
$app->singleton('view', function () {
    $view = new Philo\Blade\Blade(__DIR__ . '/../public', __DIR__ . '/../cache');
    $view = $view->view();
    $view->addExtension('html', 'blade');
    return $view;
});
function loadConfig($repository, $configPath)
{
    $configPath = realpath($configPath);
    foreach (\Symfony\Component\Finder\Finder::create()->files()->name('*.php')->in($configPath) as $file) {
        $directory = dirname($file->getRealPath());
        if ($tree = trim(str_replace($configPath, '', $directory), DIRECTORY_SEPARATOR)) {
            $tree = str_replace(DIRECTORY_SEPARATOR, '.', $tree) . '.';
        }
        $nesting = $tree;
        $files[$nesting . basename($file->getRealPath(), '.php')] = $file->getRealPath();
    }
    foreach ($files as $key => $path) {
        $repository->set($key, require $path);
    }
}

(new \Dotenv\Dotenv(__DIR__ . '/../', '.env'))->load();
loadConfig($app['config'], app('path.config'));
$app->instance('request', Illuminate\Http\Request::capture());

require_once 'validate.php';
//before entry api to start session
session();

//service
$app->singleton('auth', Prop\Auth::class);
$app->singleton('user', Prop\UserService::class);
$app->singleton('faker', function () {
    return \Faker\Factory::create();
});
$app->singleton('envLoader', function () {
    return new \Prop\EnvLoader('');
});

//exception and error
$handler = new \Prop\Exception\Handler();
error_reporting(-1);
set_error_handler([$handler, 'handleError']);
set_exception_handler([$handler, 'handleException']);
register_shutdown_function([$handler, 'handleShutdown']);
if (config('app.env') == 'production') {
    ini_set('display_errors', 'Off');
}