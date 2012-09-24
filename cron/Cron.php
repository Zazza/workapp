<?php
class Cron extends CronPreload {
	function index() {
		$flag = FALSE;
		if ($this->registry["ip"] == $this->registry["local"]) {
			$flag = TRUE;
		}

		if ($flag) {
			$this->sendNotify();
			$this->checkMail();
		}
	}
	
	function sendNotify() {
		$helpers = new Helpers_Helpers();
	
		$users = $this->registry["user"]->getUsersList();

		foreach($users as $uid) {
			$nowdate = date("Ymd");
	
			$i = 0; $notify = array();
	
			if ($uid["notify"]) {
				if (date("Ymd", strtotime($uid["last_notify"])) < date("Ymd")) {
					if ($uid["time_notify"] < date("H:i:s")) {
	
						$tasks = $this->registry["tt"]->getTasksDate($uid["id"], $nowdate);
	
						foreach($tasks as $part) {
							if ($data = $this->registry["tt"]->getTask($part["id"])) {
								$notify[$i]["id"] = $data[0]["id"];
								$notify[$i]["text"] = $data[0]["text"];
	
								$notify[$i]["email"] = $uid["email"];
	
								$i++;
	
								unset($obj); unset($author); unset($ruser);
							}
						}
							
						if (count($notify) > 0) {
							$helpers->sendNotify($notify, $i);
						}
							
						$this->registry["user"]->setNotifyTime($uid["id"]);
					}
				}
			}
		}
	}

	function checkMail() {
		$mailClass = new Model_Mail();
	
		$users = $this->registry["user"]->getUsersList();
	
		foreach($users as $user) {
			$mailboxes = $mailClass->getUserInMailboxes($user["id"]);
			foreach($mailboxes as $mailbox) {
	
				$mbox = $mailbox["email"];
	
				$mailClass->uid = $user["id"];
	
				$mailClass->checkMail($mbox);
			}
		}
	}
}
?>