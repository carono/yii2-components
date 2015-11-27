<?php
namespace carono\components\dumper;

use yii\db\Connection;
use yii\helpers\FileHelper;

abstract class BaseDumper
{
	public $backup = '';
	public $db = 'db';
	public $compress = true;
	public $user;
	public $password;
	public $host = '127.0.0.1';
	public $port;
	public $file;

	abstract function isDevelopServer();

	abstract function export();

	abstract function import($file);

	protected function write()
	{
		$args = func_get_args();
		foreach ($args as &$message) {
			if (strlen($message) == 1 && !is_numeric($message)) {
				$message = "\n" . str_repeat($message, 70) . "\n";
			}
		}
		echo join(" ", $args) . "\n";
	}

	public function init()
	{
		return true;
	}

	public function checkAccess()
	{
		return strpos(ini_get('disable_functions'), "exec") === false;
	}

	/**
	 * @return Connection
	 */
	public function getDbConnection()
	{
		return \Yii::$app->{$this->db};
	}

	public function getBaseName()
	{
		$arr = explode(":", $this->getDbConnection()->dsn);
		foreach (explode(";", $arr[1]) as $elem) {
			$param = explode('=', $elem);
			if ($param[0] == "dbname") {
				return $param[1];
			}
		}
		return null;
	}

	public function getPassword()
	{
		return $this->getDbConnection()->password;
	}

	public function getUser()
	{
		return $this->getDbConnection()->username;
	}

	public function getPort($default = null)
	{
		return $this->port ? $this->port : $default;
	}

	public function getHost()
	{
		return $this->host ? $this->host : "127.0.0.1";
	}

	/**
	 * @param array|string $command
	 *
	 * @return string
	 */
	protected function exec($command)
	{
		if (is_array($command)) {
			$command = join(' ', $command);
		}
		echo "\nEXEC: $command\n\n";
		return exec($command);
	}

	protected function isArchive($path)
	{
		return FileHelper::getMimeType($path) == "application/x-gzip";
	}

	/**
	 * @param null   $suffix
	 * @param string $extension
	 *
	 * @return string
	 */
	public function formFileName($suffix = null, $extension = 'sql')
	{
		if (!$this->file) {
			$prefix = date("Ymd");
			$base = $this->getBaseName();
			return $prefix . "_" . $base . ($suffix ? "_" . $suffix : "") . ($extension ? "." . $extension : "");
		} else {
			return $this->file;
		}
	}
}