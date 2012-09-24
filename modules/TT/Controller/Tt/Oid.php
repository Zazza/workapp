<?php
class Controller_Tt_Oid extends Controller_Tt {
	public function index() {
		$this->view->setTitle("Задачи для объекта");
		
		$this->view->setLeftContent($this->view->render("left_tt", array()));
		
		if (isset($this->args[1])) {
        	$this->registry["tt"]->links = "tt/" . $this->args[0] . "/" . $this->args[1] . "/";
        	$tasks = $this->registry["tt"]->getOidTasks($this->args[1]);
        } else {
        	$this->__call("tt", "index");
        }
        
        $this->showTasks($tasks);
    }
}
?>