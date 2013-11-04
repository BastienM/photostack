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
use Application\Model\UsersTable;
use Application\Model\ImagesTable;
use Application\Form\LoginForm;
use Zend\Session\SessionManager;
use Zend\Session\Container;

class IndexController extends AbstractActionController
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
        
        /*
         * Verifying if user is already logged in to select which
         * menu items to draw
         */
        if  ($userSession->offsetExists('isLogged') && $userSession->isLogged === true)
        {
            $menubarItems = array(
                'account' => array(
                    'class' => 'uk-button uk-button-primary',
                    'url'      => 'home',
                    'icon'     => '',
                    'text'     => ' Account'
                    ),
                'logout' => array(
                    'class' => '',
                    'url'      => 'logout',
                    'icon'     => '',
                    'text'     => ' Log Out'
                    )
                ); 
        }
        else 
        {
            $menubarItems = array(
                'signin' => array(
                    'class' => 'uk-button uk-button-success',
                    'url'      => 'signin',
                    'icon'     => 'uk-icon-lock',
                    'text'     => ' Sign In'
                    ),
                'signup' => array(
                    'class' => 'uk-button uk-button-primary',
                    'url'      => 'signup',
                    'icon'     => 'uk-icon-signin',
                    'text'     => ' Sign Up'
                    )
                );
        }

        $form  = new LoginForm();
        $users = new Users();
        $form->bind($users);

        /**
         * $users contains the list of all users
         * who has upload at least one image
         *
         * @var array
         */
        $users = $this->getUsersTable()->getUsersOwningPhotoList();

        /**
         * Fetching pseudos in a new array
         */
        foreach ($users as $user)
        {
            $userliste[] = $user['username'];
        }

        /**
         * $username equal the randomly selected user
         * through $userliste index keys
         * 
         * @var string
         */
        $randomUser = $userliste[array_rand($userliste)];

        /*
         * Picking up only the photos whose are owned by the selected user
         */
        $imageSet = $this->getImagesTable()->getUserImages($randomUser);

        return new ViewModel(array(
            'menubarItems'    => $menubarItems,
            'form'            => $form,
            'images'          => $imageSet,
            'user'            => $randomUser,
            'usersList'       => $this->getUsersTable()->getUsersList(),
            ));
    }
}