<?php

/**
* This file is part of the Workapp project.
*
* (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
*
*/

namespace Otms\System\Model;

use Engine\Model;
use PDO;

/**
 * User Model class
 *
 * Класс для работы с пользовательскими данными
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */

class User extends Model {
	/**
	 * Путь к картине(аватарке) по умолчанию
	*
	* @var string $noavatar
	*/
	public $noavatar = "img/noavatar.gif";
	
	/**
	 * ID задачи
	 * 
	 * @var int $tid
	 */
	public $tid;
	
	/**
	 * Время хранения значения в memcached в секундах (по умолчанию 300 секунд)
	 * Если значение в memcached есть - считаем, что пользователь имеет статус online
	 * 
	 * @var int $online_time
	 */
	private $online_time = 300;
    
	/**
	* Получает данные пользователя
	* Сохраняет статус присутствия пользователя в $data["status"]
	*
	* @param int $uid
	* @return array
	*/
    public function getUserInfo($uid) {
    	$data = array();
    
    	$this->memcached->set("ui:" . $uid);
    
    	if (!$this->memcached->load()) {
    
    		$sql = "SELECT users.id AS id, users.login AS `login`, users.pass AS pass, users.quota, users.name AS `name`, users.soname AS `soname`, users.signature AS signature, users.icq, users.skype, users.adres, users.phone, users.avatar, users.email AS email, users.notify, users.time_notify, users.email_for_task, p.admin AS admin, g.id AS gid, p.group, g.name AS gname
        	        FROM users 
        	        LEFT JOIN users_priv AS p ON (users.id = p.id)
        	        LEFT JOIN users_subgroup AS g ON (p.group = g.id)
        	        WHERE users.id = :uid LIMIT 1";
    		 
    		$res = $this->registry['db']->prepare($sql);
    		$param = array(":uid" => $uid);
    		$res->execute($param);
    		$data = $res->fetchAll(PDO::FETCH_ASSOC);
    
    		if (count($data) == 1) {
    			$dbava = $data[0]["avatar"];
    
    			if ($data[0]["avatar"] != "") {
    				$data[0]["avatar"] = $this->registry["siteName"] . $this->registry["uri"] . $this->registry["path"]["avadir"] . $dbava;
    				$data[0]["avatarpath"] = $this->registry["rootPublic"] . $this->registry["path"]["avadir"] . $dbava;
    			} else {
    				$data[0]["avatar"] = $this->registry["siteName"] . $this->registry["uri"] . $this->noavatar;
    				$data[0]["avatarpath"] = $this->registry["rootPublic"] . $this->noavatar;
    			}
    
    			$data[0]["uid"] = $data[0]["id"];
    			$data = $data[0];
    
    			$this->memcached->save($data);
    		}
    	} else {
    		$data = $this->memcached->get();
    	}
    	
    	$data["status"] = $this->getStatus($uid);
    
    	return $data;
    }
    
    /**
     * Меняет статус присутствия пользователя на online 
     * $this->memcached["status[UID]"], где [UID] - int ID пользователя
     */
    public function setOnline() {
    	$this->memcached->set("status" . $this->registry["ui"]["id"]);
    	if ($this->memcached->load()) {
    		$this->memcached->delete();
    	}
    
    	$this->memcached->saveTime("online", $this->online_time);
    }
    
    /**
     * Меняет статус присутствия пользователя на offline
     * $this->memcached["status[UID]"], где [UID] - int ID пользователя
    */
    public function setOffline() {
    	$this->memcached->set("status" . $this->registry["ui"]["id"]);
    	$this->memcached->delete();
    }
    
    /**
     * Получить статус присутствия пользователя
     * 
     * @param int $uid
     * @return TRUE
     * @return FALSE
     */
    public function getStatus($uid) {
    	$this->memcached->set("status" . $uid);
    	if ($this->memcached->load()) {
    		return true;
    	} else {
    		return false;
    	}
    }

    /**
     * Получить ID группы по ID пользователя
     * 
     * @param int $uid
     * @return int
     */
    public function getGidFromUid($uid) {
		$sql = "SELECT up.group AS `group`
        FROM users_priv AS up
        WHERE up.id = :uid LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":uid" => $uid);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
        
