<?php

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Blocked.");
}

if (session_status() === PHP_SESSION_NONE) session_start();
$connection = null;
try {
    $servername = "localhost";
    $username = $_SESSION["username"]; //"root";
    $password = $_SESSION["password"]; //"p";
    $dbname = "notes";
    $connection = new mysqli($servername, $username, $password, $dbname);
}
catch (Exception $e) {
    die("Couldn't connect to database.");
}

try {
    $table = $_POST["table"];
    $id = $_POST["id"];
    $message = $_POST["message"];
    $done = $_POST["done"];
    
    $sql = "";
    if ($message !== "") {
        $sql = "UPDATE $table SET message='$message', done=$done WHERE id=$id;";
    }
    else {
        $sql = "UPDATE $table done=$done WHERE id=$id;";
    }

    $connection->query($sql);
    echo "Success";
}
catch (Exception $e) {
    die($e);
}

?>