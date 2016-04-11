<?php

namespace framework;

/**
 * Class BaseRoleAccess
 * Control of login of the user, properties assigned to the logged user and the different roles
 * assigned.
 *
 * This class is used for each controller to check the defined behavior structure.  This is done
 * using the function "validateBehavior".
 *
 * @package framework
 */
class BaseRoleAccess {
    private $sessionLogin;
    private $session;

    private $sessionStorageName = 'BaseAccessManagement';
    private $sessionTimeout = 30;

    private $roles = [];
    private $properties = [];

    /**
     * BaseRoleAccess constructor.
     * @param $sessionObject Session object to store itself
     */
    private function __construct($sessionObject) {
        /**
         * Verify that parameters are correctly set to configure timeout and storage name of the
         * RoleAccess object in session.
         */
        $paramSettings = require('../config/params.php');
        if(isset($paramSettings['roleAccess'])) {
            $roleAccessSettings = $paramSettings['roleAccess'];
            $roleAccessSessionName = isset($roleAccessSettings['sessionName']) ? $roleAccessSettings['sessionName'] : '';

            if($roleAccessSessionName != '')
                $this->sessionStorageName = $roleAccessSessionName;

            if(isset($roleAccessSettings['timeout'])) {
                $timeoutValue = $roleAccessSettings['timeout'];
                if(is_numeric($timeoutValue))
                    $this->sessionTimeout = $timeoutValue;
            }
        }
        $this->session = $sessionObject;
    }

    /**
     * Get an instance of the BaseRoleAccess object.
     * In order to obtain the object, it is verified if it already exists in session.
     * a) If that's the case, return the stored object
     * b) Otherwise create a new one and store it in session
     *
     * It is used a static function since the constructor cannot return the obtained instance from session
     * (it overwrites the object).
     *
     * @param BaseSession $session BaseSession object
     * @return BaseRoleAccess
     */
    public static function getInstance($session) {
        //Step 1: obtain configuration parameters for role access object
        $paramSettings = require('../config/params.php');
        if(isset($paramSettings['roleAccess'])) {
            //Verify that configuration has session name
            $roleAccessSettings = $paramSettings['roleAccess'];
            $roleAccessSessionName = isset($roleAccessSettings['sessionName']) ? $roleAccessSettings['sessionName'] : '';

            //Verify that configuration has timeout value
            if(isset($roleAccessSettings['timeout']))
                $timeoutValue = is_numeric($roleAccessSettings['timeout']) ? $roleAccessSettings['timeout'] : null;
        }

        //Step 2: Verify that session has not expired, otherwise erase it from session
        $roleSessionStorage = $session->get($roleAccessSessionName);
        if($roleSessionStorage instanceof \framework\BaseRoleAccess) {
            //verify that session has not been stored for more than sessionTimeout minutes, otherwise erase it
            if(time() - $roleSessionStorage->sessionLogin > ($timeoutValue * 60)) {
                $session->remove($roleAccessSessionName);
                $roleSessionStorage = new BaseRoleAccess($session);
            }
        }
        else
            $roleSessionStorage = new BaseRoleAccess($session); //call to constructor

        //return obtained or new object
        return $roleSessionStorage;
    }

    /**
     * Store the role access object into session
     */
    private function storeRoleAccess() {
        $this->session->set($this->sessionStorageName, $this);
    }

    /**
     * Verifies if the logged user has a role
     * @param $roleName Role name
     * @return mixed Whether the user has a role assigned
     */
    public function hasRole($roleName) {
        return in_array($roleName, $this->roles);
    }

    /**
     * Remove role from user
     * @param $roleName Role name
     */
    public function removeRole($roleName) {
        if(in_array($roleName, $this->roles))
            array_diff($this->roles, [$roleName]);
    }

    /**
     * Add role to the logged user
     * @param $roleName
     */
    public function addRole($roleName) {
        if(!in_array($roleName, $this->roles))
            array_push($this->roles, $roleName);
    }

    /**
     * Set properties for the logged user
     * @param $property Property name
     * @param $value Property value
     */
    public function setProperty($property, $value) {
        $this->properties[$property] = $value;
    }

    /**
     * Get a property for the logged user
     * @param $property Property name
     * @return mixed|null Value of property
     */
    public function getProperty($property) {
        if(isset($this->properties[$property]))
            return $this->properties[$property];
        return null;
    }

    /**
     * Login a user.  The role access object is stored in session
     * @param array $roleList List of roles assigned to the user
     */
    public function login($roleList = []) {
        //set login session time
        $this->sessionLogin = time();

        //assign roles to logged user
        foreach($roleList as $roleItem)
            $this->addRole($roleItem);

        //store role access object into session
        $this->storeRoleAccess();
    }

    /**
     * Logout a user.  Erases the role access object from session
     */
    public function logout() {
        $this->session->remove($this->sessionStorageName);
    }

