<?php

namespace framework;

/**
 * Class BaseApplication
 * Object used as orchestrator
 * 1) Verifies parameters sent in request
 * 2) Calls the controller specified in request
 * @package framework
 */
class BaseApplication {
    private $isPost;
    private $requestParameters;

    /**
     * Class constructor
     * Verifies request type: POST or GET
     * Set variables in $requestParameters list
     */
    public function __construct() {
        if(count($_POST) > 0) {
            $this->isPost = true;
            foreach ($_POST as $param => $value)
                $this->requestParameters[$param] = $value;
        }
    }

    /**
     * Run application framework
     */
    public function run() {
        //Step 1: Obtain REQUEST URI values
        $requestURI = $_SERVER["REQUEST_URI"];

        //Step 2: Remove bars from beginning and end if found
        if(substr($requestURI, -1) == '/')
            $requestURI = substr($requestURI, 0, strlen($requestURI) - 1);

        if(substr($requestURI, 0, 1) == '/')
            $requestURI = substr($requestURI, 1, strlen($requestURI));

        //Step 3: Obtain list of request by exploding into array the URI
        $requestList = explode('/', $requestURI);

        //Step 4: If 'index.php' is found, ignore it
        $initIndex = 0;
        if($requestList[0] == 'index.php')
            $initIndex++;

        //Step 5: Construct controller and action name
        $controllerName = '\controllers\\' . ucfirst($requestList[$initIndex]) . 'Controller';
        $actionName = 'action' . ucfirst($requestList[$initIndex + 1]);

        //Step 6: Verify that class and method exists to invoke
        if(class_exists($controllerName, true)) {
            $controller = new $controllerName();

            if(method_exists($controller, $actionName)) {

                //Step 7: If method exists, construct request parameters and save them in BaseRequest object
                if (!$this->isPost)
                    for ($paramIndex = $initIndex + 2; $paramIndex < count($requestList); $paramIndex += 2)
                        $this->requestParameters[$requestList[$paramIndex]] = $requestList[$paramIndex + 1];

                if (count($this->requestParameters) > 0)
                    $controller->setRequest(new BaseRequest($this->requestParameters));

                //Step 8: Invoke action from controller
                $controller->$actionName();
            }
            else
                die('Action ' . ucfirst($requestList[$initIndex + 1]) . ' does not exist.');
        }
        else
            die('Controller ' . ucfirst($requestList[$initIndex]) . ' does not exist.');
    }
}