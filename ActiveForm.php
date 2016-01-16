<?php

namespace carono\components;

use yii\bootstrap\ActiveForm as BootstrapForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;


class ActiveForm extends BootstrapForm
{
	public $fieldClass = 'carono\components\ActiveField';

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

	public static function begin2($config = [])
	{
		$error = Html::tag('div', '{error}', ["class" => "col-lg-8"]);
		$input = Html::tag('div', '{input}', ["class" => "col-lg-4"]);
		$template = "{label}\n" . $input . $error;
		$config = ArrayHelper::merge(
			[
				'options'     => ['class' => 'form-horizontal'],
				'fieldConfig' => [
					'template'     => $template,
					'labelOptions' => [
						'class' => 'col-lg-3 control-label'
					],
				],
			], $config
		);
		return self::begin($config);
	}
}