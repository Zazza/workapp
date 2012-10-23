<?php

/**
 * This file is part of the Workapp project.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace app;

use Engine\Bootstrap;
use Engine\View;
use Otms\System\Model\Ui;
use Otms\System\Model\Api;
use Otms\System\Model\Logs;
use Otms\System\Model\User;
use Otms\System\Controller;

/**
 * Preload class
 *
 * Класс дополняет Bootstrap.
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */

class Preload extends Bootstrap {
    function start() {
        $view = new View();
        $this->registry->set('view', $view);

                $view->setDescription($this->registry["keywords"]);
                $view->setKeywords($this->registry["description"]);

                $ui = new Ui();

                if (isset($_POST[session_name()])) {
                        session_id($_POST[session_name()]);
                }

                session_start();

                $loginSession = & $_SESSION["login"];
                if (isset($loginSession["id"])) {
                        $ui->getInfo($loginSession);

                        $this->registry["logs"] = new Logs();
                        $this->registry["user"] = new User();

                        $this->registry["user"]->setOnline();

                        $this->registry->set("users_sets", $ui->getSet("bu"));

                        $this->registry->set("ajax_notice_sets", $ui->getSet("ajax_notice"));

                } else if (mb_substr($this->registry["url"], 1, 3) == "api") {
                        $api = new Api();
                        if (!$api->login()) {
                                return false;
                        }
                } else {
                        $login = new Controller\Login();
                        $login->index();

                        return false;
                }

                return true;
    }
}
