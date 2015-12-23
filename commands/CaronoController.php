<?php

namespace carono\components\commands;


use yii\console\Controller;
use yii\helpers\Console;

class CaronoController extends Controller
{
	public function actionInstall()
	{
		$this->clearOut();
	}

	protected function clearOut()
	{
		Console::clearScreen();
	}
}