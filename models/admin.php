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

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['username, email, group_id, status', 'required'],
            ['username', 'length', 'min'=>5, 'max'=>32],
            ['password', 'length', 'min'=>8],
            ['email', 'email'],
            ['group_id', 'numerical', 'integerOnly' => true],
        ];
    }

    public function hasPassword($password, $salt)
    {
        return md5($salt.$password);
    }

    public function getListGroup()
    {
        return [1=>'Administrator', 2=>'Staff'];
    }

    public function getListStatus()
    {
        return ['Not Active', 'Active'];
    }
}
