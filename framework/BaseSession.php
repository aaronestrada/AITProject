<?php

namespace framework;

/**
 * Class BaseSession
 * @package framework
 *
 * This class implements the function of the session storage in server
 * Makes use of the $_SESSION variable from PHP
 */
class BaseSession {

    //Set session variable
    public function set($variable, $value) {
        $_SESSION[$variable] = $value;
    }

    /**
     * Obtain session variable
     * @param $variable Variable to be obtained
     * @return null
     */
    public function get($variable) {
        if(isset($_SESSION[$variable]))
            return $_SESSION[$variable];
        return null;
    }

    /**
     * Remove a variable stored in session
     * @param $variable Name of the variable
     */
    public function remove($variable) {
        if(isset($_SESSION[$variable]))
            unset($_SESSION[$variable]);
    }

    //BaseSession constructor
    public function __construct() {
        //Verifies that session has not been set
        if (session_status() == PHP_SESSION_NONE)
            session_start();
    }
}