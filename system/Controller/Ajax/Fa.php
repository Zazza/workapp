<?php
class Controller_Ajax_Fa extends Engine_Ajax {
    private $count = 0;
    
    private $file = array();
    
    private $tree = null;
    
    function save() {
    	$save = new Controller_Ajax_FASave();
    	$save->index();
    }
}
?>