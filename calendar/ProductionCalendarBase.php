<?php

namespace carono\components\calendar;

class ProductionCalendarBase
{
	public static $holidays
		= [
			2015 => [
				'2015-01-01',
				'2015-01-02',
				'2015-01-03',
				'2015-01-04',
				'2015-01-05',
				'2015-01-06',
				'2015-01-07',
				'2015-01-08',
				'2015-01-09',
				'2015-01-10',
				'2015-01-11',
				'2015-01-17',
				'2015-01-18',
				'2015-01-24',
				'2015-01-25',
				'2015-01-31',
				'2015-02-01',
				'2015-02-07',
				'2015-02-08',
				'2015-02-14',
				'2015-02-15',
				'2015-02-21',
				'2015-02-22',
				'2015-02-23',
				'2015-02-28',
				'2015-03-01',
				'2015-03-07',
				'2015-03-08',
				'2015-03-09',
				'2015-03-14',
				'2015-03-15',
				'2015-03-21',
				'2015-03-22',
				'2015-03-28',
				'2015-03-29',
				'2015-04-04',
				'2015-04-05',
				'2015-04-11',
				'2015-04-12',
				'2015-04-18',
				'2015-04-19',
				'2015-04-25',
				'2015-04-26',
				'2015-05-01',
				'2015-05-02',
				'2015-05-03',
				'2015-05-04',
				'2015-05-09',
				'2015-05-10',
				'2015-05-11',
				'2015-05-16',
				'2015-05-17',
				'2015-05-23',
				'2015-05-24',
				'2015-05-30',
				'2015-05-31',
				'2015-06-06',
				'2015-06-07',
				'2015-06-12',
				'2015-06-13',
				'2015-06-14',
				'2015-06-20',
				'2015-06-21',
				'2015-06-27',
				'2015-06-28',
				'2015-07-04',
				'2015-07-05',
				'2015-07-11',
				'2015-07-12',
				'2015-07-18',
				'2015-07-19',
				'2015-07-25',
				'2015-07-26',
				'2015-08-01',
				'2015-08-02',
				'2015-08-08',
				'2015-08-09',
				'2015-08-15',
				'2015-08-16',
				'2015-08-22',
				'2015-08-23',
				'2015-08-29',
				'2015-08-30',
				'2015-09-05',
				'2015-09-06',
				'2015-09-12',
				'2015-09-13',
				'2015-09-19',
				'2015-09-20',
				'2015-09-26',
				'2015-09-27',
				'2015-10-03',
				'2015-10-04',
				'2015-10-10',
				'2015-10-11',
				'2015-10-17',
				'2015-10-18',
				'2015-10-24',
				'2015-10-25',
				'2015-10-31',
				'2015-11-01',
				'2015-11-04',
				'2015-11-07',
				'2015-11-08',
				'2015-11-14',
				'2015-11-15',
				'2015-11-21',
				'2015-11-22',
				'2015-11-28',
				'2015-11-29',
				'2015-12-05',
				'2015-12-06',
				'2015-12-12',
				'2015-12-13',
				'2015-12-19',
				'2015-12-20',
				'2015-12-26',
				'2015-12-27',
			],
			2016 => [
				'2016-01-01',
				'2016-01-02',
				'2016-01-03',
				'2016-01-04',
				'2016-01-05',
				'2016-01-06',
				'2016-01-07',
				'2016-01-08',
				'2016-01-09',
				'2016-01-10',
				'2016-01-16',
				'2016-01-17',
				'2016-01-23',
				'2016-01-24',
				'2016-01-30',
				'2016-01-31',
				'2016-02-06',
				'2016-02-07',
				'2016-02-13',
				'2016-02-14',
				'2016-02-21',
				'2016-02-22',
				'2016-02-23',
				'2016-02-27',
				'2016-02-28',
				'2016-03-05',
				'2016-03-06',
				'2016-03-07',
				'2016-03-08',
				'2016-03-12',
				'2016-03-13',
				'2016-03-19',
				'2016-03-20',
				'2016-03-26',
				'2016-03-27',
				'2016-04-02',
				'2016-04-03',
				'2016-04-09',
				'2016-04-10',
				'2016-04-16',
				'2016-04-17',
				'2016-04-23',
				'2016-04-24',
				'2016-04-30',
				'2016-05-01',
				'2016-05-02',
				'2016-05-03',
				'2016-05-07',
				'2016-05-08',
				'2016-05-09',
				'2016-05-14',
				'2016-05-15',
				'2016-05-21',
				'2016-05-22',
				'2016-05-28',
				'2016-05-29',
				'2016-06-04',
				'2016-06-05',
				'2016-06-11',
				'2016-06-12',
				'2016-06-13',
				'2016-06-18',
				'2016-06-19',
				'2016-06-25',
				'2016-06-26',
				'2016-07-02',
				'2016-07-03',
				'2016-07-09',
				'2016-07-10',
				'2016-07-16',
				'2016-07-17',
				'2016-07-23',
				'2016-07-24',
				'2016-07-30',
				'2016-07-31',
				'2016-08-06',
				'2016-08-07',
				'2016-08-13',
				'2016-08-14',
				'2016-08-20',
				'2016-08-21',
				'2016-08-27',
				'2016-08-28',
				'2016-09-03',
				'2016-09-04',
				'2016-09-10',
				'2016-09-11',
				'2016-09-17',
				'2016-09-18',
				'2016-09-24',
				'2016-09-25',
				'2016-10-01',
				'2016-10-02',
				'2016-10-08',
				'2016-10-09',
				'2016-10-15',
				'2016-10-16',
				'2016-10-22',
				'2016-10-23',
				'2016-10-29',
				'2016-10-30',
				'2016-11-04',
				'2016-11-05',
				'2016-11-06',
				'2016-11-12',
				'2016-11-13',
				'2016-11-19',
				'2016-11-20',
				'2016-11-26',
				'2016-11-27',
				'2016-12-03',
				'2016-12-04',
				'2016-12-10',
				'2016-12-11',
				'2016-12-17',
				'2016-12-18',
				'2016-12-24',
				'2016-12-25',
				'2016-12-31'
			]
		];
}