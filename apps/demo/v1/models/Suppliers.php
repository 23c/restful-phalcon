<?php

/**
 * @description :model demo
 * @author      :23c<phpcool@163.com>
 * @datetime    :2016-11-05
 */

class Suppliers extends ModelBase
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $company;

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'suppliers';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Suppliers[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Suppliers
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    /**
     * demo method
     * @param $name
     * @param int $limit
     * @return bool
     */
    public function getCompanyList($name, $limit = 10)
    {
        if (empty($name)) {
            return false;
        }

        $supply = self::find(array(
            'columns' => 'id,company',
            'conditions' => "company =:name:",
            'bind' => array('name' => $name),
            'limit' => $limit
        ));

        if ($supply) {
            return $supply->toArray();
        }

        return false;
    }

}