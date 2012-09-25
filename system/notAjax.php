<?php 
class notAjax extends Engine_Interface {
	private $_view = null;
	
	function __construct() {
		parent::__construct();
		
		$this->view = $this->registry["view"];
	}
	
	public function run() {
		$this->dashboard();
		$this->users();
		$this->fa();
	}
	
	private function dashboard() {
		$ui = new Model_Ui();
		
		$dashboard = new Model_Dashboard();
			
		$dash["events"] = $dashboard->findEvents();
			
		if ($dashboard->getServiceVar()) {
			$dash["service"] = true;
		}
			
		$dash["notify"] = $dashboard->getnumEventsVar();
			
		if (count($dash["events"]) == 0) {
			$dash["events"] = "<p id='emptyEvents'>Новых событий нет</p>";
		}
			
		if (isset($this->registry["module_chat"])) {
			$chat = $this->registry["module_chat"];
			$rooms = $chat->getChatsRoom();
			$dash["rooms"] = $chat->getRenderRooms();
			$dash["numChats"] = count($rooms);
		}
			
		$dash = json_encode($dash);
			
		$this->view->setBottomPanel($this->view->render("fastmenu_dashboard", array("dash" => $dash)), 0);
	}
	
	private function users() {
		$json = $this->registry["user"]->getUserList();
		$json["listUsers"] = $this->view->render("users_bplist", array("listUsers" => $json["listUsers"]));
		$ulist = json_encode($json);
		$this->view->setBottomPanel($this->view->render("fastmenu_users", array("ulist" => $ulist)), 1);
		}
	
	private function fa() {
		$content = $this->view->render("fa_content", array());
		$this->view->setContent($content);
	}
}
?>