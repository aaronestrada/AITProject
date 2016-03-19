<?php

namespace controllers;

class UserController extends \framework\BaseController {

    public function actionIndex() {
        $objDocument = new \models\Document();
        $documentList = $objDocument->getAllDocuments();
        print_r($this->request->getParam('num'));

        $this->render('index', ['documentList' => $documentList]);
    }
}