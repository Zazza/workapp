<?php
class Model_Validate extends Engine_Model {
    
    public $contactname;
    public $contactsoname;
    
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

    public function login($login) {
    	$user = new Model_User();
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
    
    public function password($password) {
        $err = null;
        
        if ( !preg_match( '/^[0-9A-Za-z_-]{6,31}$/', $password ) ) {
			$err = 'Пароль должен состоять из символов английского алфавита, быть длиной не менее 6 и не более 31 символов! Из специальных символов разрешается использовать символы: "-", "_"';
		}
        
        if ($err != null) {
            return $err;
        }
    }
    
    public function name($name) {
        $err = null;
        
        if ( !preg_match( '/^[0-9A-Za-zА-Яа-я_-]{1,128}$/ui', $name ) ) {
			$err = 'Поле "Имя" должно содержать от 1 до 128 символов. Из специальных символов разрешается использовать символы: "-", "_"';
		}

        if ($err != null) {
            return $err;
        }
    }
    
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
?>