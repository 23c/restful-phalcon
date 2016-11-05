<?php

/**
 * @description :日志记录
 * @author      :23c<phpcool@163.com>
 * @datetime    :2016-11-05
 */

namespace Demo\Lib\Tool;

use Phalcon\DI,
    Phalcon\Logger\Adapter\File as FileAdapter;

;

class Logger {

    private static $_instance = null;
    public static $_log_id = 0;
    public static $_datetime = 0;

    /**
     * 
     * @return FileAdapter
     */
    private static function getLogger() {
        $config = DI::getDefault()->get("config");
        $path = $config->dir->log . '/' . date('Ym');
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $filename = $path . "/" . date('Ymd') . ".log";
        if (!isset(self::$_instance[$filename])) {
            self::$_instance[$filename] = new FileAdapter($filename);
        }
        self::$_log_id = base_convert(uniqid(), 16, 10);
        
        $time = explode(' ', microtime());
        $date_time = date('Y-m-d H:i:s ', $time[1]) . $time[0];
        self::$_datetime = $date_time;
        return self::$_instance[$filename];
    }

    /**
     * 
     * @param type $name可以是alert, emergency, critical等
     * @param type $arguments
     */
    public static function __callStatic($name, array $arguments) {
        $logger = self::getLogger();
        $logger = $logger->$name(implode('||', $arguments));
    }

    private static function convertStr(array $data) {
        foreach($data as $key=>$value){
            if(is_array($value)){
                $data[$key] = json_encode($value, JSON_UNESCAPED_UNICODE);
            }
        }
        $string = implode('|,|', $data);
        return sprintf("[logid:%s][%s] || %s", self::$_log_id, self::$_datetime, $string);
    }

    /**
     * 
     * @param array $data
     */
    public static function log(array $data) {
        $logger = self::getLogger();
        $string = self::convertStr($data);
        $logger->log('LOG', $string);
    }

    /**
     * 
     * @param array $data
     */
    public static function debug(array $data) {
        $logger = self::getLogger();
        $string = self::convertStr($data);
        $logger->debug($string);
    }

    /**
     * 
     * @param array $data
     */
    public static function error(array $data) {
        $logger = self::getLogger();
        $string = self::convertStr($data);
        $logger->error($string);
    }

    /**
     * 
     * @param array $data
     */
    public static function info(array $data) {
        $logger = self::getLogger();
        $string = self::convertStr($data);
        $logger->info($string);
    }

    /**
     * 
     * @param array $data
     */
    public static function notice(array $data) {
        $logger = self::getLogger();
        $string = self::convertStr($data);
        $logger->notice($string);
    }

    /**
     * 
     * @param array $data
     */
    public static function warning(array $data) {
        $logger = self::getLogger();
        $string = self::convertStr($data);
        $logger->warning($string);
    }
}
