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
use Application\Form\LoginForm;

class AuthController extends AbstractActionController
{
    public function loginAction()
    {
        /**
         * Initializing Login Form
         */
        $formLogin = new LoginForm();
        $users = new Users();
        $formLogin->bind($users);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $formLogin->setData($request->getPost());

            if ($formLogin->isValid()) {
                var_dump($users);
            }
        }

        return new ViewModel(array(
            'form'  => $form,
            ));
    }

    public function signAction()
    {
        
    }
}