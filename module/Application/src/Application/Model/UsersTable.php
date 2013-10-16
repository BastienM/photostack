<?

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Adapter\AdapterAwareInterface;
use Zend\Db\Sql\Select;

class UsersTable extends AbstractTableGateway implements AdapterAwareInterface
{
    /*
     * Table name in database
     */
    protected $table ='users';

    /**
     * setDbAdapter allow to call the class from within the
     * ServiceManager and initialize the Adapter in the same time
     *
     * @param Adapter $adapter called via getServiceConfig() in Module.php
     */
    public function setDbAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new HydratingResultSet();

        $this->initialize();
    }

    /**
     * fetchAll fetchs the whole table
     *
     * @return array
     */
    public function fetchAll()
    {
        $resultSet = $this->select();
        return $resultSet;
    }

    /**
     * getUserInfo fetchs all the information about the user
     *
     * @param  string $pseudo user's pseudo
     *
     * @return array contains all user's info
     */
    public function getUserInfo($username)
    {
        $username  = $username;
        $rowset = $this->select(array('username' => $username));
        $row = $rowset->current();

        if (!$row) {
            throw new \Exception("Could not find user $username");
        }

        return $row;
    }

    /**
     * getUsersOwningPhotoList fetchs all usernames whom had upload
     * at least one image
     *
     * @return array usernames list
     */
    public function getUsersOwningPhoto()
    {
        $select = new Select();
        $list = $this->select(function ($select)
        {
            $select->join('images', 'users.username = images.owner')
            ->where('`images`.owner = `users`.username')
            ->group('users.username');
        });

        return $list;
    }

    /**
     * getUsersList fetchs all existing users
     *
     * @return array users list
     */
    public function getUserList()
    {
        $usersList = $this->select(function ($select)
        {
            $select->columns(array('username'));
        });

        return $usersList->toArray();
    }

    /*
     *  under construction
     * 
    public function saveUserInfo(Users $users)
    {
        $data = array(
            'username'   => $users->username,
            'password' => $users->password,
            'mail'     => $users->mail,
            'age'      => $users->age,
            );

        $username = (string)$users->username;

        if ($this->getUserInfo($username) == null) {
            $this->insert($data);
        } elseif ($this->getUserInfo($username) !== null) {
            $this->update(
                $data,
                array(
                    'username' => $username,
                    )
                );
        } else {
            throw new \Exception('Form username does not exist');
        }
    }*/

    /**
     * deleteUser delete the user account whose username is provided
     *
     * @param  int $username user's username
     *
     */
    public function deleteUser($username)
    {
        $this->delete(array('username' => $username));
    }
}