<?php
namespace framework;

/**
 * Class BaseView
 * @package framework
 *
 * Class to manage the views for the application
 * Makes use of ob_start and ob_flush to get content from views.
 * Possibility to define different layouts.
 */
class BaseView {
    //whether the display uses a layout or not
    private $hasLayout;

    //layout name, stored in folder /views/layouts/
    private $layoutName;

    public function __construct($layoutName = 'main') {
        $this->layoutName = $layoutName;
        $this->hasLayout = (trim($layoutName) !== '');
    }

    /**
     * Change layout
     * @param $layoutName Layout name, stored in folder /views/layouts
     */
    public function setLayoutName($layoutName) {
        $this->layoutName = $layoutName;
    }

    /**
     * Change layout status, i.e. whether to display it or not
     * @param $layoutStatus
     */
    public function hasLayout($layoutStatus) {
        $this->hasLayout = $layoutStatus;
    }

    /**
     * Render layout with obtained content
     * @param $viewContent Content obtained
     * @param array $params Params sent to layout
     */
    public function renderLayout($viewContent, $params = []) {
        if($this->hasLayout === true) {
            $layoutPathFile = 'views/layouts/' . $this->layoutName . '.php';

            if (is_file(realpath(__DIR__ . '/../' . $layoutPathFile))) {
                //include variables from list as variables for view
                foreach ($params as $paramItem => $value)
                    ${$paramItem} = $value;

                //set layout content and include file
                $layoutContent = $viewContent;
                include_once('../' . $layoutPathFile);

                unset($layoutContent);
                foreach($params as $paramItem => $value)
                    unset(${$paramItem});
            } else
                die('Error: layout ' . $this->layoutName . ' does not exist');
        }
        else echo $viewContent;
    }

    /**
     * Render specified view
     * @param $controller Controller name
     * @param $view View name
     * @param array $params List of parameters sent to the view
     */
    public function render($controller, $view, $params = []) {
        $viewPathFile = 'views/' . $controller . '/' . $view . '.php';
        if(is_file(realpath(__DIR__ . '/../' . $viewPathFile))) {
            //include variables from list as variables for view
            foreach($params as $paramItem => $value)
                ${$paramItem} = $value;

            //include specified view
            ob_start();
            require_once('../' . $viewPathFile);
            $viewContent = ob_get_contents();
            ob_end_clean();

            //unset variables after used
            foreach($params as $paramItem => $value)
                unset(${$paramItem});

            //render layout with view content
            $this->renderLayout($viewContent);
        }
        else
            die('Error: view ' . $controller . '/' . $view . ' does not exist');
    }

}