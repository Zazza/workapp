<?php

/**
 * This file is part of the Workapp project.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\System\Component;

use Engine\Model;

/**
 * Users class
 *
 * Данный класс обеспечивает работу с пользователями из любого модуля системы
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Users extends Model {
	/**
	 * Преобразует массив пользователей в HTML дерево
	 * 
	 * @param array $arr
	 * @param boolean $groups
	 */
	private function print_array($arr, $groups = true) {
		if (!is_array($arr)) {
			return;
		}
	
		while(list($key, $val) = each($arr)) {
			if (!is_array($val)) {
				if ($val == null) {
					$val = "пусто";
				}
	
				$this->tree .= "<ul><li><div style='margin: 0 0 0 10px'>" . $val . "</div></li></ul>";
			}
			if (is_array($val)) {
				if ($key != "0") {
					if(is_numeric($key)) {
						$gid = $this->registry["user"]->getSubgroupName($key);
						if ($groups) {
							$this->tree .= "<ul><li><span class='folder'><label class='checkbox'><input type='checkbox' name='gruser[]' id='hg" . $key . "' class='gruser' title='" . $gid . "' value='" . $key . "' />&nbsp;" . $gid . "</label></span>";
						} else {
							$this->tree .= "<ul><li><span class='folder'><label class='checkbox'>" . $gid . "</label></span>";
						}
					} else {
						$this->tree .= "<ul><li><span class='folder'>&nbsp;" . $key . "</span>";
					}
				}
	
				$this->print_array($val, $groups);
	
				if ($key != "0") {
					$this->tree .= "</li></ul>";
				}
			}
		}
	}
	
	/**
	 * Формирует HTML дерево для выбора ОДНОГО пользователя.
	 * Например: делегировать задачу
	 * Шаблон: .../users/lightustr.tpl
	 * 
	 * @return string
	 */
	function users_tree() {
		$groups = $this->registry["user"]->getGroups();
		$uniq_groups = $this->registry["user"]->getUniqGroups();
		$users = $this->registry["user"]->getUsersGroups();
	
		$sortlist = array();
		foreach($groups as $group) {
			foreach($users as $user) {
				if ( ($user["gname"] == $group["sname"]) and ($group["sname"] != null) ) {
					$udata = $this->render("users_lightdata", array("data" => $this->registry["user"]->getUserInfo($user["id"])));
					$sortlist[$group["pname"]][$group["sid"]][] = $udata;
				}
			}
		}
	
		$this->print_array($sortlist);
	
		return $this->render("users_lightustr", (array("group" => $uniq_groups, "list" => $this->tree)));
	}
	
	/**
	 * Формирует HTML дерево для выбора пользователей.
	 * Например: ответственные в задаче
	 * Шаблон: .../users/lightustr.tpl
	 * 
	 * @return string
	 */
	function onlyUsers_tree() {
		$groups = $this->registry["user"]->getGroups();
		$uniq_groups = $this->registry["user"]->getUniqGroups();
		$users = $this->registry["user"]->getUsersGroups();
	
		$sortlist = array();
		foreach($groups as $group) {
			foreach($users as $user) {
				if ( ($user["gname"] == $group["sname"]) and ($group["sname"] != null) ) {
					$udata = $this->render("users_lightdata", array("data" => $this->registry["user"]->getUserInfo($user["id"]), "radio" => true));
					$sortlist[$group["pname"]][$group["sid"]][] = $udata;
				}
			}
		}
	
		$this->print_array($sortlist, false);
	
		return $this->render("users_lightustr", (array("group" => $uniq_groups, "list" => $this->tree, "notcheckall" => true)));
	}
}