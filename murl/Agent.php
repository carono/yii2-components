<?php
namespace carono\components\murl;
class Agent
{
	public static function Opera($version = null)
	{
		return "Opera/9.99 (Windows NT 5.1; U; en-US) Presto/9.9.9";
	}

	public static function Safari($version = null)
	{
		return "Mozilla/5.0 (Windows NT 5.1; en-US) AppleWebKit/535.12 (KHTML, like Gecko) Version/5.0.1 Safari/535.12";
	}

	public static function Chrome($version = null)
	{
		return "Mozilla/5.0 (Windows NT 5.1; en-US) AppleWebKit/535.12 (KHTML, like Gecko) Chrome/22.0.1229.79 Safari/535.12";
	}

	public static function Firefox($version = null)
	{
		return "Mozilla/5.0 (Windows NT 5.1; en-US; rv:1.9.1.3) Gecko/20100101 Firefox/8.0";
	}

	public static function IE($version = null)
	{
		switch ($version) {
			case (9):
				return "Mozilla/4.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)";
			case (8):
				return "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)";
			case (7):
				return "Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 6.0)";
			case (6):
				return "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)";
			default;
				return self::IE(9);
		}
	}
} 