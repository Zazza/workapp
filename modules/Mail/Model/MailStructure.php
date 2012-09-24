<?php
class Model_MailStructure extends Modules_Model {
	public $connect;
	public $mes_num;
	public $uidl;
	
	public $to;
	
	public $header;
	private $body_part;
	private $attach;
	
	private $charset = null;
	
	public $newMail;
	
	public $emailTask = false;
	public $mailInTask = false;
	public $task = array();
	
	public $textMailAction = null;
	public $notLog = true;
	
	function getHeader() {
		$this->header = imap_headerinfo($this->connect, $this->mes_num);
	}
	
	function getPartMail($part_section, $part, $section = array()) {
		if (!empty($part->parts)) {
			$section[] = $part_section + 1;
			for ($p = 0; $p < count($part->parts); $p++) {
				$this->getPartMail($p, $part->parts[$p], $section);
			}
		} else {
			$section_string = null;
			$section_string = implode($section, ".");
			if ($section_string != null) {
				$section_string .= "." . ($part_section + 1);
			} else {
				$section_string .= $part_section + 1;
			}
			
			if ($part->subtype == 'PLAIN') {
				if ( (isset($part->disposition)) and (strtolower($part->disposition) == "attachment") ) {
					$filename = $this->getAttachName($part);
					$md5 = $this->saveAttach($part, $section_string, $filename);
					$this->attach[$md5] = $filename;
				} else {
					$this->body_part[count($this->body_part)]["text"] = $this->getMailText($part, $section_string);
					$this->body_part[count($this->body_part)-1]["type"] = "text";
				}
			} elseif ($part->subtype == "HTML") {
				if ( (isset($part->disposition)) and (strtolower($part->disposition) == "attachment")  ) {
					$filename = $this->getAttachName($part);
					$md5 = $this->saveAttach($part, $section_string, $filename);
					$this->attach[$md5] = $filename;
				} else {
					$this->body_part[count($this->body_part)]["text"] = $this->getMailText($part, $section_string);
					$this->body_part[count($this->body_part)-1]["type"] = "html";
				}
			} else {
				$filename = $this->getAttachName($part);
				$md5 = $this->saveAttach($part, $section_string, $filename);
				$this->attach[$md5] = $filename;
			}
		}
	}
	
	function fetchMail() {
		$this->body_part = array();
		
		$this->body_array = array();
		$this->attach = array();
		$this->section = 0;
		
		$mail["uid"] = $this->uidl;

		$st = imap_fetchstructure($this->connect, $this->mes_num);
		$this->st = $st;

		if (!empty($st->parts)) {
			for ($p = 0; $p < count($st->parts); $p++) {
				$part = $st->parts[$p];

				$this->getPartMail($p, $part);
			}
		} else {
			if ($st->subtype == 'PLAIN') {
				$this->body_part[count($this->body_part)]["text"] = $this->getMailText($st, 1);
				$this->body_part[count($this->body_part)-1]["type"] = "text";
			} elseif ($st->subtype == "HTML") {
				$this->body_part[count($this->body_part)]["text"] = $this->getMailText($st, 1);
				$this->body_part[count($this->body_part)-1]["type"] = "html";
			} else {
				$filename = $this->getAttachName($st);
				$md5 = $this->saveAttach($st, 1, $filename);
				$this->attach[$md5] = $filename;
			}
		}

		$mail["body"] = $this->body_part;
		$mail["attach"] = $this->attach;
		
		$mail["subject"] = null;
		if (isset($this->header->subject)) {
			$elements = imap_mime_header_decode($this->header->subject);
			foreach ($elements as $element) {
				$charset = $element->charset;
	
				if ( (strtolower($charset) != "default") and (strtolower($charset) != "x-unknown") ) {
					$mail["subject"] .= iconv($charset, "UTF-8", $element->text);
				} else {
					if ($this->charset != null) {
						$mail["subject"] .= iconv($this->charset, "UTF-8", $element->text);
					} else {
						$mail["subject"] .= $element->text;
					}
				}
			}
		}
		if ($mail["subject"] == "") {
			$mail["subject"] = "Без темы";
		}

		$mail["date"] = date("Y-m-d H:i:s", $this->header->udate);

		$mail["personal"] = '0';
		
		foreach($this->header->from as $from) {
			if (isset($from->personal)) {
				$elements = imap_mime_header_decode($from->personal);
				foreach($elements as $element) {
					$charset = $element->charset;
	
					if ($charset != "default") {
						$mail["personal"] = iconv($charset, "UTF-8", $element->text);
					} else {
						$mail["personal"] = $element->text;
					}
				}
			}
	
			$mail["mailbox"] = $from->mailbox;
			$mail["host"] = $from->host;
		}

		$mail["to"] = $this->to;
		
		if ($json = json_decode(base64_decode($mail["subject"]))) {
			$this->mailAction($mail);
		} else {
			$this->action($mail);
		}

		return $mail;
	}
	
