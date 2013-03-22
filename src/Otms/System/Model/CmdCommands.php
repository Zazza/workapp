<?php

/**
 * This file is part of the Workapp project.
*
* (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
*
*/

namespace Otms\System\Model;

use Engine\Model;
use Otms\System\Model\Dashboard;
use Otms\System\Model\Ui;
use ReflectionClass;

/**
 * CmdCommands Model class
 * 
 * Класс реализует команды в командной строке. Команда - private функция, например _exit()
 * Аргументы команда получает из массива $this->args[]
 * Команда должна возвращать TRUE в случае успеха, а результат (если есть) содержится в переменной $this->result
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */

class CmdCommands extends Model {
	/**
	 * переменная для доступа к словам в запросе введённом из командной строки
	* @var array
	*/
	protected $args;
	
	/**
	 * private переменная для хранения результата
	* @var string
	*/
	private $result = null;
	
	/**
	 * private переменная для хранения истории
	* @var array
	*/
	private $_history = array();
	
	/**
	 * Получает строку введенную пользователем и пытается выполнить команду
	 * $this->args - строка разбитая на слова: explode(" ", $string)
	 * 
	 * При доступном модуле memcached:
	 * В массиве $this->_history[] хранится:
	 *    $string - строка
	 *    date("H:i:s") - время команды
	 *    $this->result - результат
	 *    
	 * @param string $string
	 * @return True or False
	 */
	public function set($string) {
		if (mb_strpos($string, " ")) {
			$firstWord = mb_substr($string, 0, mb_strpos($string, " "));
		} else {
			$firstWord = mb_substr($string, 0, mb_strlen($string));
		}

		$this->args = explode(" ", $string);
		
		$method = "_" . strtolower($firstWord);
		
		if (mb_substr($method, 0, 2) != "__") {
			$flag = $this->$method();

			if ($this->registry["memc"]["enable"]) {
				$this->memcached->set("cmd_ui:" . $this->registry["ui"]["id"]);
				if ($this->memcached->load()) {
					$this->_history = $this->memcached->get();
				}
				$result["message"] = $string;
				$result["date"] = date("H:i:s");
				$result["text"] = $this->result;
				$this->_history[] = $result;
				$this->memcached->save($this->_history);
			}
			
			return $flag;
		} else {
			return false;
		}
	}
	
	/**
	 * Getter Возвращает результат
	 * @return string $this->result;
	 */
	public function get() {
		return $this->result;
	}
	
	/**
	 * Сохраняет строку в историю
	 * При доступном модуле memcached:
	 * В массиве $this->_history[] хранится:
	 *    $string - строка
	 *    date("H:i:s") - время команды
	 *    
	 * @param string $string
	 */
	public function setHistory($string) {
		if ($this->registry["memc"]["enable"]) {
			$this->memcached->set("cmd_ui:" . $this->registry["ui"]["id"]);
			if ($this->memcached->load()) {
				$this->_history = $this->memcached->get();
			}
			$result["message"] = $string;
			$result["date"] = date("H:i:s");
			$result["text"] = "";
			$this->_history[] = $result;
			$this->memcached->save($this->_history);
		}
	}
	/**
	 * Получает историю из memcached (если доступен)
	 * @return array;
	 */
	public function getHistory() {
		$result = array();
		
		$this->memcached->set("cmd_ui:" . $this->registry["ui"]["id"]);
		if ($this->memcached->load()) {
			$result = $this->memcached->get();
		}
		
		if ($result != NULL) {
			return $result;
		} else {
			return array();
		}
	}
	
	/**
	 * Преобразует массив в строку
	 * 
	 * Результат обрамляется тегами <pre class='pre'></pre>
	 * 
	 * @param array $var
	 * @return string
	 */
	private function array_to_string($var){
		if (is_array($var)) {
			$output = "<pre class='pre'>";
			$this->var_dump_to_string($var,$output);
			$output .= "</pre>";
		} else {
			$output = $var;
		}
		
		return $output;
	}
	
