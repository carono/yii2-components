<?php
namespace carono\components;

use yii\base\Application;
use yii\base\BootstrapInterface;


/**
 * Class Bootstrap
 *
 * @package carono\components
 */
class Bootstrap implements BootstrapInterface
{

	/**
	 * Bootstrap method to be called during application bootstrap stage.
	 *
	 * @param Application $app the application currently running
	 */
	public function bootstrap($app)
	{
		if ($app instanceof \yii\console\Application) {
			$name = "dumper";
			if (isset($app->controllerMap[$name])) {
				$name = "dumper2";
			}
			$app->controllerMap[$name] = 'carono\commands\DumperController';
		}
	}
}