<?php
namespace carono\components\commands;

use carono\components\Currency;
use yii\console\Controller;

class CurrencyController extends Controller
{
	public function actionIndex()
	{
		foreach ($this->getData() as $currency) {
			if (!$model = Currency::find()->where(["code" => $currency["code"]])->one()) {
				$model = new Currency();
			}
			$model->setAttributes($currency);
			$model->save(false);
		}
	}

	public function getData()
	{
		return [
			[
				"name"          => "Russian Ruble",
				"code"          => "RUB",
				"number"        => '643',
				"fraction"      => 2,
				"fraction_name" => "Kopeck",
				"standard"      => 'ISO 4217',
				"unicode"       => 8381
			],
			[
				"name"          => "US Dollar",
				"code"          => "USD",
				"number"        => '840',
				"fraction"      => 2,
				"fraction_name" => "Cent",
				"standard"      => 'ISO 4217',
				"unicode"       => 36
			],
			[
				"name"          => "Euro",
				"code"          => "EUR",
				"number"        => '978',
				"fraction"      => 2,
				"fraction_name" => "Cent",
				"standard"      => 'ISO 4217',
				"unicode"       => 8364
			]
		];
	}
}
