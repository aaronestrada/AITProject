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
use models\Tag;
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
     * Execute the function before executing a specified action.
     * To set the shopping list quantity of documents in cart, it is necessary
     * to execute this action and store the value in a variable passed to the layouts
     */
    public function beforeAction() {
        $documentsInCartCount = 0;
        if ($this->roleAccess->isLoggedIn()) {
            $objUser = new User();
            $objUser = $objUser->fetchOne($this->roleAccess->getProperty('id'));

            if ($objUser != null)
                $documentsInCartCount = $objUser->getDocumentCartItems(true);
        }
        $this->setLayoutVariable('documentInCartCount', $documentsInCartCount);
    }
    
    /**
     * Show initial page
     */
    public function actionIndex() {
        $this->setLayoutVariable('hideHomeLink', true);
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
                ->distinct()
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

            //Obtain list of tags
            $objTagQuery = new BaseQuery();
            $objTagQuery->select()
                ->order(['name' => 'ASC']);

            $objTag = new Tag();
            $objTagList = $objTag->queryAllFromObject($objTagQuery);

            //Store search field values in session
            $objSession = new BaseSession();
            $objSession->set('searchQuery', ['searchText' => $searchText, 'tags' => $tags]);

            //Show results in view "search"
            $this->setLayout('main_search');
            $this->render('search', [
                'documentList' => $documentList != null ? $documentList : [],
                'documentsInCart' => $documentsInCart,
                'purchasedDocuments' => $purchasedDocuments,
                'userLoggedIn' => $userLoggedIn,
                'tagList' => $objTagList
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
     * @param bool $listOnly If it is required only the list with IDs
     * @return array List of documents
     */
    private function getDocumentsInCart($listOnly = false) {
        $userId = $this->roleAccess->getProperty('id');

        $objUser = new User();
        $objUser = $objUser->fetchOne($userId);

        $documentList = [];
        if ($objUser != null)
            if($listOnly === false)
                $documentList = $objUser->getDocumentsInCart();
            else
                $documentList = $objUser->getDocumentCartItems();

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
        $documentIDList = $this->getDocumentsInCart(true);

        /**
         * Store the documents from the shopping cart at the moment the user starts the checkout process
         * so it is possible to verify if there are no changes of the cart prior to execute the
         * charging process.
         */
        $this->roleAccess->setProperty('checkoutProcessDocuments', $documentIDList);

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

            /**
             * Step 1: verify that checkout list has not been changed while the user was on the checkout
             * process.
             */
            $originalCartList = $this->roleAccess->getProperty('checkoutProcessDocuments');
            $currentCartList = $this->getDocumentsInCart();

            $checkoutListChanged = false;
            if(count($originalCartList) == count($currentCartList)) {
                foreach($currentCartList as $currentDocumentItem) {
                    if (!in_array($currentDocumentItem->id, $originalCartList)) {
                        $checkoutListChanged = true;
                        break;
                    }
                }
            }
            else $checkoutListChanged = true;

            if($checkoutListChanged === false) {
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

                    if ($objUser != null) {
                        //Step 1: Create purchase
                        $objPurchase = new Purchase();
                        $objPurchase->user_id = $objUser->id;
                        $objPurchase->created_at = date('Y-m-d h:i:s');
                        $objPurchase->insert();

                        if ($objPurchase->id != null) {
                            $purchaseId = $objPurchase->id;
                            //Step 2: Associate purchase with documents in cart
                            if (count($currentCartList) > 0) {
                                foreach ($currentCartList as $documentItem) {
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

                                if ($objCartList != null)
                                    foreach ($objCartList as $cartItem) {
                                        $cartItem->delete();
                                    }
                            } else array_push($errorList, 'cart_empty');
                        }
                    } else array_push($errorList, 'user_not_found');
                } else array_push($errorList, 'credit_card_invalid');
            } else array_push($errorList, 'cart_modified');
        } else array_push($errorList, 'connection_refused');

        //Obtain the alert to show in the checkout or in the post-checkout page
        $this->hasLayout(false);
        $alertHtml = $this->render('partial/checkoutAlert', ['errorList' => $errorList], false);

        if (count($errorList) == 0) {
            /**
             * Since the checkout process has been successful, it will redirect to another page, thus
             * it is necessary to store the alert into session to show it in the next page
             */
            $objSession = new BaseSession();
            $objSession->set('checkoutAlert', $alertHtml);
            $resultData['status'] = 'ok';
        }
        else
            $resultData['alertHtml'] = $alertHtml;

        $this->hasLayout(false);

        JSONProcess::returnJsonOutput($resultData);
        exit();
    }
}