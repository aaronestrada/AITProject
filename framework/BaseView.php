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

    private $cssFiles = [];
    private $jsStartFiles = [];
    private $jsEndFiles = [];

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
     * @param array $frameworkVariables List of framework variables to be used inside the layouts
     * @return Rendered content
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

                /**
                 * Include the layout view.  The content of the sent view is stored in the variable $layoutContent
                 * which has to be included inside each layout file to be displayed correctly.
                 * As last step, return the rendered content from the output streaming (ob_start and ob_end_clean).
                 *
                 * If the variable is displayed on screen or stored as variable is checked outside this method.
                 */
                ob_start();
                include_once('../' . $layoutPathFile);
                $layoutViewContent = ob_get_contents();
                ob_end_clean();

                unset($layoutContent);
                foreach($params as $paramItem => $value)
                    unset(${$paramItem});

                //Unset framework variables from the view
                foreach(array_keys($frameworkVariables) as $variableItem)
                    unset($this->$variableItem);

                return $layoutViewContent;
            } else
                \framework\BaseError::throwMessage(404, 'Layout ' . $this->layoutName . ' does not exist');             
        }
        else return $viewContent;
    }

    /**
     * Render specified view
     * @param $controller Controller name
     * @param $view View name
     * @param array $params List of parameters sent to the view
     * @param array $frameworkVariables List of framework set variables to be used inside the views / layouts
     * @return rendered content
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
            return $this->renderLayout($viewContent, $this->layoutVariables, $frameworkVariables);
        }
        else
            \framework\BaseError::throwMessage(404, 'View ' . $controller . '/' . $view . ' does not exist');
    }

    /**
     * Add a JS script file for displaying it into views or layouts
     * @param $scriptFile Script file path (local or global)
     * @param int $jsPosition Position to store de JS (START or END)
     */
    public function addScript($scriptFile, $jsPosition = JS_POSITION_END) {
        if(!in_array($jsPosition, [JS_POSITION_START, JS_POSITION_END]))
            $jsPosition = JS_POSITION_END;

        switch($jsPosition) {
            case JS_POSITION_START:
                if(!in_array($scriptFile, $this->jsStartFiles))
                    array_push($this->jsStartFiles, $scriptFile);
                break;
            case JS_POSITION_END:
                if(!in_array($scriptFile, $this->jsEndFiles))
                    array_push($this->jsEndFiles, $scriptFile);
                break;
        }
    }

    /**
     * Get list of JS scripts, given a position
     * @param int $jsPosition Position of list to return
     * @return array List of set JS scripts
     */
    public function getScripts($jsPosition = JS_POSITION_END) {
        switch($jsPosition) {
            case JS_POSITION_START:
                return $this->jsStartFiles;
                break;
            case JS_POSITION_END:
                return $this->jsEndFiles;
                break;
        }
        return [];
    }

    /**
     * Add a CSS file to be included in views or layouts
     * @param $cssFile CSS script file path (global or local)
     */
    public function addCSSScript($cssFile) {
        if(!in_array($cssFile, $this->cssFiles))
            array_push($this->cssFiles, $cssFile);
    }

    /**
     * Return list of CSS script files
     * @return array List of CSS files
     */
    public function getCSSScripts() {
        return $this->cssFiles;
    }
    
}