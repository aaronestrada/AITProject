<?php
namespace framework;

/**
 * Class BaseController
 * @package framework
 *
 * This is the main base controller, used by BaseApplication class in order to make calls
 * of actions specified in each child instance of this class.
 *
 * BaseController functions as a bridge between components of the framework:
 * - Session
 * - Role access
 * - Views
 * - Request parameters
 */
class BaseController {
    //BaseView object
    private $view;

    //BaseRequest object for controller
    protected $request;

    //BaseSession $session object for controller
    protected $session;
    
    protected $roleAccess;

    /**
     * BaseController constructor.
     * Initialize view, request and session objects
     */
    public function __construct() {
        $configParams = require('../config/config.php');

        //Create new view object that will be called by BaseApplication to render pages
        $this->view = new BaseView(isset($configParams['defaultLayout']) ? $configParams['defaultLayout'] : 'main');

        //Create new BaseRequest object to store POST or GET values
        $this->request = new BaseRequest();

        //Create new BaseSession object to manage sessions in application
        $this->session = new BaseSession();

        /**
         * Get BaseRoleAccess object to manage access to controllers and actions.  Instead of generating a new object,
         * it is necessary to obtain it from session.  If the object does not exist in session, create a new one
         * and store it
         */
        $this->roleAccess = BaseRoleAccess::getInstance($this->session);
    }

    /**
     * Specify if BaseView object will render layout or not
     * @param $hasLayout
     */
    public function hasLayout($hasLayout) {
        $this->view->hasLayout($hasLayout);
    }

    /**
     * Set layout name (stored in /views/layouts folder)
     * @param $layoutName Layout name (without .php extension)
     */
    public function setLayout($layoutName) {
        $this->view->setLayoutName($layoutName);
    }

    /**
     * Set request object for obtaining parameters
     * @param $objectRequest
     */
    public function setRequest($objectRequest) {
        $this->request = $objectRequest;
    }

    /**
     * Set variable for layout
     * @param $variableName Variable name
     * @param $variableValue Value for the variable
     */
    public function setLayoutVariable($variableName, $variableValue) {
        $this->view->setLayoutVariable($variableName, $variableValue);
    }

    /**
     * Render specified view
     * @param $view View name
     * @param array $params List of parameters sent to the view
     * @param bool $outputContent Define wheter to render the output view or not
     * @return Rendered view content
     */
    public function render($view, $params = [], $outputContent = true) {
        $frameworkVariables = [
            'request' => $this->request,
            'session' => $this->session,
            'roleAccess' => $this->roleAccess,
            'view' => $this->view
        ];

        $controllerViewFolder = strtolower(str_replace('controllers\\', '', str_replace('Controller', '', get_class($this))));
        
        //Get view content
        $renderedContent = $this->view->render($controllerViewFolder, $view, $params, $frameworkVariables);

        //if render output, echo and stop the application
        if($outputContent === true) {
            echo $renderedContent;
            exit();
        }

        //Otherwise, return string of rendered view
        return $renderedContent;
    }

    /**
     * Protected function: behavior structure of a controller
     * @return array
     */
    protected function behavior() {
        return [];
    }

    /**
     * Validate behavior of the controller.  Verifies the access to a controller action using the
     * BaseRoleAccess object set in session
     * @param $actionName Action name to be validated
     * @return bool Whether the validation is successful or not
     */
    public function validateBehavior($actionName) {
        return $this->roleAccess->validateBehavior($actionName, $this->behavior());
    }

    /**
     * Redirect to another page via Controller
     * @param $redirectPage Page to redirect
     */
    public function redirect($redirectPage) {
        header("Location: /" . $redirectPage);
        exit();
    }

    /**
     * Function executed before executing the called action
     * Implementing this behavior is done on each BaseController child
     */
    public function beforeAction() {}

    /**
     * Function executed after executing the called action
     * Implementing this behavior is done on each BaseController child
     */
    public function afterAction() {}
}