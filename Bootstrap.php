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

			if (($gii = $app->getModule('gii')) && isset($gii->generators["giiant-model"])) {
				if (!isset($gii->generators["giiant-model"]["templates"])) {
					if (is_array($gii->generators["giiant-model"])) {
						$gii->generators["giiant-model"]["templates"] = [];
					} else {
						$gii->generators["giiant-model"] = [
							"class"     => 'schmunk42\giiant\generators\model\Generator',
							"templates" => []
						];
					}
				}
				$template = '@vendor/carono/yii2-components/templates/giiant-model';
				$gii->generators["giiant-model"]["templates"]["caronoModel"] = $template;
				$app->controllerMap['giix'] = 'carono\components\commands\GiixController';
			}
		}
	}
}