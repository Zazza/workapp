<?php
class Model_FASave extends Engine_Model {
	private $filename = null;
	private $md5 = null;
	
	function save() {
		if ($this->registry["auth"]) {
			$input = fopen("php://input", "r");
			$temp = tmpfile();
			$realSize = stream_copy_to_stream($input, $temp);
			fclose($input);
			 
			if ($realSize != $this->getSize()){
				return false;
			};

			$sql = "INSERT INTO fm_fs (`md5`, `filename`, `pdirid`, `size`) VALUES (:md5, :filename, :curdir, :size)";
			 
			$res = $this->registry['db']->prepare($sql);
			$param = array(":md5" => $this->md5, ":filename" => $this->filename, ":curdir" => '1', ":size" => $realSize);
			$res->execute($param);

			$target = fopen($this->registry['path']['root'] . "/" . $this->registry['path']['upload'] . $this->md5, "w");
			fseek($temp, 0, SEEK_SET);
			stream_copy_to_stream($temp, $target);
			fclose($target);

			return true;
		} else {
			return false;
		}
	}

	function getName() {
		$this->filename = $_GET['qqfafile'];
		$this->md5 = md5($this->filename . date("YmdHis"));

		return $this->filename;
	}

	function getSize() {
		if (isset($_SERVER["CONTENT_LENGTH"])){
			return (int)$_SERVER["CONTENT_LENGTH"];
		} else {
			throw new Exception('Getting content length is not supported.');
		}
	}
}
?>