<?php

namespace carono\components;

class FH
{
	public static function getAppDir()
	{
		$dir = dirname(__FILE__);
		while ($dir && (basename($dir) != 'protected') && !(file_exists($dir . DIRECTORY_SEPARATOR . 'yii'))) {
			$dir = dirname($dir);
		}
		return $dir;
	}

	public static function getConfigDir()
	{
		return self::getProtectedDir() . DIRECTORY_SEPARATOR . "config";
	}

	public static function getProtectedDir()
	{
		if (self::isYii2()) {
			return self::getAppDir();
		} else {
			return self::getAppDir() . DIRECTORY_SEPARATOR . "protected";
		}
	}

	public static function getRuntimeDir()
	{
		return self::getProtectedDir() . DIRECTORY_SEPARATOR . "runtime";
	}

	public static function getYii()
	{
		return self::getFile('yii.php');
	}

	public static function getYiic()
	{
		return self::getFile('yiic.php');
	}

	public static function getYiit()
	{
		return self::getFile('yiit.php');
	}

	public static function isYii2()
	{
		return file_exists(self::getAppDir() . DIRECTORY_SEPARATOR . 'yii');
	}

	/**
	 * @param string $config
	 * @param bool   $create
	 *
	 * @return string
	 * @throws \Exception
	 */
	public static function getConfigFile($config, $create = false)
	{
		return self::getFile(self::getConfigDir() . DIRECTORY_SEPARATOR . $config, true, $create);
	}

	public static function addExtension($path, $extension = 'php')
	{
		if (strpos($path, '.' . $extension) === false) {
			return $path . '.' . $extension;
		} else {
			return $path;
		}
	}

	public static function getFile($fileName, $fromApp = false, $create = false)
	{
		$fileName = self::addExtension(rtrim($fileName, '/\\'));
		if (!dirname($fileName)) {
			if ($fromApp) {
				$dir = self::getAppDir();
			} else {
				$dir = self::getDir();
			}
			$fileName = $dir . DIRECTORY_SEPARATOR . $fileName;
		}
		if (!file_exists($fileName)) {
			if ($create) {
				file_put_contents($fileName, '');
			} else {
				throw new \Exception("File '$fileName' not found");
			}
		}
		return $fileName;
	}

	public static function getDir()
	{
		$protected = self::getProtectedDir();
		$file = $protected . DIRECTORY_SEPARATOR . 'framework.php';
		if (file_exists($file)) {
			return rtrim(include $file, '/\\');
		} elseif (is_dir($dir = $protected . DIRECTORY_SEPARATOR . 'framework')) {
			return $dir;
		} elseif (is_dir($dir = self::getAppDir() . DIRECTORY_SEPARATOR . 'framework')) {
			return $dir;
		} else {
			throw new \Exception("Framework dir not found");
		}
	}

	public static function getConfig($fileName, $default = [])
	{
		$file = '';
		try {
			$file = self::getConfigFile($fileName);
		} catch (\Exception $e) {
		}
		if ($file) {
			if (!is_array($data = require $file)) {
				$data = [];
			}
			return $data;
		} else {
			if (is_array($default)) {
				return $default;
			} elseif (is_string($default)) {
				return self::getConfig($default, []);
			} else {
				return [];
			}
		}
	}

	/**
	 * @param string $config
	 * @param string $param
	 * @param mixed  $value
	 */
	public static function setParam($config, $param, $value)
	{
		$paths = self::_getArrayPath($param);
		self::getConfigFile($config, true);
		$data = self::getConfig($config);
		$val =& $data;
		$x =& $data;
		foreach ($paths as $path) {
			if (isset($val[$path])) {
				if (is_string($val)) {
					$val = array($val);
				}
				$x =& $val;
				$val =& $val[$path];
			} elseif (!is_null($value)) {
				if (!is_array($val)) {
					$val = [];
				}
				$val[$path] = '';
				$val =& $val[$path];
			}
		}
		if (is_null($value)) {
			unset($x[array_pop($paths)]);
		} else {
			$val = $value;
		}
		$file = self::getConfigFile($config, true);
		file_put_contents($file, self::_arrayToPhpSource($data));
	}

	/**
	 * @param string $config
	 * @param string $param
	 *
	 * @return array|null
	 */
	public static function getParam($config, $param)
	{
		$paths = self::_getArrayPath($param);
		$data = self::getConfig($config);
		foreach ($paths as $path) {
			if (!isset($data[$path])) {
				return null;
			} else {
				$data = $data[$path];
			}
		}
		return $data;
	}

	/**
	 * @param string $config
	 * @param string $param
	 */
	public static function deleteParam($config, $param)
	{
		self::setParam($config, $param, null);
	}

	/**
	 * @param array $array
	 * @param int   $deep
	 *
	 * @return string
	 */
	private static function _arrayToPhpSource($array, $deep = 1)
	{
		if ($deep == 1) {
			$result = "<?php\nreturn [\n";
		} else {
			$result = "";
		}
		foreach ($array as $key => $element) {
			$offset = str_repeat("\t", $deep);
			$result .= $offset;
			if (is_array($element)) {
				if (is_numeric($key)) {
					$result .= "[\n" . self::_arrayToPhpSource($element, $deep + 1) . "" . $offset . "],\n";
				} else {
					$result .= "'$key' => [\n" . self::_arrayToPhpSource($element, $deep + 1) . "" . $offset . "],\n";
				}
			} else {
				$element = self::varToPhp($element);
				if (is_numeric($key)) {
					$result .= "$element,\n";
				} else {
					$result .= "'$key' => $element,\n";
				}
			}
		}
		if ($deep == 1) {
			$result .= "];";
		}
		return $result;
	}

	/**
	 * @param mixed $element
	 *
	 * @return string
	 */
	public static function varToPhp($element)
	{
		$result = $element;
		if (is_string($element)) {
			$element = "'$element'";
			$result = str_replace("\\", "\\\\", $element);
		} elseif (is_bool($element)) {
			$result = $element ? 'true' : 'false';
		}
		return $result;
	}

	/**
	 * @param string $path
	 *
	 * @return array
	 */
	private static function _getArrayPath($path)
	{
		$path = str_replace('\.', '{REPLACE}', $path);
		$result = [];
		foreach (explode('.', $path) as $item) {
			$result[] = str_replace('{REPLACE}', '.', $item);
		}
		return $result;
	}

	/**
	 * @param string $config
	 * @param string $prefix
	 */
	public static function save($config, $prefix = '')
	{
		$name = md5($prefix . '_' . self::getConfigFile($config));
		$file = self::getConfigFile($config);
		$data = file_get_contents($file);
		file_put_contents(self::getRuntimeDir() . DIRECTORY_SEPARATOR . $name . '.php', $data);
	}

	/**
	 * @param string $config
	 * @param string $prefix
	 *
	 * @return bool
	 */
	public static function restore($config, $prefix = '')
	{
		$name = md5($prefix . '_' . self::getConfigFile($config));
		if (file_exists($stored = self::getRuntimeDir() . DIRECTORY_SEPARATOR . $name . '.php')) {
			$data = file_get_contents($stored);
			file_put_contents(self::getConfigFile($config), $data);
			@unlink($stored);
			return true;
		} else {
			return false;
		}
	}

	public static function clearConfig($config)
	{
		try {
			file_put_contents(self::getConfigFile($config), self::_arrayToPhpSource([]));
		} catch (\Exception $e) {
		}
	}
}