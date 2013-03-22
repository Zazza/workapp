<?php

/**
 * This file is part of the Workapp project.
 *
 * Chat Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Chat\Model;

use Symfony\Component\Validator\Constraints\True;

use Engine\Modules\Model;

/**
 * Model\Chat class
 *
 * Класс для управления командами чата
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class ChatCommands extends Model {
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
	 * Получает строку введенную пользователем и пытается выполнить команду
	 * $this->args - строка разбитая на слова: explode(" ", $string)
	 * 
	 * @param string $string
	 * @return boolean
	 *    TRUE - команда требует ответа (любого вывода на экран)
	 *    FALSE - команда не требует ответа (любого вывода на экран)
	 */
	public function set($string) {
		if (mb_substr($string, 0, 1) == "/") {
			if (mb_strpos($string, " ")) {
				$firstWord = mb_substr($string, 1, mb_strpos($string, " ") - 1);
			} else {
				$firstWord = mb_substr($string, 1, mb_strlen($string) - 1);
			}

			$this->args = explode(" ", $string);
			
			$method = "_" . strtolower($firstWord);
			
			if (mb_substr($method, 0, 2) != "__") {
				return $this->$method();
			} else {
				return false;
			}
		} else {
			$this->result = $string;
			
			return false;
		}
	}
	
	/**
	 * Getter
	 * @return string $this->result
	 */
	public function get() {
		return $this->result;
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
			$output = "<pre>";
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
			if(is_array($value)){
				$output.= $prefix.$key.": \n";
				$this->var_dump_to_string($value,$output,"  ".$prefix);
			} else{
				$output.= $prefix.$key.": ".$value."\n";
			}
		}
	}
	
	/**
	 * Команда help
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
					$cmds[] = "/" . mb_substr($name, 1, mb_strlen($name) - 1);
				}
			} 
		}

		$cmd = implode(", ", $cmds);
		
		$this->result = "<b>" . $cmd . "</b>";
		
		return true;
	}
	
	/**
	 * Команда private
	 * Отправить личное сообщение пользователю чата (комнаты)
	 * 
	 * @return FALSE
	 */
	private function _private() {
		if ($this->args[1] == $this->registry["ui"]["login"]) {
			$result = array();
			
			$count = count($this->args);

			for($i=2; $i<$count; $i++) {
				$result[] = $this->args[$i];
			}
		
			$this->result = "<b>[Private]</b> <span style='color: green; font-style: italic'>" . implode(" ", $result) . "</span>";
		}
		
		return false;
	}

	public function __call($method, $args) {
		$this->result = "Unknown command: <b>/" . mb_substr($method, 1, mb_strlen($method) - 1) . "</b><br /> Try <b>/help</b>";
		
		return true;
	}
}