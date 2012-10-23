<?php

/**
 * This file is part of the Workapp project.
 *
 * Filemanager Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Filemanager\Controller\Filemanager;

use Engine\Modules\Controller;
use Otms\Modules\Filemanager\Model;

/**
 * Controller\Filemanager\Attach class
 * 
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Attach extends Controller {
	
	function index() {
		$mfile = new Model\File($this->config);
		
		if (isset($_GET["md5"])) {
			if (!strpos($_GET["md5"], "/")) {
				$data = $mfile->attachFromMD5($_GET["md5"]);
				
				$flag = false;
				if ($this->registry["ui"]["id"] == $data[0]["uid"]) {
					$flag = true;
				}
				if (!$flag) {
					$right = json_decode($data[0]["right"], true);
					foreach($right as $key=>$val) {
						if ($key == "frall") {
							if ($val > 0) {
								$flag = true;
							}
						}
						
						if ($key == "fg" . $this->registry["ui"]["gid"]) {
							if ($val > 0) {
								$flag = true;
							}
						}
						
						if ($key == "user" . $this->registry["ui"]["id"]) {
							if ($val > 0) {
								$flag = true;
							}
						}
					}
				}
				
				if ($flag) {
					$fn = $_GET["md5"];
					
					$file = $this->registry["rootPublic"] . $this->registry["path"]["upload"] . $fn;
					
					if (file_exists($file)) {
						$data[0]["filename"] = str_replace(" ", "_", $data[0]["filename"]);
						
						header ("Content-Type: application/octet-stream");
						header ("Accept-Ranges: bytes");
						header ("Content-Length: " . filesize($file));
						header ("Content-Disposition: attachment; filename=" . $data[0]["filename"]);
		
						readfile($file);
					}
				}
			}
		}
		
		if (isset($_GET["filename"])) {
			$filename = $_GET["filename"];
			if (!strpos($filename, "/")) {

				$curdir = $_GET["did"];

				$data = $mfile->attachFromName($filename, $curdir);
				
				$flag = false;
				if ($this->registry["ui"]["id"] == $data[0]["uid"]) {
					$flag = true;
				}
				if (!$flag) {
					$right = json_decode($data[0]["right"], true);
					foreach($right as $key=>$val) {
						if ($key == "frall") {
							if ($val > 0) {
								$flag = true;
							}
						}
						
						if ($key == "fg" . $this->registry["ui"]["gid"]) {
							if ($val > 0) {
								$flag = true;
							}
						}
						
						if ($key == "user" . $this->registry["ui"]["id"]) {
							if ($val > 0) {
								$flag = true;
							}
						}
					}
				}
				
				if ($flag) {				
					$fn = $data[0]["md5"];
	
					$file = $this->registry["rootPublic"] . $this->registry["path"]["upload"] . $fn;
	
					if (file_exists($file)) {
						$filename = str_replace(" ", "_", $filename);
						
						header ("Content-Type: application/octet-stream");
						header ("Accept-Ranges: bytes");
						header ("Content-Length: " . filesize($file));
						header ("Content-Disposition: attachment; filename=" . $filename);
	
						readfile($file);
					}
				}
			}
		}
		
		exit();
	}
}