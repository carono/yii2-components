<?php

namespace carono\components;

use yii\bootstrap\ActiveForm as BootstrapForm;


class ActiveForm extends BootstrapForm
{
	public $fieldClass = 'app\components\ActiveField';

	/**
	 * @param \yii\base\Model $model
	 * @param string          $attribute
	 * @param array           $options
	 *
	 * @return \carono\components\ActiveField
	 */
	public function field($model, $attribute, $options = [])
	{
		return parent::field($model, $attribute, $options);
	}
}