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
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        /**
         * $db is a SQL adapter to connect database throught
         * PHP's PDO object
         *
         * @var object
         */
        $db = new \Zend\Db\Adapter\Adapter(array(
            'driver' => 'Pdo_Mysql',
            'database' => 'zend_gallery',
            'username' => 'root',
            'password' => 'debian'
            ));

        /*
         * Choosing a random number between 1 and the total of users
         * then retrieving his associated username
         */
        // $sql = "SELECT COUNT(pseudo) FROM users";

        $username = "Hellow";

        /**
         * $imagesTable is a clone of a table
         * with the same name in Database
         *
         * @var array
         * @param   string $table name of the table to clone
         * @param   object $db Db Adapter to use to connect
         */
        $imagesTable = new TableGateway('images', $db);

        /*
         * Picking up only the photos whose are owned by the selected user
         */
        $imageSet = $imagesTable->select(array('owner' => $username));

        return new ViewModel(array(
            'images' => $imageSet,
            'user'   => $username,
            ));
    }
}
