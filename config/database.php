
<?php

class Database {

    private $host = "localhost";
    private $database = "maranatha_shop";
    private $username = "root";
    private $password = "";

    public $connection;

    public function connect() {

        $this->connection = null;

        try {

            $this->connection = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->database . ";charset=utf8mb4",
                $this->username,
                $this->password
            );

            $this->connection->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

        } catch (PDOException $error) {

            die("Error de conexión: " . $error->getMessage());

        }

        return $this->connection;
    }
}
