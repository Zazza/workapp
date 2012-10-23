<?php

/**
 * This file is part of the Workapp project.
*
* (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
*
*/

namespace Otms\System\Model;

use Engine\Model;
use PDO;

/**
 * Validate Model class
 *
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */

class Validate extends Model {
    
	/**
	 * email validate
	 * 
	 * @param string $email
	 * @return NULL
	 * @return string $err
	 */
    public function email($email) {
        $err = null;
        
		if ($email == null) {
			$err = "E-mail не может быть пустым!";
		} else {
    		if ( !preg_match( '/^([\.0-9a-zA-Z_-]+@([a-zA-Z_-][0-9a-zA-Z_-]+\.)+[a-zA-Z]+)$/', $email ) ) {
    			$err = "E-mail введён не верно!";
    		}
        }
        
        if ($err != null) {
            return $err;
        }
    }

    /**
     * login validate
    *
    * @param string $login
    * @return NULL
    * @return string $err
    */
    public function login($login) {
    	$user = new User();
	        $err = null;
	
			if ( !preg_match( '/^[0-9A-Za-z_-]{4,31}$/', $login ) ) {
				$err = 'Логин должен состоять из символов английского алфавита, быть длиной не менее 4 и не более 31 символов! Из специальных символов разрешается использовать символы: "-", "_"';
			}
	        
	        if ($err == null) {
	            if ($user->issetLogin($login)) {
	                $err = "Пользователь с таким логином уже зарегистрирован!";
	            }
	        }
	
	        if ($err != null) {
	            return $err;
	        }

    }
    
    /**
     * password validate
    *
    * @param string $password
    * @return NULL
    * @return string $err
    */
    public function password($password) {
        $err = null;
        
        if ( !preg_match( '/^[0-9A-Za-z_-]{6,31}$/', $password ) ) {
			$err = 'Пароль должен состоять из символов английского алфавита, быть длиной не менее 6 и не более 31 символов! Из специальных символов разрешается использовать символы: "-", "_"';
		}
        
        if ($err != null) {
            return $err;
        }
    }
    
    /**
     * name validate
    *
    * @param string $name
    * @return NULL
    * @return string $err
    */
    public function name($name) {
        $err = null;
        
        if ( !preg_match( '/^[0-9A-Za-zА-Яа-я_-]{1,128}$/ui', $name ) ) {
			$err = 'Поле "Имя" должно содержать от 1 до 128 символов. Из специальных символов разрешается использовать символы: "-", "_"';
		}

        if ($err != null) {
            return $err;
        }
    }
    
    /**
     * soname validate
    *
    * @param string $soname
    * @return NULL
    * @return string $err
    */
    public function soname($soname) {
        $err = null;
        
        if ( !preg_match( '/^[0-9A-Za-zА-Яа-я_-]{1,128}$/ui', $soname ) ) {
			$err = 'Поле "Фамилия" должно содержать от 1 до 128 символов. Из специальных символов разрешается использовать символы: "-", "_"';
		}
        
        if ($err != null) {
            return $err;
        }
    }
}    