		if (count($data) > 0) {
        	return $data[0]["group"];
		}
    }
    
    /**
     * Получить ID пользователя по логину
     * 
    * @param string $login
    * @return int
    */
    public function getUserId($login) {
		$sql = "SELECT id 
        FROM users
        WHERE login = :login
        LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":login" => $login);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
        
		if ( (isset($data[0]["id"])) and (is_numeric($data[0]["id"])) ) {
        	return $data[0]["id"];
		}
    }
    
    /**
     * Получить количество черновиков задач
     * 
     * @param int $uid
     * @return int
     */
    public function getDraftNumTasks($uid) {
		$sql = "SELECT count(id) AS count
        FROM draft
        WHERE who = :uid";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":uid" => $uid);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		
		return $data[0]["count"];
    }
    
    /**
     * Получить информацию о пользователях в группе
     * 
     * @param int $gid
     * @return array
     */
    public function getUserInfoFromGroup($gid) {
        $data = array();

        $this->memcached->set("gid:" . $gid);
        
        if (!$this->memcached->load()) {
	    	$sql = "SELECT users.id AS uid
	    	FROM users
	    	LEFT JOIN users_priv AS p ON (users.id = p.id)
	    	LEFT JOIN users_subgroup AS g ON (p.group = g.id)
	    	WHERE g.id = :gid";
	    	
	    	$res = $this->registry['db']->prepare($sql);
	    	$param = array(":gid" => $gid);
	    	$res->execute($param);
	    	$row = $res->fetchAll(PDO::FETCH_ASSOC);
	    	
	    	$this->memcached->save($row);
        } else {
        	$row = $this->memcached->get();
        }

    	foreach($row as $part) {
    		$data[] = $this->getUserInfo($part["uid"]);
    	}

    	return $data;
    }
    
    /**
	 * Добавить пользователя
	 * 
	 * @param string $login
	 * @param string $pass
	 * @param int $quota
	 * @param string $name
	 * @param string $soname
	 * @param string $signature
	 * @param string $email
	 * @param boolean $notify
	 * @param string (time) $time_notify
	 * @param boolean $email_for_task = FALSE
	 * 
	 * @return int $uid
	 */
    public function addUser($login, $pass, $quota, $name, $soname, $signature, $email, $notify, $time_notify, $email_for_task = 0) {
        if (!isset($time_notify)) {
            $time_notify = "08:00:00";
        }
        
        $sql = "INSERT INTO users (login, pass, `quota`, `name`, `soname`, signature, email, notify, time_notify, email_for_task) VALUES (:login, :pass, :quota, :name, :soname, :signature, :email, :notify, :time_notify, :eft)";
        $res = $this->registry['db']->prepare($sql);
		$param = array(":login" => $login, ":pass" => md5(md5($pass)), ":quota" => $quota, ":name" => $name, ":soname" => $soname, ":signature" => $signature, ":email" => $email, ":notify" => $notify, ":time_notify" => $time_notify, ":eft" => $email_for_task);
		$res->execute($param);

		$uid = $this->registry['db']->lastInsertId();
        
        return $uid;
    }
    
    /**
     * Назначить права пользователя (админ, обычный)
     * Указать пользователя группу
     * 
     * @param int $uid
     * @param string $priv - "admin" or NULL
     * @param int $gname - GID
     */
    public function addUserPriv($uid, $priv, $gname) {
        if ($priv == "admin") {
            $admin = 1;
        } else {
            $admin = 0;
        }
        
        $sql = "INSERT INTO users_priv (id, admin, `group`) VALUES (:id, :admin, :group)";
        $res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $uid, ":admin" => $admin, ":group" => $gname);
		$res->execute($param);

		$this->memcached->set("gid:" . $gid);
		$this->memcached->delete();
    }
    
    /**
     * Правка данных пользователя
    *
    * @param int $uid
    * @param string $login
    * @param int $quota
    * @param string $name
    * @param string $soname
    * @param string $signature
    * @param string $email
    * @param boolean $notify
    * @param string (time) $time_notify
    * @param boolean $email_for_task = FALSE
    */
    public function editUser($uid, $login, $quota, $name, $soname, $signature, $email, $notify, $time_notify, $email_for_task = 0) {
    	$this->memcached->set("ui:" . $uid);
    	$this->memcached->delete();
    	
    	$this->memcached->set("login" . $uid);
    	$this->memcached->delete();
    	
        if (!isset($time_notify)) {
            $time_notify = "08:00:00";
        }

        $sql = "UPDATE users SET `login` = :login, `quota` = :quota, `name` = :name, `soname` = :soname, signature = :signature, email = :email, notify = :notify, time_notify = :time_notify, email_for_task = :eft WHERE id = :id LIMIT 1";
        $res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $uid, ":login" => $login, ":name" => $name, ":quota" => $quota, ":soname" => $soname, ":signature" => $signature, ":email" => $email, ":notify" => $notify, ":time_notify" => $time_notify, ":eft" => $email_for_task);
		$res->execute($param);
    }
    
    /**
     * Изменение пароля
    *
    * @param int $uid
    * @param string $pass
    */
    public function editUserPass($uid, $pass) {
    	$this->memcached->set("ui:" . $uid);
    	$this->memcached->delete();
    	
    	$this->memcached->set("login" . $uid);
    	$this->memcached->delete();
    	
        $sql = "UPDATE users SET pass = :pass WHERE id = :id LIMIT 1";
        $res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $uid, ":pass" => md5(md5($pass)));
		$res->execute($param);
    }
    
    /**
     * Смена привелегий пользователя (админ, обычный) и группы
     * @param int $uid
     * @param string $priv - "admin" or NULL
     * @param int $gname - GID
     */
    public function editUserPriv($uid, $priv, $gname) {
    	$this->memcached->set("ui:" . $uid);
    	$this->memcached->delete();
    	
    	$this->memcached->set("login" . $uid);
    	$this->memcached->delete();

    	$this->memcached->set("gid:" . $gid);
    	$this->memcached->delete();
    	
        if ($priv == "admin") {
            $admin = 1;
        } else {
            $admin = 0;
        }

        $sql = "UPDATE users_priv SET id = :id, admin = :admin, `group` = :group WHERE id = :id LIMIT 1";
        $res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $uid, ":admin" => $admin, ":group" => $gname);
		$res->execute($param);
    }
    
    /**
     * Получить список всех пользователей с информацией
     * 
     * @return array
     */
    public function getUsersList() {
		$sql = "SELECT users.id AS id, users.login AS login, users.name AS `name`, users.soname AS `soname`, users.email AS email, users.notify, users.time_notify, users.last_notify, p.admin AS admin, p.group AS gid, g.name AS gname
        FROM users 
        LEFT JOIN users_priv AS p ON (users.id = p.id)
        LEFT JOIN users_group AS g ON (p.group = g.id)
        ORDER BY users.id";
		
		$res = $this->registry['db']->prepare($sql);
		$res->execute();
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
        
        return $data;
    }
    
    /**
     * Получить список всех пользователей и их групп
    *
    * @return array
    */
    public function getUsersGroups() {
    	$data = array();
    
    	$sql = "SELECT u.id, ug.id AS gid, ug.name AS gname, g.name AS pgname
    	FROM users AS u
    	LEFT JOIN users_priv AS up ON (up.id = u.id)
    	LEFT JOIN users_subgroup AS ug ON (ug.id = up.group)
    	LEFT JOIN users_group AS g ON (g.id = ug.pid)
    	GROUP BY up.id
    	ORDER BY u.soname";
    
    	$res = $this->registry['db']->prepare($sql);
    	$res->execute();
    	$data = $res->fetchAll(PDO::FETCH_ASSOC);
    
    	return $data;
    }
    
    /**
     * Проверка существаования логина
     * 
     * @param string $login
     * @return TRUE
     * @return FALSE
     */
	public function issetLogin($login) {
		$sql = "SELECT COUNT(id) AS count FROM users WHERE login = :login";

		$res = $this->registry['db']->prepare($sql);
		$param = array(":login" => $login);
		$res->execute($param);
		$row = $res->fetchAll(PDO::FETCH_ASSOC);

		if (count($row) > 0) $count = $row[0]["count"];

		if ($count > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	/**
     * Проверка существаования логина из другой системы
     * (Например, общая задача используется в нескольких системах)
     * 
     * @param string $email
     * @return TRUE
     * @return FALSE
     */
	public function issetRemoteContact($email) {
		$sql = "SELECT id FROM troubles_remote_contact WHERE `email` = :email LIMIT 1";

		$res = $this->registry['db']->prepare($sql);
		$param = array(":email" => $email);
		$res->execute($param);
		$row = $res->fetchAll(PDO::FETCH_ASSOC);

		if (count($row) == 1) {
			$this->tid = $row[0]["id"];
			
			return true;
		} else {
			return false;
		}
	}
    
	/**
	 * Удалить пользователя
	 * 
	 * @param int $uid
	 */
    public function delUser($uid) {
    	$this->memcached->set("gid:" . $gid);
    	$this->memcached->delete();
    	
    	$this->memcached->set("ui:" . $uid);
    	$this->memcached->delete();
    	
		$sql = "DELETE FROM users WHERE id = :uid LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":uid" => $uid);
		$res->execute($param);
        
		$sql = "DELETE FROM users_priv WHERE id = :uid LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":uid" => $uid);
		$res->execute($param);
		
		$sql = "DELETE FROM users_sets WHERE `uid` = :uid LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":uid" => $uid);
		$res->execute($param);
    }
    
    /**
     * Получить список групп (2 уровень)
     * 
     * @return array
     */
    public function getGroups() {
		$sql = "SELECT ug.id AS pid, ug.name AS pname, usg.id AS sid, usg.name AS sname
        FROM users_group AS ug
        LEFT JOIN users_subgroup AS usg ON (usg.pid = ug.id)
        ORDER BY ug.id";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array();
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

        return $data;
    }
    
    /**
     * Получить список групп (1 уровень)
    *
    * @return array
    */
    public function getUniqGroups() {
		$sql = "SELECT ug.id AS pid, ug.name AS pname
        FROM users_group AS ug
        ORDER BY ug.id";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array();
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);

        return $data;
    }

    /**
     * Получить имя группы (1 уровень)
    *
    * @param int $gid - group ID
    * @return string
    */
    public function getGroupName($gid) {
		$sql = "SELECT `name` 
        FROM users_group
        WHERE id = :gid
        LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":gid" => $gid);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
        
		if (count($data) > 0) {
        	return $data[0]["name"];
		}
    }
    
    /**
     * Получить имя группы (2 уровень)
    *
    * @param int $sid - subgroup ID
    * @return string
    */
    public function getSubgroupName($sid) {
		$sql = "SELECT `name` 
        FROM users_subgroup
        WHERE id = :sid
        LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":sid" => $sid);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
        
        return $data[0]["name"];
    }
    
    /**
     * Получить имя группы (2 уровень)
    *
    * @param int $sid - subgroup ID
    * @return array
    */
    public function getCatName($id) {
    	$data = array();
    
    	$sql = "SELECT id, name
    	FROM users_subgroup
    	WHERE id = :id
    	LIMIT 1";
    
    	$res = $this->registry['db']->prepare($sql);
    	$res->execute(array(":id" => $id));
    	$data = $res->fetchAll(PDO::FETCH_ASSOC);
    
    	return $data[0];
    }
    
    /**
     * Получить список групп (2 уровень) по ID группы (1 уровень)
    *
    * @param int $pid - (parent) group ID
    * @return array
    */
    public function getSubgroups($pid) {
		$sql = "SELECT id, `name` 
        FROM users_subgroup
        WHERE pid = :pid
        ORDER BY id";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":pid" => $pid);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
        
        return $data;
    }
    
    /**
     * Создать группу (2 уровень)
     * 
     * @param int $pid - (parent) group ID
     * @param string $name - имя группы
     */    
    public function addSubgroup($pid, $name) {
    	$sql = "INSERT INTO users_subgroup (pid, name) VALUES (:pid, :name)";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":pid" => $pid, ":name" => $name);
		$res->execute($param);
    }
    
    /**
     * Удалить группу (2 уровень)
     * 
     * @param int $id
     */
	public function delSubgroup($id) {
		$sql = "DELETE FROM users_subgroup WHERE id = :id LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $id);
		$res->execute($param);
    }
    
    /**
     * Переименовать группу (2 уровень)
     * 
     * @param int $id - group ID
     * @param string $name - новое имя группы
     */
	public function editCat($id, $name) {
		$sql = "UPDATE users_subgroup SET name = :name WHERE id = :id LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $id, ":name" => $name);
		$res->execute($param);
    }
    
    /**
     * Получить ID группы (1 уровень) по названию
     * 
     * @param string $gname
     * @return int
     */
    public function getGroupId($gname) {
		$sql = "SELECT id 
        FROM users_group
        WHERE `name` = :gname
        LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":gname" => $gname);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
        
        return $data[0]["id"];
    }
    
    /**
     * Получить ID группы (2 уровень) по названию
    *
    * @param string $subgname
    * @return int
    */
    public function getSubgroupId($subgname) {
    	$sql = "SELECT id
            FROM users_subgroup
            WHERE `name` = :gname
            LIMIT 1";
    
    	$res = $this->registry['db']->prepare($sql);
    	$param = array(":gname" => $subgname);
    	$res->execute($param);
    	$data = $res->fetchAll(PDO::FETCH_ASSOC);
    
    	if (count($data) > 0) {
	    	return $data[0]["id"];
    	}
    }
    
    /**
     * Создать группу (1 уровень)
     * 
     * @param string $gname
     * @return TRUE
     * @return FALSE - если такое имя группы уже существует
     */
    public function addGroups($gname) {
        if ($gname == "") {
            return FALSE;
        }
        
		$sql = "SELECT id
        FROM users_group
        WHERE `name` = :name
        LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":name" => htmlspecialchars($gname));
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
        
        $flag = FALSE;
        
        if (!isset($data[0]["id"])) {
            $flag = TRUE;
        }
        
        if ($flag) {
    		$sql = "INSERT INTO users_group (`name`) VALUES (:name)";
    		
    		$res = $this->registry['db']->prepare($sql);
    		$param = array(":name" => htmlspecialchars($gname));
    		$res->execute($param);
            
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
     * Изменить имя группы (1 уровень)
     * 
    * @param int $gid
    * @param string $gname
    * @return TRUE
    * @return FALSE - если такое имя группы уже существует
    */
    public function editGroupName($gid, $gname) {
        if ($gname == "") {
            return FALSE;
        }
        
		$sql = "SELECT id
        FROM users_group
        WHERE `name` = :name
        LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":name" => htmlspecialchars($gname));
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
        
        $flag = FALSE;
        
        if (!isset($data[0]["id"])) {
            $flag = TRUE;
        } elseif ($gid == $data[0]["id"]) {
            $flag = TRUE;
        }
        
        if ($flag) {
    		$sql = "UPDATE users_group SET `name` = :gname WHERE id = :gid LIMIT 1";
    		
    		$res = $this->registry['db']->prepare($sql);
    		$param = array(":gid" => $gid, ":gname" => htmlspecialchars($gname));
    		$res->execute($param);
            
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
     * Удалить группу (1 уровень)
     * 
     * @param int $gid
     */
    public function delGroup($gid) {
		$sql = "DELETE FROM users_group WHERE id = :gid LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":gid" => $gid);
		$res->execute($param);
    }
    
    /**
     * Подписать текущего пользователя на изменения в задаче
     * 
     * @param int $tid
     */
    public function spam($tid) {
        $sql = "SELECT COUNT(id) AS count FROM troubles_spam WHERE uid = :uid AND tid = :tid LIMIT 1";
        
		$res = $this->registry['db']->prepare($sql);
		$param = array(":uid" => $this->registry["ui"]["id"], ":tid" => $tid);
		$res->execute($param);
		$row = $res->fetchAll(PDO::FETCH_ASSOC);
        
        if ($row[0]["count"] == 0) {
            $sql = "INSERT INTO troubles_spam (tid, uid) VALUES (:tid, :uid)";
            
    		$res = $this->registry['db']->prepare($sql);
    		$param = array(":uid" => $this->registry["ui"]["id"], ":tid" => $tid);
    		$res->execute($param);
        } else {
            $sql = "DELETE FROM troubles_spam WHERE uid = :uid AND tid = :tid LIMIT 1";
            
    		$res = $this->registry['db']->prepare($sql);
    		$param = array(":uid" => $this->registry["ui"]["id"], ":tid" => $tid);
    		$res->execute($param);
        }
    }
    
    /**
     * Изменить время уведомления о задачах на день
     * 
     * @param int $uid
     */
    public function setNotifyTime($uid) {
        $sql = "UPDATE users SET last_notify = NOW() WHERE id = :uid LIMIT 1";
        
		$res = $this->registry['db']->prepare($sql);
		$param = array(":uid" => $uid);
		$res->execute($param);
    }
    
    /**
     * Получить список пользователей и групп СПЕЦИАЛЬНО для формы создания или правки задачи
     * 
     * @return array
     */
	function getUsers() {
		$k=0;
		$gdata = $this->getGroups();
		$udata = $this->getUsersList();
		for($i=0; $i<count($gdata); $i++) {
			$data[$k]["id"] = $gdata[$i]["sid"];
			$data[$k]["type"] = "g";
			$data[$k]["desc"] = $gdata[$i]["sname"];
	
			foreach($udata as $part) {
				if ($part["gid"] == $gdata[$i]["sid"]) {
	
					$k++;
	
					$data[$k]["id"] = $part["id"];
					$data[$k]["type"] = "u";
					$data[$k]["desc"] = $part["name"] . " " . $part["soname"];
				}
			}
	
			$k++;
		}
	
		$data[$k]["type"] = "all";
		$data[$k]["id"] = 0;
		$data[$k]["desc"] = "все";
		
		return $data; 
	}
	
	/**
	 * Сформировать неповторяющийся список пользователей (ID) из POST запроса
	 * 
	 * @param string $post - POST запрос
	 * @return array
	 */
	function getUniqUsers($post) {
		$uniq = array();
		
		$users = array();
		if ($post["rall"] == "1") {
			$group_users = $this->getUsersList();
			foreach($group_users as $user) {
				$users[] = $user["id"];
			}
		} else {
			foreach($post["gruser"] as $part) {
				$group_users = $this->getUserInfoFromGroup($part);
				foreach($group_users as $user) {
					$users[] = $user["uid"];
				}
			}
			
			foreach($post["ruser"] as $part) {
				$users[] = $part;
			}
		}

		for($i=0; $i<count($users); $i++) {
			$flag = true; 
			foreach($uniq as $part) {
				if ($part == $users[$i]) {
					$flag = false;
				}
			}
			
			if ($flag) {
				$uniq[] = $users[$i];
			}
		}
		
		return $uniq;
	}
	
	/**
	 * Вывести полный список пользователей
	 * Информация о каждом пользователе будет оформлен по шаблону: .../users/shortinfo.tpl
	 * 
	 * @return array:
	 *    $row["onlineUsers"] - кол-во online пользователей
	 *    $row["allUsers"] - всего пользователей
	 *    $row["listUsers"] - массив пользователей (render)
	 */
	public function getUserList() {
		 
		$groups = $this->getUsersGroups();
		 
		$new_groups = array();
		if (count($groups) > 0) {
			$new_groups[0] = $groups[0];
		}
		 
		for($i=1; $i<count($groups); $i++) {
			$flag = true;
			 
			foreach($new_groups as $part) {
				if ($part["gname"] == $groups[$i]["gname"]) {
					$flag = false;
				}
			}
			 
			if ($flag) {
				$new_groups[] = $groups[$i];
			}
		}
		 
		$result = null;
		$active_num = 0;
		$all_num = 0;
		 
		foreach($new_groups as $group) {
			$users = $this->getUserInfoFromGroup($group["gid"]);
			 
			foreach($users as $user) {
				$status_bool = $this->getStatus($user["uid"]);
				$uinfo = $this->getUserInfo($user["uid"]);

				$result[$group["pgname"]][$group["gname"]][] = $this->render("users_shortinfo", array("user" => $uinfo, "status" => $status_bool));
				if ($status_bool) {
					$active_num++;
				}
				 
				$all_num++;
			}
		}

		$row["onlineUsers"] = $active_num;
		$row["allUsers"] = $all_num;
		$row["listUsers"] = $result;
		 
		return $row;
	}
	
	/**
	 * Получить занимаемое дисковое пространство текущим пользователем
	 * 
	 * @return int
	 */
	function getNowSize() {
		$sql = "SELECT SUM(f.size) AS sum
			FROM fm_fs AS f
			LEFT JOIN fm_fs_history AS h ON (h.fid = f.id)
			WHERE h.uid = :id";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $this->registry["ui"]["id"]);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
	
		return $data[0]["sum"];
	}
	
	/**
	 * Получить квоту текущего пользователя
	 * 
	 * @return int
	 */
	function getUserQuota() {
		$sql = "SELECT quota FROM users WHERE id = :id LIMIT 1";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $this->registry["ui"]["id"]);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
	
		return $data[0]["quota"];
	}
	
	/**
	 * Получить список пользователей с их квотами и занимаемым ими дисковым пространством
	 * 
	 * @return array
	 */
	function getTotal() {
		$sql = "SELECT SUM(size) AS sum FROM fm_fs";
	
		$res = $this->registry['db']->prepare($sql);
		$res->execute();
		$row = $res->fetchAll(PDO::FETCH_ASSOC);
	
		$data["all"] = $row[0]["sum"];
	
		$sql = "SELECT SUM(f.size) AS sum, u.login AS `login`, u.quota AS `quota`
			FROM fm_fs AS f
			LEFT JOIN fm_fs_history AS h ON (h.fid = f.id)
			LEFT JOIN users AS u ON (u.id = h.uid)
			GROUP BY h.uid
			ORDER BY sum DESC";
	
		$res = $this->registry['db']->prepare($sql);
		$res->execute();
		$row = $res->fetchAll(PDO::FETCH_ASSOC);
	
		$data["users"] = $row;
	
		return $data;
	}
}