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
use Symfony\Component\HttpFoundation\Response;

class Login extends Controller {
	
	function __construct() {
		parent::__construct();
    }

	public function index() {
		if (!$this->registry["auth"]) {
			
			$response = new Response();
            
            $login = new Model\Ui();
            
            if (isset($_POST["submit"])) {
                if ($login->login($_POST["login"], $_POST["password"])) {
                    $response->setContent($this->view->render("refresh", array("timer" => "1", "url" => "")));
                } else {
                    $response->setContent($this->view->render("login", array("err" => TRUE, "url" => $this->registry["siteName"])));
                }
            } else {
            	$action = (empty($_GET['main'])) ? '' : $_GET['main'];
            	if (empty($action)) {
            		$action = 'index';
            	};

            	if ($action != "index") {
            		$response->setContent($this->view->render("refresh", array("timer" => "0", "url" => "")));
            	} else {
                	$response->setContent($this->view->render("login", array("url" => $this->registry["siteName"])));
            	}
            }
            
            $response->send();
        } else {
        	$this->__call();
        }
	}
}