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

    private static $_models = array(); // class name => model
    
    public function __construct($configs = null)
    {
        if (!is_array($configs)) {
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

    public function findByPk($id)
    {
        return R::findOne($this->tableName, ' id = ?', [$id]);
    }
}

class R extends \RedBeanPHP\Facade { }