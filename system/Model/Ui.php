<?php
class Model_Ui extends Engine_Model {
	public $noavatar = "img/noavatar.gif";
	
	public function login($login, $pass) {
		$sql = "SELECT * FROM users WHERE login = :login AND pass != '' LIMIT 1";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":login" => $login);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($data) == 1) {
            if ($data[0]["pass"] === md5(md5($pass))) {

                 $loginSession = & $_SESSION["login"];
                 $loginSession["id"] = $data[0]["id"];

                 $this->startSess($loginSession["id"]);
            
                 return TRUE;
            } else {
                 return FALSE;
            }
		} else {
			return FALSE;
		}
	}
	
	public function getInfo($loginSession) {
		$data = array();
		
		$this->memcached->set("login" . $loginSession["id"]);
		
		if (!$this->memcached->load()) {

			$sql = "SELECT users.id AS id, users.login AS `login`, users.pass AS pass, users.name AS `name`, users.soname AS `soname`, users.signature AS signature, users.icq, users.skype, users.adres, users.phone, users.avatar, users.email AS email, users.notify, users.time_notify, users.email_for_task, p.admin AS admin, g.id AS gid, p.group, g.name AS gname
	                FROM users 
	                LEFT JOIN users_priv AS p ON (users.id = p.id)
	                LEFT JOIN users_subgroup AS g ON (p.group = g.id)
	                WHERE users.id = :uid LIMIT 1";
			 
			$res = $this->registry['db']->prepare($sql);
			$param = array(":uid" => $loginSession["id"]);
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
	
		if (count($data) > 0) {
			$this->registry->set("auth", TRUE);
			$this->registry->set("ui", $data);
		} else {
			$this->registry->set("auth", FALSE);
			session_destroy();
			
			$this->stopSess($loginSession["id"]);
		}
	}
	
	public function editUser($uid, $login, $name, $soname, $signature, $email, $notify, $time_notify, $email_for_task = 0) {
		$this->memcached->set("login" . $uid);
		$this->memcached->delete();
		$this->memcached->set("ui:" . $uid);
		$this->memcached->delete();
		 
		if (!isset($time_notify)) {
			$time_notify = "08:00:00";
		}
	
		$sql = "UPDATE users SET `login` = :login, `name` = :name, `soname` = :soname, signature = :signature, email = :email, notify = :notify, time_notify = :time_notify, email_for_task = :eft WHERE id = :id LIMIT 1";
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $uid, ":login" => $login, ":name" => $name, ":soname" => $soname, ":signature" => $signature, ":email" => $email, ":notify" => $notify, ":time_notify" => $time_notify, ":eft" => $email_for_task);
		$res->execute($param);
	}
	
	public function editAdvUser($icq, $skype, $adres, $phone) {
		$this->memcached->set("login" . $this->registry["ui"]["id"]);
		$this->memcached->delete();
		$this->memcached->set("ui:" . $this->registry["ui"]["id"]);
		$this->memcached->delete();
		 
		$sql = "UPDATE users SET icq = :icq, skype = :skype, adres = :adres, phone = :phone WHERE id = :uid LIMIT 1";
		$res = $this->registry['db']->prepare($sql);
		$param = array(":uid" => $this->registry["ui"]["id"], ":icq" => $icq, ":skype" => $skype, ":adres" => $adres, ":phone" => $phone);
		$res->execute($param);
	}
	
	public function editUserPass($uid, $pass) {
		$this->memcached->set("login" . $uid);
		$this->memcached->delete();
		$this->memcached->set("ui:" . $uid);
		$this->memcached->delete();

		$sql = "UPDATE users SET pass = :pass WHERE id = :id LIMIT 1";
		$res = $this->registry['db']->prepare($sql);
		$param = array(":id" => $uid, ":pass" => md5(md5($pass)));
		$res->execute($param);
	}
	
	public function saveAvatar($file) {
		$this->memcached->set("login" . $this->registry["ui"]["id"]);
		$this->memcached->delete();
		
		$this->memcached->set("ui:" . $this->registry["ui"]["id"]);
		$this->memcached->delete();
		 
		$err = null;
		$avatar = "ava" . $this->registry["ui"]["id"] . "_" . $file["name"];
	
		if ($file["name"] == "") {
			$err .= "<li style='margin: 5px; list-style: disc inside none'>Необходимо выбрать файл для загрузки</li>";
		}
	
		if (mb_strlen($file["name"]) > 16) {
			$err .= "<li style='margin: 5px; list-style: disc inside none'>Имя файла должно быть короче 16 символов</li>";
		};
	
		if($file["size"] > 1024*100) {
			$err .= "<li style='margin: 5px; list-style: disc inside none'>Размер файла не должен превышать 100 килобайт</li>";
		}
	
		if($file['type'] != "image/gif" and $file['type'] != "image/jpeg" and $file['type'] != "image/x-png" and $file['type'] != "image/png") {
			$err .= "<li style='margin: 5px; list-style: disc inside none'>Изображение должно быть в формате GIF, PNG или JPEG</li>";
		}
	
		if (count($err) == 0) {
	
			// Удалим остальные аватары
			$list = glob($this->registry["rootPublic"] . $this->registry["path"]["avadir"] . "ava" . $this->registry["ui"]["id"] . "_*");
			if ($list) {
				foreach ($list as $filename) {
					@unlink($filename);
				}
			}

	
			if(copy($file["tmp_name"], $this->registry["rootPublic"] . $this->registry["path"]["avadir"] . $avatar)) {
				$sql = "UPDATE users SET avatar = :ava WHERE id = :uid";
	
				$res = $this->registry['db']->prepare($sql);
				$param = array(":ava" => $avatar, ":uid" => $this->registry["ui"]["id"]);
				$res->execute($param);
			}
		}
	
		return $err;
	}
	
	public function delAva() {
		$this->memcached->set("login" . $this->registry["ui"]["id"]);
		$this->memcached->delete();
		
		$this->memcached->set("ui:" . $this->registry["ui"]["id"]);
		$this->memcached->delete();
		 
		$sql = "UPDATE users SET avatar = '' WHERE id = :uid LIMIT 1";
	
		$res = $this->registry['db']->prepare($sql);
		$param = array(":uid" => $this->registry["ui"]["id"]);
		$res->execute($param);
	
		// Удалим остальные аватары
		$list = glob($this->registry["rootPublic"] . $this->registry["path"]["avadir"] . "ava" . $this->registry["ui"]["id"] . "_*");
		if ($list) {
			foreach ($list as $filename) {
				@unlink($filename);
			}
		}
	}
	
	public function startSess($uid) {
		$sql = "INSERT INTO users_auth (auth, uid) VALUES (:auth, :uid)";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":auth" => "1", ":uid" => $uid);
		$res->execute($param);
	}
	
	public function stopSess($uid) {
		$sql = "INSERT INTO users_auth (auth, uid) VALUES (:auth, :uid)";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":auth" => "0", ":uid" => $uid);
		$res->execute($param);
		
		$this->memcached->set("status" . $uid);
		$this->memcached->delete();
		
		$this->memcached->set("cmd_ui:" . $this->registry["ui"]["id"]);
		if ($this->memcached->load()) {
			$this->memcached->delete();
		}
	}
	
	public function getSess($uid, $date) {
		$result = array();
		
		$start = strtotime($date);
		$start = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m", $start), date("d", $start), date("Y", $start)));
		$stop = strtotime($date);
		$stop = date("Y-m-d H:i:s", mktime("23", "55", "55", date("m", $stop), date("d", $stop), date("Y", $stop)));
		
		$sql = "SELECT auth, timestamp FROM users_auth WHERE `timestamp` > :start AND `timestamp` < :stop AND uid = :uid ORDER BY id";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":uid" => $uid, ":start" => $start, ":stop" => $stop);
		$res->execute($param);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($data) > 0) {
			if ($data[0]["auth"] == "0") {
				$result[] = "--- - " . $data[0]["timestamp"];
				
				for($i=1; $i<count($data); $i=$i+2) {
					if (isset($data[$i+1]["timestamp"])) {
						$result[] = $data[$i]["timestamp"] . " - " . $data[$i+1]["timestamp"];
					} else {
						$result[] = $data[$i]["timestamp"] . " - ---";
					}
				}
			} else {
				for($i=0; $i<count($data); $i=$i+2) {
					if (isset($data[$i+1]["timestamp"])) {
						$result[] = $data[$i]["timestamp"] . " - " . $data[$i+1]["timestamp"];
					} else {
						$result[] = $data[$i]["timestamp"] . " - ---";
					}
				}
			}
			
			return $result;
		} else {
			return array(0 => "---");
		}
	}
	
	public function getSet($key) {
		$this->memcached->set("stat_" . $key . $this->registry["ui"]["id"]);
		
		if (!$this->memcached->load()) {
			$sql = "SELECT val FROM users_sets WHERE `key` = :key AND uid = :uid LIMIT 1";
		
			$res = $this->registry['db']->prepare($sql);
			$param = array(":uid" => $this->registry["ui"]["id"], ":key" => $key);
			$res->execute($param);
			$val = $res->fetchAll(PDO::FETCH_ASSOC);

			if (count($val) == 0) {
				$this->memcached->save(null);
				return null;
			} else {
				$this->memcached->save($val);
			}
		} else {
			$val = $this->memcached->get();
		}

		return json_decode($val[0]["val"], true);
	}
	
	public function setSet($key, $val) {
		$sql = "UPDATE users_sets SET val = :val WHERE `key` = :key AND uid = :uid";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":uid" => $this->registry["ui"]["id"], ":key" => $key, ":val" => $val);
		$res->execute($param);
		
		$this->memcached->set("stat_" . $key . $this->registry["ui"]["id"]);
		$this->memcached->delete();
	}
	
	public function addSet($key, $val) {
		$sql = "INSERT INTO users_sets (uid, `key`, val) VALUES (:uid, :key, :val)";
		
		$res = $this->registry['db']->prepare($sql);
		$param = array(":uid" => $this->registry["ui"]["id"], ":key" => $key, ":val" => $val);
		$res->execute($param);
		
		$this->memcached->set("stat_" . $key . $this->registry["ui"]["id"]);
		$this->memcached->delete();
	}
}
?>
