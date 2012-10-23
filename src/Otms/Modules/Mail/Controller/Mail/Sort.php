<?php

/**
 * This file is part of the Workapp project.
 *
 * Mail Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Mail\Controller\Mail;

use Otms\Modules\Mail\Controller\Mail;
use Otms\Modules\Mail\Model;
use Otms\System\Model\Validate;

/**
 * Controller\Mail\Sort class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Sort extends Mail {

	function index() {
		$mailClass = new Model\Mail();
		
		$this->view->setTitle("Правила обработки для почты");
		
		$this->view->setLeftContent($this->view->render("left_mail", array("folders" => $this->folders, "enableCheck" => $this->enableCheck)));

		if (isset($_POST["submit"])) {
			$validate = new Validate();
			
			$err = array();

			if (isset($_POST["checkbox_from"])) {
				if ($txt = $validate->email($_POST["from"])) { $err[] = $txt; }
			} else {
				$_POST["from"] = null;
			}
			if (isset($_POST["checkbox_to"])) {
				if ($txt = $validate->email($_POST["to"])) { $err[] = $txt; }
			} else {
				$_POST["to"] = null;
			}
			if (isset($_POST["checkbox_subject"])) {
				if ($_POST["subject"] == "") { $err[] = 'Поле "Тема" не может быть пустой'; }
			} else {
				$_POST["subject"] = null;
			}
			
			if ( (!isset($_POST["checkbox_from"])) and (!isset($_POST["checkbox_to"])) and (!isset($_POST["checkbox_subject"])) ) {
				$err[] = 'Не указано ни одного критерия для сортировки';
			}
			
			if (count($err) == 0) {
				$mailClass->addSort($_POST);
				
				$this->view->refresh(array("timer" => "1", "url" => "mail/sort/"));
			} else {
				if (isset($_GET["mid"])) {
					$mail = $mailClass->getMailFromId($_GET["mid"]);
				}
				
				$rusers = $this->registry["user"]->getUsers();
				
				$formtask = $this->registry["module_task"]->formtask();
				$this->view->mail_addsort(array("formtask" => $formtask, "err" => $err, "mail" => $mail, "folders" => $this->folders, "rusers" => $rusers));
			}

		} elseif (isset($_POST["edit_sort"])) {
			
			if ( (isset($_GET["id"])) and (is_numeric($_GET["id"])) ) {
				$param = $mailClass->getSort($_GET["id"]);

				$validate = new Validate();
				
				$err = array();
	
				if ( (isset($_POST["from"])) and ($_POST["from"] != null) ) {
					if ($txt = $validate->email($_POST["from"])) { $err[] = $txt; }
						else { $sort["type"] = "from"; $sort["val"] = $_POST["from"]; $sort["folder_id"] = $_POST["folder"]; }
				} 
				if ( (isset($_POST["to"])) and ($_POST["to"] != null) ) {
					if ($txt = $validate->email($_POST["to"])) { $err[] = $txt; }
						else { $sort["type"] = "to"; $sort["val"] = $_POST["to"]; $sort["folder_id"] = $_POST["folder"]; }
				}
				if ( (isset($_POST["subject"])) and ($_POST["subject"] != null) ) {
					if ($_POST["subject"] == "") { $err[] = 'Поле "Тема" не может быть пустой'; }
						else { $sort["type"] = "subject"; $sort["val"] = $_POST["subject"]; $sort["folder_id"] = $_POST["folder"]; }
				}
				
				if ( (!isset($_POST["from"])) and (!isset($_POST["to"])) and (!isset($_POST["subject"])) ) {
					$err[] = 'Не указано ни одного критерия для сортировки';
				}
				
				if (count($err) == 0) {
					$mailClass->delSort($_GET["id"]);
					$mailClass->addSort($_POST);
					
					$this->view->refresh(array("timer" => "1", "url" => "mail/sort/"));
				} else {
					$task[0] = $param[0]["task"];
					$formtask = $this->registry["module_task"]->formtask($task);
					$this->view->mail_editsort(array("formtask" => $formtask, "err" => $err, "sort" => $param, "folders" => $this->folders));
				}
			}
		} elseif ( (isset($_GET["mid"])) or (isset($_GET["add"])) ) {
			$mail = array();
			
			if ( (isset($_GET["mid"])) and (is_numeric($_GET["mid"])) ) {
				$mail = $mailClass->getMailFromId($_GET["mid"]);
			}
			
			if ( (isset($_GET["id"])) and (is_numeric($_GET["id"])) ) {
				$mail = $mailClass->getSort($_GET["id"]);
			}
			
			$rusers = $this->registry["user"]->getUsers();
				
			$formtask = $this->registry["module_task"]->formtask();
			$this->view->mail_addsort(array("formtask" => $formtask, "mail" => $mail, "folders" => $this->folders, "rusers" => $rusers));
		} elseif (isset($_GET["id"])) {
			if ( (isset($_GET["id"])) and (is_numeric($_GET["id"])) ) {
				$sort = $mailClass->getSort($_GET["id"]);
				$rusers = $this->registry["user"]->getUsers();
				
				$issRusers = array(); $k = 0;

				if (isset($sort[0]["task"]["ruser"])) {
					foreach($sort[0]["task"]["ruser"] as $part) {
						$row = $this->registry["user"]->getUserInfo($part);
						 
						$k++;
        
						$issRusers[$k]["desc"] = '<p><span style="font-size: 11px; margin-right: 10px;" id="udesc[' . $row["uid"] . ']">' . $row["name"] . ' ' . $row["soname"] . '</span>';
						$issRusers[$k]["desc"] .= '<input id="uhid[' . $row["uid"] . ']" type="hidden" name="ruser[]" value="' . $row["uid"] . '" /></p>';
					}
				}
				 
				if (isset($sort[0]["task"]["gruser"])) {
					foreach($sort[0]["task"]["gruser"] as $part) {
						$gname = $this->registry["user"]->getGroupName($part);
						 
						$k++;
						 
						$issRusers[$k]["desc"] = '<p style="font-size: 11px; margin-right: 10px">' . $gname . '<input type="hidden" name="gruser[]" value="' . $part["rgid"] . '" /></p>';
					}
				}
				 
				if (isset($sort[0]["rall"])) {
					$k++;
					 
					$issRusers[$k]["desc"] = '<p style="font-size: 11px; margin-right: 10px">Все<input type="hidden" name="rall" value="1" /></p>';
				}

				$task[0] = $sort[0]["task"];
				$formtask = $this->registry["module_task"]->formtask($task);
				$this->view->mail_editsort(array("formtask" => $formtask, "sort" => $sort, "folders" => $this->folders, "rusers" => $rusers, "issRusers" => $issRusers));
			}
		} else {		
			$list = $mailClass->getSorts();
			$formtask = $this->registry["module_task"]->formtask();
			$this->view->mail_sorts(array("list" => $list, "formtask" => $formtask));
		}
		
		$this->view->showPage();		
	}
}
?>