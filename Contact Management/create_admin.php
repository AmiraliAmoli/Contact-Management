<?php

include 'config/database.php';

$username = "admin";
$fullname = "Administrator";
$password = password_hash("123456", PASSWORD_DEFAULT);

$sql = "INSERT INTO users(username, password, fullname)
        VALUES (?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "sss", $username, $password, $fullname);

if (mysqli_stmt_execute($stmt)) {
    echo "کاربر با موفقیت ایجاد شد.";
} else {
    echo "خطا در ایجاد کاربر.";
}