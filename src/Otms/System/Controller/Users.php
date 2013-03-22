<?php

/**
 * This file is part of the Workapp project.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\System\Controller;

use Engine\Controller;

class Users extends Controller {
	protected $tree;

	protected function print_array($arr) {
		if (!is_array($arr)) {
			return;
		}
	
		while(list($key, $val) = each($arr)) {
			if (!is_array($val)) {
				if ($val == null) {
					$val = "empty";
				}
	
				$this->tree .= "<ul><li><div style='margin: 0 0 0 10px'>" . $val . "</div></li></ul>";
			}
			if (is_array($val)) {
				if ($key != "0") {
					if(is_numeric($key)) {
						$gid = $this->registry["user"]->getSubgroupName($key);
						$this->tree .= "<ul><li><span class='folder'><label><input type='checkbox' name='gruser[]' class='Pgruser' value='" . $key . "' />&nbsp;" . $gid . "</label></span>";
					} else {
						$this->tree .= "<ul><li><span class='folder'>&nbsp;" . $key . "</span>";
					}
				}
	
				$this->print_array($val);
	
				if ($key != "0") {
					$this->tree .= "</li></ul>";
				}
			}
		}
	}
	
	public function index() {
        if ($this->registry["ui"]["admin"]) {

			$this->view->setTitle("Management of users");
			
			$this->view->setLeftContent($this->view->render("left_users", array()));

			$uniq_groups = $this->registry["user"]->getUniqGroups();
			
			$this->view->users_admin(array("group" => $uniq_groups));
		}
    }
}