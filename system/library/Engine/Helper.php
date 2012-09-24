<?php
class Engine_Helper extends Engine_Interface {
	protected $view;
	
	function __construct() {
		parent::__construct();
		
        $this->view = $this->registry['view'];
	}
}
?>