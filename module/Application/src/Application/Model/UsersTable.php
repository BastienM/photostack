<?

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;

class UsersTable extends AbstractTableGateway
{
    // Table name in database
    protected $table ='users';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Users());
        $this->initialize();
    }

    public function fetchAll()
    {
        $resultSet = $this->select();
        return $resultSet;
    }

    public function getUserInfo($pseudo)
    {
        $pseudo  = (string)$pseudo;
        $rowset = $this->select(array('pseudo' => $pseudo));
        $row = $rowset->current();

        if (!$row) {
            throw new \Exception("Could not find user $pseudo");
        }

        return $row;
    }

    public function saveUserInfo(Users $users)
    {
        $data = array(
            'pseudo'   => $users->pseudo
            'password' => $users->password
            'mail'     => $users->mail
            'age'      => $users->age
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