<?php

namespace carono\components\behaviors;


class TimestampBehavior extends \yii\behaviors\TimestampBehavior
{
	public $onChange;

	protected function getValue($event)
	{
		if ($this->onChange) {
			if ($this->owner->isAttributeChanged($this->onChange)) {
				return parent::getValue($event);
			} else {
				return $this->owner->{$this->attributes[$event->name][0]};
			}
		} else {
			return parent::getValue($event);
		}
	}
}