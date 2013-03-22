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
			$err = "E-mail can't be empty!";
		} else {
    		if ( !preg_match( '/^([\.0-9a-zA-Z_-]+@([a-zA-Z_-][0-9a-zA-Z_-]+\.)+[a-zA-Z]+)$/', $email ) ) {
    			$err = "E-mail wrong!";
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
				$err = 'Login has to consist of symbols of the English alphabet, to be not less than 4 and no more than 31 symbols long! From special symbols it is allowed to use symbols: "-", "_"';
			}
	        
	        if ($err == null) {
	            if ($user->issetLogin($login)) {
	                $err = "The user with such login is already registered!";
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
			$err = 'The password has to consist of symbols of the English alphabet, to be not less than 6 and no more than 31 symbols long! From special symbols it is allowed to use symbols: "-", "_"';
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
			$err = 'The field "Name" has to contain from 1 to 128 symbols. From special symbols it is allowed to use symbols: "-", "_"';
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
			$err = 'The field "Surname" has to contain from 1 to 128 symbols. From special symbols it is allowed to use symbols: "-", "_"';
		}
        
        if ($err != null) {
            return $err;
        }
    }
}    