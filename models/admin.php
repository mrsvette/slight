<?php
namespace Model;

require_once __DIR__ . '/base.php';

class AdminModel extends \Model\BaseModel
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'admin';
    }

    public function hasPassword($password, $salt)
    {
        return md5($salt.$password);
    }
}
