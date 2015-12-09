<?php

namespace carono\helpers;

class PhoneHelper
{
	/**
	 * @param $number
	 *
	 * @return mixed|null|string
	 */
	public static function normalNumber($number)
	{
		$number = preg_replace('/[^0-9]/x', '', trim($number));

		if (strlen($number) == 10) {
			$number = '7' . $number;
		}

		if (strpos($number, '7') !== 0) {
			$number = '7' . substr($number, 1);
		}

		if (preg_match('/^[0-9]{11}$/', $number)) {
			return $number;
		}

		return null;
	}

	/**
	 * @param string $number
	 *
	 * @return string
	 */
	public static function asString($number)
	{
		if ($string = self::normalNumber($number)) {
			return "+" . substr($string, 0, 1) . " (" . substr($string, 1, 3) . ") " . substr($string, 4, 3) . "-"
			. substr($string, 7);
		} else {
			return $number;
		}
	}
}