<?php

namespace controllers;


use framework\BaseController;
use framework\BaseQuery;
use libs\JSONProcess;
use models\Document;
use models\PurchaseDocument;
use models\UserDocumentCart;

class DocumentController extends BaseController {
    public function behavior() {
        //Allow access to all actions to logged in roles
        return [
            [
                'permission' => 'allow',
                'actions' => ['*'],
                'roles' => ['@']
            ]
        ];
    }

    /**
     * Add document to / remove document from cart.
     * The method verifies that the document exists, checks if the document is already
     * in the cart and validates that is has not been purchased before.
     *
     * The method validates that is an AJAX+POST call in order to be executed,
     * otherwise it will return a general error.
     */
    public function actionTogglecart() {
        $errorList = [];
        $resultData = ['status' => 'error'];
        if ($this->request->isAjaxRequest() && $this->request->isPostRequest()) {
            $userId = $this->roleAccess->getProperty('id');

            $documentId = $this->request->getParameter('document_id');
            $toggleAction = $this->request->getParameter('action');

            //Step 1: Verify that document exists and is active

            /**
             * Create query:
             * SELECT * FROM document
             * WHERE id = <document_id>
             * AND status_id = <document_status_active>
             * LIMIT 1;
             */
            $documentQuery = new BaseQuery();
            $documentQuery->select()
                ->andWhere(['id' => $documentId, 'status' => DOCUMENT_STATUS_ACTIVE]);

            $objDocument = new Document();
            $objDocument = $objDocument->queryOneFromObject($documentQuery);

            if ($objDocument != null) {
                //Step 2: Check if document is in cart
                /**
                 * Create query:
                 * SELECT COUNT(*) FROM user_document_cart
                 * WHERE user_id = <user_id> AND document_id = <document_id>;
                 */
                $documentId = $objDocument->id;
                $documentInCartQuery = new BaseQuery();
                $documentInCartQuery->select()
                    ->andWhere(['user_id' => $userId, 'document_id' => $documentId])
                    ->count();

                $objUserDocumentCart = new UserDocumentCart();
                $documentCartCount = $objUserDocumentCart->queryAllFromObject($documentInCartQuery);

                /**
                 * Step 3: In the case the user is adding to the cart, verify if is not in the purchased items
                 */
                if ($toggleAction == 'add_to_cart') {
                    if ($documentCartCount == 0) {
                        //Step 3.1: Verify that document is not already in purchased items

                        /**
                         * Create query:
                         * SELECT COUNT(*) FROM purchase_document pr_doc
                         * JOIN purchase purch ON pr_doc.purchase_id = purch.id
                         * WHERE pr_doc.document_id = <document_id> AND purch.user_id = <user_id>;
                         */
                        $purchaseDocumentQuery = new BaseQuery('pr_doc');
                        $purchaseDocumentQuery->select()
                            ->join('purchase', 'purch', ['purch.id = pr_doc.purchase_id'])
                            ->andWhere(['purch.user_id' => $userId, 'pr_doc.document_id' => $documentId])
                            ->count();

                        $objPurchaseDocument = new PurchaseDocument();
                        $purchaseDocumentCount = $objPurchaseDocument->queryAllFromObject($purchaseDocumentQuery);

                        if ($purchaseDocumentCount == 0) {
                            //Step 3.2: if document not found in cart and in purchased items, add to cart
                            $newUserDocumentCart = new UserDocumentCart();
                            $newUserDocumentCart->document_id = $documentId;
                            $newUserDocumentCart->user_id = $userId;
                            $newUserDocumentCart->insert();
                        } else array_push($errorList, 'document_already_purchased');
                    } else array_push($errorList, 'document_already_in_cart');
                }
                else {
                    /**
                     * Step 4: In the case the user is removing from cart, verify that is in database
                     * to delete the item
                     */
                    if ($documentCartCount > 0) {
                        $objDocumentToRemove = new UserDocumentCart();
                        $objDocumentToRemove = $objDocumentToRemove->fetchOne(['document_id' => $documentId, 'user_id' => $userId]);
                        if($objDocumentToRemove != null)
                            $objDocumentToRemove->delete();
                    }
                    else array_push($errorList, 'document_not_in_cart');
                }
            } else array_push($errorList, 'document_not_found');
        } else array_push($errorList, 'connection_refused');

        if (count($errorList) == 0)
            $resultData['status'] = 'ok';

        $this->hasLayout(false);
        $resultData['alertHtml'] = $this->render('partial/toggleCartAlert', ['errorList' => $errorList, 'toggleAction' => $toggleAction], false);

        JSONProcess::returnJsonOutput($resultData);
        exit();
    }
}