<?php

namespace carono\components\behaviors;


use yii\base\Behavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
* @method mixed getStoragePivots($pivotClass)
* @method mixed getPivotAttribute($pivotClass, $column, $condition = [])
* @method mixed updatePivotAttribute($pivotClass, $value, $column = null, $condition = [])
* @method void storagePivots($values, $pivotClass, $class)
* @method mixed addPivot($model, $pivotClass)
* 
*/

class PivotBehavior extends Behavior
{
    protected $_storage = [];

    protected function getMainPk()
    {
        return $this->owner->{$this->owner->primaryKey()[0]};
    }

    protected function getMainPkField()
    {
        return $this->owner->tableName() . "_id";
    }

    public function clearPivot($class)
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

    public function storagePivots($values, $pivotClass, $class)
    {
        if (!is_array($values)) {
            $values = [$values];
        } else {
            $values = array_filter($values);
        }
        foreach ($values as $value) {
            if ($model = $class::findOne($value)) {
                $this->_storage[$pivotClass][] = $model;
            }
        }
        if (!$values) {
            $this->_storage[$pivotClass] = [];
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
}