<?php

namespace carono\components\calendar;


class ProductionCalendar extends ProductionCalendarBase
{
	public $format = 'Y-m-d';
	private static $_instance;
	/**
	 * @var \DateTime
	 */
	private static $date;

	public function __toString()
	{
		return $this->date()->format($this->format);
	}

	public function date()
	{
		return self::$date;
	}

	public function timestamp()
	{
		return self::date()->getTimestamp();
	}

	/**
	 * @param \DateTime|string $date
	 *
	 * @return bool
	 */
	public static function isWorking($date)
	{
		if (is_string($date)) {
			$date = new \DateTime($date);
		}
		if (isset(self::$holidays[$year = $date->format('Y')])) {
			return !in_array($date->format('Y-m-d'), self::$holidays[$year]);
		} else {
			return !in_array(date('w', $date->getTimestamp()), [6, 0]);
		}
	}

	public function day()
	{
		return $this;
	}

	public function working()
	{
		while (!self::isWorking(self::$date)) {
			$this->next();
		}
		return $this;
	}

	public function next()
	{
		self::$date->add(new \DateInterval('P1D'));
		return $this;
	}

	public static function find()
	{
		if (self::$_instance) {
			return self::$_instance;
		} else {
			self::$_instance = new self();
			self::$date = new \DateTime();
			return self::$_instance;
		}
	}
}