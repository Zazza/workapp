<?php

/**
 * This file is part of the Workapp project.
 *
 * Task Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Task\Model;

use Engine\Modules\Model;
use PDO;

/**
 * Model\Route class
 *
 * Класс-модель для работы с задачами из писем
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Mail extends Model {
	
	/**
	 * Создать задачу
	 *
	 * @param int $remote_id - ID задачи в другой системе
	 * @param array $post
	 * @return int - ID задачи
	 */
	function addTask($remote_id, $post) {
		if ($post["task"] != '') {

			$secure = $post["secure"];
			
			// CONTACT
			$data = base64_decode($post["uavatar"]);
			$avatar = md5($post["uavatar"]);
			$filename = $this->registry["rootPublic"] . $this->registry["path"]["attaches"] . $avatar;

			$fp = fopen($filename, "wb+");
			fwrite($fp, $data);
			fclose($fp);

			if (!$this->registry["user"]->issetRemoteContact($post["uemail"])) {
				$sql = "INSERT INTO troubles_remote_contact (`email`, `name`, `soname`, `avatar`, `group`) VALUES (:email, :name, :soname, :avatar, :group)";
	
				$res = $this->registry['db']->prepare($sql);
				$param = array(":email" => $post["uemail"], ":name" => $post["uname"], ":soname" => $post["usoname"], ":avatar" => $avatar, ":group" => $post["ugname"]);
				$res->execute($param);
				
				$this->registry["user"]->tid = $this->registry['db']->lastInsertId();
			} else {
				$sql = "UPDATE troubles_remote_contact SET `name` = :name, `soname` = :soname, `avatar` = :avatar, `group` = :group WHERE `email` = :email";
		
				$res = $this->registry['db']->prepare($sql);
				$param = array(":email" => $post["uemail"], ":name" => $post["uname"], ":soname" => $post["usoname"], ":avatar" => $avatar, ":group" => $post["ugname"]);
				$res->execute($param);
			}
			// END CONTACT
			
			$sql = "INSERT INTO troubles (`remote_id`, `who`, `imp`, `secure`, `text`, `gid`) VALUES (:remote_id, :who, :imp, :secure, :text, :gid)";

			$res = $this->registry['db']->prepare($sql);
			$param = array(":remote_id" => $remote_id, ":who" => $this->registry["user"]->tid, ":imp" => $post["imp"], ":secure" => $secure, ":text" => $post["task"], ":gid" => $post["ttgid"]);
			$res->execute($param);
			
			$tid = $this->registry['db']->lastInsertId();

			// ответственные
			if (!isset($post["ruser"])) { $post["ruser"] = array(); }
			if (!isset($post["gruser"])) { $post["gruser"] = array(); }
			if (!isset($post["rall"])) { $post["rall"] = array(); }

			if ($post["rall"] == "1") {
				$sql = "INSERT INTO troubles_responsible (tid, `all`) VALUES (:tid, 1)";

				$res = $this->registry['db']->prepare($sql);
				$param = array(":tid" => $tid);
				@$res->execute($param);
			} else {
				$sql = "INSERT INTO troubles_responsible (tid, uid) VALUES (:tid, :uid)";

				$res = $this->registry['db']->prepare($sql);
				$param = array(":tid" => $tid, ":uid" => $this->uid);
				@$res->execute($param);
			}
			// END ответственные

			if ($post["type"] == "0") {

				$starttime = $post["startdate_global"] . " " . $post["starttime_global"];
				$lifetime = 0;
				$post["itertime"] = "";

			} elseif ($post["type"] == "1") {
				$post["itertime"] = "";

				$starttime = $post["startdate_noiter"] . " " . $post["starttime_noiter"];

				if ($post["timetype_noiter"] == "min") {

					$lifetime = $post["lifetime_noiter"] * 60;

				} elseif ($post["timetype_noiter"] == "hour") {

					$lifetime = $post["lifetime_noiter"] * 60 * 60;

				} elseif ($post["timetype_noiter"] == "day") {

					$lifetime = $post["lifetime_noiter"] * 24 * 60 * 60;

				} else {

					$lifetime = 0;

				}
			} elseif ($post["type"] == "2") {

				$starttime = $post["startdate_iter"] . " " . $post["starttime_iter"];

				if ($post["timetype_iter"] == "min") {

					$lifetime = $post["lifetime_iter"] * 60;

				} elseif ($post["timetype_iter"] == "hour") {

					$lifetime = $post["lifetime_iter"] * 60 * 60;

				} elseif ($post["timetype_iter"] == "day") {

					$lifetime = $post["lifetime_iter"] * 24 * 60 * 60;

				} else {

					$lifetime = 0;

				}
			}

			$sql = "INSERT INTO troubles_deadline (tid, type, opening, deadline, iteration, timetype_iteration) VALUES (:tid, :type, :opening, :deadline, :iteration, :timetype_iteration)";

			$res = $this->registry['db']->prepare($sql);
			$param = array(":tid" => $tid, ":type" => $post["type"], ":opening" => $starttime, ":deadline" => $lifetime, ":iteration" => $post["itertime"], ":timetype_iteration" => $post["timetype_itertime"]);
			$res->execute($param);

			$string = "Новая задача <a href='" . $this->registry["uri"] . "task/show/" . $tid . "/'>" . $tid . "</a>";
    		$tinfo["Текст"] = $post["task"];
   	
    		$this->registry["logs"]->set("task", $string, $tid, $tinfo);
			
    		return $tid;
		}
	}

	/**
	 * Правка задачи
	 * 
	 * @param array $remote_id - ID задачи в другой системе
	 * @param array $post - $_POST с новой задачей
	 * @return int - ID задачи
	 */
	function editTask($remote_id, $post) {
		if ($post["task"] != '') {

			$secure = $post["secure"];

			$sql = "SELECT id FROM troubles WHERE remote_id = :remote_id LIMIT 1";
		
			$res = $this->registry['db']->prepare($sql);
			$res->execute(array(":remote_id" => $remote_id));
			$tid = $res->fetchAll(PDO::FETCH_ASSOC);
			
			if (count($tid) == 1) {
				$tid = $tid[0]["id"];
			} else {
				$this->addTask($remote_id, $post);
				exit();
			}

			$sql = "UPDATE troubles SET imp = :imp, secure = :secure, text = :text, edittime = NOW(), gid = :gid WHERE id = :tid LIMIT 1";

			$res = $this->registry['db']->prepare($sql);
			$param = array(":tid" => $tid, ":imp" => $post["imp"], ":secure" => $secure, ":text" => $post["task"], ":gid" => $post["ttgid"]);
			$res->execute($param);

			$sql = "DELETE FROM troubles_responsible WHERE tid = :tid";

			$res = $this->registry['db']->prepare($sql);
			$param = array(":tid" => $tid);
			$res->execute($param);

			// ответственные
			if (!isset($post["rall"])) { $post["rall"] = array(); }

			if ($post["rall"] == "1") {
				$sql = "INSERT INTO troubles_responsible (tid, `all`) VALUES (:tid, 1)";

				$res = $this->registry['db']->prepare($sql);
				$param = array(":tid" => $tid);
				@$res->execute($param);
			} else {
				$sql = "INSERT INTO troubles_responsible (tid, uid) VALUES (:tid, :uid)";

				$res = $this->registry['db']->prepare($sql);
				$param = array(":tid" => $tid, ":uid" => $this->uid);
				@$res->execute($param);
			}
			// END ответственные

			if ($post["type"] == "0") {

				$starttime = $post["startdate_global"] . " " . $post["starttime_global"];
				$lifetime = 0;
				$post["itertime"] = "";

			} elseif ($post["type"] == "1") {
				$post["itertime"] = "";

				$starttime = $post["startdate_noiter"] . " " . $post["starttime_noiter"];

				if ($post["timetype_noiter"] == "min") {

					$lifetime = $post["lifetime_noiter"] * 60;

				} elseif ($post["timetype_noiter"] == "hour") {

					$lifetime = $post["lifetime_noiter"] * 60 * 60;

				} elseif ($post["timetype_noiter"] == "day") {

					$lifetime = $post["lifetime_noiter"] * 24 * 60 * 60;

				} else {

					$lifetime = 0;

				}
			} elseif ($post["type"] == "2") {

				$starttime = $post["startdate_iter"] . " " . $post["starttime_iter"];

				if ($post["timetype_iter"] == "min") {

					$lifetime = $post["lifetime_iter"] * 60;

				} elseif ($post["timetype_iter"] == "hour") {

					$lifetime = $post["lifetime_iter"] * 60 * 60;

				} elseif ($post["timetype_iter"] == "day") {

					$lifetime = $post["lifetime_iter"] * 24 * 60 * 60;

				} else {

					$lifetime = 0;

				}
			}

			$sql = "UPDATE troubles_deadline SET type = :type, opening = :opening, deadline = :deadline, iteration = :iteration, timetype_iteration = :timetype_iteration WHERE tid = :tid";

			$res = $this->registry['db']->prepare($sql);
			$param = array(":tid" => $tid, ":type" => $post["type"], ":opening" => $starttime, ":deadline" => $lifetime, ":iteration" => $post["itertime"], ":timetype_iteration" => $post["timetype_itertime"]);
			$res->execute($param);

			$string = "Правка задачи <a href='" . $this->registry["uri"] . "task/show/" . $tid . "/'>" . $tid . "</a>";
   			$tinfo["Текст"] = $post["task"];
			$this->registry["logs"]->set("task", $string, $tid, $tinfo);
			
			return $tid;
		}
	}
	
	/**
	 * Добавить комментарий к задаче
	 *
	 * @param array $remote_id - ID задачи в другой системе
	 * @param string $text - соощение
	 * @param int $status - ID статуса (таблица в бд: "comments_status")
	 * @param int $tdid - ID нового комментария
	 */
	function addComment($remote_id, $text, $status) {
		// CONTACT
		$data = base64_decode($text["uavatar"]);
		$avatar = md5($text["uavatar"]);
		$filename = $this->registry["rootPublic"] . $this->registry["path"]["attaches"] . $avatar;

		$fp = fopen($filename, "wb+");
		fwrite($fp, $data);
		fclose($fp);
			
		if (!$this->registry["user"]->issetRemoteContact($text["uemail"])) {
			$sql = "INSERT INTO troubles_remote_contact (`email`, `name`, `soname`, `avatar`, `group`) VALUES (:email, :name, :soname, :avatar, :group)";
	
			$res = $this->registry['db']->prepare($sql);
			$param = array(":email" => $text["uemail"], ":name" => $text["uname"], ":soname" => $text["usoname"], ":avatar" => $avatar, ":group" => $text["ugname"]);
			$res->execute($param);
			
			$this->registry["user"]->tid = $this->registry['db']->lastInsertId();
		} else {
			$sql = "UPDATE troubles_remote_contact SET `name` = :name, `soname` = :soname, `avatar` = :avatar, `group` = :group WHERE `email` = :email";

			$res = $this->registry['db']->prepare($sql);
			$param = array(":email" => $text["uemail"], ":name" => $text["uname"], ":soname" => $text["usoname"], ":avatar" => $avatar, ":group" => $text["ugname"]);
			$res->execute($param);
		}
		// END CONTACT
		
		$who = $this->registry["user"]->tid;
		
		$sql = "SELECT id FROM troubles WHERE remote_id = :remote_id LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$res->execute(array(":remote_id" => $remote_id));
		$tid = $res->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($tid) == 1) {
			$tid = $tid[0]["id"];
			
			$sql = "INSERT INTO troubles_discussion (`tid`, `uid`, `remote`, `text`, `status`) VALUES (:tid, :uid, :remote, :text, :status)";
	
			$res = $this->registry['db']->prepare($sql);
			$res->execute(array(":tid" => $tid, ":uid" => $this->registry["user"]->tid, ":remote" => "1", ":text" => $text["text"], ":status" => $status));
			
			$tdid = $this->registry['db']->lastInsertId();
			
			$string = "Новый комментарий к задаче <a href='" . $this->registry["uri"] . "task/show/" . $tid . "/'>" . $tid . "</a>";		
	
			if ($status != 0) {
				$status_text = $this->registry["task"]->getCommentStatusText($status);
				$tinfo["Статус"] = "<span style='padding: 2px 4px' class='info'>" . $status_text . "</span>";
			}
	    	$tinfo["Текст"] = $text["text"];
	    	
	    	$this->registry["logs"]->set("com", $string, $tid, $tinfo);
	    	
	    	return $tdid;
		}
	}
	
	/**
	 * Написать ответный комментарий к задаче созданной в другой системе
	 * 
	 * @param int $tid - ID задачи
	 * @param string $text - текст комментария
	 * @param int $status - ID статуса коментария (таблица в бд: "comments_status")
	 */
	function addCommentAnswer($tid, $text, $status) {
		// CONTACT
		$data = base64_decode($text["uavatar"]);
		$avatar = md5($text["uavatar"]);
		$filename = $this->registry["rootPublic"] . $this->registry["path"]["attaches"] . $avatar;

		$fp = fopen($filename, "wb+");
		fwrite($fp, $data);
		fclose($fp);
		
		if (!$this->registry["user"]->issetRemoteContact($text["uemail"])) {
			$sql = "INSERT INTO troubles_remote_contact (`email`, `name`, `soname`, `avatar`, `group`) VALUES (:email, :name, :soname, :avatar, :group)";
	
			$res = $this->registry['db']->prepare($sql);
			$param = array(":email" => $text["uemail"], ":name" => $text["uname"], ":soname" => $text["usoname"], ":avatar" => $avatar, ":group" => $text["ugname"]);
			$res->execute($param);
			
			$this->registry["user"]->tid = $this->registry['db']->lastInsertId();
		} else {
			$sql = "UPDATE troubles_remote_contact SET `name` = :name, `soname` = :soname, `avatar` = :avatar, `group` = :group WHERE `email` = :email";

			$res = $this->registry['db']->prepare($sql);
			$param = array(":email" => $text["uemail"], ":name" => $text["uname"], ":soname" => $text["usoname"], ":avatar" => $avatar, ":group" => $text["ugname"]);
			$res->execute($param);
		}
		// END CONTACT
		
		$who = $this->registry["user"]->tid;

		$sql = "INSERT INTO troubles_discussion (`tid`, `uid`, `remote`, `text`, `status`) VALUES (:tid, :uid, :remote, :text, :status)";

		$res = $this->registry['db']->prepare($sql);
		$res->execute(array(":tid" => $tid, ":uid" => $this->registry["user"]->tid, ":remote" => "1", ":text" => $text["text"], ":status" => $status));
		
		$tdid = $this->registry['db']->lastInsertId();
		
		$string = "Новый комментарий к задаче <a href='" . $this->registry["uri"] . "task/show/" . $tid . "/'>" . $tid . "</a>";		

		if ($status != 0) {
			$status_text = $this->registry["task"]->getCommentStatusText($status);
			$tinfo["Статус"] = "<span style='padding: 2px 4px' class='info'>" . $status_text . "</span>";
		}
    	$tinfo["Текст"] = $text["text"];
    	
    	$this->registry["logs"]->set("com", $string, $tid, $tinfo);
    	
    	return $tdid;
	}
	
	/**
	 * Закрыть задачу
	 *
	 * @param array $remote_id - ID задачи в другой системе
	 */
	function closeTask($remote_id) {
		$sql = "SELECT id FROM troubles WHERE remote_id = :remote_id LIMIT 1";
	
		$res = $this->registry['db']->prepare($sql);
		$res->execute(array(":remote_id" => $remote_id));
		$tid = $res->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($tid) == 1) {
			$tid = $tid[0]["id"];		
			
			$sql = "UPDATE troubles SET ending = NOW(), close = 1, cuid = :cuid WHERE id = :tid LIMIT 1";
	
			$res = $this->registry['db']->prepare($sql);
			$res->execute(array(":tid" => $tid, "cuid" => $this->uid));
	
			$task = $this->registry["task"]->getTask($tid);
			$string = "Завершена задача <a href='" . $this->registry["uri"] . "task/show/" . $tid . "/'>" . $tid . "</a>";
	
	    	$tinfo["Текст"] = $task[0]["text"];
	    	
	    	$this->registry["logs"]->set("task", $string, $tid, $tinfo);
		}
	}
}
?>