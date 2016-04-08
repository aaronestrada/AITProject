<?php

namespace controllers;

use models\Tag;

class TagController extends \framework\BaseController {

    public function actionIndex() {
        $objTag = new Tag();
        $tagList = $objTag->fetchAll();
        $this->render('index', ['tagList' => $tagList]);
    }

    public function actionList() {
        /* Session use example
        $this->session->set('abc', 'hola');
        $this->session->get('abc');
        */

        /* Layout activate example
        $this->hasLayout(true);
        */

        /* Delete tag example
        $objTag = new Tag();
        $objTag = $objTag->fetchOne(3);

        if($objTag !== null)
            $objTag->delete();
        */
    }
}