<?php

/**
 * This file is part of the Workapp project.
*
* (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
*
*/

namespace Otms\System\Ajax;

use Engine\Ajax;
use Otms\System\Model\CmdCommands;

/**
 * Cmd Ajax class
 * 
 * Класс ajax-контроллер получающий информацию от пользователя и передающий её для выполнения в модель CmdCommands()
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */

class Cmd extends Ajax {
	
	/**
	 * Получает строку из командной строки и передаёт её на выполнение в модель CmdCommands()
	 * 
	 * @param array $params
	 *    string $params["message"]
 	 * @return string
	 */
	public function addCmd($params) {
		$message = $params["message"];
		
		$cmd = new CmdCommands();

		$result = $cmd->set($message);
	
		$text = $cmd->get();

		return "<span class='ps'>" . $this->registry["ui"]["login"] . "[" . date("H:i:s") . "]#</span> <span style='color: white'>" . $message . "</span><br />" .  $text;
	}
	
	/**
	* Выводит историю команд
	*
	* @return string
	*/
	public function getHistory() {
		$cmd = new CmdCommands();
		
		$history = $cmd->getHistory();
		
		$content = NULL;
		
		foreach($history as $part) {
			$content .= "<p class='resCmd'><span class='ps'>" . $this->registry["ui"]["login"] . "[" . $part["date"] . "]#</span> <span style='color: white'>" . $part["message"] . "</span><br />" .  $part["text"] . "</p>";
		}
		
		return $content;
	}
	
	/**
	 * Сохраняет строку в историю
	 * 
	* @param array $params
	 *    string $params["string"]
	* @return string
	*/
	public function setHistory($params) {
		$string = $params["string"];
		
		$cmd = new CmdCommands();
		
		$cmd->setHistory($string);
		
		return "<span class='ps'>" . $this->registry["ui"]["login"] . "[" . date("H:i:s") . "]#</span> <span style='color: white'>" . $string . "</span>";
	}
}