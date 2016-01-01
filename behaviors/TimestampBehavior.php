<?php

namespace carono\components\behaviors;


use yii\db\ActiveRecord;

class TimestampBehavior extends \yii\behaviors\TimestampBehavior
{
	public $onChange;
	public $toTime;

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

	public function wasUpdated()
	{
		$attr = null;
		foreach ($this->attributes as $event => $attributes) {
			if ($event == ActiveRecord::EVENT_BEFORE_UPDATE) {
				$attr = is_array($attributes) ? $attributes[0] : $attributes;
			}
		}
		if ($attr) {
			$value = $this->owner->{$attr};
			$time = $this->toTime !== null ? call_user_func($this->toTime, $value) : strtotime($value);
			$dt = new \DateTime("now", new \DateTimeZone(\Yii::$app->timeZone));
			$dt->setTimestamp($time);
			$dt2 = new \DateTime("now", new \DateTimeZone(\Yii::$app->timeZone));
			return $dt2->getTimestamp() - $dt->getTimestamp();
		}
	}
}