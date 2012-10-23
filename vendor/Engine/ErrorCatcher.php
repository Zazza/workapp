<?php

/**
 * This file is part of the Workapp project Engine.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Engine;

use \Twig_Autoloader;
use \Twig_Loader_Filesystem;
use \Twig_Environment;
use Symfony\Component\HttpFoundation\Response;

/**
 * Перехватчик ошибок 
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */

class ErrorCatcher extends Singleton {
    public function __construct() {
    	parent::__construct();
    	
        set_error_handler(array($this, 'OtherErrorCatcher'));
        
        register_shutdown_function(array($this, 'FatalErrorCatcher'));

        ob_start();
    }
    
	/**
	 * Twig render
	 * 
	 * @param unknown_type $name (пример: fm_content)
	 * @param array $params - параметры
	 */
	protected function render($tpl, $params) {
		Twig_Autoloader::register();
		
		$content = new Twig_Loader_Filesystem(__DIR__);
		$twig = array('cache' => FALSE, 'autoescape' => FALSE);
		
		$layouts = new Twig_Environment($content, $twig);
	
		$template = $layouts->loadTemplate('/tpl/' . $tpl);
	
		$content = $template->render($params);
	
		return $content;
	}
    
	/**
	 * Перехват notice
	 * 
	 * @param int $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param int $errline
	 */
    public function OtherErrorCatcher($errno, $errstr, $errfile, $errline) {
    	$error = $this->registry["notice"];
    	$error[] = $this->render("notice.tpl", array("file" => $errfile, "line" => $errline, "message" => $errstr));
    	$this->registry->remove("notice");
		$this->registry->set("notice", $error);
    }
    
    /**
     * Перехват критических ошибок
     */
    public function FatalErrorCatcher() {
        $error = error_get_last();
        if (isset($error))
            if ($error['type'] == E_ERROR || $error['type'] == E_PARSE || $error['type'] == E_COMPILE_ERROR || $error['type'] == E_CORE_ERROR) {
        	
                ob_end_clean();
                
                $response = new Response($this->render("error.tpl", array("file" => $error['file'], "line" => $error['line'], "message" => $error['message'])), 500);
                
                $response->send();
            } else {
                ob_end_flush();
			}
    }
}