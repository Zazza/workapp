<?php
class Controller_Ajax_Cmd extends Engine_Ajax {
	public function addCmd($params) {
		$cmd = new Model_CmdCommands();
		
		$message = $params["message"];

		$result = $cmd->set($message);
	
		$text = $cmd->get();

		echo "<span class='ps'>" . $this->registry["ui"]["login"] . "[" . date("H:i:s") . "]#</span> <span style='color: white'>" . $message . "</span><br />" .  $text;
	}
	
	public function getHistory() {
		$cmd = new Model_CmdCommands();
		
		$history = $cmd->getHistory();
		
		foreach($history as $part) {
			echo "<p class='resCmd'><span class='ps'>" . $this->registry["ui"]["login"] . "[" . $part["date"] . "]#</span> <span style='color: white'>" . $part["message"] . "</span><br />" .  $part["text"] . "</p>";
		}
	}
	
	public function setHistory($params) {
		$string = $params["string"];
		
		$cmd = new Model_CmdCommands();
		
		$cmd->setHistory($string);
		
		echo "<span class='ps'>" . $this->registry["ui"]["login"] . "[" . date("H:i:s") . "]#</span> <span style='color: white'>" . $string . "</span>";
	}
}
?>
