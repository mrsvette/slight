<?php

namespace PanelAdmin\Controllers;

use Components\BaseController as BaseController;

class PostsController extends BaseController
{
    public function __construct($app, $user)
    {
        parent::__construct($app, $user);
    }

    public function register($app)
    {
        $app->map(['GET'], '/view', [$this, 'view']);
        $app->map(['GET', 'POST'], '/create', [$this, 'create']);
        $app->map(['GET', 'POST'], '/update/[{id}]', [$this, 'update']);
        $app->map(['POST'], '/delete/[{id}]', [$this, 'delete']);
        $app->map(['POST'], '/get-slug', [$this, 'get_slug']);
    }

    public function view($request, $response, $args)
    {
        if ($this->_user->isGuest()){
            return $response->withRedirect($this->_login_url);
        }

        $model = new \Model\PostModel();
        $posts = $model->getPosts([ 'just_default' => true]);
        
        return $this->_container->module->render($response, 'posts/view.html', [
            'posts' => $posts
        ]);
    }

    public function create($request, $response, $args)
    {
        if ($this->_user->isGuest()){
            return $response->withRedirect($this->_login_url);
        }

        $languages = \Model\PostLanguageModel::model()->findAll();
        $model = new \Model\PostModel('create');
        $categories = \Model\PostCategoryModel::model()->findAll();

        if (isset($_POST['Post'])){
            $model->status = $_POST['Post']['status'];
            $model->allow_comment = ($_POST['Post']['allow_comment'] == 'on')? 1 : 0;
            $model->post_type = $_POST['Post']['post_type'];
            $model->author_id = $this->_user->id;
            if (!empty($_POST['Post']['tags'])) {
                $model->tags = $_POST['Post']['tags'];
            }
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
            $create = \Model\PostModel::model()->save(@$model);
            if ($create > 0) {
                $post_content = \Model\PostContentModel::model();
                foreach ($_POST['PostContent']['title'] as $lang => $title) {
                    if (!empty($title) && !empty($_POST['PostContent']['content'][$lang])) {
                        $model2 = new \Model\PostContentModel;
                        $model2->post_id = $model->id;
                        $model2->title = $title;
                        if (!empty($_POST['PostContent']['slug'][$lang])){
                            $cek_slug = $post_content->findByAttributes(['slug'=>$_POST['PostContent']['slug'][$lang]]);
                            if ($cek_slug instanceof \RedBeanPHP\OODBBean) {
                                $model2->slug = $_POST['PostContent']['slug'][$lang].'2';
                            } else {
                                $model2->slug = $_POST['PostContent']['slug'][$lang];
                            }
                        } else
                            $model2->slug = $model->createSlug($title);

                        $model2->language = $lang;
                        $model2->content = $_POST['PostContent']['content'][$lang];
                        $model2->meta_keywords = $_POST['PostContent']['meta_keywords'][$lang];
                        $model2->meta_description = $_POST['PostContent']['meta_description'][$lang];
                        $model2->created_at = date("Y-m-d H:i:s");
                        $model2->updated_at = date("Y-m-d H:i:s");
                        $create_content = $post_content->save($model2);
                    }
                }
                $post_in_category = \Model\PostInCategoryModel::model();
                if (!empty($_POST['Post']['post_category']) && is_array($_POST['Post']['post_category'])) {
                    foreach ($_POST['Post']['post_category'] as $ci => $category_id) {
                        $model3 = new \Model\PostInCategoryModel();
                        $model3->post_id = $model->id;
                        $model3->category_id = $category_id;
                        $model3->created_at = date("Y-m-d H:i:s");
                        $post_in_category->save($model3);
                    }
                }
                
                $message = 'Your post is successfully created.';
                $success = true;
            } else {
                $message = 'Failed to create new post.';
                $success = false;
            }
        }

        return $this->_container->module->render($response, 'posts/create.html', [
            'languages' => $languages,
            'status_list' => $model->getListStatus(),
            'categories' => $categories,
            'message' => ($message) ? $message : null,
            'success' => $success
        ]);
    }

