<?php

class Database
{
    private $host;
    private $charset;
    private $db;
    private $dsn;
    private $user;
    private $pass;
    public $connection;

    public function __construct()
    {
        $this->host = 'shop-php.cae6jdjevcjm.us-east-1.rds.amazonaws.com';
        $this->db = 'shop';
        $this->dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db . ';';
        $this->user = 'admin';
        $this->pass = "adminroot";
        $this->openConnection();
        $this->createTables();
    }

    public function getLastId(){
        return $this->connection->LastInsertId();
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
                                            last_session DATETIME ,
                                            token_login varchar(255),
                                            token_pass varchar(255),
                                            image varchar(255));");
            $this->connection->query("CREATE TABLE IF NOT EXISTS productCategory (
	                                        id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
                                            name varchar(255) NOT NULL,
                                            description varchar(255) NOT NULL,
                                            activated int DEFAULT 1,
                                            created_at DATETIME ,
                                            last_modified DATETIME);");
            $this->connection->query("CREATE TABLE IF NOT EXISTS products (
	                                        id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
                                            name varchar(255) NOT NULL,
                                            description varchar(255) NOT NULL,
                                            units int,
                                            iva int,
                                            price_iva float, 
                                            price_no_iva float,
                                            category_id int,
                                            activated int DEFAULT 1,
                                            tag_id int DEFAULT 0,
                                            created_at DATETIME ,
                                            last_modified DATETIME);");
            $this->connection->query("CREATE TABLE IF NOT EXISTS images_product (
	                                        id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
                                            id_product int,
                                            url varchar(255),
                                            name varchar(255),
                                            created_at DATETIME);");
            $this->connection->query("CREATE TABLE IF NOT EXISTS tags (
	                                        id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
                                            color varchar(255),
                                            name varchar(255),
                                            created_at DATETIME);");
            $this->connection->query("CREATE TABLE IF NOT EXISTS discounts (
	                                        id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
                                            id_product int NOT NULL,
                                            name varchar(255),
                                            discount float NOT NULL,
                                            highlight int DEFAULT 0,
                                            start_date DATETIME,
                                            end_date DATETIME,
                                            last_updated DATETIME,
                                            created_at DATETIME);");
            $this->connection->query("CREATE TABLE IF NOT EXISTS highlights (
	                                        id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
                                            product_id int NOT NULL,
                                            category_id int NOT NULL,
                                            highlight_type int NOT NULL,
                                            title varchar(255),
                                            url varchar(255),
                                            last_updated DATETIME,
                                            created_at DATETIME);");
            $this->connection->query("CREATE TABLE IF NOT EXISTS wishlist (
                id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
                id_product int NOT NULL,
                units int NOT NULL,
                id_user int NOT NULL,
                added_at DATETIME);");
            $this->connection->query("CREATE TABLE IF NOT EXISTS carts (
                id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
                user_id int NOT NULL,
                created_at DATETIME);");
            $this->connection->query("CREATE TABLE IF NOT EXISTS cartItems (
                id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
                cart_id int NOT NULL,
                product_id int NOT NULL,
                units int NOT NULL,
                created_at DATETIME);");
            $this->connection->query('CREATE TABLE IF NOT EXISTS commands(
                id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
                cart_id int NOT NULL,
                user_id int NOT NULL,
                status int,
                address_command_id int,
                sending_price float,
                created_at DATETIME);');
            $this->connection->query('CREATE TABLE IF NOT EXISTS commandItems(
                id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
                command_id int NOT NULL,
                units int NOT NULL,
                send_price float,
                discount int,
                category_id int,
                category_name varchar(255),
                category_desc varchar(255),
                product_id int,
                product_name varchar(255),
                product_desc varchar(255),
                product_iva int,
                price_iva_unit float,
                total_iva_price float,
                created_at DATETIME);');
            $this->connection->query('CREATE TABLE IF NOT EXISTS addresses(
                id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
                alias varchar(255),
                user_id int NOT NULL,
                name varchar(255),
                lastnames varchar(255),
                email varchar(255),
                phone varchar(255),
                country varchar(255),
                address varchar(255),
                city varchar(255),
                province varchar(255),
                postal_code varchar(255),
                nif varchar(255),
                created_at DATETIME);');
            $this->connection->query('CREATE TABLE IF NOT EXISTS addressesCommands(
                id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
                alias varchar(255),
                user_id int NOT NULL,
                name varchar(255),
                lastnames varchar(255),
                email varchar(255),
                phone varchar(255),
                country varchar(255),
                address varchar(255),
                city varchar(255),
                province varchar(255),
                postal_code varchar(255),
                nif varchar(255),
                created_at DATETIME);');
        } catch (PDOException $err) {
            echo $err;
        }
    }
}