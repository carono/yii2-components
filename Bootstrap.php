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
			$commands = [
				'city'     => 'CityController',
				'currency' => 'CurrencyController',
				'dumper'   => 'DumperController'
			];
			foreach ($commands as $name => $command) {
				$name = file_exists(\Yii::getAlias("@app/commands/{$command}.php")) ? "carono" . ucfirst($name) : $name;
				$app->controllerMap[$name] = 'carono\components\commands\\' . $command;
			}
		}
	}
}