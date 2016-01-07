<?php
namespace carono\components\behaviors;

use carono\components\CurrentUser;
use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;

class Author extends AttributeBehavior
{
	public $attributes;
	public $asRobot = true;
	public $robot = null;
	public $createdAtAttribute = 'creator_id';
	public $updatedAtAttribute = 'updater_id';

	public function init()
	{
		parent::init();

		if (empty($this->attributes)) {
			$this->attributes = [
				BaseActiveRecord::EVENT_BEFORE_INSERT => [$this->createdAtAttribute, $this->updatedAtAttribute],
				BaseActiveRecord::EVENT_BEFORE_UPDATE => $this->updatedAtAttribute,
			];
		}
	}

	/**
	 * @inheritdoc
	 */
	protected function getValue($event)
	{
		if ($event->name == BaseActiveRecord::EVENT_BEFORE_INSERT) {
			if ($value = $event->sender->{$this->createdAtAttribute}) {
				return $value;
			}
		}
		return CurrentUser::getId($this->asRobot, $this->robot);
	}
}