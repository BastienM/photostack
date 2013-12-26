<?php

namespace Application\Model;

class Authentification
{
    private $username;
    private $lastTry;
    private $numberTry;
    private $isBlocked;

    public function exchangeArray($data)
    {
        $this->username = (isset($data['username'])) ? $data['username'] : null;
        $this->lastTry = (isset($data['lastTry'])) ? $data['lastTry'] : null;
        $this->numberTry = (isset($data['numberTry'])) ? $data['numberTry'] : null;
        $this->isBlocked = (isset($data['isBlocked'])) ? $data['isBlocked'] : null;
    }

    private function setUsername($username)
    {
        $this->username = $username;
    }

    public function getUsername()
    {
        return $this->username;
    }

    private function setLastTry($lastTry)
    {
        $this->lastTry = $lastTry;
    }

    public function getlastTry()
    {
        return $this->lastTry;
    }

    private function setNumberTry($numberTry)
    {
        $this->numberTry = $numberTry;
    }

    public function getNumerTry()
    {
        return $this->numerTry;
    }

    private function setIsBlocked($isBlocked)
    {
        $this->isBlocked = $isBlocked;
    }

    public function getIsBlocked()
    {
        return $this->isBlocked;
    }
}