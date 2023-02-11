<?php

// model class, get model blueprint or/and data by id
class Model
{
    private array  $data               = array();
    private array  $response_data      = array();
    private String $table              = '';
    private PDO $connection;
    public function __construct(String $table, int $id = null)
    {
        global $connection;
        $this->connection              = $connection;
        $this->table                   = $table;
        try {
            $stmt                      = $this->connection->query("DESCRIBE $this->table");
            $results                   = $stmt->fetchAll();

            if (isset($id)) {
                $stmt2                 = $this->connection->prepare("SELECT * FROM $this->table WHERE id=:id");
                $stmt2->execute(['id' => $id]);
                $table_data            = $stmt2->fetch();
                $this->data            = $table_data;
            }

            if (isset($results)) {
                foreach ($results as $result) {
                    $this->{$result['Field']} = null;
                }
            }
        } catch (\PDOException $e) {
            return $e->getMessage();
        }
    }

    // get result if model id is passed to the constructor
    public function getResult()
    {
        if (!empty($this->data)) {
            $this->response_data['success'] = true;
            $this->response_data['message'] = $this->data;
            return $this->response_data;
        }
    }

    // handle reading data from database
    public function read(array $conditions, String $join = '')
    {
        ksort($conditions);
        $keys = array_keys($conditions);
        $positional_values = [];

        $positional_arguments = "";
        foreach ($keys as $key) {
            if ($join) {
                $positional_arguments .= $key . "=? ".$join." ";
            }else{
                $positional_arguments .= $key . "=?";
            }
            array_push($positional_values, $conditions[$key]);
        }
        $query_arguments = rtrim($positional_arguments, " $join ");
        $sql = "SELECT * FROM $this->table WHERE $query_arguments";
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($positional_values);
            $result = $stmt->rowCount();
            if ($result > 1) {
                $this->response_data['success'] = true;
                $this->response_data['message'] = $stmt->fetchAll();
                return $this->response_data;
            }
            if ($result > 0) {
                $this->response_data['success'] = true;
                $this->response_data['message'] = $stmt->fetch();
                return $this->response_data;
            }
            $this->response_data['success'] = true;
            $this->response_data['message'] = null;
            return $this->response_data;
        } catch (\PDOException $e) {
            $this->response_data['success'] = false;
            $this->response_data['message'] = $e->getMessage();
            return $this->response_data;
        }
    }

    // handle data changes in the database
    public function change(array $data_params=array(), String $join=''){
        ksort($data_params);
        
        $positional_values = [];
        $key   = $data_params['key_name'];
        $value = $data_params['key_value'];
        if($join){
            $key_opt   = $data_params['key_name_opt'];
            $value_opt = $data_params['key_value_opt'];
        }
        if($join){
            unset($data_params['key_name_opt']);
            unset($data_params['key_value_opt']);
        }
        unset($data_params['key_name']);
        unset($data_params['key_value']);

        $keys = array_keys($data_params);
        $positional_arguments = "";
        foreach ($keys as $key) {
            $positional_arguments .= $key . "=?,";
            array_push($positional_values, $data_params[$key]);
        }
        $query_arguments = rtrim($positional_arguments, ",");
        
        if($join){
            $sql = "UPDATE $this->table SET $query_arguments WHERE $key = '$value' $join $key_opt = '$value_opt'";
        }else{
            $sql = "UPDATE $this->table SET $query_arguments WHERE $key = '$value'";
        }
        
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($positional_values);
            $result = $stmt->rowCount();
            if ($result) {
                return true;
            }
            return false;
        } catch (\PDOException $e) {
            $this->response_data['success'] = false;
            $this->response_data['message'] = $e->getMessage();
            return $this->response_data;
        }

    }

    // handle custom queries
    public function query($sql, $values=array()){
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($values);
            $result = $stmt->rowCount();
            if ($result > 1) {
                $this->response_data['success'] = true;
                $this->response_data['message'] = $stmt->fetchAll();
                return $this->response_data;
            }
            if ($result) {
                $this->response_data['success'] = true;
                $this->response_data['message'] = $stmt->fetch();
                return $this->response_data;
            }
            $this->response_data['success'] = true;
            $this->response_data['message'] = null;
            return $this->response_data;
        } catch (\PDOException $e) {
            $this->response_data['success'] = false;
            $this->response_data['message'] = $e->getMessage();
            return $this->response_data;
        }
    }

    // handle data storage into database
    public function store(array $data){
        ksort($data);
        $keys = array_keys($data);
        $values = array_values($data);
        $columns = '';
        $positonal_value_marks = '';
        foreach($keys as $key){
            $columns .= $key.',';
            $positonal_value_marks .= '?,';
        }
        $columns_string = rtrim($columns, ',');
        $mark_placeholders = rtrim($positonal_value_marks, ',');

        $sql = "INSERT INTO $this->table ($columns_string) VALUES ($mark_placeholders)";
        die($sql);

        try{
            $stmt            = $this->connection->prepare($sql);
            $stmt->execute($values);
            $result          = $stmt->rowCount();
            if($result){
                $data['success'] = true;
                $data['message'] = $this->connection->lastInsertId();
                return $data;
            }
        }catch(PDOException $e){
            $data['success'] = false;
            $data['message'] = $e->getMessage();
            return $data;
        }
        
    }
}

