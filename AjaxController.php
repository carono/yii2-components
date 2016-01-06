<?php

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

	public function behaviors()
	{
		return [
			[
				'class'   => 'yii\filters\ContentNegotiator',
				'formats' => [
					'application/json' => \yii\web\Response::FORMAT_JSON,
				],
			],
		];
	}

	protected function _out($result)
	{
		ob_clean();
		if (!is_string($result)) {
			$result = Json::encode($result);
		}
		echo $result;
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