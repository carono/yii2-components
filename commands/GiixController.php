<?php

namespace carono\components\commands;


use schmunk42\giiant\commands\BatchController;

class GiixController extends BatchController
{
	public $modelNamespace = 'app\models';
	public $overwrite = true;
	public $defaultAction = 'models';
	public $interactive = false;
	public $template = 'caronoModel';
}