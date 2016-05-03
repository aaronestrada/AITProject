<?php

namespace controllers;

use framework\BaseController;
use framework\BaseQuery;
use framework\BaseSession;
use libs\CreditCardValidations;
use libs\JSONProcess;
use models\Document;
use models\Purchase;
use models\PurchaseDocument;
use models\User;
use models\UserDocumentCart;

class SiteController extends BaseController {

    public function behavior() {
        return [
            [
                'permission' => 'allow',
                'actions' => ['cart', 'checkout', 'checkoutprocess'],
                'roles' => ['@']
            ]
        ];
    }


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
            if ($userLoggedIn) {
                $objUser = new User();
                $objUser = $objUser->fetchOne($this->roleAccess->getProperty('id'));

                if ($objUser != null) {
                    $documentsInCart = $objUser->getDocumentCartItems();
                    $purchasedDocuments = $objUser->getPurchasedDocumentItems();
                }
            }

            //Store search field values in session
            $objSession = new BaseSession();
            $objSession->set('searchQuery', ['searchText' => $searchText, 'tags' => $tags]);

            //Show results in view "search"
            $this->setLayout('main_search');
            $this->render('search', [
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

        if (is_numeric($documentId)) {
            $documentItem = new Document();
            $documentItem = $documentItem->fetchOne($documentId);
            if ($documentItem !== null) {
                $documentInCartCount = 0;
                $documentPurchaseCount = 0;

                $userLoggedIn = $this->roleAccess->isLoggedIn();
                if ($userLoggedIn) {
                    $objUser = new User();
                    $objUser = $objUser->fetchOne($this->roleAccess->getProperty('id'));

                    if ($objUser != null) {
                        $userId = $objUser->id;
                        /**
                         * Verify if document is already added in cart
                         * Query:
                         * SELECT COUNT(*) FROM user_document_cart
                         * WHERE document_id = <document_id>
                         * AND user_id = <user_id>;
                         */
                        $documentCartQuery = new BaseQuery();
                        $documentCartQuery->select()
                            ->andWhere(['document_id' => $documentId, 'user_id' => $userId])
                            ->count();

                        $objDocumentCart = new UserDocumentCart();
                        $documentInCartCount = $objDocumentCart->queryAllFromObject($documentCartQuery);

                        if ($documentInCartCount == 0) {
                            $documentPurchaseQuery = new BaseQuery('pr_doc');
                            $documentPurchaseQuery->select()
                                ->join('purchase', 'purch', ['purch.id = pr_doc.purchase_id'])
                                ->andWhere(['pr_doc.document_id' => $documentId, 'purch.user_id' => $userId])
                                ->count();

                            $objPurchaseDocument = new PurchaseDocument();
                            $documentPurchaseCount = $objPurchaseDocument->queryAllFromObject($documentPurchaseQuery);
                        }
                    }
                }

                $this->setLayout('main_search');
                $this->render('overview', [
                    'documentItem' => $documentItem,
                    'userLoggedIn' => $userLoggedIn,
                    'isDocumentInCart' => $documentInCartCount != 0 ? true : false,
                    'isDocumentPurchased' => $documentPurchaseCount != 0 ? true : false
                ]);
            }
        }
        $this->redirect('site/index');

    }

    /**
     * Get documents in cart added by logged in user
     * @return array List of documents
     */
    private function getDocumentsInCart() {
        $userId = $this->roleAccess->getProperty('id');

        $objUser = new User();
        $objUser = $objUser->fetchOne($userId);

        $documentList = [];
        if ($objUser != null)
            $documentList = $objUser->getDocumentsInCart();

        return $documentList;
    }

    /**
     * Show user shopping cart
     */
    public function actionCart() {
        $documentList = $this->getDocumentsInCart();

        $this->setLayout('main_search');
        $this->render('cart', ['documentList' => $documentList]);
    }

    /**
     * Show checkout page
     */
    public function actionCheckout() {
        $documentList = $this->getDocumentsInCart();

        //if documents in cart, show page, otherwise redirect to search page
        if (count($documentList) > 0) {
            $userId = $this->roleAccess->getProperty('id');

            $objUser = new User();
            $objUser = $objUser->fetchOne($userId);

            $this->render('checkout', ['documentList' => $documentList, 'objUser' => $objUser]);
        } else
            $this->redirect('site/index');
    }

    /**
     * Process the checkout request
     */
    public function actionCheckoutprocess() {
        $errorList = [];
        $resultData = ['status' => 'error'];
        if ($this->request->isAjaxRequest() && $this->request->isPostRequest()) {
            $creditCardNumber = $this->request->getParameter('card_number');
            $cvvNumber = $this->request->getParameter('card_cvv');
            $expirationMonth = $this->request->getParameter('exp_month');
            $expirationYear = $this->request->getParameter('exp_year');

            if (CreditCardValidations::validateCreditCardNumber($creditCardNumber) &&
                CreditCardValidations::validateCreditCardCVV($cvvNumber) &&
                CreditCardValidations::validateMonthNumber($expirationMonth) &&
                CreditCardValidations::validateCreditCardYear($expirationYear)
            ) {
                //Valid credit card information, save purchase
                $userId = $this->roleAccess->getProperty('id');

                $objUser = new User();
                $objUser = $objUser->fetchOne($userId);

                if($objUser != null) {
                    //Step 1: Create purchase
                    $objPurchase = new Purchase();
                    $objPurchase->user_id = $objUser->id;
                    $objPurchase->created_at = date('Y-m-d h:i:s');
                    $objPurchase->insert();

                    if($objPurchase->id != null) {
                        $purchaseId = $objPurchase->id;
                        //Step 2: Associate purchase with documents in cart
                        $documentList = $objUser->getDocumentsInCart();

                        if(count($documentList) > 0) {
                            foreach ($documentList as $documentItem) {
                                $objPurchaseDocument = new PurchaseDocument();
                                $objPurchaseDocument->document_id = $documentItem->id;
                                $objPurchaseDocument->purchase_id = $purchaseId;

                                /**
                                 * Since the price of a document can change over time, the value has to be
                                 * stored in each purchase to have a control of the incomes
                                 */
                                $objPurchaseDocument->price = $documentItem->price;

                                //Save document into purchase
                                $objPurchaseDocument->insert();
                            }

                            //Step 3: Erase document cart
                            $objDocumentCartQuery = new BaseQuery();
                            $objDocumentCartQuery->select()
                                ->andWhere(['user_id' => $objUser->id]);

                            $objDocumentCart = new UserDocumentCart();
                            $objCartList = $objDocumentCart->queryAllFromObject($objDocumentCartQuery);

                            if($objCartList != null)
                                foreach($objCartList as $cartItem) {
                                    $cartItem->delete();
                                }
                        }
                        else array_push($errorList, 'error_cart_empty');
                    }
                } else array_push($errorList, 'error_user_not_found');
            } else array_push($errorList, 'error_credit_card_invalid');
        } else array_push($errorList, 'connection_refused');

        if (count($errorList) == 0)
            $resultData['status'] = 'ok';
        else
            $resultData['errors'] = $errorList;

        $this->hasLayout(false);

        JSONProcess::returnJsonOutput($resultData);
        exit();
    }
}