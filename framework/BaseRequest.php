<?php
/**
 * Created by PhpStorm.
 * User: aarone
 * Date: 19/03/16
 * Time: 11:11 PM
 */

namespace framework;


class BaseRequest {
    public function __construct($parameters) {
        $this->parameters = $parameters;
    }

    public function getParam($name) {
        if(isset($this->parameters[$name]))
            return $this->parameters[$name];
        return null;
    }
}