<?php


class User
{
    private $id,$name,$lastnames,$email,$password,$group,$banned,$activated,$last_session,$token_pass,$token_login;

    public function __construct($name, $lastnames, $email, $password)
    {
        $this->name = $name;
        $this->lastnames = $lastnames;
        $this->email = $email;
        $this->password = $password;
        $this->group = 0;
        $this->banned = 0;
        $this->activated = 0;
        $this->last_session = null;
        $this->token_pass = bin2hex(random_bytes(16));
        $this->token_login = null;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getLastnames()
    {
        return $this->lastnames;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }



}