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
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql;
use Zend\View\Model\ViewModel;
use Application\Model\UsersTable;
use Application\Model\ImagesTable;

class IndexController extends AbstractActionController
{
    protected $imagesTable;
    protected $usersTable;

    public function getUsersTable()
    {
        if (!$this->usersTable) {
            $sm = $this->getServiceLocator();
            $this->usersTable = $sm->get('UsersTable');
        }
        return $this->usersTable;
    }

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

        $users = $this->getUsersTable()->getUsersList();

        /**
         * Fetching pseudos only in a new array
         */
        foreach ($users as $user) {
            $userliste[] = $user['pseudo'];
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
            'images' => $imageSet,
            'user'   => $randomUser,
            'users'  => $userliste,
            ));
    }
}