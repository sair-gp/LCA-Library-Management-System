<?php

class UserSession{

    public function constructor(){
        session_start();
    }

    public function setCurrentUser($cedula){
        $_SESSION['cedula'] = $cedula;
    }

    public function getCurrentUser(){
        return $_SESSION['cedula'];
    }

    public function closeSession(){
        session_unset();
        session_destroy();
    }
}

?>