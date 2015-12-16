<?php

use yii\db\Schema;
use carono\components\Migration;

class m151216_084006_cities extends Migration
{
	public function tables($array = [])
	{
		return [
			'city'             => [
				'id'        => self::primaryKey(),
				'name'      => self::string(),
				'region_id' => [self::integer(), 'region', 'id'],
				'active'    => self::boolean()->notNull()->defaultValue(true),
			],
			'region'           => [
				'id'                  => self::primaryKey(),
				'name'                => self::string(),
				"federal_district_id" => [self::integer(), 'federal_district', 'id'],
				'active'              => self::boolean()->notNull()->defaultValue(true),
			],
			'federal_district' => [
				"id"     => self::primaryKey(),
				"name"   => self::string(),
				'active' => self::boolean()->notNull()->defaultValue(true),
			],
		];
	}

	public function safeUp()
	{
		$this->upTables();
	}

	public function safeDown()
	{
		$this->downTables();
	}
}
