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

    private $layoutVariables;

    public function __construct($layoutName = 'main') {
        $this->layoutName = $layoutName;
        $this->hasLayout = (trim($layoutName) !== '');
        $this->layoutVariables = [];
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
     * Set variable for layout
     * @param $variableName Variable name
     * @param $variableValue Value for the variable
     */
    public function setLayoutVariable($variableName, $variableValue) {
        $this->layoutVariables[$variableName] = $variableValue;
    }

    /**
     * Render layout with obtained content
     * @param $viewContent Content obtained
     * @param array $params Params sent to layout
     */
    public function renderLayout($viewContent, $params = [], $frameworkVariables = []) {
        if($this->hasLayout === true) {
            $layoutPathFile = 'views/layouts/' . $this->layoutName . '.php';

            if (is_file(realpath(__DIR__ . '/../' . $layoutPathFile))) {
                //include variables from list as variables for view
                foreach ($params as $paramItem => $value)
                    ${$paramItem} = $value;

                //Set framework variables to be accessed in the view
                foreach($frameworkVariables as $variableItem => $variableValue)
                    $this->$variableItem = $variableValue;

                //set layout content and include file
                $layoutContent = $viewContent;
                include_once('../' . $layoutPathFile);

                unset($layoutContent);
                foreach($params as $paramItem => $value)
                    unset(${$paramItem});

                //Unset framework variables from the view
                foreach(array_keys($frameworkVariables) as $variableItem)
                    unset($this->$variableItem);
            } else
                \framework\BaseError::throwMessage(404, 'Layout ' . $this->layoutName . ' does not exist');             
        }
        else echo $viewContent;

        //After rendering layout, end application
        exit();
    }

    /**
     * Render specified view
     * @param $controller Controller name
     * @param $view View name
     * @param array $params List of parameters sent to the view
     */
    public function render($controller, $view, $params = [], $frameworkVariables = []) {
        $viewPathFile = 'views/' . $controller . '/' . $view . '.php';
        if(is_file(realpath(__DIR__ . '/../' . $viewPathFile))) {
            //include variables from list as variables for view
            foreach($params as $paramItem => $value)
                ${$paramItem} = $value;

            //Set framework variables to be accessed in the view
            foreach($frameworkVariables as $variableItem => $variableValue)
                $this->$variableItem = $variableValue;

            /*
             * Include specified view.  Functions ob_start and ob_end_clean are used to render the content of
             * the view without displaying it in screen.  The content must be displayed at the moment of rendering
             * the layout content.  Inside the layout the variable $viewContent is used to display whatever content
             * is inside each view.
             */
            ob_start();
            require_once('../' . $viewPathFile);
            $viewContent = ob_get_contents();
            ob_end_clean();

            //unset variables after used
            foreach($params as $paramItem => $value)
                unset(${$paramItem});

            //Unset framework variables from the view
            foreach(array_keys($frameworkVariables) as $variableItem)
                unset($this->$variableItem);

            //render layout with view content
            $this->renderLayout($viewContent, $this->layoutVariables, $frameworkVariables);
        }
        else
            \framework\BaseError::throwMessage(404, 'View ' . $controller . '/' . $view . ' does not exist');
    }

}