<?php

namespace carono\components;

use Yii;

/**
 *
 * @property integer                        $id
 * @property string                         $code
 * @property integer                        $digital_code
 * @property string                         $name
 *
 */
class Currency extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'currency';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['digital_code'], 'integer'],
			[['code'], 'string', 'max' => 3],
			[['name'], 'string', 'max' => 50]
		];
	}
}
