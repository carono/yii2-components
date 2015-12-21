<?php

/**
 * Created by PhpStorm.
 * User: alex
 * Date: 13.12.15
 * Time: 18:54
 */
namespace carono\components\widgets\assets;

use yii\web\AssetBundle;


class ClockAssets extends AssetBundle
{

    public $sourcePath = '@carono/components/widgets/assets/clock';

    public $css = [
        'jClocksGMT.css',
    ];

    public $js = [
        'ClockGMT.js',
        'jquery.clock.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];


}