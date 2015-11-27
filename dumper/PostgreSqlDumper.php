<?php
namespace carono\components\dumper;

class PostgreSqlDumper extends BaseDumper
{
	public $port = 5432;

	public function isDevelopServer()
	{
		return true;
	}

	public function init()
	{
		putenv("PGPASSWORD=" . ($this->password ? $this->password : $this->getPassword()));
		putenv("PGUSER=" . ($this->user ? $this->user : $this->getUser()));
	}

	public function export()
	{
		$dir = $this->backup . DIRECTORY_SEPARATOR;
		$cmd = [
			"pg_dump",
			"-h",
			$this->getHost(),
			"-p",
			$this->getPort(),
			"-O",
			"-F" . ($this->compress ? "c" : "p"),
			"-b",
			"-v",
			"-f",
			$file = $dir . $this->formFileName(null, 'dump'),
			$this->getBaseName(),
		];
		$this->exec($cmd);
		return $file;
	}

	public function import($file)
	{
		if ($this->isArchive($file)) {
			$cmd = [
				'pg_restore',
				"-h",
				$this->getHost(),
				"-p",
				$this->getPort(),
				'--clean',
				'--format=c',
				'--no-owner',
				'--dbname=' . $this->getBaseName(),
				$file
			];
		} else {
			$cmd = [
				'psql',
				'--dbname=' . $this->getBaseName(),
				'-d ' . $this->getBaseName(),
				'-q',
				'-f',
				$file
			];
		}
		$this->exec($cmd);
	}

	public function drop()
	{
		foreach ($this->getDbConnection()->getSchema()->tableNames as $table) {
			$command = $this->getDbConnection()->createCommand("DROP TABLE IF EXISTS \"$table\" CASCADE ;");
			$command->execute();
		}
	}
}