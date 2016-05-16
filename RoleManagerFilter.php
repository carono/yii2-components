<?php

namespace carono\components;


use carono\components\RoleManager;
use yii\filters\AccessControl;

class RoleManagerFilter extends AccessControl
{
    public function init()
    {
        $rule = [
            'allow'         => true,
            'matchCallback' => function ($rule, $action) {
                return RoleManager::checkAccess($action);
            }
        ];
        $this->rules[] = \Yii::createObject(array_merge($this->ruleConfig, $rule));
        parent::init();
    }
}