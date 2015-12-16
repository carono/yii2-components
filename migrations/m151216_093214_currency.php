<?php

use yii\db\Schema;
use carono\components\Migration;

class m151216_093214_currency extends Migration
{
	public function tables($array = [])
	{
		return [
			'currency' => [
				'id'            => self::primaryKey(),
				'name'          => self::string(),
				'code'          => self::string(),
				'number'        => self::string(),
				'fraction'      => self::integer(),
				'fraction_name' => self::string(),
				'standard'      => self::string(),
				'unicode'       => self::integer()
			],
		];
	}

	public function index($array = [])
	{
		return [
			["currency", ["code"], true]
		];
	}

	public function safeUp()
	{
		$this->upTables();
		$this->upIndex();
		(new \carono\components\commands\CurrencyController(null, null))->actionIndex();
	}

	public function safeDown()
	{
		$this->downTables();
	}
}