	/**
	 * Рекурсивно анализирует массив для предоставления информации в виде строки с отступами (var_dump)
	 * Нужно для функции array_to_string
	 */
	private function var_dump_to_string($var,&$output,$prefix=""){
		foreach($var as $key=>$value){
			if (is_array($value)){
				$output.= $prefix.$key.": \n";
				$this->var_dump_to_string($value,$output,"  ".$prefix);
			} else {
				$output.= $prefix.$key.": ".$value."\n";
			}
		}
	}

	/**
	 * Команда help
	 * 
	 * Выводит списком доступные команды: приватные методы класса, начинающиеся с "_" (_exit())
	 * 
	 * @return TRUE
	 */
	private function _help() {
		$class = new ReflectionClass($this);
		$methods = $class->getMethods();
		
		foreach($methods as $method) {
			$name = $method->getName();
			if (mb_substr($name, 0, 1) == "_") {
				if (mb_substr($name, 0, 2) != "__") {
					$cmds[] = mb_substr($name, 1, mb_strlen($name) - 1);
				}
			} 
		}

		$cmd = implode(", ", $cmds);
		
		$this->result = "<b>" . $cmd . "</b>";
		
		return true;
	}
	
	/**
	 * Команда about
	 * 
	 * Выводит информацию о системе из файла .../cmd/about.tpl
	 * 
	 * @return TRUE
	 */
	private function _about() {
		$this->result = $this->render("cmd_about", array());
		
		return true;
	}
	
	/**
	 * Команда clear
	 * 
	 * Очищает вывод командной строки, удаляет информацию из истории (memcached)
	 * 
	 * @return TRUE
	 */
	private function _clear() {
		$this->memcached->set("cmd_ui:" . $this->registry["ui"]["id"]);
		if ($this->memcached->load()) {
    		$this->memcached->delete();
    	}
		
		return true;
	}
	
	/**
	 * Команда exit
	 * 
	 * Необходима для закрытия командной строки
	 * 
	 * @return TRUE
	 */
	private function _exit() {
		return true;
	}
	
	/**
	 * Команда date
	 * 
	 * Выводит дату в формате Y-m-d
	 * 
	 * @return TRUE
	 */
	private function _date() {
		$this->result = date("Y-m-d");
	
		return true;
	}
	
	/**
	 * Команда time
	 * 
	 * Выводит время в формате H:i:s
	 * 
	 * @return TRUE
	 */
	private function _time() {
		$this->result = date("H:i:s");
	
		return true;
	}
	
	/**
	 * Команда status
	 * 
	 * Выводит статус конкретного пользователя online или offline
	 * 
	 * $this->args[1] - логин пользователя 
	 * @return TRUE
	 */
	private function _status() {
		if ( (isset($this->args[1])) and (is_string($this->args[1])) ) {
			$uid = $this->registry["user"]->getUserId($this->args[1]);
			$ui = $this->registry["user"]->getUserInfo($uid);
			
			$status_bool = $this->registry["user"]->getstatus($uid);
			if ($status_bool) {
				$status = "<span style='color: #4F4'>online</span>";
			} else { $status = "<span style='color: red'>offline</span>";
			}
			
			$this->result = "<b>Status: </b> " . $status;
		} else {
			$this->result = "Usage: status login";
		}
		
		return true;
	}
	
