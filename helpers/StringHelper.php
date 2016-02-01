<?php
namespace carono\components\helpers;

class StringHelper extends \yii\helpers\BaseStringHelper
{
	/**
	 * @param      $str
	 * @param bool $lower
	 *
	 * @return string
	 */
	public static function ucfirst($str, $lower = true)
	{
		$enc = 'utf-8';
		$str = $lower ? self::lower($str) : $str;
		return mb_strtoupper(mb_substr($str, 0, 1, $enc), $enc) . mb_substr($str, 1, mb_strlen($str, $enc), $enc);
	}

	/**
	 * @param $str
	 *
	 * @return string
	 */
	public static function lower($str)
	{
		return mb_strtolower($str, 'utf-8');
	}

	/**
	 * @param $str
	 *
	 * @return string
	 */
	public static function upper($str)
	{
		return mb_strtoupper($str, 'utf-8');
	}

	/**
	 * @param      $str
	 * @param null $lower
	 *
	 * @return string
	 */
	public static function first($str, $lower = null)
	{
		$enc = 'utf-8';
		$first = mb_substr($str, 0, 1, $enc);
		if ($lower === false) {
			return self::upper($first);
		} elseif ($lower === true) {
			return self::lower($first);
		} else {
			return $first;
		}
	}
}