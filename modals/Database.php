<?php

class Database
{
    private $host;
    private $db;
    private $dsn;
    private $user;
    private $pass;
    public $connection;

    public function __construct()
    {
        $this->host = "172.19.0.2";
        $this->db = "shop";
        $this->dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db . ';';
        $this->user = 'admin';
        $this->pass = "root";
        $this->openConnection();
        $this->createTables();
    }

    public function openConnection()
    {
        try {
            $this->connection = new PDO($this->dsn, $this->user, $this->pass);
            return $this->connection;
        } catch (PDOException $ex) {
            echo "Error: " . $ex;
            return null;
        }

    }

    public function closeConnection()
    {
        try {
            $this->connection = null;
            return $this->connection;
        } catch (PDOException $ex) {
            echo "Error: " . $ex;
            return null;
        }

    }

    public function executeQuery($query, $params)
    {
        try {
            $statement = $this->connection->prepare($query);
            $statement->execute($params);
            $result = $statement->fetchAll();
            return $result;
        } catch (PDOException $err) {
            echo "error";
        }
    }


    public function createTables()
    {
        try {
            $this->connection->query("CREATE TABLE IF NOT EXISTS users (
	                                        id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
                                            email varchar(255) NOT NULL,
                                            name varchar(255) NOT NULL,
                                            lastnames varchar(255) NOT NULL,
                                            password varchar(255) NOT NULL,
                                            role int DEFAULT 0,
                                            banned int DEFAULT 0,
                                            activated int DEFAULT 0,
                                            last_session DATE ,
                                            token_login varchar(255),
                                            token_pass varchar(255),
                                            image varchar(255));");
        } catch (PDOException $err) {
            echo $err;
        }
        /*
         * QUERY DATES
         * SELECT distinct(date) FROM day_room_busy WHERE room_id IN (SELECT id FROM rooms WHERE persons = 3 OR persons = 4) group by date having count(*)= (SELECT count(*) FROM rooms WHERE persons = 3 OR persons = 4);
         * */
    }
}