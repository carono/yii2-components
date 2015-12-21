<?php

namespace carono\components;


class TypeaheadController extends AutocompleteController
{
	public function out($code, $message = '', $result = [])
	{
		return $result;
	}

//	public function actionCity($q)
//	{
//		$cities = City::find()->filterWhere(['ilike', 'name', trim($q)])->limit(15)->orderBy('name')->all();
//		foreach (ArrayHelper::map($cities, 'id', 'name') as $id => $text) {
//			$this->result[] = ["id" => $id, "text" => $text];
//		}
//	}
}