	/**
	 * Команда info
	 * 
	 * Выводит информацию о конкретном пользователе
	 * 
	 * $this->args[1] - логин пользователя 
	 * @return TRUE
	 */
	private function _info() {
		if ( (isset($this->args[1])) and (is_string($this->args[1])) ) {
			$uid = $this->registry["user"]->getUserId($this->args[1]);
			$ui = $this->registry["user"]->getUserInfo($uid);
			
			if (count($ui) > 1) {
				$status_bool = $this->registry["user"]->getstatus($uid);
				if ($status_bool) {
					$status = "<span style='color: #4F4'>online</span>";
				} else {
					$status = "<span style='color: red'>offline</span>";
				}
				
				$this->result .= "<div>" . $ui["name"] . " " . $ui["soname"] . "</div>";
				$this->result .= "<div><b>Signature: </b>" . $ui["signature"] . "</div>";
				$this->result .= "<div><b>Adress: </b>" . $ui["adres"] . "</div>";
				$this->result .= "<div><b>Phone: </b>" . $ui["phone"] . "</div>";
				$this->result .= "<div><b>ICQ: </b>" . $ui["icq"] . " " . $ui["skype"] . "</div>";
				$this->result .= "<div><b>Email: </b>" . $ui["email"] . "</div>";
				$this->result .= "<div><b>Status: </b>" . $status . "</div>";
			} else {
				$this->result .= "---";
			}
		} else {
			$this->result = "Usage: info login";
		}
	
		return true;
	}
	
	/**
	 * Команда list
	 * 
	 * Выводит список пользователей по группам, выделяя цветом статус присутствия в системе
	 * 
	 * @return TRUE
	 */
	private function _list() {
		$groups = $this->registry["user"]->getUsersGroups();
		
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
		
		foreach($new_groups as $group) {
			$users = $this->registry["user"]->getUserInfoFromGroup($group["gid"]);
				
			foreach($users as $user) {
				$status_bool = $this->registry["user"]->getStatus($user["uid"]);
				
				if ($status_bool) {
					$result[$group["gname"]][] = "<span style='color: #4F4'>[" . $user["name"] . " " . $user["soname"] . "] " . $user["login"] . "</span>";
				} else {
					$result[$group["gname"]][] = "<span style='color: red'>[" . $user["name"] . " " . $user["soname"] . "] " . $user["login"] . "</span>";
				}
			}
		}
		
		$this->result = $this->array_to_string($result);

		return true;
	}
	
	/**
	 * Команда msg
	 * 
	 * Отправялет сообщение конкретному пользователю, группе пользователей или всем
	 * Сообщение будет послану функиональному модулю Logs и сохранено с ключём "service" 
	 * 
	 * $this->args[1]:
	 *    all
	 *    group=group_name
	 *    login
	 * $this->args[2] - сообщение
	 * 
	 * @return TRUE
	 */
	private function _msg() {
		if ( (isset($this->args[1])) and (is_string($this->args[1])) ) {
			
			if (count($this->args) > 2) {

				$result = array();
				for($i=2; $i<count($this->args); $i++) {
					$result[] = $this->args[$i];
				}
				$result_str = implode(" ", $result);

				if ($this->args[1] == "all") {
					
					$users = $this->registry["user"]->getUsersList();
					 
					foreach($users as $user) {
						$string = $this->render("logs_msg", array("msg" => $result_str));
						$this->registry["logs"]->uid = $user["id"];
						$this->registry["logs"]->set("service", $string, "");
					}
					
					$this->result = "message success send";
					
				} else if (mb_substr($this->args[1], 0, mb_strpos($this->args[1], "=")+1) == "group=") {
					
					$gid = $this->registry["user"]->getSubgroupId(mb_substr($this->args[1], mb_strpos($this->args[1], "=")+1, mb_strlen($this->args[1]) - mb_strpos($this->args[1], "=")));

					if (isset($gid)) {
						$users = $this->registry["user"]->getUserInfoFromGroup($gid);
						 
						foreach($users as $user) {
							$string = $this->render("logs_msg", array("msg" => $result_str));
							$this->registry["logs"]->uid = $user["uid"];
							$this->registry["logs"]->set("service", $string, "");
						}
						
						$this->result = "message success send";
					} else {
						$this->result = "group invalid";
					}					
				} else {
					$uid = $this->registry["user"]->getUserId($this->args[1]);

					if (isset($uid)) {
						$string = $this->render("logs_msg", array("msg" => $result_str));
						$this->registry["logs"]->uid = $uid;
						$this->registry["logs"]->set("service", $string, "");

						$this->result = "message success send";
					} else {
						$this->result = "login invalid";
					}
				}
			} else {
				$this->result = "message empty";
			}
		} else {
			$this->result = "Usage: msg [all,group=group_name,login] msg";
		}
		
		return true;
	}
	
