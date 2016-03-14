<?php

namespace carono\components;

use yii\helpers\ArrayHelper;

/**
 * Class Breadcrumbs
 *
 * for use, define public functions like crumbModuleControllerAction($param1,...) or crumbControllerAction($param1,...)
 * call in Controller->render
 * @package carono\components
 */
class Breadcrumbs
{
    public $home = ["encode" => false, "label" => '<span class="glyphicon glyphicon-home"></span>', "url" => '/'];
    private static $_instance;

    /**
     * @param \yii\web\Controller $controller
     * @param array               $params
     *
     * @return array
     */
    public static function form($controller, $params)
    {
        $method = "crumb";
        if ($controller->module->id != 'basic') {
            $method .= ucfirst($controller->module->id);
        }
        $method .= ucfirst($controller->id) . ucfirst($controller->action->id);
        if (method_exists(self::instance(), $method)) {
            $links = call_user_func_array([self::instance(), $method], $params);
        } else {
            $links = [];
        }
        return ArrayHelper::merge([self::instance()->home], $links);
    }

    public static function instance()
    {
        if (self::$_instance) {
            return self::$_instance;
        } else {
            return self::$_instance = new self;
        }
    }
}