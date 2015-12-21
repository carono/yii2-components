<?php

namespace carono\components;

use dosamigos\typeahead\Bloodhound;
use dosamigos\typeahead\TypeAhead;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use yii\base\Model;
use yii\bootstrap\ActiveField as BootstrapActiveField;
use yii\bootstrap\Html as BaseHtml;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class ActiveField extends BootstrapActiveField
{
	public function addClass($class)
	{
		BaseHtml::addCssClass($this->options, $class);
		return $this;
	}

	public function setClass($class)
	{
		$this->options["class"] = '';
		$this->addClass($class);
		return $this;
	}

	public function depDrop($url, $items = [], $depends = [], $options = [])
	{
		$items = self::modelsToOptions($items);
		$allowClear = ArrayHelper::getValue($options, 'allowClear', true);
		$placeholder = null;
		if (!is_null($prompt = ArrayHelper::getValue($options, 'prompt'))) {
			$placeholder = $prompt;
			$items = ArrayHelper::merge([null => ''], $items);
		}
		ArrayHelper::remove($options, 'placeholder');
		ArrayHelper::remove($options, 'allowClear');
		ArrayHelper::remove($options, 'prompt');
		return $this->widget(
			DepDrop::classname(), [
				'options'        => $options,
				'type'           => \kartik\depdrop\DepDrop::TYPE_SELECT2,
				'data'           => $items,
				'select2Options' => [
					'pluginOptions' => [
						'allowClear' => $allowClear,

					]
				],
				'pluginOptions'  => [
					'depends'     => $depends,
					'url'         => $url,
					'placeholder' => $placeholder
				]
			]
		);
	}

	public function dropDownList($items, $options = [], $field = 'name')
	{
		$models = self::modelsToOptions($items, $field);
		return parent::dropDownList($models, $options);
	}

	public function dropDownList2($items, $options = [], $field = 'name')
	{
		$models = self::modelsToOptions($items, $field);
		$settings = [
			'data'      => $models,
			"model"     => $this->model,
			"attribute" => $this->attribute,
			"theme"     => Select2::THEME_BOOTSTRAP,
			"options"   => $options
		];
		return $this->widget(Select2::className(), $settings);
	}

	public static function modelsToOptions($items, $field = 'name')
	{
		$models = $items;
		if (is_string($items)) {
			$items = $items::find()->all();
		}
		if ($items && (reset($items) && current($items) instanceof Model)) {
			$models = ArrayHelper::map($items, 'primaryKey', $field);
		}
		return $models;
	}

	public function typeahead($url, $clientOptions = [])
	{

		$engine = new Bloodhound(
			[
				'name'          => 'countriesEngine',
				'clientOptions' => [
					'datumTokenizer' => new \yii\web\JsExpression("Bloodhound.tokenizers.obj.whitespace('name')"),
					'queryTokenizer' => new \yii\web\JsExpression("Bloodhound.tokenizers.whitespace"),
					'remote'         => [
						'url'      => Url::to([$url, 'q' => 'QRY']),
						'wildcard' => 'QRY'
					]
				]
			]
		);

		return $this->widget(
			TypeAhead::className(), [
				'options'       => ['class' => 'form-control'],
				'engines'       => [$engine],
				'clientOptions' => [
					'highlight' => true,
					'minLength' => 2
				],
				'dataSets'      => [
					[
						'displayKey' => 'text',
						'source'     => $engine->getAdapterScript()
					]
				]
			]
		);
	}

	public function sex($options = [])
	{
		$items[0] = \Yii::t('yii', 'Female');
		$items[1] = \Yii::t('yii', 'Male');
		return parent::dropDownList($items, $options);
	}

	public function boolean($options = [])
	{
		$items = [\Yii::t('yii', 'No', [], 'ru'), \Yii::t('yii', 'Yes', [], 'ru')];
		return parent::dropDownList($items, $options);
	}
}