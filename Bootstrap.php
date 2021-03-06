<?php
namespace carono\components;

use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\gii\Module;


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
		/**
		 * @var Module $gii
		 */
		\Yii::setAlias('@carono', '@vendor/carono/yii2-components');
		if ($app instanceof \yii\console\Application) {
			$commands = [
				'city'     => 'CityController',
				'currency' => 'CurrencyController',
				'dumper'   => 'DumperController',
				'carono'   => 'CaronoController'
			];
			foreach ($commands as $name => $command) {
				$name = file_exists(\Yii::getAlias("@app/commands/{$command}.php")) ? "carono" . ucfirst($name) : $name;
				$app->controllerMap[$name] = 'carono\components\commands\\' . $command;
			}
		}
	}
}