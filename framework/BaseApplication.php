<?php
namespace framework;

/**
 * Class BaseApplication
 * Object used as orchestrator between controller and requests from the URI
 * 1) Verifies parameters sent in request
 * 2) Calls the controller specified in request
 * @package framework
 */
class BaseApplication {
    private $isPost;
    private $requestParameters;
    private $environment;

    /**
     * Class constructor
     * Verifies request type: POST or GET
     * Set variables in $requestParameters list
     *
     * @param string $environment Environment to set the application
     */
    public function __construct($environment = 'dev') {
        if (count($_POST) > 0) {
            $this->isPost = true;
            foreach ($_POST as $param => $value)
                $this->requestParameters[$param] = $value;
        }

        $this->environment = in_array($environment, ['dev', 'prd']) ? $environment : 'dev';
    }

    /**
     * Run application framework
     */
    public function run() {

        //if is a development enviroment, show errors
        if ($this->environment == 'dev') {
            error_reporting(-1);
            ini_set('display_errors', 'On');
        }

        /**
         * Step 1: Obtain REQUEST URI values
         * For GET requests, it is possible to manage two types of requests:
         * 1) /controller/action/param1/val1/param2/val2/...
         * 2) /controller/action?param1=val1&param2=val2&...
         *
         * On step 5 this verification is made in order to obtain the parameter values according
         * to the different settings.
         */
        $requestURI = trim($_SERVER["REQUEST_URI"]);

        //Step 2: Remove bars from beginning and end if found
        if (substr($requestURI, -1) == '/')
            $requestURI = substr($requestURI, 0, strlen($requestURI) - 1);

        if (substr($requestURI, 0, 1) == '/')
            $requestURI = substr($requestURI, 1, strlen($requestURI));

        $actionRequestName = '';
        $controllerRequestName = '';
        $getParamsList = [];
        $requestList = '';
        $initIndex = 0;

        if ($requestURI == '') {
            $configurationItems = require('../config/config.php');
            if (isset($configurationItems['defaultPage'])) {
                if (isset($configurationItems['defaultPage']['controller']) && isset($configurationItems['defaultPage']['action'])) {
                    $actionRequestName = $configurationItems['defaultPage']['action'];
                    $controllerRequestName = $configurationItems['defaultPage']['controller'];
                }
            }
        } else {
            //Step 3: Obtain list of request by exploding into array the URI
            $requestList = explode('/', $requestURI);

            //Step 4: If 'index.php' is found, ignore it
            if ($requestList[0] == 'index.php')
                $initIndex++;

            //Step 5: Construct controller and action name
            $actionRequestName = isset($requestList[$initIndex + 1]) ? $requestList[$initIndex + 1] : '';

            //Step 5.1: Verify that request call is not in the form /controller/action?param1=value1&param2=value2...
            $actionRequestDivision = explode('?', $actionRequestName);
            if (count($actionRequestDivision) == 2) {
                //Obtain action request name
                $actionRequestName = $actionRequestDivision[0];

                //Obtain list of request parameters
                $paramsList = explode('&', $actionRequestDivision[1]);
                if (count($paramsList) > 0) {
                    foreach ($paramsList as $paramItem) {
                        /*
                         * Since the parameters can have a double equal sign (e.g. variable==),
                         * explode function won't work.  Extract value manually with strpos and substr functions
                         */
                        $equalPosition = strpos($paramItem, '=');

                        $paramName = substr($paramItem, 0, $equalPosition);
                        $paramValue = '';

                        //Verify that parameter value is not empty to subtract value
                        if ($equalPosition + 1 < strlen($paramItem))
                            $paramValue = substr($paramItem, $equalPosition + 1, strlen($paramItem));

                        if ($paramValue !== $paramItem)
                            $getParamsList[$paramName] = urldecode($paramValue);
                    }
                }
            }

            //Get controller name
            $controllerRequestName = isset($requestList[$initIndex]) ? $requestList[$initIndex] : '';
        }

        $controllerName = '\controllers\\' . ucfirst($controllerRequestName) . 'Controller';
        $actionName = 'action' . ucfirst($actionRequestName);

        //Step 6: Verify that class and method exists to invoke
        if (class_exists($controllerName, true)) {
            $controller = new $controllerName();

            //Step 7: verifies that method exists inside the controller
            if (method_exists($controller, $actionName)) {

                //Step 8: validate controller behavior prior to execute action
                $accessValidated = $controller->validateBehavior($actionRequestName);

                if ($accessValidated) {
                    //Step 9: If method exists, construct request parameters and save them in BaseRequest object
                    if (!$this->isPost)
                        if (count($getParamsList) > 0) {
                            foreach ($getParamsList as $paramIndex => $paramValue)
                                $this->requestParameters[$paramIndex] = $paramValue;
                        } else {
                            for ($paramIndex = $initIndex + 2; $paramIndex < count($requestList); $paramIndex += 2)
                                $this->requestParameters[$requestList[$paramIndex]] = isset($requestList[$paramIndex + 1]) ? $requestList[$paramIndex + 1] : null;
                        }

                    //Step 10: save request parameters in BaseRequest class
                    if (count($this->requestParameters) > 0)
                        $controller->setRequest(new BaseRequest($this->requestParameters, $this->isPost));

                    //Step 11: Invoke action from controller
                    $controller->$actionName();
                } else \framework\BaseError::throwMessage(403, 'Forbidden: Action "' . ucfirst($actionRequestName) . '" cannot be accessed by this role.');
            } else \framework\BaseError::throwMessage(404, 'Action "' . ucfirst($actionRequestName) . '" does not exist.');
        } else \framework\BaseError::throwMessage(404, 'Controller "' . ucfirst($controllerRequestName) . '" does not exist.');
    }
}