	/**
	 * Команда registry
	 * 
	 * Выводит значение из реестра ($this->registry)
	 * Команда доступна для выполнения только пользователю с админискими правами в системе
	 * 
	 * $this->args[1] - ключ в реестре
	 * 
	 * @return TRUE
	 */
	private function _registry() {
		if ($this->registry["ui"]["admin"]) {
			if ( (isset($this->args[1])) and (is_string($this->args[1])) ) {
				if (isset($this->registry[$this->args[1]])) {
					$this->result = $this->array_to_string($this->registry[$this->args[1]]);
				} else {
					$this->result = "argument not found";
				}
			} else {
				$this->result = "Usage: registry [arg]";
			}
		} else {
			$this->result = "access denied!";
		}
		
		return true;
	}
	
	/**
	 * Команда cache
	*
	* Выводит значение из memcached ($this->memcached)
	* Команда доступна для выполнения только пользователю с админискими правами в системе
	*
	* $this->args[1] - ключ в memcached
	*
	* @return TRUE
	*/
	private function _cache() {
		if ($this->registry["ui"]["admin"]) {
			if ( (isset($this->args[1])) and (is_string($this->args[1])) ) {
				
				$this->memcached->set($this->args[1]);
				
				if ($this->memcached->load()) {
					$this->result = $this->array_to_string($this->memcached->get());
				} else {
					$this->result = "argument not found";
				}
			} else {
				$this->result = "Usage: cache [arg]";
			}
		} else {
			$this->result = "access denied!";
		}
	
		return true;
	}
	
	/**
	 * Команда sessTime
	*
	* Выводит время входа-выхода из системы для конкретного пользователя за нужную дату
	*
	* $this->args[1] - логин пользователя
	* $this->args[2] - дата (Ymd)
	*
	* @return TRUE
	*/
	private function _sessTime() {
		if ( (isset($this->args[1])) and (is_string($this->args[1])) ) {
				
			if (count($this->args) > 2) {
		
				$result = array();
		
				$count = count($this->args);
					
				for($i=2; $i<$count; $i++) {
					$result[] = $this->args[$i];
					$result = implode(" ", $result);
				}
		

				$uid = $this->registry["user"]->getUserId($this->args[1]);
	
				if (isset($uid)) {
					$ui = new Ui();
					
					if ($this->args[2] == "today") {
						$this->args[2] = date("Ymd");
					}
					
					$sessTime = $ui->getSess($uid, $this->args[2]);
					
					$this->result = $this->array_to_string($sessTime);
				} else {
					$this->result = "login invalid";
				}
			} else {
				$this->result = "Usage: sessTime login date [date format: 'Y-m-d' or 'Ymd']";
			}

		} else {
			$this->result = "Usage: sessTime login date [date format: 'Y-m-d' or 'Ymd']";
		}
		
		return true;
	}
	
	/**
	 * Команда dashboard
	*
	* Выводит список событий за нужную дату
	*
	* $this->args[1] - дата (Ymd)
	*
	* @return TRUE
	*/
	private function _dashboard() {
		if ( (isset($this->args[1])) and (is_string($this->args[1])) ) {
			$dashboard = new Dashboard();
			
			if ($this->args[1] == "today") {
				$this->args[1] = date("Ymd");
			}
			
			$session = & $_SESSION["dashboard"];
			$session["date"] = $this->args[1];
			
			$listevents = $dashboard->getEvents();
			
			$list = null;
			if (count($listevents) == 0) {
				$list = "Events are absent";
			}

			foreach($listevents as $event) {	
				$list .= $this->render("event", array("event" => $event));
			}
			
			$this->result = $list;
		} else {
			$this->result = "Usage: dashboard date [date format: 'Y-m-d' or 'Ymd']";
		}
		
		return true;
	}
	
