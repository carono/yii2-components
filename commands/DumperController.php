<?php

namespace carono\components\commands;

use carono\components\dumper\BaseDumper;
use carono\components\dumper\PostgreSqlDumper;
use yii\console\Controller;
use yii\i18n\Formatter;
use Yii;

/**
 * Class DumperCommand
 *
 * @property BaseDumper $dumper
 */
class DumperController extends Controller
{
	public $backup = '@app/backups';
	public $db = 'db';
	public $user;
	public $password;
	protected $dumper;

	public function init()
	{
		switch ($driver = \Yii::$app->{$this->db}->getDriverName()) {
			case "pgsql":
				$this->dumper = new PostgreSqlDumper();
				break;
			default:
				echo "Implementation for $driver not found";
				exit;
		}
		$this->dumper->db = $this->db;

		if (!$this->backup) {
			if (isset(\Yii::$app->params["dumperBackup"])) {
				$this->backup = Yii::getAlias(\Yii::$app->params["dumperBackup"]);
			}
		}
		$this->dumper->backup = $this->backup;
		if (!is_dir($this->dumper->backup)) {
			if ($this->dumper->backup) {
				echo "Backup dir is not exist: " . $this->dumper->backup;
			} else {
				echo "Backup dir is not exist, set Yii::params[dumperBackup]";
			}
			exit;
		}
		$this->dumper->user = $this->user;
		$this->dumper->password = $this->password;
		$this->dumper->init();
	}

	public function actionDrop()
	{
		$base = $this->dumper->getBaseName();
		if ($this->confirm("Drop all tables in $base ?")) {
			$this->dumper->drop();
		}
	}

	public function actionImport($u = null, $p = null)
	{
		$this->actionList();
		$files = $this->getFiles();
		$index = $this->prompt('Select file index[1..' . count($files) . ']: ');
		if (!isset($files[$index - 1])) {
			echo "Out of range, try again";
			exit;
		}
		$this->dumper->import(current($files[$index - 1]));
	}

	public function actionExport($u = null, $p = null)
	{
		$file = $this->dumper->export();
		echo "Finish: " . $file . "\n";
	}

	public function getFiles()
	{
		$path = $this->dumper->backup;
		$result = [];
		$files1 = scandir($path);
		$x = 0;
		foreach ($files1 as $file) {
			if ($file != "." && $file != "..") {
				$result[$x++] = [$file => $path . DIRECTORY_SEPARATOR . $file];
			}
		}
		return $result;
	}

	public function actionList()
	{
		$files = $this->getFiles();
		$format = new Formatter();
		$x = 1;
		echo "Find " . count($files) . " backup files:\n\n";
		foreach ($files as $key => $value) {
			$file = key($value);
			$fullPath = current($value);
			$skip = str_repeat(" ", 37 - strlen($file));
			echo $x++ . ". " . $file . ' ' . $skip . ' ' . $format->asShortSize(filesize($fullPath)) . "\n\r";
		}
	}
}