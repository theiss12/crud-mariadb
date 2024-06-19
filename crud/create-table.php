<?php

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Blocked.");
}

if (session_status() === PHP_SESSION_NONE) session_start();
$connection = null;
try {
    $servername = "localhost";
    $username = $_SESSION["username"];
    $password = $_SESSION["password"];
    $dbname = "notes";
    $connection = new mysqli($servername, $username, $password, $dbname);
}
catch (Exception $e) {
    die("Couldn't connect to database.");
}

try {
    $table = $_POST["table"];
    $sql = "
        CREATE TABLE $table (
            id int NOT NULL AUTO_INCREMENT,
            message varchar(255) NOT NULL,
            done bool NOT NULL,
            PRIMARY KEY (id)
        )
    ";
    $connection->query($sql);
    echo "Success";
}
catch (Exception $e) {
    die($e);
}

?>