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

    /**
     * getImagesTable is method which allow us to
     * use ImagesTable (TableGateway object)
     * dynamicly through the Service Manager
     *
     * @return object TableGateway instance of ImagesTable
     */
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

        $manager = new SessionManager();
        $manager->start();

        $userSession = new Container('user');

        if  (!$userSession->offsetExists('lastUri'))
        {
            $userSession->lastUri = $this->getRequest()->getHeader('Referer')->getUri();
        }

        if  ($userSession->offsetExists('isLogged') && $userSession->isLogged === true)
        {
            $this->redirect()->toUrl($userSession->lastUri);
        }

        $form  = new LoginForm();
        $users = new Users();
        $form->bind($users);

        $view = new ViewModel;

        $request = $this->getRequest();

        if ($request->isPost()) {

            $form->setData($request->getPost());

            if ($form->isValid()) {

                 $data = $request->getPost();

                $users->exchangeArray($data['users']);

                $authInfo = $this->getAuthentificationTable()->getUserAuthInfo($users->getMail());
                
                if(!isset($authInfo) || $authInfo == null) {

                    $this->getAuthentificationTable()->createLog($users->getMail());
                }

                if($authInfo['numberTry'] >= 3) {

                    $view->error = "<strong><small><i class='fa fa-lock'></i></strong> Your account is now locked.</small>";

                    if ($authInfo['isBlocked'] !== 1) {

                        $this->getAuthentificationTable()->blockAccount($users->getMail());
                    }

                } else {

                    $userDB = $this->getUsersTable()->getUserInfo($users->getMail());

                    $bcrypt = new Bcrypt();

                    if ($bcrypt->verify($users->getPassword(), $userDB['password']))
                    {

                        /*
                         * Clearing AuthLog
                         */
                        $this->getAuthentificationTable()->unlockAccount($users->getMail());

                        $userSession->isLogged = true;
                        $userSession->mail = $users->getMail();
                        $userSession->username = $userDB['username'];

                        $role = $this->getUsersTable()->getUserRole($userSession->mail);
                        $userSession->role = $role[0]['role'];
                        
                        $this->redirect()->toUrl($userSession->lastUri);

                    } else {
                        /*
                         * We add the fail attemp to the user's counter
                         */
                        $this->getAuthentificationTable()->authentificationFailed($users->getMail());
                        
                        if($authInfo['numberTry'] == 2) {

                            $view->error = "<strong><i class='fa fa-exclamation-triangle'></i></strong><small> Password and/or Mail incorrect</small>.
                                        <hr>
                                     <small>Last attempt before your account is being locked</small>";
                        } else {

                            $view->error = "<strong><i class='fa fa-exclamation-triangle'></i></strong><small> Password and/or Mail incorrect.</small>";
                        }
                    }
                }
            }
        }

        $view->form = $form;
        $view->usersList = $this->getUsersTable()->getUsersList();
        return $view;
    }

    public function signupAction()
    {
        $form = new SignupForm();
        $users = new Users();
        $form->bind($users);

        $view = new ViewModel;

        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $request->getPost();

                $users->exchangeArray($data['users']);

                $registered = $this->getUsersTable()->saveUserInfo($users);

                if($registered){

                    $view->message = $registered;

                } else {

                    $view->message = "Account already exists";
                }
            }
        }

        $view->form  = $form;
        $view->usersList = $this->getUsersTable()->getUsersList();
        return $view;
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
        $this->redirect()->toRoute('home');
    }

    public function unlockAction() {

        $user = (string) $this->params()->fromRoute('user', 0);
        $userInfo = $this->getUsersTable()->getUserInfoByUser($user);

        $this->getAuthentificationTable()->unlockAccount($userInfo['mail']);
        $this->redirect()->toRoute('account');
    }
}