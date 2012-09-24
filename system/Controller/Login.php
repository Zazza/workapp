<?php
class Controller_Login extends Engine_Controller {
	
	function __construct() {
		parent::__construct();
    }

	public function index() {
		if (!$this->registry["auth"]) {
            
            $login = new Model_Ui();
            
            if (isset($_POST["submit"])) {
                if ($login->login($_POST["login"], $_POST["password"])) {
                    echo $this->view->render("refresh", array("timer" => "1", "url" => ""));
                } else {
                    echo $this->view->render("login", array("err" => TRUE, "url" => $this->registry["siteName"]));
                }
            } else {        
                echo $this->view->render("login", array("url" => $this->registry["siteName"]));
            }
        } else {
        	$this->__call();
        }
	}
}
?>