	/**
	 * Команда task
	*
	* Команда доступна для выполнения только пользователю с админискими правами в системе
	* Действие с задачей:
	* $this->args[1]:
	*    info - вывести данные по задаче
	*    close - закрыть задачу
	* $this->args[2] - ID задачи
	*
	* @return TRUE
	*/
	private function _task() {
		if ( (isset($this->args[1])) and (is_string($this->args[1])) ) {
		
			if ($this->registry["ui"]["admin"]) {
				if ($this->args[1] == "close") {
					if ( (isset($this->args[2])) and (is_numeric($this->args[2])) ) {
						$tt = $this->registry["module_task"];
						$tt->closeTask($this->args[2]);
						
						$this->result = "task " . $this->args[2] . " closed";
					} else {
						$this->result = "ID invalid";
					}
				} else if ($this->args[1] == "info") {
					if ( (isset($this->args[2])) and (is_numeric($this->args[2])) ) {
						$tt = $this->registry["module_task"];
						$task = $tt->getTask($this->args[2]);
						
						$this->result = $this->array_to_string($task);
					} else {
						$this->result = "ID invalid";
					}
				} else {
					$this->result = "Usage: task [info, close] ID";
				}				
			} else {
				$this->result = "access denied!";
			}
		
		} else {
			$this->result = "Usage: task [info, close] ID";
		}
		
		return true;
	}
	
	/**
	 * Команда modules
	 * 
	* Команда доступна для выполнения только пользователю с админискими правами в системе
	* Выводит список модулей доступных в системе
	*
	* @return TRUE
	*/
	private function _modules() {
		if ($this->registry["ui"]["admin"]) {
			$dirs = array();
			$dir = $this->registry["rootPublic"] . "/../" . $this->registry['path']['src'] . '/Otms/Modules/';
			$dh = opendir($dir);
			while (false !== ($filename = readdir($dh))) {
				if ( ($filename != ".") and ($filename != "..") ) { 
					$dirs[] = $filename;
				}
			}
			
			$this->result = $this->array_to_string($dirs);
		} else {
			$this->result = "access denied!";
		}
		
		return true;
	}
	
	/**
	 * Команда models
	*
	* Команда доступна для выполнения только пользователю с админискими правами в системе
	* Выводит список всех моделей в системе
	*
	* @return TRUE
	*/
	private function _models() {
		if ($this->registry["ui"]["admin"]) {
			$dirs = array(); $files = array();
			
			$dir = $this->registry["rootPublic"] . "/../" . $this->registry['path']['src'] . '/Otms/Modules/';
			$dh = opendir($dir);
			while (false !== ($filename = readdir($dh))) {
				if ( ($filename != ".") and ($filename != "..") ) { 
					$dirs[] = $filename;
				}
			}
			
			$sdir = $this->registry["rootPublic"] . "/../" . $this->registry['path']['src'] . '/Otms/System/' . "Model/";
			$dh  = opendir($sdir);
			while (false !== ($filename = readdir($dh))) {
				if ( ($filename != ".") and ($filename != "..") ) {
					$files[] = "Model\\" . substr($filename, 0, strpos($filename, "."));
				}
			}
			
			foreach($dirs as $module) {
				$dir = $this->registry["rootPublic"] . "/../" . $this->registry['path']['src'] . '/Otms/Modules/' . $module . "/Model/";
				if (is_dir($dir)) {
					$dh  = opendir($dir);
					while (false !== ($filename = readdir($dh))) {
						if ( ($filename != ".") and ($filename != "..") ) {
							$dir = $this->registry["rootPublic"] . "/../" . $this->registry['path']['src'] . '/Otms/Modules/' . $module . "/Model/" . $filename . "/";
							$dirname = $filename;
							if (is_dir($dir)) {
								$dh  = opendir($dir);
								while (false !== ($filename = readdir($dh))) {
									if ( ($filename != ".") and ($filename != "..") ) {
										$files[] = "Model\\" . $dirname . "_" . substr($filename, 0, strpos($filename, "."));
									}
								}
							} else {
								$files[] = "Model\\" . substr($filename, 0, strpos($filename, "."));
							}
						}
					}
				}
				
				$this->result = $this->array_to_string($files);
			}
		} else {
			$this->result = "access denied!";
		}
		
		return true;
	}
	