    public function update($request, $response, $args)
    {
        if ($this->_user->isGuest()){
            return $response->withRedirect($this->_login_url);
        }

        if (empty($args['id']))
            return false;

        $languages = \Model\PostLanguageModel::model()->findAll();
        $model = \Model\PostModel::model()->findByPk($args['id']);
        $post = new \Model\PostModel();
        $categories = \Model\PostCategoryModel::model()->findAll();

        if (isset($_POST['Post'])){
            $model->status = $_POST['Post']['status'];
            $model->allow_comment = ($_POST['Post']['allow_comment'] == 'on')? 1 : 0;
            $model->post_type = $_POST['Post']['post_type'];
            if (!empty($_POST['Post']['tags'])) {
                $model->tags = $_POST['Post']['tags'];
            }
            $model->updated_at = date('Y-m-d H:i:s');
            $update = \Model\PostModel::model()->update($model);
            if ($update) {
                $post_content = \Model\PostContentModel::model();
                foreach ($_POST['PostContent']['title'] as $lang => $title) {
                    if (!empty($title) && !empty($_POST['PostContent']['content'][$lang])) {
                        $model2 = new \Model\PostContentModel;
                        $model2->post_id = $model->id;
                        $model2->title = $title;
                        if (!empty($_POST['PostContent']['slug'][$lang])){
                            $cek_slug = $post_content->findByAttributes(['slug'=>$_POST['PostContent']['slug'][$lang]]);
                            if ($cek_slug instanceof \RedBeanPHP\OODBBean) {
                                $model2->slug = $_POST['PostContent']['slug'][$lang].'2';
                            } else {
                                $model2->slug = $_POST['PostContent']['slug'][$lang];
                            }
                        } else
                            $model2->slug = $model->createSlug($title);

                        $model2->language = $lang;
                        $model2->content = $_POST['PostContent']['content'][$lang];
                        $model2->meta_keywords = $_POST['PostContent']['meta_keywords'][$lang];
                        $model2->meta_description = $_POST['PostContent']['meta_description'][$lang];
                        $model2->updated_at = date("Y-m-d H:i:s");
                        $create_content = $post_content->update($model2);
                    }
                }
                $post_in_category = \Model\PostInCategoryModel::model();
                if (!empty($_POST['Post']['post_category']) && is_array($_POST['Post']['post_category'])) {
                    foreach ($_POST['Post']['post_category'] as $ci => $category_id) {
                        $model3 = new \Model\PostInCategoryModel();
                        $model3->post_id = $model->id;
                        $model3->category_id = $category_id;
                        $model3->created_at = date("Y-m-d H:i:s");
                        $post_in_category->save($model3);
                    }
                }

                $message = 'Your post is successfully updated.';
                $success = true;
            } else {
                $message = 'Failed to update new post.';
                $success = false;
            }
        }

        return $this->_container->module->render($response, 'posts/update.html', [
            'languages' => $languages,
            'status_list' => $model->getListStatus(),
            'categories' => $categories,
            'message' => ($message) ? $message : null,
            'success' => $success
        ]);
    }

    public function delete($request, $response, $args)
    {
        if ($this->_user->isGuest()){
            return $response->withRedirect($this->_login_url);
        }

        if (!isset($args['id'])) {
            return false;
        }

        $model = \Model\PostModel::model()->findByPk($args['id']);
        $delete = \Model\PostModel::model()->delete($model);
        if ($delete) {
            $delete2 = \Model\PostContentModel::model()->deleteAllByAttributes(['post_id'=>$args['id']]);
            $delete3 = \Model\PostInCategoryModel::model()->deleteAllByAttributes(['post_id'=>$args['id']]);
            $message = 'Your page is successfully created.';
            echo true;
        }
    }

    public function get_slug($request, $response, $args)
    {
        if ($this->_user->isGuest()){
            return $response->withRedirect($this->_login_url);
        }

        if (!isset($_POST['title'])) {
            return false;
        }

        $model = new \Model\PostModel();
        return $model->createSlug($_POST['title']);
    }
}