<?php

/**
 * This file is part of the Workapp project.
 *
 * Photo Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Photo\Controller;

use Otms\Modules\Photo\Model\Photofile;
use Otms\Modules\Photo\Model;

use Engine\Modules\Controller;

/**
 * Controller\Photo class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Photo extends Controller {

	public function index() {
		$this->view->setTitle("Фотографии");
		
		$file = new Model\Photofile();
		$file->showTree(0);
		
		if (isset($_GET["id"])) {
			$fm = & $_SESSION["fm"];

			if ($_GET["id"] == "0") {
				$fm["dir"] = 0;
				$fm["dirname"] = "/";
			} else if (is_numeric($_GET["id"])) {
				$fm["dir"] = $_GET["id"];
				$dirname = $file->getNameFromDir($_GET["id"]);
				$fm["dirname"] = $dirname[0]["name"];
			} else {
				$fm["dir"] = 0;
				$fm["dirname"] = "/";
			}
			
			$this->view->index(array("session_name" => session_name(), "session_id" => session_id(), "config" => $this->config["photo"]));
		} else if (isset($_GET["group"])) {
			if ($_GET["group"] == "sel") {
				$mphoto = new Model\Photo();
				$sels = $mphoto->getAllSels();
				
				$this->view->fm_sels(array("data" => $sels));
			} else if ($_GET["group"] == "tags") {
				$mphoto = new Model\Photo();
				$tags = $mphoto->getAllTags();
			
				$this->view->fm_tags(array("data" => $tags));
			}
		} else if (!isset($_GET["id"])) {
			$this->view->index(array("session_name" => session_name(), "session_id" => session_id(), "config" => $this->config["photo"]));
		}
		
		$sess = 0; $fav = 0; $sel = 0;
		$sphoto = & $_SESSION["photo"];
		if (isset($sphoto["tag"])) {
			if (count($sphoto["tag"]) > 0) {
				$sess = 1; $sel = 1;
			}
		}
		if (isset($sphoto["sel"])) {
			if (count($sphoto["sel"]) > 0) {
				$sess = 1; $sel = 1;
			}
		}
		if (isset($sphoto["fav"])) {
			if ($sphoto["fav"] == 1) {
				$sess = 1; $fav = 1;
			}
		}
		
		$render = "";
		if ($sess) {
			$this->view->setLeftContent($this->view->render("block_leftsort", array("fav" => $fav, "sel" => $sel)));
		} else {		
			$this->view->setLeftContent($this->view->render("block_left", array("tree" => $file->getTree())));
		}
		
		$this->view->setLeftContent($this->view->render("block_leftmenu", array()));
		$this->view->setAdvBottomPanel($this->view->render("block_bottom", array()));
		
		$this->view->showPage();
	}
}
?>