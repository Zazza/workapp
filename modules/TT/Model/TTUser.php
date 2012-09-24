<?php 
class Model_TTUser extends Modules_Model {
    public function getRemoteUserInfo($uid) {
    	$data = array();
    
    	$this->memcached->set("rui" . $uid);
    
    	if (!$this->memcached->load()) {
    
    		$sql = "SELECT users.id AS id, users.email AS `email`, users.name AS `name`, users.soname AS `soname`, users.avatar, users.group AS gname
    	        FROM troubles_remote_contact AS users
    	        WHERE users.id = :uid LIMIT 1";
    			
    		$res = $this->registry['db']->prepare($sql);
    		$param = array(":uid" => $uid);
    		$res->execute($param);
    		$data = $res->fetchAll(PDO::FETCH_ASSOC);
    
    		if (count($data) == 1) {
    			$dbava = $data[0]["avatar"];
    
    			if ($data[0]["avatar"] != "") {
    				$data[0]["avatar"] = $this->registry["siteName"] . $this->registry["uri"] . $this->registry["path"]["attaches"] . $dbava;
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
    
    	return $data;
    }
}
?>