<?php
/**
 * User: Карно
 * Date: 02.11.2015
 * Time: 13:54
 */

namespace carono\components;


use app\models\User;
use yii\helpers\ArrayHelper;

class CurrentUser
{

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