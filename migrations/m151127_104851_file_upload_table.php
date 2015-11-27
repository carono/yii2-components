<?php

class m151127_104851_file_upload_table extends \carono\components\Migration
{
	public function tables($array = [])
	{
		return [
			'file_upload' => [
				"id"         => self::primaryKey(),
				"user_id"    => [self::integer(), 'user', 'id'],
				"name"       => self::string(),
				"folder"     => self::string(),
				"extension"  => self::string(),
				"mime_type"  => self::string(),
				"path"       => self::string(),
				"slug"       => self::string(),
				"size"       => self::integer(),
				"created"    => self::dateTime(),
				"updated"    => self::dateTime(),
				"data"       => self::text(),
				"session"    => self::string(),
				"md5"        => self::string(),
				"active"     => self::boolean()->notNull()->defaultValue(true),
				"file_exist" => self::boolean()->notNull()->defaultValue(true),
				"binary"     => self::binary()
			]
		];
	}

	public function up()
	{
		$this->upTables();
	}

	public function down()
	{
		$this->downTables();
	}
}
