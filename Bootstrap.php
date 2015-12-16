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
			$name = file_exists(\Yii::getAlias('@app/commands/DumperController.php')) ? "caronoDumper" : "dumper";
			$app->controllerMap[$name] = 'carono\components\commands\DumperController';

			$name = file_exists(\Yii::getAlias('@app/commands/CityController.php')) ? "caronoCity" : "city";
			$app->controllerMap[$name] = 'carono\components\commands\CityController';
		}
	}
}