	function action($mail) {
		$startdate["startdate_global"] = date("Y-m-d"); $startdate["starttime_global"] = date("H:i:s");
		$startdate["startdate_noiter"] = date("Y-m-d"); $startdate["starttime_noiter"] = date("H:i:s");
		$startdate["startdate_iter"] = date("Y-m-d"); $startdate["starttime_iter"] = date("H:i:s");
						
		$mailClass = new Model_Mail();
		$sorts = $mailClass->getSorts();

		foreach($sorts as $part) {
			$k = 0;
			foreach($part as $parted) {
				if ($parted["type"] == "to") {
					if ($parted["val"] == $mail["to"]) {
						if ($parted["action"] == "remove") {
							$this->emailTask = true;
						} else if ($parted["action"] == "task") {
							$sort = $mailClass->getSortByTo($parted["val"]);
							$sort += $startdate;
							$sort["task"] = "1";
							$k++;
						}
					}
				}
				
				if ($parted["type"] == "from") {
					if ($parted["val"] == $mail["mailbox"] . "@" . $mail["host"]) {
						if ($parted["action"] == "remove") {
							$this->emailTask = true;
						} else if ($parted["action"] == "task") {
							$sort = $mailClass->getSortByFrom($parted["val"]);
							$sort += $startdate;
							$sort["task"] = "1";
							$k++;
						}
					}
				}
				
				if ($parted["type"] == "subject") {
					if (mb_strpos($mail["subject"], $parted["val"]) !== false) {
						if ($parted["action"] == "remove") {
							$this->emailTask = true;
						} else if ($parted["action"] == "task") {
							$sort = $mailClass->getSortBySubject($parted["val"]);
							$sort += $startdate;
							$sort["task"] = "1";
							$k++;
						}
					}
				}
			}
			if ($k == count($part)) {
				$this->mailInTask = true;
				$this->task = $sort;
			}
		} 
	}
	
	function mailAction($mail) {
		$ttmail = new Model_TtMail();
		$ttmail->uid = $this->uid;
		
		$settings = new Model_Settings();
		$otms_mail = $settings->getMailbox();
		
		$json = json_decode(base64_decode($mail["subject"]));
	
		if ( ($json->name == "OTMS") and (isset($json->method)) ) {
			$this->emailTask = true;

	    	if ($otms_mail["email"] != $mail["mailbox"] . "@" . $mail["host"]) {
	    		if ($json->method == "addtask") {
	    			$this->textMailAction = "Новая задача(другая OTMS)";
			    	foreach($mail["body"] as $part) {
			    		$part = json_decode(base64_decode($part["text"]), true);

			    		$tid = $ttmail->addTask($json->tid, $part);
			    	}
			    	
	    			foreach($mail["attach"] as $key=>$part) {
						if ($part != "") {
							$sql = "INSERT INTO mail_attach (tid, md5, filename) VALUES (:tid, :md5, :filename)";
		        
					        $res = $this->registry['db']->prepare($sql);
							$param = array(":tid" => $tid, ":md5" => $key, ":filename" => $part);
							$res->execute($param);
						}
					}
	    		} elseif ($json->method == "edittask") {
	    			$this->textMailAction = "Правка задачи(другая OTMS)";
	    			foreach($mail["body"] as $part) {
			    		$part = json_decode(base64_decode($part["text"]), true);

			    		$tid = $ttmail->editTask($json->tid, $part);
			    	}
			    	
			    	if ( (isset($mail["attach"])) and (count($mail["attach"]) > 0) ) {
			    		$sql = "DELETE FROM mail_attach WHERE tid = :tid";
		        
					    $res = $this->registry['db']->prepare($sql);
						$param = array(":mid" => $tid);
						$res->execute($param);
			    	}
			    	
	    			foreach($mail["attach"] as $key=>$part) {
						if ($part != "") {
							$sql = "INSERT INTO mail_attach (tid, md5, filename) VALUES (:tid, :md5, :filename)";
		        
					        $res = $this->registry['db']->prepare($sql);
							$param = array(":tid" => $tid, ":md5" => $key, ":filename" => $part);
							$res->execute($param);
						}
					}
	    		} elseif ($json->method == "closetask") {
	    			$this->textMailAction = "Задача закрыта(другая OTMS)";
	    			
	    			$ttmail->closeTask($json->tid);
	    		} elseif ($json->method == "comment") {
	    			$this->textMailAction = "Комментарий к задаче(другая OTMS)";
	    			
	    			foreach($mail["body"] as $part) {
	    				$part = json_decode(base64_decode($part["text"]), true);
	    				
	    				if ($json->rc) {
	    					$tdid = $ttmail->addCommentAnswer($json->tid, $part, $part["status"]);
	    				} else {
	    					$tdid = $ttmail->addComment($json->tid, $part, $part["status"]);
	    				}
	    			}
	    			
    				foreach($mail["attach"] as $key=>$part) {
						if ($part != "") {
							$sql = "INSERT INTO mail_attach (tdid, md5, filename) VALUES (:tdid, :md5, :filename)";
		        
					        $res = $this->registry['db']->prepare($sql);
							$param = array(":tdid" => $tdid, ":md5" => $key, ":filename" => $part);
							$res->execute($param);
						}
					}

	    		}
	    	} else {
				$this->notLog = false;
			}
		}
	}

