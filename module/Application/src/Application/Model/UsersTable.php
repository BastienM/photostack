<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Adapter\AdapterAwareInterface;
use Zend\Db\Sql\Select;
use Zend\Crypt\Password\Bcrypt;

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
     * getUserInfo fetchs all the information about the user
     *
     * @param $mail
     * @internal param string $pseudo user's pseudo
     * @return array contains all user's info
     */
    public function getUserInfo($mail)
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
     * getUsersOwningPhotoList fetchs all usernames whom had upload
     * at least one image
     *
     * @return array usernames list
     */
    public function getUsersOwningPhotoList()
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
    public function getUsersList()
    {
        $usersList = $this->select(function ($select)
        {
            $select->columns(array('username'));
        });

        return $usersList->toArray();
    }

    public function saveUserInfo(Users $users)
    {
        $bcrypt = new Bcrypt();

        $random_pwd = $this->generatePassword(12);

        $data = array(
            'username' => $users->getUsername(),
            'password' => $bcrypt->create($random_pwd),
            'mail'     => $users->getMail(),
            'age'      => $users->getAge(),
            'role'     => 'user',
            );

        $DBinfo = $this->getUserInfo($users->getMail());

        if (!isset($DBinfo) || empty($DBinfo)) {
            $this->insert($data);
            echo($random_pwd);
        }
        else if (!empty($DBinfo) || $DBinfo['username'] === $data['username'])
        {
            echo('Mail already associated with an account');
        }
    }

    /**
     * deleteUser delete the user account whose username is provided
     *
     * @param $mail
     * @internal param int $pseudo user's pseudo
     */
    public function deleteUser($mail)
    {
        $this->delete(array('username' => $mail));
    }


    /**
     * getUserRole retrieve the user's role from DB
     *
     * @param $mail
     * @return array
     */
    public function getUserRole($mail) {

       $role =  $this->select(function ($select) use ($mail) {
                    $select->columns(array('role'));
                    $select->where(array('mail' => $mail));
                });

        return $role->toArray();
    }

    /**
     * generatePassword is a method to generate a random
     * password with a provided length
     *
     * @param  integer $length password length wanted
     *
     * @return string generated password
     */
    private function generatePassword($length)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count = mb_strlen($chars);

        for ($i = 0, $result = ''; $i < $length; $i++)
        {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }

        return $result;
    }
}