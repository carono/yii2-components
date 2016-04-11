<?php
namespace carono\components\helpers;

class FioHelper
{

    public static function toArray($rawString)
    {
        while (strpos($rawString, "  ") !== false) {
            $rawString = str_replace("  ", " ", $rawString);
        }
        $arr = explode(' ', trim($rawString));
        $result["second_name"] = array_key_exists(0, $arr) ? trim(StringHelper::ucfirst($arr[0])) : null;
        $result["first_name"] = array_key_exists(1, $arr) ? trim(StringHelper::ucfirst($arr[1])) : null;
        $result["patronymic"] = array_key_exists(2, $arr) ? trim(StringHelper::ucfirst($arr[2])) : null;
        return $result;
    }
} 