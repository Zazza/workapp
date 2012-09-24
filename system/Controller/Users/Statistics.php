<?php
class Controller_Users_Statistics extends Controller_Users {

	public function index() {			
		if ($this->registry["ui"]["admin"]) {

			$this->view->setTitle("Статистика");
			
			$this->view->setLeftContent($this->view->render("left_users", array()));

			$users = new Model_User();
				
			$data = $users->getTotal();
				
			if (($data["all"] / 1024 / 1024) > 1) {
				$data["all_val"] = round($data["all"] / 1024 / 1024, 2);
				$data["all_unit"] = "mb";
			};
			if (($data["all"] / 1024 / 1024 / 1024) > 1) {
				$data["all_val"] = round($data["all"] / 1024 / 1024 / 1024, 2);
				$data["all_unit"] = "gb";
			};
				
			foreach($data["users"] as $part) {
				if ($part["quota"] == 0) {
					$user[$part["login"]]["quota_val"] = "<span style='font-size: 18px; position: relative; top: 3px'>&infin;</span> (квота не задана)";
				}
				
				if (($part["quota"] / 1024 / 1024) > 1) {
					$user[$part["login"]]["quota_val"] = round($part["quota"] / 1024 / 1024, 2);
					$user[$part["login"]]["quota_unit"] = "mb";
				};
				if (($part["quota"] / 1024 / 1024 / 1024) > 1) {
					$user[$part["login"]]["quota_val"] = round($part["quota"] / 1024 / 1024 / 1024, 2);
					$user[$part["login"]]["quota_unit"] = "gb";
				};
					
				if (($part["sum"] / 1024 / 1024) > 1) {
					$user[$part["login"]]["val"] = round($part["sum"] / 1024 / 1024, 2);
					$user[$part["login"]]["unit"] = "mb";
				};
				if (($part["sum"] / 1024 / 1024 / 1024) > 1) {
					$user[$part["login"]]["val"] = round($part["sum"] / 1024 / 1024 / 1024, 2);
					$user[$part["login"]]["unit"] = "gb";
				};
					
				if ($part["quota"] != 0) {
					$user[$part["login"]]["percent"] = round($part["sum"] / $part["quota"] * 100, 0);
				} else {
					$user[$part["login"]]["percent"] = "0";
				}
			}
				
			$this->view->users_statistic(array("total" => $data, "users" => $user));
		}
	}
}
?>