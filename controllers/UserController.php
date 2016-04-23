<?php

namespace controllers;
use framework\BaseQuery;
use models\User;
use models\Tag;

class UserController extends \framework\BaseController {

    /**
     * Action executed to show login form
     */
    public function actionLogin() {
        //Step 0: verify that user is not logged in to process login action
        if($this->roleAccess->isLoggedIn())
            $this->redirect('tag/index');

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
                    $this->redirect('tag/index');
                }
            }
        }

        //Step 8: If no POST request or not possible to find the user, show login page
        $this->render('login');
    }

    public function actionLogout() {
        $this->roleAccess->logout();
        $this->redirect('user/login');
    }
}