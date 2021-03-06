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
use Zend\Session\SessionManager;
use Zend\Session\Container;
use Application\Form\UploadForm;

class AccountController extends AbstractActionController
{
    protected $imagesTable;
    protected $usersTable;

    /**
     * getUsersTable is method which allow us to
     * use UsersTable (TableGateway object)
     * dynamically through the Service Manager
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
    public function getImagesTable()
    {
        if (!$this->imagesTable) {
            $sm = $this->getServiceLocator();
            $this->imagesTable = $sm->get('ImagesTable');
        }
        return $this->imagesTable;
    }

    public function indexAction()
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

        $view = new ViewModel(array(
            'usersList'       => $this->getUsersTable()->getUsersList(),
        ));

        if  ($userSession->offsetExists('role') && $userSession->role == "user")
        {
            $form = new UploadForm('upload-form');
            $imageSet = $this->getImagesTable()->getUserImages($userSession->username);

            $view->form = $form;
            $view->images = $imageSet;
        }

        if  ($userSession->offsetExists('role') && $userSession->role == "admin")
        {
            $view->userAuthList = $this->getUsersTable()->getUserAuthInfoList();
            $view->setTemplate('application/account/index_admin.phtml');
        }

        if  ($userSession->offsetExists('uploadInfo'))
        {
            $view->uploadInfo = $userSession->uploadInfo;
            unset($userSession->uploadInfo);
        }

        return $view;
    }

    public function removeAction() {

        $id = (integer) $this->params()->fromRoute('id', 0);
        $this->getImagesTable()->deleteImage($id);

        $this->redirect()->toRoute('account');
    }

    public function deleteAction() {

        $user = (string) $this->params()->fromRoute('user', 0);

        $this->getUsersTable()->deleteUser($user);
        $this->getImagesTable()->deleteUserImages($user);

        $this->redirect()->toRoute('account');
    }
}