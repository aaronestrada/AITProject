<?php
namespace framework;

class BaseController {
    private $objectView;
    private $hasLayout;
    protected $request;

    public function __construct() {
        $this->objectView = new BaseView();
        $this->request = new BaseRequest();
    }

    public function hasLayout($hasLayout) {
        $this->hasLayout = $hasLayout;
    }

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
        $this->objectView->hasLayout($this->hasLayout);
        $this->objectView->render($controllerViewFolder, $view, $params);
    }
}