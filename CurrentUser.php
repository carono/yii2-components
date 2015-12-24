<?php

namespace carono\components;


use app\models\User;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class CurrentUser
{
	public static $className = 'app\models\User';

	/**
	 * @param Model|string $message
	 */
	public static function setFlashError($message)
	{
		if ($message instanceof Model) {
			$message = Html::errorSummary($message);
		}
		self::setFlash('error', $message);
	}

	/**
	 * @param $message
	 */
	public static function setFlashSuccess($message)
	{
		self::setFlash('success', $message);
	}

	/**
	 * @param $message
	 */
	public static function setFlashWarning($message)
	{
		self::setFlash('warning', $message);
	}

	/**
	 * @param $message
	 */
	public static function setFlashInfo($message)
	{
		self::setFlash('info', $message);
	}

	/**
	 * @param null $key
	 *
	 * @return string
	 */
	public static function showFlash($key = null)
	{
		$session = \Yii::$app->getSession();
		if (!$key) {
			$out = '';
			foreach ($session->getAllFlashes(false) as $key => $value) {
				$out .= self::showFlash($key);
			}
			return $out;
		} else {
			switch ($key) {
				case "success":
					$htmlOptions = ["class" => "alert alert-success"];
					break;
				case "error":
					$htmlOptions = ["class" => "alert alert-danger"];
					break;
				case "info":
					$htmlOptions = ["class" => "alert alert-info"];
					break;
				case "warning":
					$htmlOptions = ["class" => "alert alert-warning"];
					break;
				default:
					$htmlOptions = ["class" => "alert alert-info"];
			}
			if ($session->hasFlash($key)) {
				return Html::tag('div', $session->getFlash($key), $htmlOptions);
			}
		};
	}

	/**
	 * @param $name
	 * @param $message
	 */
	public static function setFlash($name, $message)
	{
		if (\Yii::$app->getSession()) {
			\Yii::$app->getSession()->setFlash($name, $message);
		}
	}

	/**
	 * @param null $user
	 *
	 * @return bool
	 */
	public static function isMe($user = null)
	{
		return self::user($user)->id == self::getId(true);
	}

	/**
	 * @param      $user
	 * @param bool $asRobot
	 *
	 * @return User
	 */
	public static function user($user, $asRobot = true)
	{
		if ($model = self::findUser($user)) {
			return $model;
		} else {
			return self::get($asRobot);
		}
	}

	public static function findUser($user)
	{
		$model = null;
		if (is_numeric($user)) {
			$model = User::findOne($user);
		} elseif (is_string($user)) {
			$model = User::findByUsername($user);
		} elseif ($user instanceof User) {
			$model = $user;
		}
		return $model;
	}

	public static function getRobot($login = null)
	{
		$user = null;
		$login = $login ? $login : (isset(\Yii::$app->params["robot"]) ? \Yii::$app->params["robot"] : null);
		$user = self::findUser($login);
		return $user;
	}

	/**
	 * @return \yii\web\User
	 */
	public static function webUser()
	{
		return \Yii::$app->user;
	}

	public static function isGuest()
	{
		return \Yii::$app->user->isGuest;
	}

	/**
	 * @param bool $asRobot
	 * @param null $robot
	 *
	 * @return User|null
	 */
	public static function get($asRobot = false, $robot = null)
	{
		$user = null;
		if (isset(\Yii::$app->components["user"]) && !\Yii::$app->user->isGuest) {
			$user = User::findOne(\Yii::$app->user->identity->getId());
		}
		if ($asRobot && !$user) {
			$user = self::getRobot($robot);
		}
		return $user;
	}

	public static function getId($asRobot = false, $robot = null)
	{
		return ArrayHelper::getValue(self::get($asRobot, $robot), 'id');
	}
}