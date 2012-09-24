<?php
class Controller_Tt_Task extends Controller_Tt {
	public function index() {
		$this->view->setTitle("Задачи");
		
		$this->view->setLeftContent($this->view->render("left_tt", array()));
		
		if (isset($this->args[1])) {
			$this->registry["tt"]->links = "tt/" . $this->args[0] . "/" . $this->args[1] . "/";
		} else {
			$this->registry["tt"]->links = "tt/" . $this->args[0] . "/";
		}
			
		if (isset($this->args[1])) {
			if($this->args[1] == "iter") {
				$tasks = $this->registry["tt"]->getIterTasks();
				$this->view->tt_caltype(array("caltype" => $cal["type"], "date" => $this->model->editDate(date("Y-m-d"))));
			} elseif($this->args[1] == "time") {
				$tasks = $this->registry["tt"]->getTimeTasks();
				$this->view->tt_caltype(array("caltype" => $cal["type"], "date" => $this->model->editDate(date("Y-m-d"))));
			} elseif($this->args[1] == "noiter") {
				$tasks = $this->registry["tt"]->getNoiterTasks();
				$this->view->tt_caltype(array("caltype" => $cal["type"], "date" => $this->model->editDate(date("Y-m-d"))));
			} elseif($this->args[1] == "me") {
				$sortmytt = & $_SESSION["sortmytt"];
				if ( (!isset($sortmytt["sort"])) or (!isset($sortmytt["id"])) ) {
					$sortmytt["sort"] = "date";
					$sortmytt["id"] = "false";
				}

				$sort_groups = $this->registry["tt"]->getSortGroupsMe();
				$this->view->setLeftContent($this->view->render("left_sortmytt", array("sort" => $sortmytt, "sg" => $sort_groups)));

				//$top[0] = $this->view->render("top_tt", array());
				//$top[1] = $this->view->render("top_sort", array("sort" => $sortmytt, "sg" => $sort_groups));
				//$this->view->top_top(array("blocks" => $top));

				$tasks = $this->registry["tt"]->getMeTasks();
			}
		} else {
			$this->__call("tt", "index");
		}

		$this->showTasks($tasks);
	}
}
?>