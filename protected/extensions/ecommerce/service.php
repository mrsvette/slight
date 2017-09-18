<?php
namespace Extensions;

class EcommerceService
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
        $sql = "CREATE TABLE IF NOT EXISTS `tbl_ext_product` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(128) NOT NULL,
          `description` text,
          `type` int(11) NOT NULL,
          `created_at` datetime NOT NULL,
          `created_by` int(11) NOT NULL,
          `updated_at` datetime DEFAULT NULL,
          `updated_by` int(11) DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
        
        CREATE TABLE IF NOT EXISTS `tbl_ext_product_images` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `product_item_id` int(11) NOT NULL,
          `image` varchar(128) NOT NULL,
          `thumb` varchar(128) DEFAULT NULL,
          `src` varchar(128) NOT NULL,
          `type` int(11) DEFAULT '0',
          `created_at` datetime NOT NULL,
          `created_by` int(11) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
        
        CREATE TABLE IF NOT EXISTS `tbl_ext_product_items` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `product_id` int(11) NOT NULL,
          `name` varchar(128) NOT NULL,
          `description` text,
          `parent_id` int(11) DEFAULT '0',
          `level` int(11) DEFAULT '0',
          `created_at` datetime NOT NULL,
          `created_by` int(11) NOT NULL,
          `updated_at` datetime DEFAULT NULL,
          `updated_by` int(11) DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
        
        CREATE TABLE IF NOT EXISTS `tbl_ext_product_prices` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `product_item_id` int(11) NOT NULL,
          `name` varchar(128) NOT NULL,
          `price` double NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
        
        $model = new \Model\OptionsModel();
        $install = $model->installExt($sql);

        return $install;
    }
}