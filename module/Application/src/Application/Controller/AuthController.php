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
use Application\Model\Users;
use Application\Form\LoginForm;
use Application\Form\SignupForm;

class AuthController extends AbstractActionController
{
    public function signinAction()
    {
        /**
         * Initializing Login Form
         */
        $form = new LoginForm();
        $users = new Users();
        $form->bind($users);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                var_dump($users);
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
                var_dump($users);
            }
        }

        return new ViewModel(array(
            'form'  => $form,
        ));
    }
}