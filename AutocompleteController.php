<?php
namespace carono\components;


class AutocompleteController extends AjaxController
{
	public function out($code, $message = '', $result = [])
	{
		return ["results" => $result];
	}

//	public function actionCity($q)
//	{
//		$cities = City::find()->filterWhere(['ilike', 'name', trim($q)])->limit(15)->orderBy('name')->all();
//		foreach (ArrayHelper::map($cities, 'id', 'name') as $id => $text) {
//			$this->result[] = ["id" => $id, "text" => $text];
//		}
//	}
}