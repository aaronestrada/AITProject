<?php
namespace framework;

class BaseController {
    //BaseView object
    private $view;

    //BaseRequest object for controller
    protected $request;

    public function __construct() {
        $configParams = require_once('../config/config.php');

        //Create new view object that will be called by BaseApplication to render pages
        $this->view = new BaseView(isset($configParams['defaultLayout']) ? $configParams['defaultLayout'] : 'main');

        //Create new BaseRequest object to store POST or GET values
        $this->request = new BaseRequest();
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
     * Render specified view
     * @param $view View name
     * @param array $params List of parameters sent to the view
     */
    public function render($view, $params = []) {
        $controllerViewFolder = strtolower(str_replace('controllers\\', '', str_replace('Controller', '', get_class($this))));
        $this->view->render($controllerViewFolder, $view, $params);
    }
}