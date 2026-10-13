<?php

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "contact_manager";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("خطا در اتصال به دیتابیس: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");