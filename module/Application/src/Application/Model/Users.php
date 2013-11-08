<?php

namespace Application\Model;

class Users
{
    private $username;
    private $password;
    private $mail;
    private $age;
    
    public function exchangeArray($data)
    {
        $this->username   = (isset($data['username'])) ? $data['username'] : null;
        $this->password   = (isset($data['password'])) ? $data['password'] : null;
        $this->mail       = (isset($data['mail'])) ? $data['mail'] : null;
        $this->age        = (isset($data['age'])) ? $data['age'] : null;
    }

    private function setUsername($username)
    {
        $this->username = $username;
    }
    public function getUsername()
    {
        return $this->username;
    }

    private function setPassword($password)
    {
        $this->password = $password;
    }
    public function getPassword()
    {
        return $this->password;
    }

    private function setMail($mail)
    {
        $this->mail = $mail;
    }
    public function getMail()
    {
        return $this->mail;
    }

    private function setAge($age)
    {
        $this->age = $age;
    }
    public function getAge()
    {
        return $this->age;
    }
}