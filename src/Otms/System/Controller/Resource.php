<?php

/**
 * This file is part of the Workapp project.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\System\Controller;

use Otms\System\Model;
use Engine\Controller;
use Symfony\Component\HttpFoundation\Response;

class Resource extends Controller {
	function index() {
		if (!isset($this->registry['get']['module'])) {
			return FALSE;
		}
		
		if (!isset($this->registry['get']['file'])) {
			return FALSE;
		}
		
		$type = mb_substr($this->registry['get']['file'], 0, strpos($this->registry['get']['file'], '/'));
		
		if (($type != 'css') and ($type != 'js')) {
			return FALSE;
		}

		$response = new Response();
		
		$resource = new Model\Resource();
		$content = $resource->getContent($this->registry['get']['module'], $this->registry['get']['file']);

		if ($type == "css") {
			$response->headers->set('Content-Type', 'text/css');
		} else {
			$response->headers->set('Content-Type', 'application/javascript');
		}
	
		$response->setContent($content);
		$response->send();
		
		exit();
	}
}