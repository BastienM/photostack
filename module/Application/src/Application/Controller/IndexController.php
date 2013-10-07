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
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        /**
         * $db is a SQL adapter to connect Application to a database
         *
         * @var object
         */
        $db = new \Zend\Db\Adapter\Adapter(array(
            'driver'   => 'Pdo_Mysql',
            'database' => 'zend_gallery',
            'username' => 'root',
            'password' => 'debian'
            ));

        /*
         * Creating SQL query to retrieve users's pseudo
         * who owns photo already
         * 
         * "SELECT `users`.pseudo
         *  FROM `users`, `images`
         *  WHERE `images`.owner = `users`.pseudo
         *  GROUP BY users.pseudo;"
         *  
         */
        $sql = new Sql($db);

        $select = $sql->select()
                      ->from('users')
                      ->join('images', 'users.pseudo = images.owner')
                      ->where('`images`.owner = `users`.pseudo')
                      ->group('users.pseudo');

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        /**
         * Getting rid of multi-dimensional array
         * and stocking pseudos in a simple one
         */
        foreach ($result as $user) {
            $userliste[] = $user['pseudo'];
        }

        /**
         * $username equal the randomly selected user
         * through $userliste index keys
         * 
         * @var string
         */
        $username = $userliste[array_rand($userliste)];

        /**
         * $imagesTable is a clone of a table
         * with the same name in Database
         *
         * @var     array
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
            'users'  => $userliste,
        ));
    }
}