	function getAttachName($part) {
		$attach = null;
		
		if ( (isset($part->ifparameters)) and ($part->ifparameters)) {
			foreach($part->parameters as $parted) {
				$elements = imap_mime_header_decode($parted->value);
				
				foreach($elements as $element) {
					$charset = $element->charset;
	
					if (strtolower($charset) != "default") {
						$attach .= iconv($charset, "UTF-8", $element->text);
					} else {
						if ($this->charset != null) {
							$attach .= iconv($this->charset, "UTF-8", $element->text);
						} else {
							$attach .= $element->text;
						}
					}
				}
			}
		} elseif ( (isset($part->ifdparameters)) and ($part->ifdparameters)) {
			foreach($part->dparameters as $parted) {
				$elements = imap_mime_header_decode($parted->value);
				
				foreach($elements as $element) {
					$charset = $element->charset;
	
					if (strtolower($charset) != "default") {
						$attach .= iconv($charset, "UTF-8", $element->text);
					} else {
						if ($this->charset != null) {
							$attach .= iconv($this->charset, "UTF-8", $element->text);
						} else {
							$attach .= $element->text;
						}
					}
				}
			}
		}

		return $attach;
	}
	
	function saveAttach($part, $section, $filename) {
		switch($part->encoding) {
			case 0: $data = imap_fetchbody($this->connect, $this->mes_num, $section); break;
			case 1: $data = imap_fetchbody($this->connect, $this->mes_num, $section); break;
			case 2: $data = imap_fetchbody($this->connect, $this->mes_num, $section); break;
			case 3: $data = base64_decode(imap_fetchbody($this->connect, $this->mes_num, $section)); break;
			case 4: $data = quoted_printable_decode(imap_fetchbody($this->connect, $this->mes_num, $section)); break;
			case 5: $data = imap_fetchbody($this->connect, $this->mes_num, $section); break;
		}

		$md5 = md5($this->uidl . $filename);
		$filename = $this->registry["rootPublic"] . $this->registry["path"]["attaches"] . $md5;

		$fp = fopen($filename, "wb+");
		fwrite($fp, $data);
		fclose($fp);
		
		return $md5;
	}

	function getMailText($part, $p) {
		switch($part->encoding) {
			case 0: $body = imap_fetchbody($this->connect, $this->mes_num, $p); break;
			case 1: $body = imap_fetchbody($this->connect, $this->mes_num, $p); break;
			case 2: $body = imap_fetchbody($this->connect, $this->mes_num, $p); break;
			case 3: $body = base64_decode(imap_fetchbody($this->connect, $this->mes_num, $p)); break;
			case 4: $body = quoted_printable_decode(imap_fetchbody($this->connect, $this->mes_num, $p)); break;
			case 5: $body = imap_fetchbody($this->connect, $this->mes_num, $p); break;
		}

		foreach($part->parameters as $parted) {
			if (strtolower($parted->attribute) == "charset") {
				if (strtolower($parted->value) != "x-unknown") {
					$this->charset = $parted->value;
					
					$body = iconv($parted->value, "UTF-8", $body);
				}
			}
		}
		
		if (count((array)$part->parameters) == 0) {
			$body = iconv("CP1251", "UTF-8", $body);
		}

		return $body;
	}
}
?>