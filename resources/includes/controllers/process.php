<?php
namespace php_sys\system;

if (isset($_POST['process'])) {
    $process = $_POST['process'];
} else {
    echo _msg(500, "Invalid Arguments!");
    exit;
}

if ($process == "login") {
    login();
    exit;
} else if ($process == "logout") {
    session_destroy();
} else {
    echo _msg(500, "Invalid Arguments!");
    exit;
}

function login()
{

    if (!isset($_POST["username"]) or !isset($_POST["password"])) {
        echo _msg(500, "Please make sure Username and Password field is not empty, or refresh the web page!");
        exit;
    }

    $uname = $_POST["username"];
    $pass = $_POST["password"];    

    $query = "SELECT * FROM tbl_users WHERE fld_username = '" . $uname . "'";
    
    $row = ExecuteRow($query);

    if ($row) {

        if (md5($pass) == $row['fld_password']) {

            $data = [
                "id" => md5($row['id']),
                "fname" => $row['fld_firstname'],
                "lname" => $row['fld_lastname'],
                "email" => $row['fld_email'],
                "image" => $row['img'],
                "position" => $row['position'],
                "lob" => $row['lob'],
                "company" => $row['company'],
                "contact" => $row['contact'],
            ];

            $_SESSION['user_login'] = "YES";
            $_SESSION['user_id'] = md5($row['id']);
            $_SESSION['user_name'] = $row['fld_lastname'] . ", " . $row['fld_firstname'];
            $_SESSION['firstname'] = $row['fld_firstname'];
            $_SESSION['lastname'] = $row['fld_lastname'];
            $_SESSION['user_username'] = $row['fld_username'];
            $_SESSION['position'] =  $row['position'];
            $_SESSION['lob'] =  $row['lob'];
            $_SESSION['contact'] =  $row['contact'];
            $_SESSION['email'] =  $row['fld_email'];
            $_SESSION['avatar'] =  $row['img'];
            $_SESSION['company'] =  $row['company'];

            $insert_logs = "INSERT INTO `sie_management`.`tbl_logs` (`user_id`, `process` ,`notes`) VALUES ($row[id], 'LOGIN', 'LOGGED IN @" . get_client_ip() . "');";
            Execute($insert_logs);
            echo _msg(200, $data);

        } else {

            echo _msg(500, "INVALID PASSWORD.");
            exit;
        }
    } else {
        // echo _msg(500, "INVALID USERNAME.");
        exit;
    }
}
