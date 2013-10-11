<?

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Adapter\AdapterAwareInterface;

class ImagesTable extends AbstractTableGateway implements AdapterAwareInterface
{
    // Table name in database
    protected $table ='images';

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

    public function getImageInfo($id)
    {
        $id  = (int)$id;
        $rowset = $this->select(array('id' => $id));
        $row = $rowset->current();

        if (!$row) {
            throw new \Exception("Could not find image id#$id");
        }

        return $row;
    }

    public function getUserImages($pseudo)
    {
        $userImages = $this->select(array('owner' => $pseudo));

        return $userImages;
    }

    // public function saveUserInfo(Users $users)
    // {
    //     $data = array(
    //         'pseudo'   => $users->pseudo
    //         'password' => $users->password
    //         'mail'     => $users->mail
    //         'age'      => $users->age
    //         );

    //     $pseudo = (string)$users->pseudo;

    //     if ($this->getUserInfo($pseudo) == null) {
    //         $this->insert($data);
    //     } elseif ($this->getUserInfo($pseudo) !== null) {
    //         $this->update(
    //             $data,
    //             array(
    //                 'pseudo' => $pseudo,
    //                 )
    //             );
    //     } else {
    //         throw new \Exception('Form pseudo does not exist');
    //     }
    // }

    public function deleteImage($id)
    {
        $this->delete(array('id' => $id));
    }
}