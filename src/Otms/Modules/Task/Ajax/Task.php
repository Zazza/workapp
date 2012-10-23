<?php

/**
 * This file is part of the Workapp project.
 *
 * Task Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Task\Ajax;

use Engine\Modules\Ajax;
use Otms\Modules\Objects\Model\Ai;
use Otms\Modules\Objects\Model\Object;
use Otms\Modules\Objects\Model\Template;
use Otms\System\Model\Find;
use Otms\System\Component\Users;

/**
 * Ajax\Task class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Task extends Ajax {
	/**
	 * Удалить группу (проект) задач
	 * 
	 * @param array
	 *    $params["gid"]
	 */
    public function delGroup($params) {
        $gid = $params["gid"];
        
        $this->registry["task"]->delGroup($gid);
    }

    /**
     * Добавить комментарий к задаче
     * 
     * @param array
     *    $params["tid"] - ID задачи
     *    $params["text"] - текст комментария
     *    $params["status"] - статус комментрия
     *    $params["json"] - JSON строка содержит список прикреплённых файлов
     */
    public function addComment($params) {
    	$this->registry["task"]->uid = $this->registry["ui"]["id"];
    	
        $tid = $params["tid"];
        $text = $params["text"];
        $status = $params["status"];
        $post["attaches"] = json_decode($params["json"], true);

        $this->registry["task"]->addComment($tid, $text, $status, $post, false);
        
        $this->registry["task"]->spamUsers("Новый комментарий", $tid);
    }
    
    /**
     * Закрыть задачу
     * 
     * @param array
     *     $params["tid"] - ID задачи
     */
    public function closeTask($params) {
        $tid = $params["tid"];
        
        $this->registry["task"]->closeTask($tid);
        
        $this->registry["task"]->spamUsers("Задача закрыта", $tid);
    }

    /**
     * Удалить черновик
     * 
     * @param array
     *    $params["did"] - ID черновика
     */
    public function delDraft($params) {
    	$did = $params["did"];
    	
    	$this->registry["task"]->delDraft($did);
    }
    
    /**
     * Выбрать сортировку для задач
     * 
     * @param array
     *    $params["sort"]:
     *       "group" - по группе
     *       "obj" - по объектам
     *       "imp" - по приоритету
     *       "type" - по типу
     *       "date" - по дате (по умолчанию)
     *    $params["id"] - ID для сортировки, например, сортировка "по приоритету", ID = 3
     */
    public function setSortMyTt($params) {
    	$sort = $params["sort"];
    	$id = $params["id"];
    	
    	$sortmytt = & $_SESSION["sortmytt"];
    	$sortmytt["sort"] = $sort;
    	$sortmytt["id"] = $id;
    }
    
    /**
     * Список пользователей.
     * Множественный выбор (для ответственных).
     * 
     * @return string (twig render) - .../tt/utree.tpl
     */
	public function getListUsers() {
		$class_users = new Users();
		$tree = $class_users->users_tree();
		return $this->view->render("tt_utree", array("list" => $tree));
    }
    
    /**
     * Список пользователей. 
     * Выбрать можно только одного (делегировать задачу).
     * 
     * @return string (twig render) - .../tt/onlyUtree.tpl
     */
    public function getDelegateUsers() {
    	$class_users = new Users();
    	$tree = $class_users->onlyUsers_tree();
    	return $this->view->render("tt_onlyUtree", array("list" => $tree));
    }
}
?>