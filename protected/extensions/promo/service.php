<?php
namespace Extensions;

class PromoService
{
    protected $basePath;
    protected $themeName;
    protected $adminPath;

    public function __construct($settings = null)
    {
        $this->basePath = (is_object($settings))? $settings['basePath'] : $settings['settings']['basePath'];
        $this->themeName = (is_object($settings))? $settings['theme']['name'] : $settings['settings']['theme']['name'];
        $this->adminPath = (is_object($settings))? $settings['admin']['path'] : $settings['settings']['admin']['path'];
    }
    
    public function install()
    {
        $sql = "";
        
        $model = new \Model\OptionsModel();
        $install = $model->installExt($sql);

        return $install;
    }
}
