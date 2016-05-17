<?php

namespace controllers;

use framework\BaseController;
use framework\BaseQuery;
use framework\BaseSession;
use libs\JSONProcess;
use libs\Validations;
use models\Document;
use models\Purchase;
use models\User;

class UserController extends BaseController {

    public function behavior() {
        return [
            [
                'permission' => 'allow',
                'actions' => ['edit', 'edituser', 'purchases'],
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
     * Action executed to show login form
     */
    public function actionLogin() {
        //Step 0: verify that user is not logged in to process login action
        if ($this->roleAccess->isLoggedIn())
            $this->redirect('site/index');

        //Step 1: Verify if user has made a POST request to obtain parameters
        $attemptLogin = false;
        if ($this->request->isPostRequest()) {
            $attemptLogin = true;
            //Step 2: Obtain request parameters from form
            $email = $this->request->getParameter('email');
            $password = $this->request->getParameter('password');

            //Step 3: Construct query
            $objQuery = new BaseQuery();
            $objQuery->select()
                ->andWhere(['email' => trim($email)])
                ->andWhere(['status' => USER_STATUS_ACTIVE]);

            //Step 4: Retrieve user
            $objUser = new User();
            $objUser = $objUser->queryOneFromObject($objQuery);

            //Step 5: Verify that user has been found
            if ($objUser != null) {
                //Step 6: Check that password matches with what user has entered in form if user has been found
                $passwordCheck = $objUser->checkPassword($password);

                if ($passwordCheck === true) {
                    //Step 7: If password matches, set login properties
                    $this->roleAccess->setProperty('id', $objUser->id);
                    $this->roleAccess->setProperty('firstname', $objUser->firstname);
                    $this->roleAccess->setProperty('lastname', $objUser->lastname);
                    $this->roleAccess->setProperty('email', $objUser->email);

                    //Step 8: Login user
                    $this->roleAccess->login(['user']);

                    //Step 9: Redirect to another page
                    $this->redirect('site/index');
                }
            }
        }

        //Obtain message from session and erase it
        $objSession = new BaseSession();
        $userFormValidationAlert = $objSession->get('userFormValidationAlert');
        $objSession->remove('userFormValidationAlert');

        //Step 8: If no POST request or not possible to find the user, show login page
        $this->render('login', [
            'attemptLogin' => $attemptLogin,
            'userFormValidationAlert' => $userFormValidationAlert
        ]);
    }

    public function actionLogout() {
        $this->roleAccess->logout();
        $this->redirect('site/index');
    }

    public function actionRegister() {
        if ($this->roleAccess->isLoggedIn())
            $this->redirect('site/index');

        $this->render('register', [
            'isEdition' => false,
            'objUser' => null,
            'userFormValidationAlert' => null
        ]);
    }

    /**
     * Construct list with form values for register or edition
     * @return array List of values from form
     */
    private function getUserFormValues() {
        $formValues = [
            'email' => trim(strtolower($this->request->getParameter('email'))),
            'firstname' => trim($this->request->getParameter('firstname')),
            'lastname' => trim($this->request->getParameter('lastname')),
            'password' => $this->request->getParameter('password'),
            'confirmPassword' => $this->request->getParameter('confirm-password'),
            'birthdate' => ''
        ];

        $birthdate_day = $this->request->getParameter('birthdate_day');
        $birthdate_month = $this->request->getParameter('birthdate_month');
        $birthdate_year = $this->request->getParameter('birthdate_year');

        if ($birthdate_day != '' && $birthdate_month != '' && $birthdate_year != '')
            $formValues['birthdate'] = $birthdate_year . '-' . $birthdate_month . '-' . $birthdate_day;

        return $formValues;
    }

    /**
     * Validate user form (register or edit)
     * @param $formValues List of form values to validate
     * @param bool $isEdition If is a validation from edition or not.  False by default
     * @param $oldEmail User old email to validate only if changed
     * @return array List of errors
     */
    public function validateUserForm($formValues, $isEdition = false, $oldEmail = null) {
        $errorList = [];

        //get values from form list
        $email = $formValues['email'];
        $firstname = $formValues['firstname'];
        $lastname = $formValues['lastname'];
        $password = $formValues['password'];
        $confirmPassword = $formValues['confirmPassword'];
        $birthdate = $formValues['birthdate'];

        //validate an empty email field
        if ($email == '')
            array_push($errorList, 'error_email_empty');
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) //revalidate email format
            array_push($errorList, 'error_email_invalid');
        else {
            /**
             * In case of an edition, it has to include the validation of the repeated mail
             */
            $validateEmail = true;
            if ($isEdition)
                $validateEmail = trim($oldEmail) != $email;

            if($validateEmail) {
                //verify that email has not been taken by another user
                $userEmailCountQuery = new BaseQuery();
                $userEmailCountQuery->select()
                    ->andWhere(['email' => $email])
                    ->count();

                $objUserEmail = new User();
                $userEmailCount = $objUserEmail->queryAllFromObject($userEmailCountQuery);

                if ($userEmailCount > 0)
                    array_push($errorList, 'error_email_already_exists');
            }
        }

        //Verify empty first name field
        if ($firstname == '')
            array_push($errorList, 'error_firstname_empty');

        //Verify empty last name field
        if ($lastname == '')
            array_push($errorList, 'error_lastname_empty');

        /**
         * Password fields must be validated in the following cases:
         * 1) The user is registering
         * 2) The user is editing its password and has entered at least one of two values in fields
         */
        $validatePasswordFields = true;
        if ($isEdition)
            $validatePasswordFields = ($password != '') || ($confirmPassword != '');

        if ($validatePasswordFields) {
            //Verify empty password field
            if ($password == '')
                array_push($errorList, 'error_password_empty');

            //Verify empty password confirmation field
            if ($confirmPassword == '')
                array_push($errorList, 'error_confirm-password_empty');

            //Verify that password and confirmation password match
            if (($password != '') && ($confirmPassword != '') && ($password != $confirmPassword))
                array_push($errorList, 'error_passwords_do_not_match');
        }

        if (!Validations::validateDate($birthdate))
            array_push($errorList, 'error_birthdate_invalid');

        return $errorList;
    }

    /**
     * Action made to register user into the system.
     * Requirements: it must be an AJAX and a POST request to function, otherwise
     * it will display an error.
     *
     * The output for this action is a JSON string.
     */
    public function actionRegisteruser() {
        $resultData = ['status' => 'ok'];

        if ($this->request->isAjaxRequest() && $this->request->isPostRequest()) {
            $formValues = $this->getUserFormValues();
            $errorList = $this->validateUserForm($formValues);
        } else {
            //No POST or AJAX call, return error
            JSONProcess::returnJsonOutput(['status' => 'error']);
            exit();
        }

        /**
         * If there are errors in the validation, obtain the rendered alert stored in
         * partial/userformvalidation.php and send it to the output.
         * The result will be obtained by the AJAX call made with Javascript and display
         * the alert in the document.
         *
         * Otherwise, store in session and show in next page.
         */
        $this->hasLayout(false);
        $alertHtml = $this->render('partial/userformvalidation', ['errorList' => $errorList, 'isEdition' => false], false);

        if (count($errorList) > 0) {
            /**
             * If there are errors in the validation, obtain the rendered alert stored in
             * partial/registervalidation.php and send it to the output.
             * The result will be obtained by the AJAX call made with Javascript and display
             * the alert in the document
             */
            $resultData['status'] = 'error';
            $resultData['alertHtml'] = $alertHtml;
        } else {
            //If no errors, store user in database
            $objUser = new User();
            $objUser->firstname = $formValues['firstname'];
            $objUser->lastname = $formValues['lastname'];
            $objUser->birthdate = $formValues['birthdate'];
            $objUser->email = $formValues['email'];
            $objUser->status = USER_STATUS_ACTIVE;
            $objUser->created_at = date('Y-m-d h:i:s');
            $objUser->setPassword($formValues['password']);
            $objUser->insert();

            //store message in session
            $objSession = new BaseSession();
            $objSession->set('userFormValidationAlert', $alertHtml);
        }

        JSONProcess::returnJsonOutput($resultData);
        exit();
    }

    /**
     * Display the edition form for user information
     */
    public function actionEdit() {
        $objUser = new User();
        $objUser = $objUser->fetchOne($this->roleAccess->getProperty('id'));

        if ($objUser != null) {

            //Obtain message from session and erase it
            $objSession = new BaseSession();
            $userFormValidationAlert = $objSession->get('userFormValidationAlert');
            $objSession->remove('userFormValidationAlert');

            $this->render('register', [
                'isEdition' => true,
                'objUser' => $objUser,
                'userFormValidationAlert' => $userFormValidationAlert
            ]);
        } else
            $this->redirect('site/index');
    }

    /**
     * Action made to edit user information.
     * Requirements: it must be an AJAX and a POST request to function, otherwise
     * it will display an error.
     *
     * The output for this action is a JSON string.
     */
    public function actionEdituser() {
        $resultData = ['status' => 'ok'];
        $errorOnProcess = true;

        if ($this->request->isAjaxRequest() && $this->request->isPostRequest()) {
            $objUser = new User();
            $objUser = $objUser->fetchOne($this->roleAccess->getProperty('id'));

            if ($objUser != null) {
                $errorOnProcess = false;
                $formValues = $this->getUserFormValues();
                $errorList = $this->validateUserForm($formValues, true, $objUser->email);
            }
        }

        if ($errorOnProcess) {
            //No POST or AJAX call, return error
            JSONProcess::returnJsonOutput(['status' => 'error']);
            exit();
        }

        /**
         * If there are errors in the validation, obtain the rendered alert stored in
         * partial/userformvalidation.php and send it to the output.
         * The result will be obtained by the AJAX call made with Javascript and display
         * the alert in the document
         */
        $this->hasLayout(false);
        $alertHtml = $this->render('partial/userformvalidation', ['errorList' => $errorList, 'isEdition' => true], false);

        if (count($errorList) > 0) {
            $resultData['status'] = 'error';
            $resultData['alertHtml'] = $alertHtml;
        } else {
            //If no errors, update user in database
            $objUser->firstname = $formValues['firstname'];
            $objUser->lastname = $formValues['lastname'];
            $objUser->birthdate = $formValues['birthdate'];
            $objUser->email = $formValues['email'];
            $objUser->modified_at = date('Y-m-d h:i:s');
            if ($formValues['password'] != '')
                $objUser->setPassword($formValues['password']);
            $objUser->update();

            //Update first name and last name from logged in session
            $this->roleAccess->setProperty('firstname', $formValues['firstname']);
            $this->roleAccess->setProperty('lastname', $formValues['lastname']);

            //store message in session
            $objSession = new BaseSession();
            $objSession->set('userFormValidationAlert', $alertHtml);
        }

        JSONProcess::returnJsonOutput($resultData);
        exit();
    }

    /**
     * Display purchased documents for a user
     */
    public function actionOrders() {
        $purchaseQuery = new BaseQuery();
        $purchaseQuery->select()
            ->andWhere(['user_id' => $this->roleAccess->getProperty('id')])
            ->order(['created_at' => 'DESC']);

        $documentObjectList = [];

        $objPurchase = new Purchase();
        $purchaseList = $objPurchase->queryAllFromObject($purchaseQuery);

        if($purchaseList != null) {
            foreach ($purchaseList as $purchaseItem) {
                $documentObjectList[$purchaseItem->id] = [];
                $purchasedDocuments = $purchaseItem->getPurchasedDocuments();
                foreach ($purchasedDocuments as $purchaseDocumentItem) {
                    $objDocument = new Document();
                    $objDocument = $objDocument->fetchOne($purchaseDocumentItem->document_id);

                    if ($objDocument != null) {
                        array_push($documentObjectList[$purchaseItem->id], [
                            'documentItem' => $objDocument,
                            'purchasePrice' => $purchaseDocumentItem->price
                        ]);
                    }
                }
            }
        }

        //Obtain message from session and erase it
        $objSession = new BaseSession();
        $checkoutAlertSuccess = $objSession->get('checkoutAlert');
        $objSession->remove('checkoutAlert');

        $this->setLayout('main_search');
        $this->render('orders', [
            'purchaseList' => $purchaseList != null ? $purchaseList : [],
            'documentObjectList' => $documentObjectList,
            'checkoutAlertSuccess' => $checkoutAlertSuccess
        ]);
    }
}