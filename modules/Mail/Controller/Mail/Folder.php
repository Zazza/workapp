<?php
class Controller_Mail_Folder extends Controller_Mail {

	function index() {
		$mailClass = new Model_Mail();
		
		$this->view->setLeftContent($this->view->render("left_mail", array("folders" => $this->folders, "enableCheck" => $this->enableCheck)));

		if (isset($_POST["edit_submit"])) {
			
			$this->view->setTitle("Правка");
			
			$err = array();
			$str = htmlspecialchars($_POST["folder"]);
			$strlen = mb_strlen($_POST["folder"]);
			if ( ($strlen < 1) or ($strlen > 64) ) { $err[] = "Название папки должно быть от 1 до 64 символов"; }
			
			if (count($err) == 0) {
				$mailClass->editFolder($_GET["id"], $str);
				
				$this->view->refresh(array("timer" => "1", "url" => "mail/folder/"));
			} else {
				$this->view->mail_folder(array("err" => $err, "folders" => $this->folders));
			}
		
		} elseif (isset($_POST["submit"])) {
			
			$this->view->setTitle("Новая папка");

			$err = array();
			$str = htmlspecialchars($_POST["folder"]);
			$strlen = mb_strlen($_POST["folder"]);
			if ( ($strlen < 1) or ($strlen > 64) ) { $err[] = "Название папки должно быть от 1 до 64 символов"; }
			
			if (count($err) == 0) {
				$mailClass->addFolder($str);
				
				$this->view->refresh(array("timer" => "1", "url" => "mail/folder/"));
			} else {
				$this->view->mail_folder(array("err" => $err, "folders" => $this->folders));
			}
		} else {
			if (isset($_GET["id"])) {
				
				$this->view->setTitle("Правка");
				
				foreach($this->folders as $part) {
					if ($part["id"] == $_GET["id"]) {
						$folder = $part;
					}
				}
				$this->view->mail_editfolder(array("folder" => $folder));
			} else {
				
				$this->view->setTitle("Новая папка");
				
				$this->view->mail_folder(array("folders" => $this->folders));
			}
		}
		
		$this->view->showPage();
	}
}
?>