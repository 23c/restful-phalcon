<?php

!defined('APP_PATH') && define('APP_PATH', realpath(__DIR__ . '/../../../'));

$config = array(
    'dbMaster' => array(
        'adapter' => 'Mysql',
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => '',
        'dbname' => 'dbmaster',
        'charset' => 'utf8',
        'persistent' => false,
    ),
    'dbSlave' => array(
        'adapter' => 'Mysql',
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => '',
        'dbname' => 'dbslave',
        'charset' => 'utf8',
        'persistent' => false,
    ),
    'redis' => array(
        'host' => '127.0.0.1',
        'port' => 6379,
        'auth' => '',
        'persistent' => false,
    ),
    'redis_r' => array(
        'host' => '127.0.0.1',
        'port' => 6379,
        'auth' => '',
        'persistent' => false,
    ),
    'memcached' => array(array(
            'host' => '127.0.0.1',
            'port' => '11211',
            'weight' => 1,
        )),
    'dir' => array(
        'log' => __DIR__ . '/../../../log',
    ),
);

return new Phalcon\Config( $config );
