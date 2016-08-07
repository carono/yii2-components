<?php

namespace carono\components\behaviors;


use yii\base\Behavior;
use yii\base\Model;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @method mixed getStoragePivots($pivotClass)
 * @method mixed getPivotAttribute($pivotClass, $column, $condition = [])
 * @method mixed updatePivotAttribute($pivotClass, $value, $column = null, $condition = [])
 * @method void clearStorage($pivotClass)
 * @method void storagePivots($models, $pivotClass, $modelClass = null)
 * @method void storagePivot($model, $pivotClass, $modelClass = null)
 * @method void savePivots($clear = false)
 * @method mixed addPivot($model, $pivotClass)
 */

/**
 * Class PivotBehavior
 *
 * @package carono\components\behaviors
 */
class PivotBehavior extends Behavior
{
    protected $_storage = [];

    public function deletePivots($class)
    {
        return $class::deleteAll([$this->getMainPkField() => $this->getMainPk()]);
    }


    public function getStoragePivots($pivotClass)
    {
        if (isset($this->_storage[$pivotClass])) {
            return $this->_storage[$pivotClass];
        } else {
            return [];
        }
    }

    public function getPivotStorage()
    {
        return $this->_storage;
    }

    public function getPivotAttribute($pivotClass, $column, $condition = [])
    {
        if (is_numeric($condition)) {
            $pv = new $pivotClass;
            $mainPk = $this->getMainPkField();
            $pk = $pv->primaryKey();
            $slavePk = current(array_diff($pk, [$mainPk]));
            $condition = [$slavePk => $condition];
        }
        $condition = array_merge($condition, [$this->getMainPkField() => $this->getMainPk()]);
        return $pivotClass::find()->andWhere($condition)->select([$column])->scalar();
    }

    public function updatePivotAttribute($pivotClass, $value, $column = null, $condition = [])
    {
        if (is_numeric($condition)) {
            $pv = new $pivotClass;
            $mainPk = $this->getMainPkField();
            $pk = $pv->primaryKey();
            $slavePk = current(array_diff($pk, [$mainPk]));
            $condition = [$slavePk => $condition];
        }
        $condition = array_merge($condition, [$this->getMainPkField() => $this->getMainPk()]);
        $pivotClass::updateAll([$column => $value], $condition);
    }

    public function clearStorage($pivotClass)
    {
        unset($this->_storage[$pivotClass]);
    }

    public function storagePivots($models, $pivotClass, $modelClass = null)
    {
        if (!is_array($models)) {
            $models = [$models];
        }
        foreach ($models as $model) {
            $this->storagePivot($model, $pivotClass, $modelClass);
        }
    }

    public function storagePivot($model, $pivotClass, $modelClass = null)
    {
        if (is_numeric($model) && $modelClass) {
            $model = $modelClass::findOne($model);
        } elseif (is_array($model)) {
            $model = \Yii::createObject($model);
        }
        if (!($model instanceof Model)) {
            throw new \Exception('Cannot determine or model not found');
        }
        $this->_storage[$pivotClass][] = $model;
    }

    public function savePivots($clear = false)
    {
        foreach ($this->getPivotStorage() as $pivotClass => $items) {
            if ($clear) {
                $this->deletePivots($pivotClass);
            }
            foreach ($items as $item) {
                $this->addPivot($item, $pivotClass);
            }
        }
    }

    public function addPivot($model, $pivotClass)
    {
        /**
         * @var ActiveRecord $pv
         */
        $pv = new $pivotClass;
        $mainPk = $this->getMainPkField();
        $pk = $pv->primaryKey();
        if (!in_array($mainPk, $pk)) {
            throw  new \Exception("Fail found pk $mainPk in " . $pivotClass);
        }
        $slavePk = current(array_diff($pk, [$mainPk]));
        $attr[$mainPk] = $this->getMainPk();
        $attr[$slavePk] = $model->id;
        if ($find = (new ActiveQuery($pivotClass))->andWhere($attr)->one()) {
            return $find;
        } else {
            $pv->setAttributes($attr);
            $pv->save();
            return $pv;
        }
    }

    protected function getMainPk()
    {
        return $this->owner->{$this->owner->primaryKey()[0]};
    }

    protected function getMainPkField()
    {
        return $this->owner->tableName() . "_id";
    }
}