    /**
     * Verifies whether a user has logged in or not.  It verifies only if the Role access object
     * is stored into session.
     *
     * @return bool Whether the user is logged in or not
     */
    public function isLoggedIn() {
        return ($this->session->get($this->sessionStorageName) instanceof \framework\BaseRoleAccess);
    }

    /**
     * Validates the behavior of a controller based on the type of access, actions and permissions
     * assigned in each controller.
     *
     * The "behavior" function of each controller must return an array with different rules, having 3
     * different levels:
     * 1) permission: if the rule is for allowing or denying (allow, deny) to a specific
     *    role list for some action list
     *
     * 2) actions: List of actions that will be applied for that rule.  Accepted values:
     *      - Empty list or ['*'], meaning that the rule will be applied to all the actions
     *      - List of actions
     *
     * 3) roles: List of roles for which the rule will be applied.  Accepted values:
     *      - Empty list, meaning that no role has to be associated
     *      - List of roles
     *      - ['*'], meaning that the rule will be applied to all the roles (same as empty list)
     *      - ['@'], meaning that the rule will verify if the user is logged in
     *
     * If two rules contain the same action, if one is accomplished it will be as validated, so be careful on
     * setting the right values, because the validation will take the first rules and could ignore the rest.
     *
     * Structure for $controllerBehavior:
     *  [
     *      //behavior 1 rules
     *      [
     *          'permission' => 'allow',
     *          'actions' => ['*'],
     *          'roles' => ['*']
     *      ],
     *      //behavior 2 rules
     *      [
     *          'permission' => 'deny',
     *          'actions' => ['action1', 'action2', ...],
     *          'roles' => ['role1', 'role2', ...]
     *      ],
     *      ...
     *  ]
     *
     * @param $actionName Name of the executed action
     * @param array $controllerBehavior Behavior structure for a controller
     * @return bool Whether the behavior has been validated or not in order to execute the action
     */
    public function validateBehavior($actionName, $controllerBehavior) {
        $globalBehaviorValidator = false;
        $behaviorValidationList = [];

        if(count($controllerBehavior) > 0) {
            foreach ($controllerBehavior as $behaviorItem) {
                //by default, "allow" access as default
                $permissionValue = 'allow';
                if (isset($behaviorItem['permission'])) {
                    if (in_array($behaviorItem['permission'], ['allow', 'deny']))
                        $permissionValue = $behaviorItem['permission'];
                }

                //obtain list of actions to give / deny access
                $actionList = isset($behaviorItem['actions']) ? $behaviorItem['actions'] : null;

                //by default, give access if not specified the actions
                $hasAccessFromAction = true;
                if (is_array($actionList) && count($actionList) > 0) {
                    $hasAccessFromAction = false;
                    foreach ($actionList as $actionItem) {
                        if ($actionItem == '*') {
                            $hasAccessFromAction = true;
                            break;
                        }
                    }

                    //if * not found, verify if action is on the list
                    if ($hasAccessFromAction == false)
                        $hasAccessFromAction = in_array($actionName, $actionList);
                }

                //obtain list of roles to be give / deny access
                $roleList = isset($behaviorItem['roles']) ? $behaviorItem['roles'] : null;

                //by default, give access if no role is specified
                $hasAccessFromRole = true;
                if (is_array($roleList) && count($roleList) > 0) {
                    $hasAccessFromRole = false;
                    $hasAccessFromRoleLoggedIn = false;

                    foreach ($roleList as $roleItem) {
                        //if is *, has access automatically
                        if ($roleItem == '*') {
                            $hasAccessFromRole = true;
                            break;
                        } elseif ($roleItem == '@') {
                            //verifies that user is logged in
                            $hasAccessFromRole = $this->isLoggedIn();
                            $hasAccessFromRoleLoggedIn = true;
                            break;
                        }
                    }

                    //if it was not a @ configuration, verify that user has the role from the list
                    if (($hasAccessFromRole == false) && ($hasAccessFromRoleLoggedIn == false)) {
                        foreach ($roleList as $roleItem)
                            if ($this->hasRole($roleItem)) {
                                $hasAccessFromRole = true;
                                break;
                            }
                    }
                }

                //depending on the permission access, validate the rule as allow or deny
                switch ($permissionValue) {
                    case 'allow':
                        $globalBehaviorValidator = $hasAccessFromAction && $hasAccessFromRole;
                        break;
                    case 'deny':
                        $globalBehaviorValidator = !($hasAccessFromAction && $hasAccessFromRole);
                        break;
                }

                //store validated rule in list
                array_push($behaviorValidationList, $globalBehaviorValidator);
            }

            //if a rule is positive, return true, ignoring the rest of rules
            foreach($behaviorValidationList as $behaviorValidationItem)
                if($behaviorValidationItem == true)
                    return true;

            //if no rule was accomplished, return false
            return false;
        }

        //no behavior attached, return true
        return true;
    }

}