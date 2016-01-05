<?php

namespace carono\components;

use Yii;
use yii\base\Security;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * Class FileUpload
 *
 * @package carono\components
 * @property string $fullname
 */
class FileUpload extends \app\models\base\FileUpload
{
	const F_FILES = 'files';

	public function behaviors()
	{
		return [
			'timestamp' => [
				'class'      => 'yii\behaviors\TimestampBehavior',
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['created', 'updated'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['updated'],
				],
				'value'      => new Expression('NOW()'),
			],
			'author'    => [
				'class'      => 'carono\components\behaviors\Author',
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['user_id'],
				],
			],
		];
	}

	public static function upload($file, $name = null, $dir = self::F_FILES, $slug = null, $data = null, $delete = true)
	{
		if (is_null($dir)) {
			$dir = self::F_FILES;
		}
		$dir = trim($dir);
		$filePath = '';
		if (is_string($file)) {
			$filePath = Yii::getAlias($file);
		} elseif ($file instanceof UploadedFile) {
			$filePath = $file->tempName;
			$name = $name ? $name : $file->name;
		}
		$name = $name ? $name : basename($filePath);
		$sec = new Security();
		while (FileUpload::find()->where(["path" => $uniquePath = md5($sec->generateRandomString())])->one()) {
		}
		$dirSlug = $dir;
		if (!is_dir($dir) && (!$dir = self::getFolder($dirSlug))) {
			if (!$dir) {
				throw new \Exception("Folder for param '$dirSlug' is not set");
			} else {
				throw new \Exception("Folder '$dir' not found");
			}
		}
		$fullPath = self::formPath($uniquePath, $dir);
		if (!FileHelper::createDirectory(dirname($fullPath))) {
			throw new \Exception("Can't create folder '{" . dirname($fullPath) . "}'");
		}
		if (!file_exists($filePath)) {
			throw new \Exception('File not loaded or not exist');
		}
		if (is_uploaded_file($filePath)) {
			if (!move_uploaded_file($filePath, $fullPath)) {
				throw new \Exception('Unknown upload error');
			}
		} elseif ($delete ? !rename($filePath, $fullPath) : !copy($filePath, $fullPath)) {
			throw new \Exception('Failed to write file to disk');
		}
		$info = pathinfo($name);
		$fileUpload = new self();

		$fileUpload->session = isset(Yii::$app->session) ? Yii::$app->session->getId() : null;
		$fileUpload->user_id = CurrentUser::getId(1);
		$fileUpload->data = !is_null($data) ? json_encode($data) : null;
		$fileUpload->mime_type = FileHelper::getMimeType($fullPath);
		$fileUpload->md5 = md5_file($fullPath);
		$fileUpload->folder = $dirSlug;
		$fileUpload->path = $uniquePath;
		$fileUpload->slug = $slug;
		$fileUpload->size = filesize($fullPath);
		if (!$extension = strtolower(ArrayHelper::getValue($info, "extension"))) {
			$extension = ArrayHelper::getValue(FileHelper::getExtensionsByMimeType($fileUpload->mime_type), 0);
		}
		$fileUpload->name = basename($name, '.' . $extension);
		$fileUpload->extension = $extension;
		if ($fileUpload->save()) {
			return $fileUpload;
		} else {
			$fileUpload->deleteFile();
			return null;
		}
	}

	public function getFullPath()
	{
		return self::formPath($this->path, self::getFolder($this->folder));
	}

	public function fileExist()
	{
		return file_exists($this->getFullPath());
	}

	public function deleteFile()
	{
		if ($this->fileExist()) {
			@unlink($this->getFullPath());
			if ($f = !$this->fileExist()) {
				$this->updateAttributes(["file_exist" => false, "active" => false]);
			}
			return $f;
		} else {
			return true;
		}
	}

	public static function getFolder($param)
	{
		if (is_dir($param)) {
			return $param;
		} else {
			return Yii::getAlias(
				ArrayHelper::getValue(Yii::$app->params, $param, @Yii::$app->params["fileUploadFolder"])
			);
		}
	}

	public static function formPath($path, $folder = null)
	{
		$p = [$folder];
		for ($i = 0; $i < 3; $i++) {
			$p[] = $path[$i];
		}
		$p[] = $path;
		return join(DIRECTORY_SEPARATOR, $p);
	}

	public function getFullname()
	{
		return join('.', array_filter([$this->name, $this->extension]));
	}
}