<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\AdapterAwareInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;

class ImagesTable extends AbstractTableGateway implements AdapterAwareInterface
{
    /*
     * Table name in database
     */
    protected $table = 'images';

    /**
     * setDbAdapter allow to call the class from within the
     * ServiceManager and initialize the Adapter in the same time
     *
     * @param Adapter $adapter called via getServiceConfig() in Module.php
     * @return void|\Zend\Db\Adapter\AdapterAwareInterface
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
     * @throws \Exception
     * @return array result row of ID#
     */
    public function getImageInfo($id)
    {
        $id = (int)$id;
        $rowset = $this->select(array('id' => $id));
        $row = $rowset->current();

        if (!$row) {
            throw new \Exception("Could not find image id#$id");
        }

        return $row;
    }


    /**
     * getUserImages fetchs all the images of  the username provided
     *
     * @param $pseudo
     * @param bool $paginated
     * @return \Zend\Db\ResultSet\ResultSet|\Zend\Paginator\Paginator
     * contains all user's images info
     */
    public function getUserImages($pseudo, $paginated = false)
    {
        if ($paginated) {

            $sql = $this->getSql();
            $select = $sql->select();

            $select->where(array('owner' => $pseudo));

            $adapter = new \Zend\Paginator\Adapter\DbSelect($select, $sql);
            $paginator = new \Zend\Paginator\Paginator($adapter);

            return $paginator;
        }

        $userImages = $this->select(array('owner' => $pseudo));

        return $userImages;
    }

    /*
     * under construction
     */
    public function saveImageInfo($image)
    {
        $data = array(
            'url'       => $image['url'],
            'name'      => $image['name'],
            'uploaded'  => $image['date'],
            'owner'     => $image['owner'],
            'publishId' => $image['id'],
            'weight'    => $image['size'],
        );

        $this->insert($data);
    }

    /**
     * deleteImage delete the image whose ID# is provided
     *
     * @param  int $id image's ID#
     *
     */
    public function deleteImage($id)
    {
        /* add a method to delete also the
         * picture physicly
         */
        $this->delete(array('id' => $id));

    }

    public function deleteUserImages($user)
    {

        $this->delete(array('owner' => $user));
    }
}