<?php

namespace carono\components;

use kartik\datetime\DateTimePicker;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use kartik\typeahead\Typeahead;
use yii\base\Model;
use yii\bootstrap\ActiveField as BootstrapActiveField;
use yii\bootstrap\Html as BaseHtml;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\DatePicker;
use yii\redactor\widgets\Redactor;

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
            DepDrop::className(), [
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

    public function dropDownList($items = null, $options = [], $field = 'name')
    {
        if (is_null($items) && method_exists($this->model, 'getRelationClass')) {
            $items = $this->model->getRelationClass($this->attribute);
        }
        $models = self::modelsToOptions($items, $field);
        return parent::dropDownList($models, $options);
    }

    public function dropDownList2($items = null, $options = [], $field = 'name')
    {
        $models = self::modelsToOptions(is_null($items) ? $this->model->className() : $items, $field);
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
        if (is_null($items)) {
            $items = [];
        }
        $models = $items;
        if (is_string($items) && class_exists($items)) {
            $items = $items::find()->all();
        }
        if ($items && (reset($items) && current($items) instanceof Model)) {
            $models = ArrayHelper::map($items, 'primaryKey', $field);
        }
        return $models;
    }

    public function typeahead($url, $options = [], $widgetOptions = [])
    {
        Html::addCssClass($options, 'form-control');
        $dataSet = ArrayHelper::remove($widgetOptions, 'dataset', []);
        $query = ArrayHelper::remove($widgetOptions, 'query', 'q');
        $options = ArrayHelper::merge(
            [
                'options' => $options,
                'dataset' => [
                    ArrayHelper::merge(
                        [
                            'remote'  => [
                                'url'      => Url::to([$url, $query => 'QRY']),
                                'wildcard' => 'QRY',
                            ],
                            'display' => 'text',
                        ], $dataSet
                    )
                ],
            ], $widgetOptions
        );
        return $this->widget(Typeahead::className(), $options);
    }

    public function date($options = [])
    {
        Html::addCssClass($options, 'form-control');
        return $this->widget(
            DatePicker::className(), [
                'language'   => 'ru',
                'dateFormat' => 'dd.MM.yyyy',
                'options'    => $options
            ]
        );
    }

    public function dateTime($options = [])
    {
        Html::addCssClass($options, 'form-control');
        return $this->widget(
            DateTimePicker::className(), [
                'options'       => ['placeholder' => 'Select operating time ...'],
                'convertFormat' => true,
                'pluginOptions' => ArrayHelper::merge(
                    [
                        'format'         => 'dd.MM.yyyy H:i',
                        'todayHighlight' => true
                    ], $options
                )
            ]
        );
    }

    public function redactor($options = [])
    {
        return $this->widget(
            Redactor::className(), [
                'clientOptions' => [
                    'imageManagerJson' => ['/redactor/upload/image-json'],
                    'imageUpload'      => ['/redactor/upload/image'],
                    'fileUpload'       => ['/redactor/upload/file'],
                    'lang'             => 'ru',
                    'plugins'          => ['clips', 'fontcolor', 'imagemanager']
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

    public function bitMask($items, $options = [])
    {
        $result = [];
        foreach ($items as $id => $name) {
            if ($this->model->{$this->attribute} & (1 << (int)$id - 1)) {
                $result[] = $id;
            }
        }
        $this->model->{$this->attribute} = $result;
        return parent::checkboxList($items, $options);
    }
}