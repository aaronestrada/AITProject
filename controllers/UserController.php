<?php

namespace controllers;
use framework\BaseQuery;
use libs\JSONProcess;
use libs\Validations;
use models\User;

class UserController extends \framework\BaseController {

    public function behavior() {
        return [
            [
                'permission' => 'allow',
                'actions' => ['cart'],
                'roles' => ['@']
            ]
        ];
    }

    /**
     * Action executed to show login form
     */
    public function actionLogin() {
        //Step 0: verify that user is not logged in to process login action
        if($this->roleAccess->isLoggedIn())
            $this->redirect('site/index');

        //Step 1: Verify if user has made a POST request to obtain parameters
        $attemptLogin = false;
        if($this->request->isPostRequest()) {
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
            if($objUser != null) {
                //Step 6: Check that password matches with what user has entered in form if user has been found
                $passwordCheck = $objUser->checkPassword($password);

                if($passwordCheck === true) {
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

        //Step 8: If no POST request or not possible to find the user, show login page
        $this->render('login', ['attemptLogin' => $attemptLogin]);
    }

    public function actionLogout() {
        $this->roleAccess->logout();
        $this->redirect('site/index');
    }

    public function actionRegister() {
        if($this->roleAccess->isLoggedIn())
            $this->redirect('site/index');

        $this->render('register');
    }

    /**
     * Action made to register user into the system.
     * Requirements: it must be an AJAX and a POST request to function, otherwise
     * it will display an error.
     *
     * The output for this action is a JSON string.
     */
    public function actionRegisteruser() {
        $errorList = [];
        $resultData = ['status' => 'ok'];

        if($this->request->isAjaxRequest() && $this->request->isPostRequest()) {
            $email = trim($this->request->getParameter('email'));
            $firstname = trim($this->request->getParameter('firstname'));
            $lastname = trim($this->request->getParameter('lastname'));
            $password = $this->request->getParameter('password');
            $confirmPassword = $this->request->getParameter('confirm-password');
            $birthdate_day = $this->request->getParameter('birthdate_day');
            $birthdate_month = $this->request->getParameter('birthdate_month');
            $birthdate_year = $this->request->getParameter('birthdate_year');

            //validate an empty email field
            if($email == '')
                array_push($errorList, 'error_email_empty');
            elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) //revalidate email format
                array_push($errorList, 'error_email_invalid');
            else {

                //verify that email has not been taken previously
                $userEmailCountQuery = new BaseQuery();
                $userEmailCountQuery->select()
                    ->andWhere(['email' => $email])
                    ->count();

                $objUserEmail = new User();
                $userEmailCount = $objUserEmail->queryAllFromObject($userEmailCountQuery);

                if ($userEmailCount > 0)
                    array_push($errorList, 'error_email_already_exists');
            }

            //Verify empty first name field
            if($firstname == '')
                array_push($errorList, 'error_firstname_empty');

            //Verify empty last name field
            if($lastname == '')
                array_push($errorList, 'error_lastname_empty');

            //Verify empty password field
            if($password == '')
                array_push($errorList, 'error_password_empty');

            //Verify empty password confirmation field
            if($confirmPassword == '')
                array_push($errorList, 'error_confirm-password_empty');

            //Verify that password and confirmation password match
            if(($password != '') && ($confirmPassword != '') && ($password != $confirmPassword))
                array_push($errorList, 'error_passwords_do_not_match');

            if($birthdate_day != '' && $birthdate_month != '' && $birthdate_year != '') {
                $birthdate = $birthdate_year . '-' . $birthdate_month . '-' . $birthdate_day;
                if (!Validations::validateDate($birthdate))
                    array_push($errorList, 'error_birthdate_invalid');
            }
            else
                array_push($errorList, 'error_birthdate_invalid');

        }
        else {
            //No POST or AJAX call, return error
            JSONProcess::returnJsonOutput(['status' => 'error']);
            exit();
        }

        if(count($errorList) > 0) {
            /**
             * If there are errors in the validation, obtain the rendered alert stored in
             * partial/registervalidation.php and send it to the output.
             * The result will be obtained by the AJAX call made with Javascript and display
             * the alert in the document
             */
            $this->hasLayout(false);
            $resultData['status'] = 'error';
            $resultData['alertHtml'] = $this->render('partial/registervalidation', ['errorList' => $errorList], false);
        }
        else {
            //If no errors, store user in database
            $objUser = new User();
            $objUser->firstname = $firstname;
            $objUser->lastname = $lastname;
            $objUser->birthdate = $birthdate;
            $objUser->email = $email;
            $objUser->status = USER_STATUS_ACTIVE;
            $objUser->created_at = date('Y-m-d h:i:s');
            $objUser->setPassword($password);
            $objUser->insert();
        }

        JSONProcess::returnJsonOutput($resultData);
        exit();
    }

    public function actionCart() {
        $userId = $this->roleAccess->getProperty('id');

        $objUser = new User();
        $objUser = $objUser->fetchOne($userId);

        $documentList = [];
        if($objUser != null)
            $documentList = $objUser->getDocumentsInCart();

        $this->render('cart', ['documentList' => $documentList]);
    }
}