<?php

namespace app\validators;

use carono\helpers\PhoneHelper;
use yii\validators\Validator;

class PhoneValidator extends Validator
{
	public function init()
	{
		parent::init();
		if ($this->message === null) {
			$this->message = \Yii::t('errors', 'Wrong phone format.');
		}
	}

	protected function validateValue($value)
	{
		if (!PhoneHelper::normalNumber($value)) {
			return [$this->message];
		}
	}
}