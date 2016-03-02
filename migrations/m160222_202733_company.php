<?php

use yii\db\Schema;
use carono\components\Migration;

class m160222_202733_company extends Migration
{
    public function tables($array = [])
    {
        return [
            "company" => [
                "id"            => self::primaryKey(),
                "name"          => self::string(),
                "full_name"     => self::string(),
                "ogrn"          => self::string(),
                "inn"           => self::string(),
                "kpp"           => self::string(),
                "okpo"          => self::string(),
                "founding_date" => self::dateTime(),
                "created"       => self::dateTime(),
                "updated"       => self::dateTime(),
            ]
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
