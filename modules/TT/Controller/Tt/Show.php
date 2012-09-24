<?php
class Controller_Tt_Show extends Controller_Tt {

	public function index() {
		$this->view->setTitle("Просмотр задачи");
		
		$this->view->setLeftContent($this->view->render("left_tt", array()));
		//$top[0] = $this->view->render("top_tt", array());
		//$this->view->top_top(array("blocks" => $top));
		
		$author = array(); $ruser = array();

		if (isset($this->args[1])) {
			if ($data = $this->registry["tt"]->getTask($this->args[1])) {

				if (count($data) > 0) {
					$numComments = $this->registry["tt"]->getNumComments($this->args[1]);
					$newComments = $this->registry["tt"]->getNewCommentsFromTid($this->args[1]);
					
					$lastCommentDate = $this->registry["tt"]->addTaskView($this->args[1]);

					if ($data[0]["remote_id"] == 0) {
						$author = $this->registry["user"]->getUserInfo($data[0]["who"]);
					} else {
						$author = $this->registry["tt_user"]->getRemoteUserInfo($data[0]["who"]);
					}

					foreach($data as $part) {
						if (isset($part["uid"])) {
							if ($part["uid"] != 0) {
								$user = $this->registry["user"]->getUserInfo($part["uid"]);

								$ruser[] = "<a style='cursor: pointer' onclick='getUserInfo(" . $part["uid"] . ")'>" . $user["name"] . " " . $user["soname"] . "</a>";
							}
						}

						if (isset($part["rgid"])) {
							if ($part["rgid"] != 0) {
								$ruser[] = "<span style='color: #5D7FA6'><b>" . $this->registry["user"]->getSubgroupName($part["rgid"]) . "</b></span>";
							}
						}

						if ($part["all"] == 1) {
							$ruser[] = "<span style='color: #D9A444'><b>Все</b></span>";
						}
					}

					$group = null;
					if ($data[0]["gid"] != "0") {
						$group = $this->registry["tt"]->getGroupName($data[0]["gid"]);
					}

					$object = new Model_Object();
					$ai = new Model_Ai();
					$forms = $ai->getForms();

					$cuser = $this->registry["user"]->getUserInfo($data[0]["cuid"]);
					
					$notObj = false;
					if ($obj = $object->getShortObject($data[0]["oid"])) {
						$notObj = true;

						$numTroubles = $object->getNumTroubles($data[0]["oid"]);
						$advInfo = $ai->getAdvancedInfo($data[0]["oid"]);
						$numAdvInfo = $ai->getNumAdvancedInfo($data[0]["oid"]);
						
						$rObject = $this->registry["module_objects"]->renderObject($this->registry["ui"], $obj, $advInfo, $numAdvInfo, $forms, $numTroubles, $group);
						$this->view->setMainContent($rObject);
					}

					$this->view->tt_task(array("data" => $data, "author" => $author, "ruser" => $ruser, "cuser" => $cuser, "numComments" => $numComments, "newComments" => $newComments, "notObj" => $notObj, "obj" => $obj));

					$comments = $this->registry["tt"]->getComments($this->args[1]);
					$history = $this->registry["logs"]->getHistory("task", $this->args[1]);
					for($i=0; $i<count($comments); $i++) {
						$result[$i] = $comments[$i];
						$result[$i]["type"] = "comment"; 
					}
					for($j=0; $j<count($history); $j++) {
						$result[$j+$i] = $history[$j];
						$result[$j+$i]["ui"] = $this->registry["user"]->getUserInfo($history[$j]["uid"]);
						$result[$j+$i]["type"] = "history";
					}
					
					for($i=0; $i<count($result); $i++) {
						if (isset($result[$i]["id"])) {
							$min = $result[$i];
							for($j=$i; $j<count($result); $j++) {
								if (isset($result[$j]["id"])) {
									if ($result[$j]["timestamp"] < $result[$i]["timestamp"]) {
										$min = $result[$j];
										$result[$j] = $result[$i];
										$result[$i] = $min;
									}
								}
							}
						}
					}
					if (count($comments) > 0) {
						$this->view->setMainContent("<div style='padding: 10px 0 20px 40px; font-size: 14px; color: #999'>Комментарии:</div>");
					}
					
					//Первый лог (о создании задачи) не нужен!
					unset($result[0]);
					
					foreach ($result as $part) {
						if ($part["date"] >= $lastCommentDate) { $new = true; } else { $new = false; }
						
						$this->view->tt_comment(array("comment" => $part, "data" => $data, "new" => $new));
					}
					
					if (!$data[0]["close"]) {
						$status = $this->registry["tt"]->getCommentsStatus();
						$this->view->tt_formcomment(array("tid" => $this->args[1], "status" => $status));
					}
				} else {
					$this->view->setMainContent("<p>Задача не найдена</p>");
				}
			} else {
				$this->view->setMainContent("<p>Задача не найдена</p>");
			}
		}

		$this->view->showPage();
	}
}
?>