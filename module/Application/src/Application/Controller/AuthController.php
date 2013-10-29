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
use Application\Model\Users;
use Application\Model\UsersTable;
use Application\Form\LoginForm;
use Application\Form\SignupForm;

class AuthController extends AbstractActionController
{
    protected $usersTable;

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

    public function signinAction()
    {
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

                if ($bcrypt->verify($users->getPassword(), $userDB['password']))
                {
                    echo "The password is correct! \n";
                }
                else
                {
                    echo "The password is NOT correct.\n";
                }
            }
        }

        return new ViewModel(array(
            'form'  => $form,
        ));
    }

    public function signupAction()
    {
        /**
         * Initializing Login Form
         */
        $form = new SignupForm();
        $users = new Users();
        $form->bind($users);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $request->getPost();
                $users->exchangeArray($data['users']);

                $this->getUsersTable()->saveUserInfo($users);
            }
        }

        return new ViewModel(array(
            'form'  => $form,
        ));
    }
}