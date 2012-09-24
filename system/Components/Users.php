<?php
class Components_Users extends Engine_Model {
	private $online_time = 300;
	
	public function issetLogin($login) {
		$login = $login[0];
		return $this->registry["user"]->issetLogin($login);
	}
	
	public function getGidFromUid($uid) {
		$uid = $uid[0];
		return $this->registry["user"]->getGidFromUid($uid);
	}
	
	public function setOnline() {
		$this->registry["user"]->setOnline();
	}
	
	public function setOffline() {
		$this->registry["user"]->setOnline();
	}
	
	public function getStatus($uid) {
		$uid = $uid[0];
		$this->registry["user"]->getStatus($uid);
	}
	
	public function getUsersList() {
		return $this->registry["user"]->getUsersList();
	}
	
	function users_date() {
		return $this->render("users_date", (array("date" => $groupsSess)));
	}
	
	public function getUserInfoFromGroup($params) {
		return $this->registry["user"]->getUserInfoFromGroup($params[0]);
	}
	
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
	
	function getGroupName($param) {
		return $this->registry["user"]->getGroupName($param[0]);
	}
	
	function getUserInfo($param) {
		return $this->registry["user"]->getUserInfo($param[0]);
	}
	
	function getUserId($param) {
		return $this->registry["user"]->getUserId($param[0]);
	}
}
?>