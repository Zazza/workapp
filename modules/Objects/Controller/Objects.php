<?php
class Controller_Objects extends Modules_Controller {
	protected $templates;
	protected $tree_depth = array();
	protected $object = null;
	protected $depth = array();
	
	protected $mtemplate;

	public function __construct($config) {
		parent::__construct($config);
		
		$this->object = new Model_Object();
		$this->mtemplate = new Model_Template();
		
		$this->templates = $this->mtemplate->getTemplates();
	}
	
	public function index() {
		header("Location: " . $this->registry["uri"] . "objects/list/");
	}
}
?>
