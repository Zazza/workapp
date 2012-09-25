<?php
class Preload extends Engine_Bootstrap {
    function start() {
        $view = new View_Index();
        $this->registry->set('view', $view);
        
		$view->setDescription($this->registry["keywords"]);
		$view->setKeywords($this->registry["description"]);
		
		$ui = new Model_Ui();

		if (isset($_POST[session_name()])) {
			session_id($_POST[session_name()]);
		}
		
		session_start();

		$loginSession = & $_SESSION["login"];
		if (isset($loginSession["id"])) {
			$ui->getInfo($loginSession);
			
			$this->registry["logs"] = new Model_Logs();
			$this->registry["user"] = new Model_User();
			
			$this->registry["user"]->setOnline();
			
			$ui = new Model_Ui();
			$this->registry->set("users_sets", $ui->getSet("bu"));
			
			$this->registry->set("ajax_notice_sets", $ui->getSet("ajax_notice"));
			
		} else if (mb_substr($this->registry["url"], 1, 3) == "api") {
			$api = new Model_Api();
			if (!$api->login()) {
				return false;
			}
		} else {
			$login = new Controller_Login();
			$login->index();
			 
			return false;
		}
		
		$modules = new Modules_Modules();
		$modules->load();
		
		return true;
    }
}
?>
