<?php
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Blocked.");
}
if (session_status() === PHP_SESSION_NONE) session_start();
$_SESSION["username"] = $_POST["username"];
$_SESSION["password"] = $_POST["password"];
echo $_SESSION["username"] . " - " . $_SESSION["password"];
?>