<?php
class Crud {
    public $hostname, $database, $username, $password, $connection;

    public function __construct() {
        $this->hostname = "hostname";
        $this->database = "database";
        $this->username = "username";
        $this->password = "password";

        try {
            $this->connection = new PDO("mysql:host=$this->hostname;dbname=$this->database", $this->username, $this->password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $error) {
            $json = array('error' => $error->getMessage(), 'date' => date("Y-m-d H:i:s"), 'return' => false);
            echo json_encode($json);
            // self::error($error);
        }
    }
    function custom($sql) {
        try {
            $statement = $this->connection->prepare($sql);
            $result = $statement->execute();
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC); // assuming $result == true
            return $rows;
        } catch (PDOException $error) {
            $this->error($error);
        }
    }
    function select($table, $condition=null, $limit=null) {
        $sql = "SELECT * FROM $table";
        $condition = ($condition!='') ? $sql .= " WHERE $condition" : null;
        $limit = ($limit!='') ? $sql .= " LIMIT $limit" : null;

        try {
            $statement = $this->connection->prepare($sql);
            $result = $statement->execute();
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC); // assuming $result == true
            return $rows;
        } catch (PDOException $error) {
            $this->error($error);
        }
    }
    function delete($table, $condition=null) {
        $sql = "DELETE FROM `$table`";
        $condition = ($condition!='') ? $sql .= " WHERE $condition" : $sql .= "WHERE id = 0";

        try {
            $statement = $this->connection->prepare($sql);
            $statement->execute();
            $status = ($statement->rowCount()) ? true : false;
            $delete = array('delete' => $status, 'totalRow' => $statement->rowCount());
            return $delete;
        } catch (PDOException $error) {
            $this->error($error);
        }
    }
    function insert($table, $array) {
        $sql = "INSERT INTO $table (`";
        $key = array_keys($array);
        $value = array_values($array);
        $sql .= implode("`, `", $key);
        $sql .= "`) VALUES ('";
        $sql .= implode("', '", $value);
        $sql .= "')";

        $sql1="SELECT MAX( id ) FROM `$table`";
        try {
            $statement = $this->connection->prepare($sql);
            $statement->execute();
            $statement2 = $this->connection->prepare($sql1);
            $statement2->execute();
            $rows = $statement2->fetchAll(); // assuming $result == true
            $insert = array('insert' => true, 'id' => $rows[0][0]);
            return $insert;
        } catch (PDOException $error) {
            $this->error($error);
        }
    }
    function update($table, $array, $condition) {
        $sql = "UPDATE `$table` SET ";
        $fields = array();
        foreach ($array as $k => $v) {
            $fields[] = "`$k` = '$v'";
        }
        $sql .= implode(", ", $fields);
        $sql .= " WHERE " . $condition;

        $sql = $this->filter($sql);

        try {
            $statement = $this->connection->prepare($sql);
            $statement->execute();
            $status = ($statement->rowCount()) ? true : false;
            $update = array('update' => $status, 'totalRow' => $statement->rowCount());
            return $update;
        } catch (PDOException $error) {
            $this->error($error);
        }
    }

    function filter($sql){
        $sql = strip_tags($sql,'<b><i><br>');
        return $sql;
    }
    function count($rows){
        $number = count($rows);
        return $number;
    }
    function error($error) {
        $json = array('error' => $error->getMessage(), 'date' => date("Y-m-d H:i:s"), 'return' => false);
        exit(json_encode($json));
    }
}
