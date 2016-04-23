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

    /**
     * BaseRequest constructor.
     * @param array $parameters List of parameters to be stored in class
     */
    public function __construct($parameters = [], $isPost = false) {
        $this->parameters = $parameters;
        $this->isPost = $isPost === true ? true : false;
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
}