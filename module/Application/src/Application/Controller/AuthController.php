<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Crypt\Password\Bcrypt;
use Zend\Session\SessionManager;
use Zend\Session\Container;
use Application\Model\Users;
use Application\Model\UsersTable;
use Application\Model\Authentification;
use Application\Model\AuthentificationTable;
use Application\Form\LoginForm;
use Application\Form\SignupForm;

class AuthController extends AbstractActionController
{
    protected $usersTable;
    protected $authentificationTable;

    /**
     * getUsersTable is method which allow us to
     * use UsersTable (TableGateway object)
     * dynamicly through the Service Manager 
     *
     * @return object TableGateway instance of UsersTable
     */
    public function getUsersTable()
    {
        if (!$this->usersTable)
        {
            $sm = $this->getServiceLocator();
            $this->usersTable = $sm->get('UsersTable');
        }
        return $this->usersTable;
    }

    public function getAuthentificationTable()
    {
        if (!$this->authentificationTable)
        {
            $sm = $this->getServiceLocator();
            $this->authentificationTable = $sm->get('AuthentificationTable');
        }
        return $this->authentificationTable;
    }

    public function signinAction()
    {

        /*
         * Opening session
         */
        $manager = new SessionManager();
        $manager->start();

        /*
         * Using user's namespace session
         */
        $userSession = new Container('user');

        /**
         * Saving the page which user is coming from
         */
        if  (!$userSession->offsetExists('lastUri'))
        {
            $userSession->lastUri = $this->getRequest()->getHeader('Referer')->getUri();
        }

        /**
         * If user is already logged, we redirect him from where he come from
         */
        if  ($userSession->offsetExists('isLogged') && $userSession->isLogged === true)
        {
            $this->redirect()->toUrl($userSession->lastUri);
        }

        /**
         * Initializing Login Form and the needed
         * components
         */
        $form  = new LoginForm();
        $users = new Users();
        $form->bind($users);

        /**
         * $request contains the HTTP request datas
         *
         * @var array
         */
        $request = $this->getRequest();

        /**
         * If our request IS a $_POST request
         */
        if ($request->isPost()) {
            $form->setData($request->getPost());

            /**
             * If the datas passes form's validator & filter
             */
            if ($form->isValid()) {

                /**
                 * $data our $_POST datas
                 *
                 * @var array
                 */
                $data = $request->getPost();

                /**
                 * Then we hydrate our Users object
                 */
                $users->exchangeArray($data['users']);

                /**
                 * Retrieving info about user authentification logs
                 */
                $authInfo = $this->getAuthentificationTable()->getUserAuthInfo($users->getMail());
                
                /**
                 * If he hasn't any yet, we create him one
                 */
                if(!isset($authInfo) || $authInfo == null) {

                    $this->getAuthentificationTable()->createLog($users->getMail());
                }

                /**
                 * If the user failed 3 times to login
                 */
                if($authInfo['numberTry'] >= 3) {

                    $error = "<strong><small><i class='fa fa-lock'></i></strong> Your account is now locked.</small>";

                    /**
                     * If the account hasn't been locked yet
                     */
                    if ($authInfo['isBlocked'] !== 1) {

                        $this->getAuthentificationTable()->blockAccount($users->getMail());
                    }

                } else {

                    /**
                     * $userDB return an array containing User's infos
                     *
                     * @var array
                     */
                    $userDB = $this->getUsersTable()->getUserInfo($users->getMail());

                    /*
                     * Bcrypt allow us to verify that the plain password equal
                     * the one crypted in the Database
                     */
                    $bcrypt = new Bcrypt();

                    /**
                     * Checking if our hashed password in the Database matchs the one
                     * provided by the form
                     */
                    if ($bcrypt->verify($users->getPassword(), $userDB['password']))
                    {
                        /*
                         * Setting infos in the session
                         */
                        $userSession->isLogged = true;
                        $userSession->mail = $users->getMail();
                        $userSession->username = $userDB['username'];

                        $role = $this->getUsersTable()->getUserRole($userSession->mail);
                        $userSession->role = $role[0]['role'];
                        
                        /*
                         * Redirection the user to the index
                         */
                        // $this->redirect()->toRoute('home');
                        $this->redirect()->toUrl($userSession->lastUri);

                    } else {
                        /*
                         * We add the failed failed to his counter
                         */
                        $this->getAuthentificationTable()->authentificationFailed($users->getMail());
                        
                        /**
                         * Last warning on the 2nd attempt
                         */
                        if($authInfo['numberTry'] == 2) {

                            $error = "<strong><i class='fa fa-exclamation-triangle'></i></strong><small> Password and/or Mail incorrect</small>.
                                        <hr>
                                     <small>Last attempt before your account is being locked</small>";
                        } else {

                            $error = "<strong><i class='fa fa-exclamation-triangle'></i></strong><small> Password and/or Mail incorrect.</small>";
                        }
                    }
                }
            }
        }

        return new ViewModel(array(
            'form'      => $form,
            'usersList' => $this->getUsersTable()->getUsersList(),
            'error'     => @$error,
            ));
    }

    public function signupAction()
    {
        /**
         * Initializing Signup Form and the needed
         * components
         */
        $form = new SignupForm();
        $users = new Users();
        $form->bind($users);

        /**
         * $request contains the HTTP request datas
         *
         * @var array
         */
        $request = $this->getRequest();

        /**
         * If our request IS a $_POST request
         */
        if ($request->isPost()) {
            $form->setData($request->getPost());

            /**
             * If the datas passes form's validator & filter
             */
            if ($form->isValid()) {
                $data = $request->getPost();

                /**
                 * We hydrate our object manually (Fieldset seems to fails to do it)
                 */
                $users->exchangeArray($data['users']);

                /**
                 * And we save the details
                 */
                $registered = $this->getUsersTable()->saveUserInfo($users);

                if($registered){

                    $msg = $registered;
                } else {

                    $msg = "Account already exists";
                }
            }
        }

        return new ViewModel(array(
            'form'  => $form,
            'usersList' => $this->getUsersTable()->getUsersList(),
            'message'   => @$msg,
            ));
    }

    public function logoutAction()
    {
        /*
         * Opening session
         */
        $manager = new SessionManager();
        $manager->start();
        
        /*
         * Using user's namespace session
         */
        $userSession = new Container('user');

        /*
         * Clearing the user's namespace session
         */
        $userSession->getManager()->getStorage()->clear('user');

        /*
         * Redirecting the user to the index
         */
//        $url = $this->getRequest()->getHeader('Referer')->getUri();
//        $this->redirect()->toUrl($url);
        $this->redirect()->toRoute('home');
    }
}