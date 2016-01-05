<?php
namespace carono\components;


use yii\helpers\ArrayHelper;
use yii\mail\BaseMailer;

class EmailFast
{
	public static function send($to, $subject, $template, $data = [], $files = [])
	{
		/**
		 * @var $m BaseMailer
		 */
		if (!isset(\Yii::$app->params["noReplyEmail"])) {
			$noReply = 'noreply@localhost';
		} else {
			$noReply = \Yii::$app->params["noReplyEmail"];
		}
//		if (!\Yii::$app->params["master"]) {
//			$subject .= " [" . join(', ', (array)$to) . "]";
//			$to = ArrayHelper::getValue(CurrentUser::get(), 'email');
//		}
		$m = \Yii::$app->mailer;
		if (!self::viewExist($template)) {
			$html = $m->getView()->render($m->htmlLayout, ['content' => $template], $m);
			$sender = \Yii::$app->mailer->compose()->setHtmlBody($html);
		} else {
			$sender = \Yii::$app->mailer->compose($template, $data);
		}
		if (!is_array($files)) {
			$files = [$files];
		}
		foreach ($files as $file) {
			if ($file instanceof FileUpload) {
				$options = ["fileName" => $file->fullname];
				$sender->attach($file->getFullPath(), $options);
			} else {
				$sender->attach($file);
			}
		}

		foreach ((array)$to as $email) {
			$sender->setTo($email)->setFrom($noReply)->setSubject($subject)->send();
		}
	}

	private static function viewExist($view)
	{
		if (strlen($view) <= 255) {
			if (file_exists(\Yii::getAlias("@app/mail") . '/' . $view . ".php")) {
				return true;
			}
		}
		return false;
	}
}