<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';


if (!isset($_GET['id'])) {
    header("Location: ../dashboard.php");
    exit;
}

$id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| دریافت عکس مخاطب
|--------------------------------------------------------------------------
*/

$sql = "SELECT photo
        FROM contacts
        WHERE id=? AND user_id=?";

$stmt = mysqli_prepare($conn, $sql);

if(!$stmt){
    die("خطا در آماده‌سازی کوئری: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) != 1) {
    die("مخاطب پیدا نشد.");
}

$contact = mysqli_fetch_assoc($result);

/*
|--------------------------------------------------------------------------
| حذف عکس
|--------------------------------------------------------------------------
*/

if ($contact['photo'] != "default.png") {

    $photoPath = "../assets/uploads/contacts/" . $contact['photo'];

    if (file_exists($photoPath)) {
        unlink($photoPath);
    }

}

/*
|--------------------------------------------------------------------------
| حذف از دیتابیس
|--------------------------------------------------------------------------
*/

$sql = "DELETE FROM contacts
        WHERE id=? AND user_id=?";

$stmt = mysqli_prepare($conn, $sql);
if(!$stmt){
    die("خطا در آماده‌سازی کوئری: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);

if (mysqli_stmt_execute($stmt)) {

    header("Location: ../dashboard.php");
    exit;

} else {

    die("خطا در حذف مخاطب.");

}