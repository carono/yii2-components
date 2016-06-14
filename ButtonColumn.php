<?php

namespace carono\components;

use yii\helpers\Html;

class ButtonColumn
{
    public $icon;
    public $title;
    public $url;
    public $options = [];

    public function asLink()
    {
        if ($this->icon) {
            $span = Html::tag('span', '', ["class" => $this->icon]);
        } else {
            $span = '';
        }
        $this->options["title"] = $this->title;
        return Html::a($span, $this->url, $this->options);
    }
}