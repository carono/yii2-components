<?php
/**
 * User: Карно
 * Date: 16.10.2015
 * Time: 2:29
 */

namespace carono\components;


use yii\helpers\Json;

class AjaxController extends \yii\web\Controller
{
	const SUCCESS = 0;
	const ERROR = 1;
	public $enableCsrfValidation = false;
	public $code = 0;
	public $result = [];
	public $message = '';

	protected function _out($result)
	{
		ob_clean();
		if (!is_string($result)) {
			$result = Json::encode($result);
		}
		die($result);
	}

	public function runAction($route, $params = [])
	{
		try {
			parent::runAction($route, $params);
		} catch (\Exception $e) {
			self::_out($this->out($e->getCode(), $e->getMessage()));
		}
	}

	public function afterAction($action, $result)
	{
		self::_out($result ? $result : $this->out($this->code, $this->message, $this->result));
	}


	protected function out($code, $message = '', $result = [])
	{
		return Json::encode(["code" => $code, "message" => $message, "result" => (array)$result]);
	}
}