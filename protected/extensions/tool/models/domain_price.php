<?php
namespace ExtensionsModel;

require_once __DIR__ . '/../../../models/base.php';

class DomainPriceModel extends \Model\BaseModel
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ext_tool_domain_price';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['reseller_id', 'required', 'on' => 'create'],
        ];
    }

    public function get_price($data) {
        $sql = "SELECT t.price, t.updated_at, TIMESTAMPDIFF(HOUR, t.updated_at, NOW()) AS hours_different     
        FROM {tablePrefix}ext_tool_domain_price t 
        WHERE t.tld =:tld AND t.reseller_id =:reseller_id";

        if (isset($data['hours_different'])) {
            $sql .= ' HAVING hours_different <='.$data['hours_different'];
        }
        $params = [
            'tld' => $data['tld'],
            'reseller_id' => $data['reseller_id']
        ];

        $sql = str_replace(['{tablePrefix}'], [$this->_tbl_prefix], $sql);

        $row = \Model\R::getRow( $sql, $params );

        return $row;
    }

    public function save_price($data) {
        $model = self::model()->findByAttributes(['tld' => $data['tld'], 'reseller_id' => $data['reseller_id']]);
        if ($model instanceof \RedBeanPHP\OODBBean) {
            $model->price = $data['price'];
            $model->updated_at = date('c');
            $update = self::model()->update($model);
        } else {
            $model = new \ExtensionsModel\DomainPriceModel('create');
            $model->tld = $data['tld'];
            $model->reseller_id = $data['reseller_id'];
            $model->price = $data['price'];
            $model->created_at = date('c');
            $model->updated_at = date('c');
            $save = self::model()->save($model);
        }

        return true;
    }
}
