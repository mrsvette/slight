<?php
namespace Model;

require_once __DIR__ . '/base.php';

class VisitorModel extends \Model\BaseModel
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'visitor';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            //['ip_address, url', 'safe'],
        ];
    }

    public function deactivate($session_id)
    {
        $sql = 'UPDATE tbl_visitor SET active = 0 WHERE session_id = :session_id AND active = 1';

        $update = R::exec( $sql, ['session_id' => $session_id] );

        return $update;
    }

    public function getCookie($name='_ma',$expiration=true)
    {
        if(!empty($_COOKIE[$name])){
            $pecah = explode("-",$_COOKIE[$name]);
            if($expiration){
                if(!empty($pecah[1]))
                    return date("Y-m-d H:i:s",strtotime($pecah[1]));
                else
                    return null;
            }else
                return $pecah[0];
        }
        return null;
    }
}
