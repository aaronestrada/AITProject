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
        if(count($_POST) > 0) {
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
        if($this->environment == 'dev') {
            error_reporting(-1);
            ini_set('display_errors', 'On');
        }

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
        $actionRequestName = isset($requestList[$initIndex + 1]) ? $requestList[$initIndex + 1] : '';
        $controllerRequestName = isset($requestList[$initIndex]) ? $requestList[$initIndex] : '';

        $controllerName = '\controllers\\' . ucfirst($controllerRequestName) . 'Controller';
        $actionName = 'action' . ucfirst($actionRequestName);

        //Step 6: Verify that class and method exists to invoke
        if(class_exists($controllerName, true)) {
            $controller = new $controllerName();

            //Step 7: verifies that method exists inside the controller
            if(method_exists($controller, $actionName)) {

                //Step 8: validate controller behavior prior to execute action
                $accessValidated = $controller->validateBehavior($actionRequestName);

                if($accessValidated) {
                    //Step 9: If method exists, construct request parameters and save them in BaseRequest object
                    if (!$this->isPost)
                        for ($paramIndex = $initIndex + 2; $paramIndex < count($requestList); $paramIndex += 2)
                            $this->requestParameters[$requestList[$paramIndex]] = isset($requestList[$paramIndex + 1]) ? $requestList[$paramIndex + 1] : null;

                    //Step 10: save request parameters in BaseRequest class
                    if (count($this->requestParameters) > 0)
                        $controller->setRequest(new BaseRequest($this->requestParameters));

                    //Step 11: Invoke action from controller
                    $controller->$actionName();
                }
                else \framework\BaseError::throwMessage(403, 'Forbidden: Action "' . ucfirst($actionRequestName) . '" cannot be accessed by this role.');
            }
            else \framework\BaseError::throwMessage(404, 'Action "' . ucfirst($actionRequestName) . '" does not exist.');
        }
        else \framework\BaseError::throwMessage(404, 'Controller "' . ucfirst($controllerRequestName) . '" does not exist.');
    }
}