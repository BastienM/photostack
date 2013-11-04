<?

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Adapter\AdapterAwareInterface;

class ImagesTable extends AbstractTableGateway implements AdapterAwareInterface
{
    /*
     * Table name in database
     */
    protected $table ='images';

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
     * getImageInfo fetch all the infos about the 
     * image's ID# provided
     *
     * @param  int $id image's ID#
     *
     * @return array result row of ID#
     */
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

    /**
     * getUserImages fetchs all the images of 
     * the username provided
     *
     * @param  string $pseudo [description]
     *
     * @return [type]         [description]
     */
    public function getUserImages($pseudo)
    {
        $userImages = $this->select(array('owner' => $pseudo));

        return $userImages;
    }

    /*
     * under construction
     * 
    public function saveImageInfo(Users $users)
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
    }*/

    /**
     * deleteImage delete the image whose ID# is provided
     *
     * @param  int $id image's ID#
     *
     */
    public function deleteImage($id)
    {
        $this->delete(array('id' => $id));
    }
}