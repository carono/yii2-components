<?php

/**
 * Created by PhpStorm.
 * User: alex
 * Date: 13.12.15
 * Time: 18:46
 */

namespace carono\components\widgets;

use Yii;
use yii\base\Widget;
use yii\bootstrap\ActiveField;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use carono\components\widgets\assets\ClockAssets;

class Clock extends Widget
{


    public $options = [];

    public $tag = 'div';

    public $htmlClass = "time";

    public $visible = true;

    public $timeZones = [
            -12=>"Eniwetok",
            -11=>"Samoa",
            -10=>"Hawaii",
            -9=>"Alaska",
            -8=>"PST, Pacific US",
            -7=>"MST, Mountain US",
            -6=>"CST, Central US",
            -5=>"EST, Eastern US",
            -4=>"Atlantic, Canada",
            -3=>"Brazilia, Buenos Aries",
            -2=>"Mid-Atlantic",
            -1=>"Cape Verdes",
            0=>"Greenwich Mean Time, Dublin",
            +1=>"Berlin, Rome",
            +2=>"Israel, Cairo, Калининград",
            +3=>"Moscow, Kuwait",
            +4=>"Abu Dhabi, Muscat, Самара",
            +5=>"Islamabad, Karachi",
            +6=>"Almaty, Dhaka, Новосибирск",
            +7=>"Bangkok, Jakarta",
            +8=>"Hong Kong, Beijing, Иркутск",
            +9=>"Tokyo, Osaka, Якутск",
            +10=>"Sydney, Melbourne, Guam, Магадан",
            +11=>"Magadan, Soloman Is.",
            +12=>"Fiji, Wellington, Auckland, Камчатский край",
    ];

    public function init()
    {
        parent::init();
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        if (!isset($this->options['timezone_select'])) {
            $this->options['timezone_select'] = 'timezone';
        }
        if (!isset($this->options['default_timezone'])) {
            $this->options['default_timezone'] = "+3";
        }
        if (isset($this->options['visible'])) {
            $this->visible = $this->options['visible'];
        }
    }

    public function run()
    {
        if(!$this->visible)
            return;
        $this->registerClientScript();
        ob_start();
        $this->renderHead();
        $this->renderBody();
        $this->renderFooter();
        $html = ob_get_contents();
        ob_end_clean();
        echo $html;
    }

    public function renderHead()
    {
        echo Html::beginTag('div', [
            "id"=>$this->id . "_wrap",
            "class"=>"clock_wrapper",
        ]);
        echo Html::beginTag('ul', [
            'class'=>"nav navbar-nav"
        ]);
        echo Html::beginTag('li', [
            'class'=>"dropdown"
        ]);
        echo Html::beginTag('a', [
            'class'=>"dropdown-toggle",
            "href" => '#',
            "data-toggle"=>"dropdown",
        ]);
        echo Html::tag("b","", ["class"=>"caret"]);
        echo Html::beginTag('div', [
            "id"=>$this->id,
            "class"=>"clock_container",
        ]);
        echo Html::beginTag('div', [
            "class"=>"clockHolder"
        ]);
    }

    public function renderBody()
    {
        echo Html::endTag("div");
        echo Html::beginTag('div', [
            "class"=>"digital"
        ]);
        echo Html::tag("div", date("d-m-Y"), [
            "style" => "display:none",
            'id' => "date"
        ]);
        echo Html::beginTag("div",["id"=>"clock"]);
        echo Html::endTag("div");
        echo Html::endTag("div");
        echo Html::endTag("div");
    }

    public function renderFooter()
    {

        echo Html::beginTag('ul', [
            'class'=>"dropdown-menu"
        ]);
        foreach($this->timeZones as $key=>$val) {
        echo Html::beginTag('li', [
        ]);
        echo Html::beginTag('a', [
            "href" => '#',
            "data-offset"=>$key,
        ]);
            echo $val . " (GMT" . (($key>0) ? "+" . $key : $key) . ")";
        echo Html::endTag('a');
        echo Html::endTag('li');
        }
        echo Html::endTag('a');
        echo Html::endTag('ul');
        echo Html::endTag('a');
        echo Html::endTag('li');
        echo Html::endTag('ul');
        echo Html::endTag("div");
    }

    /**
     * Registers the needed JavaScript.
     */
    public function registerClientScript()
    {
        $id = $this->options['id'];

        $options = Json::htmlEncode($this->options);
        $js = '';

        $view = $this->getView();
        ClockAssets::register($view);
        $js .= "jQuery('#$id').yiiClock($options);";
        if ($js !== '') {
            $view->registerJs($js);
        }
    }


}