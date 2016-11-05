<?php

/**
 * @description :base of all models
 * @author      :23c<phpcool@163.com>
 * @datetime    :2016-11-05
 */

use Phalcon\Logger\Adapter\File as FileAdapter;
/**
 * Abstract model,included commond methods for data access and manipulations for derived classes.
 * @uses Db\Connection
 */
abstract class ModelBase extends \Phalcon\Mvc\Model
{
    public function initialize() {
        $this->setWriteConnectionService('dbMaster');
        $this->setReadConnectionService('dbSlave');

        self::setup(array('notNullValidations' => false));

        $this->addBehavior(
            new \Phalcon\Mvc\Model\Behavior\Timestampable(
                ['beforeCreate' => ['field'  => 'createTime','format' => 'Y-m-d H:i:s']]
            )
        );
    }

    /**
     *
     * Instances of the derived classes.
     * @var array
     */
    protected static $instances = array();

    /**
     * Get instance of the derived class.
     *
     * @param bool $noSingleton 是否获取单件,默认 true.
     *
     * @return static
     */
    public static function instance($singleton=true)
    {
        $className = get_called_class();
        if(!$singleton)
        {
            return new $className;
        }
        if (!isset(self::$instances[$className]))
        {
            self::$instances[$className] = new $className;
        }
        return self::$instances[$className];
    }

    /**
     * memcached object
     *
     * @return mixed
     */
    public function cache()
    {
        return $this->getDI()->get('modelsCache');
    }
}
