<?php


class User
{
    private $id,$name,$lastnames,$email,$password,$role,$banned,$activated,$last_session,$token_pass,$token_login;

    public function __construct($name, $lastnames, $email, $password)
    {
        $this->name = $name;
        $this->lastnames = $lastnames;
        $this->email = $email;
        $this->password = $password;
        $this->role = 0;
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

    public function getId()
    {
        return $this->id;
    }

    public function getRole(): int
    {
        return $this->role;
    }

    public function getBanned(): int
    {
        return $this->banned;
    }

    public function getActivated(): int
    {
        return $this->activated;
    }

    public function getLastSession()
    {
        return $this->last_session;
    }

    public function getTokenPass(): string
    {
        return $this->token_pass;
    }

    public function getTokenLogin()
    {
        return $this->token_login;
    }

    public function setId($id){
        $this->id = $id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setLastnames($lastnames)
    {
        $this->lastnames = $lastnames;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setRole(int $role)
    {
        $this->role = $role;
    }

    public function setBanned(int $banned)
    {
        $this->banned = $banned;
    }

    public function setActivated(int $activated)
    {
        $this->activated = $activated;
    }

    public function setLastSession($last_session)
    {
        $this->last_session = $last_session;
    }

    public function setTokenPass(string $token_pass)
    {
        $this->token_pass = $token_pass;
    }

    public function setTokenLogin($token_login)
    {
        $this->token_login = $token_login;
    }



    public function  getDataInsertSql(){
        return array($this->email,$this->name,$this->lastnames,$this->password,$this->role,$this->banned,$this->activated,$this->last_session,$this->token_login,$this->token_pass);
    }

}