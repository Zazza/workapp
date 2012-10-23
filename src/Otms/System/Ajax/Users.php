<?php

/**
 * This file is part of the Workapp project.
*
* (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
*
*/

namespace Otms\System\Ajax;

use Engine\Ajax;
use Otms\System\Model\Ui;

/**
 * Users Ajax class
 * 
 * Для работы с пользовательскими данными
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */

class Users extends Ajax {
	/**
	 * Удалить группу (1 уровень)
	 * @param array $params:
	 *    $params["gid"]
	 */
    public function delGroup($params) {
        $gid = $params["gid"];
        
        $this->registry["user"]->delGroup($gid);
    }
    
    /**
     * Удалить пользователя
     * @param array $params:
	 *    $params["uid"]
     */
    public function delUser($params) {
        $uid = $params["uid"];
        
        $this->registry["user"]->delUser($uid);
    }
    
    /**
     * Сформировать форму, с выбранными пользователем, группой или всеми и скрытыми полями.
     * Необходимо для выбора ответственных в задаче
     * 
     * @param array $params:
	 *    $params["id"] - ID пользователя или группы
	 *    $params["type"] - тип u, g, all - user, group или all соответсвенно 
     */
    public function getUser($params) {
        $id = $params["id"];
        $type = $params["type"];
        
        $content = NULL;
        if ($type == "u") {
            
            $data = $this->registry["user"]->getUserInfo($id);
            $content .= "<p><span id='udesc[" . $data["uid"] . "]' style='font-size: 11px; margin-right: 10px'>" . $data["name"] . " " . $data["soname"] . "</span>";
            $content .= '<input id="uhid[' . $data["uid"] . ']" type="hidden" name="ruser[]" value="' . $data["uid"] . '" /></p>';
            
        } elseif ($type == "g") {

            $gname = $this->registry["user"]->getGroupName($id);
            $content .= '<p style="font-size: 11px; margin-right: 10px">' . $gname . '<input type="hidden" name="gruser[]" value="' . $id . '" /></p>';
        } elseif ($type == "all") {

            $content .= '<p style="font-size: 11px; margin-right: 10px">Все<input type="hidden" name="rall" value="1" /></p>';
        }
        
        return $content;
    }
    
    /**
     * Подписать текущего пользователя на задачу
     * 
     * @param array $params:
     *    $params["tid"] - ID задачи
     */
    public function spam($params) {
        $tid = $params["tid"];
        
        $this->registry["user"]->spam($tid);
    }

    /**
     * Вывести инофрмацию о пользователе.
     * Шаблон: userInfo.tpl
     * 
     * @param array $params
     *    $params["uid"]
     */
    public function getUI($params) {
    	$uid = $params["uid"];
    	
    	$data = $this->registry["user"]->getUserInfo($uid);
    	
    	return $this->view->render("userInfo", array("post" => $data));
    }

    /**
     * Вывести дерево группы по его ID
     * @param array $params
     *    $params["pid"] - (parent) group ID
     */
    public function getTree($params) {
        $pid = $params["pid"];

        $tree = $this->registry["user"]->getSubgroups($pid);
        
        return $this->view->render("users_structure", array("tree" => $tree));
    }
    
    /**
     * Создать группу (2 уровень)
     * 
     * @param array $params
     *    $params["pid"] - (parent) group ID
     */
	public function addTree($params) {
        $pid = $params["pid"];
        $name = htmlspecialchars($params["name"]);

        $this->registry["user"]->addSubgroup($pid, $name);
    }
    
    /**
     * Переименовать группу (2 уровень)
     * 
     * @param array $params
     *    $params["id"]
     */
	public function delCat($params) {
        $id = $params["id"];

        $this->registry["user"]->delSubgroup($id);
    }
    
    /**
     * 
     * @param array $params
     */
	public function editCat($params) {
        $id = $params["id"];
        $name = htmlspecialchars($params["name"]);
        
        $this->registry["user"]->editCat($id, $name);
    }
    
    /**
     * Получить имя группы (2 уровень)
     * 
     * @param array $params
     *    $params["id"]
     */
    public function getCatName($params) {
        $id = $params["id"];
        
        $cat = $this->registry["user"]->getCatName($id);
         
        return $cat["name"];
    }
    
    /**
     * 
     */
    public function getUserList() {
    	$json = $this->registry["user"]->getUserList();
    	$json["listUsers"] = $this->view->render("users_bplist", array("listUsers" => $json["listUsers"]));
    	
    	return json_encode($json);
    }
    
    /**
     * Обновить set (настройка пользователя) по ключу
     * @param array $param
     *    $param["key"] - ключ
     *    $param["type"] - тип данных
     *    $param["val"] - значение
     *    $param["bool"] - flag, TRUE или FALSE
     *    
     * Пример: key: bu [{"gr":{"OTMS":"1"}}, где gr - тип данных, OTMS - $param["val"], 1 - $param["bool"]]
     */
    public function setSet($param) {
		$key = $param["key"];
		$type = $param["type"];
				
		$ui = new Ui();
		$res = $this->registry["users_sets"];
		$check = $res;
		
		$res[$type][$param["val"]] = $param["bool"];
		$res = json_encode($res);
		
		if (is_array($check)) {
			$ui->setSet($key, $res);
		} else {
			$ui->addSet($key, $res);
		}
	}
	
	/**
	 * Отправка сообщения
	 * 
	 * @param array $param
	 *    $param["msg"]
	 *    $param["uid"]
	 */
	public function sendMsg($param) {
		$msg = $param["msg"];
		$uid = $param["uid"];
		
		$string = $this->view->render("logs_msg", array("msg" => $msg));
		$this->registry["logs"]->uid = $uid;
		$this->registry["logs"]->set("service", $string, "");
	}
}