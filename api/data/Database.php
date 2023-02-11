<?php

// Database connection and queries class
class Database{
    private PDO $connection;
    protected array $response = array();
    

    public function __construct(String $dsn, String $username, String $password) {
        try{
            $options = [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $this->connection = new \PDO($dsn, $username, $password, $options);
        }catch(PDOException $e){
            $this->response['success'] = false;
            $this->response['message'] = $e->getMessage();
            return json_encode($this->response);
        }
        
    }

    public function getConnection(): object {
            return $this->connection;
    }

    

}