	/**
	 * Команда model
	*
	* Команда доступна для выполнения только пользователю с админискими правами в системе
	* Выводит аргументы и методы модуля (ReflectionClass)
	* $this->args[1] - имя модели
	*
	* @return TRUE
	*/
	private function _model() {
		if ($this->registry["ui"]["admin"]) {
			if ( (isset($this->args[1])) and (is_string($this->args[1])) ) {
				$module = new ReflectionClass($this->args[1]);
				$this->result = $this->array_to_string($module->getMethods());
			} else {
				$this->result = "Usage: model [Model_Name]";
			}
		} else {
			$this->result = "access denied!";
		}
		
		return true;
	}
	
	/**
	 * Команда getMethodParameters
	*
	* Команда доступна для выполнения только пользователю с админискими правами в системе
	* Выводит информацию по ужному методу (ReflectionClass)
	* $this->args[1] - имя модели
	* $this->args[2] - имя метода
	*
	* @return TRUE
	*/
	private function _getMethodParameters() {
		if ($this->registry["ui"]["admin"]) {
			if ( (isset($this->args[1])) and (is_string($this->args[1])) ) {
				if ( (isset($this->args[2])) and (is_string($this->args[2])) ) {
					$module = new ReflectionClass($this->args[1]);
					$this->result = $this->array_to_string($module->getMethod($this->args[2])->getParameters());
				} else {
					$this->result = "Usage: getMethodParameters [Model_Name] [MethodName]";
				}
			} else {
				$this->result = "Usage: getMethodParameters [Model_Name] [MethodName]";
			}
		} else {
			$this->result = "access denied!";
		}
		
		return true;
	}
	
	/**
	 * Команда function
	*
	* Команда доступна для выполнения только пользователю с админискими правами в системе
	* Выполняет метод класса
	* $this->args[1] - имя модели
	* $this->args[2] - имя метода
	* $this->args[3,4,5...] - аргументы метода
	*
	* @return TRUE
	*/
	private function _function() {
		if ($this->registry["ui"]["admin"]) {
			$params = array();
			
			if ( (isset($this->args[1])) and (is_string($this->args[1])) ) {
				if ( (isset($this->args[2])) and (is_string($this->args[2])) ) {
					$module = new ReflectionClass($this->args[1]);
					$obj = $module->newInstance();
					$method = $this->args[2];	
					
					for($i=3; $i<count($this->args); $i++) {
						$params[] = $this->args[$i];
					}
					$this->result = $this->array_to_string(call_user_func_array(array($obj, $method), $params));
				} else {
					$this->result = "Usage: function [Model_Name] [MethodName] [args]";
				}
			} else {
				$this->result = "Usage: function [Model_Name] [MethodName] [args]";
			}
		} else {
			$this->result = "access denied!";
		}
		
		return true;
	}
	
	/**
	* В случае ошибки выводит её
	*
	* @return TRUE
	*/
	public function __call($method, $args) {
		$this->result = "Unknown command: <b>" . mb_substr($method, 1, mb_strlen($method) - 1) . "</b><br /> Try <b>help</b>";
		
		return true;
	}
}