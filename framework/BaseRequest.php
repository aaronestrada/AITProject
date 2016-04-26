<?php

namespace framework;


/**
 * Class BaseRequest
 * @package framework
 *
 * Class storing the parameters sent via POST or GET in request
 */
class BaseRequest {
    private $parameters;
    private $isPost;
    private $isAjax;

    /**
     * BaseRequest constructor.
     * @param array $parameters List of parameters to be stored in class
     * @param bool $isPost if is a POST request
     * @param bool $isAjax if is an AJAX request
     */
    public function __construct($parameters = [], $isPost = false, $isAjax = false) {
        $this->parameters = $parameters;
        $this->isPost = $isPost === true ? true : false;
        $this->isAjax = $isAjax === true ? true : false;
    }

    /**
     * Obtain parameter from request
     * @param $name Name of parameter
     * @return mixed|null
     */
    public function getParameter($name) {
        if(isset($this->parameters[$name]))
            return $this->parameters[$name];
        return null;
    }

    /**
     * Obtain whether the request made was by POST or not
     * @return bool TRUE if is POST request
     */
    public function isPostRequest() {
        return $this->isPost;
    }

    /**
     * Obtain whether the request made was by AJAX or not
     * @return bool TRUE if is AJAX request
     */
    public function isAjaxRequest() {
        return $this->isAjax;
    }
}