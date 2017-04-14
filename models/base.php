<?php
namespace Model;

require_once __DIR__ . '/../components/rb.php';

class BaseModel extends \RedBeanPHP\SimpleModel
{
    protected $connectionString;
    protected $username;
    protected $password;
    protected $frozen = true;
    protected $is_connected = false;
    protected $bean_type = 'default';
    protected $tableName;
    protected $_errors;
    protected $_scenario;

    private static $_models = array(); // class name => model
    
    public function __construct($configs = null)
    {
        if (!is_array($configs)) {
            if (!empty($configs))
                $this->_scenario = $configs;

            $configs = require __DIR__ . '/../configs/main.php';
        }

        $this->connectionString = $configs['settings']['db']['connectionString'];
        $this->username = $configs['settings']['db']['username'];
        $this->password = $configs['settings']['db']['password'];
        $this->tableName = $configs['settings']['db']['tablePrefix'].$this->tableName();

        if (!$this->is_connected) {
            $this->setup();
        }
    }
    
    public function setup()
    {
        if (!R::testConnection())
            R::setup($this->connectionString, $this->username, $this->password, $this->frozen);

        $this->is_connected = true;
        return true;
    }

    public static function model($className=__CLASS__)
    {
        if(isset(self::$_models[$className]))
            return self::$_models[$className];
        else
        {
            $model = self::$_models[$className] = new $className(null);
            return $model;
        }
    }

    /**
     * Usage : $rb = \Model\AdminModel::model()->getRb();
     * @return string
     */
    public function getRb()
    {
        return R::getVersion();
    }

    public function findByAttributes($params)
    {
        $field = array();
        foreach ($params as $attr => $val){
            $field[] = $attr. '= :'. $attr;
        }

        $sql = implode(" AND ", $field);

        return R::findOne($this->tableName, $sql, $params);
    }

    public function findAllByAttributes($params)
    {
        $field = array();
        foreach ($params as $attr => $val){
            $field[] = $attr. '= :'. $attr;
        }

        $sql = implode(" AND ", $field);

        return R::find($this->tableName, $sql, $params);
    }

    public function findAll()
    {
        return R::find($this->tableName);
    }

    public function findByPk($id)
    {
        return R::findOne($this->tableName, ' id = ?', [$id]);
    }

    public function save($bean)
    {
        $validate = $this->validate($bean);
        if ( is_array($validate) ){
            $this->_errors = $validate;
            return false;
        }

        //Create an extension to by-pass security check in R::dispense
        R::ext('xdispense', function( $type ){
            return R::getRedBean()->dispense( $type );
        });

        $dispense = R::xdispense($this->tableName);
        $attributes = get_object_vars($bean->bean);
        foreach ($attributes as $attribute => $value){
            $dispense->{$attribute} = $value;
        }

        $save = R::store($dispense);

        return ($save > 0)? true : false;
    }

    public function update($bean)
    {
        $validate = $this->validate($bean);
        if ( is_array($validate) ){
            $this->_errors = $validate;
            return false;
        }

        $update = R::store($bean);

        return ($update > 0)? true : false;
    }

    public function delete($bean)
    {
        if (!is_object($bean))
            return false;
        
        $delete = R::trash($bean);
        return true;
    }

    public function validate($bean)
    {
        if (is_array($this->rules())) {
            require __DIR__ . '/../components/validator.php';
            $validator = new \Components\Validator($bean);
            $errors = [];
            foreach ($this->rules() as $i => $rule){
                $val = $validator->execute($rule);
                if (is_array($val) && count($val)>0)
                    array_push($errors, $val);
            }

            return (count($errors) > 0)? $errors : true;
        }

        return true;
    }

    public function getErrors($in_array = true)
    {
        if ($in_array)
            return $this->_errors;
        else {
            $msg = "<ul>Please check the following errors :";
            foreach ($this->_errors as $i => $error){
                $msg .= "<li>".array_values($error)[0]."</li>";
            }
            $msg .= "</ul>";
            return $msg;
        }
    }
}

class R extends \RedBeanPHP\Facade { }