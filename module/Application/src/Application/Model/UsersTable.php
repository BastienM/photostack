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
    // Table name in database
    protected $table ='users';

    public function setDbAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new HydratingResultSet();

        $this->initialize();
    }

    public function fetchAll()
    {
        $resultSet = $this->select();
        return $resultSet;
    }

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

    public function getUsersList()
    {
        $select = new Select();
        $usersList = $this->select(function ($select)
        {
            $select->join('images', 'users.pseudo = images.owner')
            ->where('`images`.owner = `users`.pseudo')
            ->group('users.pseudo');
        });

        return $usersList;
    }

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
    }

    public function deleteUser($pseudo)
    {
        $this->delete(array('pseudo' => $pseudo));
    }
}