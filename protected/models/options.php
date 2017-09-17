<?php
namespace Model;

require_once __DIR__ . '/base.php';

class OptionsModel extends \Model\BaseModel
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'options';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['option_name, option_value', 'required'],
        ];
    }

    public function getOptions($data)
    {
        $sql = 'SELECT t.option_name, t.option_value  
          FROM tbl_options t 
          WHERE 1';

        $options = R::getAll( $sql );
        $items = [];
        if (is_array($options)) {
            foreach ($options as $i => $option) {
                $items[$option['option_name']] = $option['option_value'];
            }
        }

        return $items;
    }

    /**
     * Installing the new extension from ext service
     * @param $sql
     * @param $params
     * @return int
     */
    public function installExt($sql, $params)
    {
        $execute = R::exec($sql, $params);
        return $execute;
    }
}
