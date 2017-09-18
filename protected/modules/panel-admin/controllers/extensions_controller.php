<?php

namespace PanelAdmin\Controllers;

use Components\BaseController as BaseController;

class ExtensionsController extends BaseController
{

    public function __construct($app, $user)
    {
        parent::__construct($app, $user);
    }

    public function register($app)
    {
        $app->map(['GET'], '/view', [$this, 'view']);
        $app->map(['GET', 'POST'], '/setup', [$this, 'setup']);
    }

    public function view($request, $response, $args)
    {
        if ($this->_user->isGuest()){
            return $response->withRedirect($this->_login_url);
        }

        $tools = new \PanelAdmin\Components\AdminTools($this->_settings);
        $installed_exts = $this->_settings['params']['extensions'];
        if (empty($installed_exts))
            $installed_exts = false;
        else
            $installed_exts = json_decode($installed_exts, true);

        return $this->_container->module->render($response, 'extensions/view.html', [
            'extensions' => $tools->getExtensions(),
            'installed_exts' => $installed_exts
        ]);
    }

    public function setup($request, $response, $args)
    {
        if ($this->_user->isGuest()){
            return $response->withRedirect($this->_login_url);
        }

        $tools = new \PanelAdmin\Components\AdminTools($this->_settings);

        if (isset($_POST['id'])){
            $model = \Model\OptionsModel::model()->findByAttributes(['option_name'=>'extensions']);
            if ($model instanceof \RedBeanPHP\OODBBean) {
                $exts = [];
                if (!empty($model->extension))
                    $exts = json_decode($model->extension, true);

                if ((int)$_POST['install'] < 1){
                    if (in_array($_POST['id'], $exts)) {
                        $items = [];
                        foreach ($exts as $i => $ext) {
                            if ($ext != $_POST['id'])
                                array_push($items, $ext);
                        }
                        $exts = $items;
                    }
                } else {
                    if (!in_array($_POST['id'], $exts))
                        array_push($exts, $_POST['id']);
                }

                $model->option_value = json_encode($exts);
                $model->updated_at = date('Y-m-d H:i:s');
                $save = \Model\OptionsModel::model()->update($model);
            } else {
                $exts = [$_POST['id']];
                $model = new \Model\OptionsModel();
                $model->option_name = 'extensions';
                $model->option_value = json_encode($exts);
                $model->created_at = date('Y-m-d H:i:s');
                $save = \Model\OptionsModel::model()->save($model);
            }
            
            if ($save) {
                $message = ($_POST['install'] > 0)? 'Ekstensi '.$_POST['id'].' berhasil diaktifkan' : 'Sukses meng-nonaktifkan ekstensi '.$_POST['id'];
                $success = true;
                $hooks = new \PanelAdmin\Components\AdminHooks($this->_settings);
                $omodel = new \Model\OptionsModel();
                $hooks->onAfterParamsSaved($omodel->getOptions());

                $className = "Extensions\\".ucfirst($_POST['id'])."Service";
                $ecommerce = new $className($this->_settings);
                if (is_object($ecommerce) && method_exists($ecommerce, 'install')) {
                    try {
                        $ecommerce->install();
                    } catch (\Exception $e) {
                        var_dump($e->getMessage());
                    }
                }
            } else {
                $message = 'Gagal menyimpan data.';
                $success = false;
            }

            return json_encode(['success'=>$success, 'message'=>$message]);
        }
    }
}
