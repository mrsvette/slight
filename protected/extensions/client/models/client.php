<?php
namespace ExtensionsModel;

require_once __DIR__ . '/../../../models/base.php';

class ClientModel extends \Model\BaseModel
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ext_client';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['email, first_name, last_name', 'required'],
        ];
    }

    public function hasPassword($password, $salt)
    {
        return md5($salt.$password);
    }
}
