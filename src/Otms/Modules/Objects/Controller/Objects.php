<?php

/**
 * This file is part of the Workapp project.
 *
 * Object Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Objects\Controller;

use Engine\Modules\Controller;
use Otms\Modules\Objects\Model\Object;
use Otms\Modules\Objects\Model\Template;

/**
 * Controller\Objects class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Objects extends Controller {
	protected $templates;
	protected $tree_depth = array();
	protected $object = null;
	protected $depth = array();
	
	protected $mtemplate;

	public function __construct($config) {
		parent::__construct($config);
		
		$this->object = new Object();
		$this->mtemplate = new Template();
		
		$this->templates = $this->mtemplate->getTemplates();
	}
	
	public function index() {
		$this->view->refresh(array("timer" => "1", "url" => "objects/lists/"));
		$this->view->showPage();
	}
}
?>
