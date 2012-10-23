<?php

/**
 * This file is part of the Workapp project.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\System\Controller;

use Engine\Controller;
use Otms\System\Model;

class Find extends Controller {
	protected $findSess = null;
	protected $numFind = null;
	protected $find = null;

	public function __construct() {
		parent::__construct();
		
		$this->find = new Model\Find();
		
		$this->findSess = & $_SESSION["find"];
		
		if (isset($_POST["find"])) {
			$_POST["find"] = htmlspecialchars($_POST["find"]);
			$this->findSess["string"] = $_POST["find"];
		} else {
			if (!isset($this->findSess["string"])) {
				$this->findSess["string"] = "";
			}
		}
		
		$tfind = explode(" ", substr($this->findSess["string"], 0, 64));
		
		$this->numFind = $this->find->getNumFinds($tfind);
	}
	
	public function index() {
		header("Location: " . $this->registry["uri"] . "find/task/");
	}
}