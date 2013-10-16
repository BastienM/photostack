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
    public function getUserInfo($pseudo)
    {
        $pseudo  = $pseudo;
        $rowset = $this->select(array('pseudo' => $pseudo));
        $row = $rowset->current();

        if (!$row) {
            throw new \Exception("Could not find user $pseudo");
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
            $select->join('images', 'users.pseudo = images.owner')
            ->where('`images`.owner = `users`.pseudo')
            ->group('users.pseudo');
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
            $select->columns(array('pseudo'));
        });

        return $usersList->toArray();
    }

    /*
     *  under construction
     * 
    public function saveUserInfo(Users $users)
    {
        $data = array(
            'pseudo'   => $users->pseudo,
            'password' => $users->password,
            'mail'     => $users->mail,
            'age'      => $users->age,
            );

        $pseudo = (string)$users->pseudo;

        if ($this->getUserInfo($pseudo) == null) {
            $this->insert($data);
        } elseif ($this->getUserInfo($pseudo) !== null) {
            $this->update(
                $data,
                array(
                    'pseudo' => $pseudo,
                    )
                );
        } else {
            throw new \Exception('Form pseudo does not exist');
        }
    }*/

    /**
     * deleteUser delete the user account whose username is provided
     *
     * @param  int $pseudo user's pseudo
     *
     */
    public function deleteUser($pseudo)
    {
        $this->delete(array('pseudo' => $pseudo));
    }
}