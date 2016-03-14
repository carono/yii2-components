<?php

namespace carono\components;

use yii\helpers\ArrayHelper;

/**
 * Class Breadcrumbs
 *
 * for use, define public functions like crumbModuleControllerAction($param1,...) or crumbControllerAction($param1,...)
 * call in Controller->render
 *
 * @package carono\components
 */
class Breadcrumbs
{
    public $home = ["encode" => false, "label" => '<span class="glyphicon glyphicon-home"></span>', "url" => '/'];

    /**
     * @param \yii\web\Controller $controller
     * @param array               $params
     *
     * @return array
     */
    public function form($controller, $params)
    {
        $method = "crumb";
        if ($controller->module->id != 'basic') {
            $method .= ucfirst($controller->module->id);
        }
        $method .= ucfirst($controller->id) . ucfirst($controller->action->id);
        if (method_exists($this, $method)) {
            $links = call_user_func_array([$this, $method], $params);
        } else {
            $links = [];
        }
        return ArrayHelper::merge([$this->home], $links);
    }
}