<?php

namespace carono\components;

use yii\base\InlineAction;
use yii\console\Controller;

class UserCommand extends Controller
{
	public $userClass = 'app\models\User';

	public static function roles()
	{
		/*
		return [
			'guest'   => null,
			'user'    => null,
			'manager' => ['user']
		];
		*/
		return [];
	}

	public static function permission()
	{
		/*
		return [
			'*:*:*'            => ['root'],
			'Basic:Profile:*'  => ['user'],
			'Basic:Site:Login' => ['guest'],
			'Basic:Site:Index' => ['guest','user'],
			'Basic:Site:Error' => ['guest','user'],
		];
		*/
		return [];
	}

	public static function defaultUsers()
	{
		/*
		return [
			[
				"login"       => "noreply@example.ru",
				"password"    => "",
				"second_name" => "Robot",
				"first_name"  => "",
				"patronymic"  => "",
			],
			[
				"login"       => "root@example.ru",
				"password"    => "password",
				"second_name" => "",
				"first_name"  => "",
				"patronymic"  => "",
				"role"        => 'root'
			],
		];
		*/
		return [];
	}

	/**
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function findUser($data)
	{
		return call_user_func($this->userClass . "::findByUsername", $data["login"]);
	}

	public function actionIndex()
	{
		if ($this->confirm('Create roles and users?')) {
			$this->actionFillRoles();
			$this->actionDefault();
		} else {
			echo "Abort...\n";
		}
	}

	/**
	 * @param array $data
	 *
	 * @return boolean
	 */
	public function updateUser($data)
	{
		if (!$model = $this->findUser($data)) {
			$model = new $this->userClass;
		}
		$model->setAttributes($data);
		return $model->save();
	}


	public function actionDefault()
	{
		$users = self::defaultUsers();
		foreach ($users as $user) {
			$this->updateUser($user);
			RoleManager::revokeAll($user["login"]);
			if (isset($user["role"])) {
				RoleManager::assign($user["role"], $user["login"]);
			}
		}
	}

	public function actionFillRoles()
	{
		$roles = self::roles();
		$permissions = self::permission();
		foreach ($roles as $role => $parents) {
			RoleManager::createRole($role);
			RoleManager::removeChildren($role);
			if (is_array($parents)) {
				foreach ($parents as $parent) {
					if (RoleManager::getRole($parent)) {
						RoleManager::addParent($role, $parent);
					}
				}
			}
		}
		foreach ($permissions as $permission => $roles1) {
			foreach (self::normalizePermission($permission) as $name) {
				RoleManager::createPermission($name);
				foreach ($roles1 as $role) {
					RoleManager::addChild($role, $name);
					echo "Set $name for $role\n";
				}
			}
		}
		$diffRoles = array_diff(array_keys(RoleManager::auth()->getRoles()), array_keys($roles));
		foreach ($diffRoles as $role) {
			RoleManager::removeRole($role);
		}
	}

	public static function normalizePermission($expressionPermission)
	{
		if (strpos($expressionPermission, '*') !== false) {
			$arr = explode(':', $expressionPermission);
			$modules = self::collectModules($arr[0]);
			$controllers = self::collectControllers($modules, $arr[1]);
			$actions = self::collectActions($controllers, $arr[2]);
			$permissions = [];
			foreach ($actions as $action) {
				$permissions[] = RoleManager::formPermissionByAction($action);
			}
			return $permissions;
		} else {
			return [$expressionPermission];
		}
	}

	public static function collectActions($controllers, $id)
	{
		$actions = [];
		foreach ($controllers as $controller) {
			if ($id == "*") {
				foreach (get_class_methods($controller) as $method) {
					if (strpos($method, 'action') === 0 && $method != "actions") {
						$name = substr($method, 6);
						$actions[] = new InlineAction($name, $controller, $method);
					}
				}
			} elseif (method_exists($controller, $method = 'action' . $id)) {
				$actions[] = new InlineAction($id, $controller, $method);
			}
		}
		return $actions;
	}

	public static function collectControllers($modules, $id)
	{
		$controllers = [];
		foreach ($modules as $module) {
			if ($id == "*") {
				$f = function ($v) {
					return str_replace('Controller', '', basename($v, '.php'));
				};
				if ($module) {
					$names = array_map(
						$f, glob(\Yii::getAlias("@app/modules/{$module->id}/controllers/*Controller.php"))
					);
				} else {
					$names = array_map($f, glob(\Yii::getAlias('@app/controllers/*Controller.php')));
				}
			} else {
				$names = [$id];
			}
			foreach ($names as $name) {
				if ($module) {
					$className = join('\\', ['app', 'modules', $module->id, 'controllers', $name . "Controller"]);
				} else {
					$className = join('\\', ['app', 'controllers', $name . "Controller"]);
				}
				if (class_exists($className)) {
					$controllers[] = new $className($name, $module);
				}
			}
		}
		return $controllers;
	}

	public static function collectModules($id = '*')
	{
		$f = function ($v) {
			$name = basename($v);
			if (class_exists($className = join('\\', ['app', 'modules', $name, $name]))) {
				return new $className($name);
			} else {
				return null;
			}
		};
		$modules = array_filter(array_map($f, glob(\Yii::getAlias('@app/modules/*'))));
		if ($id == '*') {
			return array_merge($modules, [null]);
		} else {
			foreach ($modules as $module) {
				if ($module->id == strtolower($id)) {
					return [$module];
				}
			}
			return [null];
		}
	}
}
