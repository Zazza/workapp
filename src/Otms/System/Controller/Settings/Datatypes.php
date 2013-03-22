<?php

/**
 * This file is part of the Workapp project.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\System\Controller\Settings;

use Engine\Controller;
use Otms\Modules\Objects\Model\Template;

class Datatypes extends Controller {
	public function index() {
		if ($this->registry["ui"]["admin"]) {
			$this->view->setTitle("Data types");
			
			$this->view->setLeftContent($this->view->render("left_settings", array()));

			if (isset($this->get["id"])) {
	        	$this->view->settings_objects_datafield(array("id" => $this->get["id"]));
	        } else {        
		        $templates = new Template();
		        $datatypes = $templates->getDataTypes();
		
		        $this->view->settings_objects_datatype(array("datatypes" => $datatypes));
	        }
		}
	}
}