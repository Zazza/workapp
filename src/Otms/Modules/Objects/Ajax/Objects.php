<?php

/**
 * This file is part of the Workapp project.
 *
 * Object Module
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Otms\Modules\Objects\Ajax;

use Engine\Modules\Ajax;
use Otms\Modules\Objects\Model;
use Otms\System\Model\Find;

/**
 * Ajax\Objects class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
class Objects extends Ajax {
    
	/**
	 * Удалить группу
	 * 
	 * @param array
	 *    int $params["gid"] - ID группы
	 */
    public function delGroup($params) {
        $gid = $params["gid"];
        
        $this->registry["task"]->delGroup($gid);
    }
    
    /**
     * Удалить шаблон
     * 
     * @param array
     *    int $params["id"] - ID шаблона
     */
    public function delTemplate($params) {
        $id = $params["id"];
        
        $tpl = new Model\Template();
        $tpl->delTemplate($id);
    }
    
    /**
     * Получить поля шаблона
     * 
     * @param array
     *    $params["id"] - ID шаблона
     * @return string (twig render) .../objects/fields.tpl
     */
    public function getTemplateFields($params) {
        $id = $params["id"];
        
        $tpl = new Model\Template();
        $fields = $tpl->getTypeTemplate($id);

        return $this->view->render("objects_fields", array("fields" => $fields));
    }
    
    /**
     * Получить информацию об объекте
     * 
     * @param array
     *    $params["id"] - ID шаблона
     * @return string (twig render) .../objectInfo.tpl
     */
    public function getInfo($params) {
        $id = $params["id"];
        
        if (isset($this->registry["module_mail"])) {
        	$mail = true;
        } else {
        	$mail = false;
        }
        
        $object = new Model\Object();
        $data = $object->getObject($id);

        return $this->view->render("objectInfo", array("data" => $data, "mail" => $mail));
    }
    
    /**
     * Добавить "заметку" в базу знаний
     * 
     * @param array
     *    ы$params["title"] - название заметки
     *    $params["text"] - текст заметки
     *    $params["tags"] - теги
     */
    public function addAdvancedNote($params) {
    	$title = $params["title"];
    	$text = $params["text"];
    	$tags = htmlspecialchars($params["tags"]);
    
    	$advinfo = new Model\Ai();
    
    	$oaid = $advinfo->addAdvanced("0", $title, $text);
    
    	$arr = explode(",", $tags);
    	$arr = array_unique($arr);
    	foreach($arr as $part) {
    		$tag = trim($part);
    		if ($tag != "") {
    			$advinfo->addTags($oaid, $tag);
    		}
    	}
    }

    /**
     * Добавить информацию в базу знаний
     * 
     * @param array
     *    $params["id"] - ID объекта
     *    $params["title"] - название записи
     *    $params["text"] - текст записи
     *    $params["tags"] - теги
     */
    public function addAdvanced($params) {
        $id = $params["id"];
        $title = $params["title"];
        $text = $params["text"];
        $tags = htmlspecialchars($params["tags"]);
        
        $advinfo = new Model\Ai();

        $oaid = $advinfo->addAdvanced($id, $title, $text);
        
        $arr = explode(",", $tags);
		$arr = array_unique($arr);
        foreach($arr as $part) {
            $tag = trim($part);
            if ($tag != "") {
                $advinfo->addTags($oaid, $tag);
            }
        }
    }
    
    /**
     * Удалить информацию из базы знаний
     * 
     * @param array
     *    $params["id"] - ID записи
     */
    public function delAdv($params) {
        $id = $params["id"];
        
        $advinfo = new Model\Ai();
        
        $advinfo->delAdvanced($id);
    }
    
    /**
     * Правка информации из базы знаний
     * 
     * @param array
     *    $params["oid"] - ID объекта
     *    $params["title"] - название записи
     *    $params["text"] - текст записи
     *    $params["tags"] - теги
     */
    public function editAdvanced($params) {
        $oid = $params["oid"];
        $title = $params["title"];
        $text = $params["text"];
        $tags = htmlspecialchars($params["tags"]);
        
        $advinfo = new Model\Ai();
        
        $advinfo->editAdvanced($oid, $title, $text);
        $advinfo->changeTags($oid, $tags);
    }

    /**
     * Удалить форму базы знаний
     * 
     * @param array
     *    $params["id"]
     */
    public function delForm($params) {
    	$id = $params["id"];
    
    	$ai = new Model\Ai();
    
    	$ai->delForm($id);
    }
    
    /**
     * Добавить значение для типа данных селективных полей
     * 
     * @param array
     *    $params["id"] - ID типа данных
     *    $params["name"] - значение
     */
    public function addDataField($params) {
    	$id = $params["id"];
    	$name = htmlspecialchars($params["name"]);
    	
    	$template = new Model\Template();
    	
    	if (mb_substr_count($name, "-")) {
    		$data = explode("-", $name);
    		if (count($data) == 2) {
    			$data[0] = trim($data[0]);
    			$data[1] = trim($data[1]);

    			if ((is_numeric($data[0])) and (is_numeric($data[1]))) {
	    			if ($data[0] < $data[1]) {
	    				$min = $data[0];
	    				$max = $data[1];
	    			} else {
	    				$min = $data[1];
	    				$max = $data[0];
	    			}
	    			
	    			for($i=$min; $i<=$max; $i++) {
	    				$template->addDataTree($id, $i);
	    			}
    			} else {
    				$template->addDataTree($id, $name);
    			}
    		} else {
    			$template->addDataTree($id, $name);
    		}
    	} else {
    		$template->addDataTree($id, $name);
    	}
    }
    
    /**
     * Получить значения для ID типа данных
     * 
     * @param array $params
     *    $params["id"]
     * @return string (twig render) .../objects/datatree.tpl
     */
    public function getDataTree($params) {
    	$id = $params["id"];
    
    	$template = new Model\Template();
    
    	$tree = $template->getDataVals($id);
    
    	return $this->view->render("objects_datatree", array("tree" => $tree));
    }
    
    /**
     * Удалить значение типа данных селективных полей
     * @param array
     *    $params["id"]
     */
    public function delDataCat($params) {
    	$id = $params["id"];
    
    	$template = new Model\Template();
    
    	$template->delDataCat($id);
    }
    
    /**
     * Правка значения типа данных селективных полей
     * 
     * @param array
     *    $params["id"] - ID типа данных
     *    $params["name"] - значение
     */
    public function editDataCat($params) {
    	$id = $params["id"];
    	$name = htmlspecialchars($params["name"]);
    
    	$template = new Model\Template();
    
    	$template->editDataCat($id, $name);
    }
    
    /**
     * Получить одно значение селективных полей
     * 
     * @param array
     *    $params["id"] - ID типа данных
     * @return string
     */
    public function getDataCatName($params) {
    	$id = $params["id"];
    
    	$template = new Model\Template();
    
    	return $template->getDataCatName($id);
    }
    
    /**
     * Удалить все значения типа данных селективных полей
     * 
     * @param array
     *    $params["did"]
     */
    public function rmAllData($params) {
    	$did = $params["did"];
    	
    	$template = new Model\Template();
    	$template->rmAllData($did);
    }
    
    /**
     * Создать новый тип данных селективных полей
     * 
     * @param array
     *    $params["name"] - название типа данных
     */
    public function addDataType($params) {
    	$name = $params["name"];
    	
    	$template = new Model\Template();
    	$template->addDataType($name);
    }
    
    /**
     * Удалить тип данных селективных полей
     * 
     * @param array
     *    $params["id"] - ID типа данных
     */
    public function delDataType($params) {
    	$id = $params["id"];
    	
    	$template = new Model\Template();
    	$template->delDataType($id);
    }
    
    /**
     * Добавить поле для выборки объектов 
     * 
     * @param array
     *    $params["fid"] - ID поля
     * @return string (twig render) .../objects/sortField.tpl
     */
    public function addSortField($params) {
		$fid = $params["fid"];
		
		$tpl = new Model\Template();
        $field = $tpl->getField($fid);
        
		return $this->view->render("objects_sortField", array("field" => $field));
	}
	
	/**
	 * Сохранить отображение формы (координаты полей)
	 * 
	 * @param array
	 *    $params["tid"] - ID формы
	 *    $params["fid"] - ID поля
	 *    $params["x"] - x координата
	 *    $params["y"] - y координата
	 */
	public function setTemplateViewXY($params) {
		$tpl = new Model\Template();
		$tpl->setTemplateViewXY($params["tid"], $params["fid"], $params["x"], $params["y"]);
	}
	
	/**
	 * Сохранить отображение формы (размеры полей)
	 * 
	 * @param array
	 *    $params["tid"] - ID формы
	 *    $params["fid"] - ID поля
	 *    $params["w"] - ширина координата
	 *    $params["h"] - высота координата
	 */
	public function setTemplateViewSize($params) {
		$tpl = new Model\Template();
		$tpl->setTemplateViewSize($params["tid"], $params["fid"], $params["w"], $params["h"]);
	}
	
	/**
	 * Переместить оьъекты в корзину
	 * 
	 * @param array
	 *    $params["json"]
	 */
	public function removeObjs($params) {
		$json = json_decode(stripslashes($params["json"]), true);
		
		$object = new Model\Object();
		
		$data = array();
		foreach($json as $key=>$val) {
			$object->removeObject($key);
		}
	}
	
	/**
	 * Восстановить оьъекты из корзины
	 * 
	 * @param array
	 *    $params["json"]
	 */
	public function repairObjs($params) {
		$json = json_decode(stripslashes($params["json"]), true);
	
		$object = new Model\Object();
	
		$data = array();
		foreach($json as $key=>$val) {
			$object->repairObject($key);
		}
	}
	
	/**
	 * Получить информацию из базы знаний
	 * 
	 * @param array
	 *    $params["id"]
	 * @return string (twig render) .../ai.tpl
	 */
	public function getAIInfo($params) {
		$id = $params["id"];
	
		$ai = new Model\Ai();
	
		$data = $ai->getAdvanced($id);
		$aiinfo = $ai->getaiinfo();
	
		return $this->view->render("ai", array("ai" => $data, "info" => $aiinfo));
	}
	
	/**
	 * Получить форму для базы знаний
	 * 
	 * @param array
	 *    $params["id"]
	 * @return string (twig render) .../kb/formfields.tpl
	 */
	public function getFormFields($params) {
		$id = $params["id"];
		 
		$ai = new Model\Ai();
		 
		$fields = $ai->getForm($id);
		 
		return $this->view->render("kb_formfields", array("fields" => $fields));
	}
}
?>
