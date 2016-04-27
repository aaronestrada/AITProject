<?php

namespace controllers;

use framework\BaseController;
use framework\BaseQuery;
use models\Document;
use models\User;

class SiteController extends BaseController {
    /*
    public function behavior() {
        return [
            [
                'permission' => 'allow',
                'actions' => ['*'],
                'roles' => ['@']
            ]
        ];
    }
    */

    /**
     * Show initial page
     */
    public function actionIndex() {
        $this->render('index');
    }

    /**
     * Perform search of documents
     */
    public function actionSearch() {
        $searchText = trim($this->request->getParameter('searchtext'));
        $tags = trim($this->request->getParameter('tags'));
        $userLoggedIn = $this->roleAccess->isLoggedIn();

        //Verify if request has at least one of the form fields to make the search in this page
        if (($searchText != '') || ($tags != '')) {
            //Step 1: Create new BaseQuery object

            $documentQuery = new BaseQuery('doc');

            //Join left with "document_tag" and "tag" tables
            $documentQuery->select()
                ->joinLeft('document_tag', 'dt', ['doc.id = dt.document_id'])
                ->joinLeft('tag', 't', ['t.id = dt.tag_id']);

            //Add search text if not empty to "name" and "description" fields
            if ($searchText != '')
                $documentQuery->orWhere([
                    'doc.name' => $searchText,
                    'doc.description' => $searchText
                ], 'LIKE', 'OR');

            //If tags are not empty, divide by the comma and add an IN where clause
            if ($tags != '') {
                $tagList = explode(',', $tags);
                if (count($tagList) > 0)
                    $documentQuery->andInWhere('t.name', $tagList);
            }

            //Obtain only active documents, order by "created_at" and "name" fields
            $documentQuery->andWhere(['doc.status' => 1])
                ->order(['doc.created_at' => 'DESC', 'doc.name' => 'ASC']);

            //Obtain document list with made query
            $objDocument = new Document();
            $documentList = $objDocument->queryAllFromObject($documentQuery);

            //Obtain list of documents added to the cart and purchased documents for a user
            $documentsInCart = [];
            $purchasedDocuments = [];
            if($userLoggedIn) {
                $objUser = new User();
                $objUser = $objUser->fetchOne($this->roleAccess->getProperty('id'));

                if($objUser != null) {
                    $documentsInCart = $objUser->getDocumentCartItems();
                    $purchasedDocuments = $objUser->getPurchasedDocumentItems();
                }
            }

            //Show results in view "search"
            $this->render('search', [
                'searchText' => $searchText,
                'tags' => $tags,
                'documentList' => $documentList != null ? $documentList : [],
                'documentsInCart' => $documentsInCart,
                'purchasedDocuments' => $purchasedDocuments,
                'userLoggedIn' => $userLoggedIn
            ]);
        } else
            //if no parameters are found, redirect to the index page
            $this->redirect('site/index');
    }

    public function actionOverview() {
        $documentId = $this->request->getParameter('id');

        if(is_numeric($documentId)) {
            $documentItem = new Document();
            $documentItem = $documentItem->fetchOne($documentId);
            if($documentItem !== null) {
                $this->render('overview', [
                    'documentItem' => $documentItem
                ]);
            }
        }
        $this->redirect('site/index');

    }
}