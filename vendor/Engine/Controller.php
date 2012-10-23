<?php

/**
 * This file is part of the Workapp project Engine.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Engine;

use Engine\Singleton;

/**
 * Controller class
 *
 * Класс наследуемый другими Controller классами
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Controller extends Singleton {
        /**
         * Экземпляр Вида
         *
         * @var object
         */
        protected $view;

        /**
         * Request: http://fomen.com/action/args[0]/args[1]/...
         *
         * @var unknown_type
         */
        protected $action;

        /**
         * Request: http://fomen.com/action/args[0]/args[1]/...
         * @var unknown_type
         */
        protected $args;

        /**
         * Request $_GET
         * @var unknown_type
         */
        protected $get;

        /**
         * Request $_POST
         * @var unknown_type
         */
        protected $post;

        function __construct() {
                parent::__construct();

                $this->view = $this->registry['view'];

        $this->action = $this->registry["action"];
        $this->args = $this->registry["args"];
        $this->get = $this->registry["get"];
        $this->post = $this->registry["post"];
    }

    /**
     * В случае вызова несуществующей страницы $this->view->page404()
     *
     * @param string $name
     * @param array $args
     */
        public function __call($name = null, $args = null) {
                $this->view->setTitle("404");

        $this->view->page404();
        }
}
?>
