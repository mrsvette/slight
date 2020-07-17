<?php
namespace Extensions;

class ToolService
{
    protected $basePath;
    protected $themeName;
    protected $adminPath;
    protected $tablePrefix;

    public function __construct($settings = null)
    {
        $this->basePath = (is_object($settings))? $settings['basePath'] : $settings['settings']['basePath'];
        $this->themeName = (is_object($settings))? $settings['theme']['name'] : $settings['settings']['theme']['name'];
        $this->adminPath = (is_object($settings))? $settings['admin']['path'] : $settings['settings']['admin']['path'];
        $this->tablePrefix = (is_object($settings))? $settings['db']['tablePrefix'] : $settings['settings']['db']['tablePrefix'];
    }
    
    public function install()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `{tablePrefix}ext_tool_tld` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `tld` varchar(16) DEFAULT NULL,
          `registrar_id` int(11) DEFAULT '0',
          `enabled` tinyint(4) DEFAULT '1',
          `created_at` datetime NOT NULL,
          `updated_at` datetime DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;";

        $sql .= "CREATE TABLE IF NOT EXISTS `{tablePrefix}ext_tool_tld_registrar` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(128) NOT NULL,
          `url` varchar(256) DEFAULT NULL,
          `configs` text,
          `created_at` datetime NOT NULL,
          `updated_at` datetime DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;";

        $sql = str_replace(['{tablePrefix}'], [$this->tablePrefix], $sql);

        $model = new \Model\OptionsModel();
        $install = $model->installExt($sql);

        return $install;
    }

    public function uninstall()
    {
        return true;
    }
}
