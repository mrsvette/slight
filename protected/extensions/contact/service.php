<?php
namespace Extensions;

class ContactService
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
        $sql = "CREATE TABLE IF NOT EXISTS `{tablePrefix}ext_contact` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(128) NOT NULL,
          `email` varchar(128) NOT NULL,
          `phone` varchar(32) DEFAULT NULL,
          `message` text,
          `created_at` datetime DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
        COMMIT;";

        $sql = str_replace(['{tablePrefix}'], [$this->tablePrefix], $sql);
        
        $model = new \Model\OptionsModel();
        $install = $model->installExt($sql);

        return $install;
    }

    public function uninstall()
    {
        return true;
    }

    public function getMenu()
    {
        return [
            [ 'label' => 'Daftar Pesan', 'url' => 'contact/messages/view', 'icon' => 'fa fa-search' ],
        ];
    }
}
