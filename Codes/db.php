<?php
class db_connection
{

    public $host = "localhost";
    public $dbname = "sie_schedule_project";
    public $username = "root";
    public $password = "";
    public $conn;

    public function execute_query($query, $params = [])
    {
        try {
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $statement = $this->conn->prepare($query);
            $statement->execute($params);

            if (strpos($query, "INSERT") <> "") {
                return $this->conn->lastInsertId();
            } else {
                return $statement;
            }


        } catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }


}

$db = new db_connection;

function ExecuteRow($q, $d = [])
{
    global $db;
    $res = $db->execute_query($q, $d);
    if ($res) {
        return $res->fetch(PDO::FETCH_ASSOC);
    } else {
        return null;
    }

}

function ExecuteRows($q, $d = [])
{
    global $db;
    $res = $db->execute_query($q, $d);
    if ($res) {
        return $res->fetchAll(PDO::FETCH_ASSOC);
    } else {
        return null;
    }
}

function Execute($q, $d = [])
{
    global $db;
    return $db->execute_query($q, $d);
}

function ExecuteJSON($q, $d = [])
{

    $rows = ExecuteRows($q, $d);
    if ($rows) {
        $dataset = array(
            "status" => 200,
            "message" => 'success',
            "totalrecords" => count($rows),
            "data" => $rows
        );
        echo json_encode($dataset);
    } else {
        _msg(400, "Record Not Found!");
    }

}

function _msg($c, $m)
{
    $r = array("status" => $c, "message" => $m);
    echo json_encode($r);
}

?>