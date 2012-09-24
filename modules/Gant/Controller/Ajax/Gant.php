<?php
class Controller_Ajax_Gant extends Modules_Ajax {
	public function calLeft() {
		$sgant = & $_SESSION["gant"];
		$sgant["date"]++;
	}
	
	public function calRight() {
		$sgant = & $_SESSION["gant"];
		if ($sgant["date"] > 1) {
			$sgant["date"]--;
		}
	}
	
	public function calReset() {
		$sgant = & $_SESSION["gant"];
		$sgant["date"] = 1;
	}
	
	public function calLimit($params) {
		$limit = $params["limit"];
		
		$sgant = & $_SESSION["gant"];
		$sgant["limit"] = $limit;
	}
}
?>