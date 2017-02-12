<?php

/**
 * @description :config initialize
 * @author      :23c<phpcool@163.com>
 * @datetime    :2016-11-05
 */

namespace Demo\Apps;

use Phalcon\Mvc\Micro,
    Phalcon\Di,
    Phalcon\Loader,
    Phalcon\Http\Request,
    Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter,
    Phalcon\Mvc\Micro\Collection as MicroCollection;

class MicroBase extends Micro
{
    public $module = null;
    public $collection = null;
    public $version = null;

    public function __construct($di)
    {
        $this->setDi($di);
        $request = new Request();
        $this->collection = new MicroCollection();
        $this->version = $request->getHeader(API_VERSION_KEY) ? $request->getHeader(API_VERSION_KEY): 'v1';
        $this->notFound(function () {
            $this->response->setStatusCode(404, "Not Found")->sendHeaders();
            echo 'URI not found!';
        });
    }

    public function start($module = 'demo', $controller= 'Index', $action='getMessage')
    {
        try {
            $match = preg_match("/(Add|Update|Delete)$/i", $action);
            if ($match) {
                $this->show404($this);
                exit;
            }
            $uc_module = ucwords($module);
            $uc_controller = ucwords($controller);
            //loading config
            $config_path = APP_PATH . '/apps/' . $module . '/' . $this->version . '/config/config.php';

            if (file_exists($config_path)) {
                $config = include $config_path;
                $this->di->set('config', $config);
                //master database
                $this->di->set('dbMaster', function () use ($config) {
                    $eventsManager = new \Phalcon\Events\Manager();
                    $eventsManager->attach('db', function($event, $connection) {
                        if ($event->getType() == 'beforeQuery') {
                            \Demo\Lib\Tool\Logger::info(['dbMaster', $connection->getSQLStatement(), $connection->getSQLBindTypes()]);
                        }
                    });
                    $connection = new DbAdapter($config->dbv2->toArray());
                    $connection->setEventsManager($eventsManager);
                    return $connection;
                });
                //slave database
                $this->di->set('dbSlave', function () use ($config) {
                    $eventsManager = new \Phalcon\Events\Manager();
                    $eventsManager->attach('db', function($event, $connection) {
                        if ($event->getType() == 'beforeQuery') {
                            \Demo\Lib\Tool\Logger::info(['dbSlave', $connection->getSQLStatement(), $connection->getSQLBindTypes()]);
                        }
                    });
                    $connection = new DbAdapter($config->dbv2_r->toArray());
                    $connection->setEventsManager($eventsManager);
                    return $connection;
                });

                //redis models cache
                if (isset($config->redis)) {
                    $this->di->set('modelsCache',
                        function () use ($config) {
                            $frontCache = new \Phalcon\Cache\Frontend\Data(array(
                                "lifetime" => 86400
                            ));
                            $redis_conf = $config->redis;
                            unset($redis_conf->auth);
                            $cache = new \Phalcon\Cache\Backend\Redis($frontCache,
                                array_merge($redis_conf->toArray(), []));
                            return $cache;
                        });
                }

                if (isset($config->redis)) {
                    $this->di->setShared('cache',
                        function () use ($config) {
                            $frontCache = new \Phalcon\Cache\Frontend\Data(array(
                                "lifetime" => 86400
                            ));
                            $redis_conf = $config->redis;
                            unset($redis_conf->auth);
                            $cache = new \Phalcon\Cache\Backend\Redis($frontCache,
                                array_merge($redis_conf->toArray(), []));
                            return $cache;
                        });
                }

                $loader = new Loader();
                $loader->registerNamespaces(
                    array(
                        'Demo\Apps\\' . $uc_module . '\\'.ucwords($this->version).'\Controllers' => APP_PATH . '/apps/'.$module.'/'.$this->version.'/controllers',
                    )
                );

                $loader->registerDirs(array(__DIR__ . '/' . $module . '/' . $this->version . '/models'), true);
                $loader->register();

                $class = '\\Dc\\Apps\\' . $uc_module . '\\' . ucwords($this->version) . '\\Controllers\\' . $uc_controller . 'Controller';
                $method = strtoupper($this->request->getMethod());
                if (class_exists($class)) {

                    if (
                        method_exists($class, $action) ||
                        method_exists($class, $action . 'Add') ||
                        method_exists($class, $action . 'Update') ||
                        method_exists($class, $action . 'Delete')
                    ) {

                        //加载controller
                        $handler = new $class();
                        $this->collection->setHandler($handler);
                        $this->collection->setPrefix('/' . $module . '/');
                        $uri = $controller . '/' . $action;
                        switch ($method) {
                            case 'POST':
                                if ($action == 'getHomeNoteInfoById') {
                                    if (method_exists($class, $action)) {
                                        $this->collection->post($uri, $action);
                                    }
                                    break;
                                }

                                $action .= 'Add';
                                if (method_exists($class, $action)) {
                                    $this->collection->post($uri, $action);
                                }
                                break;
                            case 'PUT':
                                $action .= 'Update';
                                if (method_exists($class, $action)) {
                                    $this->collection->put($uri, $action);
                                }
                                break;
                            case 'DELETE':
                                $action .= 'Delete';
                                if (method_exists($class, $action)) {
                                    $this->collection->delete($uri, $action);
                                }
                                break;
                            default:
                                if (method_exists($class, $action)) {
                                    $this->collection->get($uri, $action);
                                }
                                break;
                        }
                        if (method_exists($class, $action)) {
                            \Dc\Lib\Tool\Logger::info(['WEB_API_LOG', $class, $action, $method, $this->request->getRawBody()]);
                            $this->mount($this->collection);
                            $this->handle();
                        } else {
                            echo $class, "\t", $action;
                            $this->show404($this);
                            exit;
                        }
                    } else {
                        throw new \Exception('资源不存在');
                    }
                } else {
                    $this->show404($this);
                    exit;
                }
            } else {
                $this->show404($this);
                exit;
            }

        } catch (\Exception $ex) {
            $err = array(
                'error_code' => -10000,
                'msg' => $ex->getMessage(),
                'class' => $class,
                'data' => array('file' => $ex->getFile(), 'line' => $ex->getLine())
            );
            echo json_encode($err);
            exit;
        }
    }

    private function show404($app)
    {
        $app->response->setStatusCode(404, "Not Found")->sendHeaders();
        $arr = array('error_code'=>404,'msg'=>'404 Not Found','data'=>'');
        $app->response->setHeader('Content-type', 'application/json');
        $app->response->setJsonContent($arr, JSON_UNESCAPED_UNICODE);
        $app->response->send();
    }
}
