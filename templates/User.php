<?php

namespace app\models;

use app\models\base\User as BaseUser;
use carono\components\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * carono User template
 *
 * Class User
 *
 * @package app\models
 * @method integer wasUpdated() implemented in app\behaviors\TimestampBehavior
 */
class User extends BaseUser implements \yii\web\IdentityInterface
{
	public $username;
	public $password;
	public $authKey;
	const CODE_TIME_EXPIRED = 300;

	/**
	 * @inheritdoc
	 */
	public static function findIdentity($id)
	{
		return User::findOne($id);
	}

	public function getUrl($action = "view")
	{
		return null;
	}

	public function rules()
	{
		return array_merge(
			parent::rules(), [
				[['password'], 'safe'],
				[['login'], 'required'],
				[['login'], 'email'],
				[['password'], 'required', 'on' => 'add']
			]
		);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPersonal()
	{
		if (!$this->personal_id && !$this->isNewRecord) {
			$personal = new Personal();
			$personal->save();
			$this->updateAttributes(["personal_id" => $personal->id]);
		}
		return parent::getPersonal();
	}

	/**
	 * @inheritdoc
	 */
	public static function findIdentityByAccessToken($token, $type = null)
	{
		return static::findOne(['access_token' => $token]);
	}

	public function getEmail()
	{
		$email = ArrayHelper::getValue($this, 'personal.email');
		return $email ? $email : $this->login;
	}

	/**
	 * Finds user by username
	 *
	 * @param  string $username
	 *
	 * @return User
	 */
	public static function findByUsername($username)
	{
		return User::find()->where('LOWER("login")=:login', [":login" => strtolower($username)])->one();
	}

	/**
	 * @param $event
	 *
	 * @return bool
	 */
	public function afterLogin($event)
	{
		$this->updateAttributes(['last_logon' => new Expression('NOW()')]);
	}

	/**
	 * @inheritdoc
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @inheritdoc
	 */
	public function getAuthKey()
	{
		return $this->authKey;
	}

	/**
	 * @inheritdoc
	 */
	public function validateAuthKey($authKey)
	{
		return $this->authKey === $authKey;
	}

	/**
	 * Validates password
	 *
	 * @param  string $password password to validate
	 *
	 * @return boolean if password provided is valid for current user
	 */
	public function validatePassword($password)
	{
		return \Yii::$app->security->validatePassword($password, $this->hash);
	}

	public static function generateToken()
	{
		while (User::findOne(["access_token" => $token = md5(\Yii::$app->security->generateRandomString())])) {
		}
		return $token;
	}

	public function beforeSave($insert)
	{
		parent::beforeSave($insert);
		if ($this->password) {
			$this->hash = \Yii::$app->security->generatePasswordHash($this->password);
		}
		if ($this->isNewRecord && !$this->activation_code) {
			$this->activation_code = md5(\Yii::$app->security->generateRandomString());
		}
		if ($this->isNewRecord && !$this->access_token) {
			$this->access_token = self::generateToken();
		}
		return !$this->hasErrors();
	}

	public function behaviors()
	{
		return [
			'timestamp' => [
				'class'      => TimestampBehavior::className(),
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['created', 'updated'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['updated'],
				],
				'value'      => new Expression('NOW()'),
			],
		];
	}
}