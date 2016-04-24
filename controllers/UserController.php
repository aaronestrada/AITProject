<?php

namespace controllers;
use framework\BaseQuery;
use models\User;
use models\Tag;

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
        if($this->request->isPostRequest()) {

            //Step 2: Obtain request parameters from form
            $email = $this->request->getParameter('email');
            $password = $this->request->getParameter('password');

            //Step 3: Construct query
            $objQuery = new BaseQuery();
            $objQuery->select()
                ->andWhere(['email' => trim($email)])
                ->andWhere(['status' => 1]);

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

                    //Step 8: Login user
                    $this->roleAccess->login(['user']);

                    //Step 9: Redirect to another page
                    $this->redirect('site/index');
                }
            }
        }

        //Step 8: If no POST request or not possible to find the user, show login page
        $this->render('login');
    }

    public function actionLogout() {
        $this->roleAccess->logout();
        $this->redirect('site/index');
    }

    public function actionRegister() {
        if($this->roleAccess->isLoggedIn())
            $this->redirect('site/index');

        $errorList = [];
        if($this->request->isPostRequest()) {
            $email = $this->request->getParameter('email');
            $firstname = $this->request->getParameter('firstname');
            $lastname = $this->request->getParameter('lastname');
            $password = $this->request->getParameter('password');
            $confirmPassword = $this->request->getParameter('confirm-password');
            $birthdate = $this->request->getParameter('birthdate');

            $userEmailCountQuery = new BaseQuery();
            $userEmailCountQuery->select()
                ->andWhere(['email' => $email])
                ->count();

            $objUserEmail = new User();
            $userEmailCount = $objUserEmail->queryAllFromObject($userEmailCountQuery);

            if($password != $confirmPassword)
                array_push($errorList, 'password_mismatch');

            if($userEmailCount != 0)
                array_push($errorList, 'user_already_exists');

            if(count($errorList) == 0) {
                $objUser = new User();
                $objUser->firstname = $firstname;
                $objUser->firstname = $lastname;
                $objUser->birthdate = $birthdate;
                $objUser->email = $email;
                $objUser->setPassword($password);
                $objUser->insert();
            }
        }

        $this->render('register', ['errorList' => $errorList]);
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