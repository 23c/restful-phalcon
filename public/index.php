<?php
use Phalcon\Loader;
use Demo\Apps\MicroBase;
use Phalcon\DI\FactoryDefault;

define('APP_PATH', realpath('..'));

define('ROOT_PATH', __DIR__.DIRECTORY_SEPARATOR);
define('Demo_LOG_ID', base_convert(uniqid(), 16, 10));
//定义版本号Key
define('API_VERSION_KEY', 'ACCESS-TOKEN');

error_reporting(E_ALL);

try {

    $di = new FactoryDefault();

    $loader = new Loader();
    $loader->registerDirs( array(
            APP_PATH . '/apps/',
            APP_PATH . '/lib/',
        )
    )->register();

    $loader->registerNamespaces(
        array(
            'Demo\Apps' => APP_PATH . '/apps',
            'Demo\Lib\Tool' => APP_PATH . '/lib/tool',
        )
    );

    $uri = $_SERVER['REQUEST_URI'];

    $uri = ($uri == '/') ? '/demo/index/getMessage' : $uri;

    // list($module, $controller, $action) = explode('/', trim($uri, '/'));

    $arr = explode('/', trim($uri, '/'));

    $module = isset($arr[0]) ? $arr[0] : 'demo';

    $controller = isset($arr[1]) ? $arr[1] : 'index';

    $action = isset($arr[2]) ? $arr[2] : 'getMessage';

    $app = new MicroBase($di);

    $app->start($module, $controller, $action);


} catch (Exception $e) {
    var_dump($e->getMessage());
}

/*

路由约定：
Sample：
http://domain/module_name/controllerName/actionName

通过rawBody的方式传参。

actionName说明：
[actionName]直接对应get请求.
[actionName]Add,对应post请求.
[actionName]Update,对应put请求.
[actionName]Delete,对应delete请求.

*/