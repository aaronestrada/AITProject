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

    /**
     * BaseRequest constructor.
     * @param array $parameters List of parameters to be stored in class
     */
    public function __construct($parameters = []) {
        $this->parameters = $parameters;
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
}