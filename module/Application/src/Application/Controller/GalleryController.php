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
use Application\Form\MainLoginForm;
use Zend\Session\SessionManager;
use Zend\Session\Container;

class GalleryController extends AbstractActionController
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

        /**
         * Initializing Login Form and the needed
         * components
         */
        $form  = new MainLoginForm();
        $users = new Users();
        $form->bind($users);

        $user = (string) $this->params()->fromRoute('user', 0);

        /*
        * Picking up only the photos whose are owned by the selected user
        */
        $imageSet = $this->getImagesTable()->getUserImages($user);

        $view = new ViewModel(array(
            'form'            => $form,
            'images'          => $imageSet,
            'user'            => $user,
            'usersList'       => $this->getUsersTable()->getUsersList(),
            ));

        /*
         * Loading another view if logged
         */
        if  ($userSession->offsetExists('isLogged') && $userSession->isLogged === true)
        {
            $view->setTemplate('application/index/logged.phtml');
        }
        
        return $view;
    }
}