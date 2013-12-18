<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Adapter\AdapterAwareInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;

class AuthentificationTable extends AbstractTableGateway implements AdapterAwareInterface
{
    /*
     * Table name in database
     */
    protected $table ='authentification';

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
     * getUserAuthInfo fetchs all the information about the
     * user's authentifications
     *
     * @param  string $mail user's mail
     *
     * @return array contains all user's authentifications info
     */
    public function getUserAuthInfo($mail)
    {
        $mail  = $mail;
        $rowset = $this->select(array('mail' => $mail));
        $row = $rowset->current();

        if (!$row) {
            return false;
        }

        return $row;
    }

    /**
     * createLog create a new row in the Database to follow
     * the user login attempts
     * @param  string $mail provided in the form
     */
    public function createLog($mail) {

        $mail = $mail;
        $date = new \DateTime();

        $data = array(
            'mail'      => $mail,
            'lastTry'   => $date->getTimestamp(),
            'numberTry' => '1',
            'isBlocked' => '0',
            );

        if ($data['mail'] && $data['lastTry']) {
            
            $this->insert($data);
        }
    }

    /**
     * authentificationFailed whenever the user fails to login,
     * we add 1 to his login attempts counter
     * 
     * @param  string $mail provided in the form
     */
    public function authentificationFailed($mail) {

        $this->update(array(
          'numberTry' => new Expression('numberTry + 1')),
          array(
              'mail' => $mail)
          );
    }

    /**
     * blockAccount when the user fails for the third time
     * to login, we lock his account
     * 
     * @param  string $mail provided in the form
     */
    public function blockAccount($mail) {

       $this->update(array(
          'isBlocked' => '1'),
          array(
              'mail' => $mail)
        );
    }

    public function unlockAccount($mail) {

        $this->update(array(
            'isBlocked' => '0',
            'numberTry' => '0'),
            array(
            'mail' => $mail)
        );
    }
}