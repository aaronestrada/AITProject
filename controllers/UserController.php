<?php

namespace controllers;

class UserController extends \framework\BaseController {

    public function actionIndex() {
        $objDocument = new \models\Document();
        $documentList = $objDocument->getAllDocuments();
        print_r($this->request->getParameter('num'));
        $this->render('index', ['documentList' => $documentList]);
    }

    public function actionList() {
        $this->render('list');
    }
}