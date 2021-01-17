<?php

class Address
{
    public $id, $alias, $user_id, $name, $lastnames, $email, $phone, $country, $address, $city,$province ,$postal_code, $nif, $created_at;

    public function __construct($alias, $user_id, $name, $lastnames, $email, $phone, $country,$province ,$address, $city, $postal_code, $nif, $created_at)
    {
        $this->nif = $nif;
        $this->alias = $alias;
        $this->user_id = $user_id;
        $this->name = $name;
        $this->lastnames = $lastnames;
        $this->province = $province;
        $this->email = $email;
        $this->phone = $phone;
        $this->country = $country;
        $this->address = $address;
        $this->city = $city;
        $this->postal_code = $postal_code;
        $this->created_at = ($created_at != null) ? $created_at : getCurrentDateTime();
    }

    public function updateChanges()
    {
        $database = new Database();
        if ($this->id != null) {
            $database->executeQuery("UPDATE addresses SET nif=?,alias=?,user_id=?,name=?,lastnames=?,email=?,phone=?,country=?,province=?,address=?,city=?,postal_code=?,created_at=?", $this->getDataForDatabase());
        } else {
            $database->executeQuery("INSERT INTO addresses (nif,alias,user_id,name,lastnames,email,phone,country,province,address,city,postal_code,created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)", $this->getDataForDatabase());
            $this->id = $database->executeQuery("SELECT id FROM addresses WHERE nif = ? AND alias = ? AND user_id=? AND name=? AND lastnames=? AND email=? AND phone=? AND country=? AND province=?AND address=? AND city=? AND postal_code=? AND created_at=? ORDER BY id DESC", $this->getDataForDatabase())[0]['id'];
        }
        $database->closeConnection();
        return $this->id;
    }

    public function getDataForDatabase()
    {
        return array(
            $this->nif, $this->alias, $this->user_id, $this->name, $this->lastnames, $this->email, $this->phone, $this->country,$this->province ,$this->address, $this->city, $this->postal_code, $this->created_at
        );
    }


}