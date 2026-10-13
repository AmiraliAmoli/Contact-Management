<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';



if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: ../dashboard.php");
    exit;
}

$id       = intval($_POST['id']);
$user_id  = $_SESSION['user_id'];

$fullname = trim($_POST['fullname']);
$phone    = trim($_POST['phone']);
$mobile   = trim($_POST['mobile']);
$email    = trim($_POST['email']);
$address  = trim($_POST['address']);

/*
 اعتبارسنجی
*/

if (empty($fullname) || strlen($fullname) < 3 || strlen($fullname) > 100) {
    die("نام مخاطب معتبر نیست.");
}

if (!preg_match('/^09[0-9]{9}$/', $mobile)) {
    die("شماره موبایل معتبر نیست.");
}

if (!empty($phone) && !preg_match('/^[0-9]{8,15}$/', $phone)) {
    die("شماره تلفن معتبر نیست.");
}

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("ایمیل معتبر نیست.");
}

if (strlen($address) > 500) {
    die("آدرس بیش از حد مجاز است.");
}

/*
 دریافت اطلاعات مخاطب
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
    die("مخاطب یافت نشد.");
}

$contact = mysqli_fetch_assoc($result);

$photo = $contact['photo'];

/*
| آپلود عکس جدید
*/

if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {

    $allowedExtensions = ['jpg','jpeg','png','webp'];

    $extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

    if (!in_array($extension, $allowedExtensions)) {
        die("فرمت تصویر مجاز نیست.");
    }

    if ($_FILES['photo']['size'] > 2 * 1024 * 1024) {
        die("حجم تصویر نباید بیشتر از 2 مگابایت باشد.");
    }

    if (getimagesize($_FILES['photo']['tmp_name']) === false) {
        die("فایل انتخاب شده تصویر نیست.");
    }

    $newPhoto = date("YmdHis") . "_" . uniqid() . "." . $extension;

    $target = "../assets/uploads/contacts/" . $newPhoto;

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {

        if ($photo != "default.png") {

            $oldFile = "../assets/uploads/contacts/" . $photo;

            if (file_exists($oldFile)) {
                unlink($oldFile);
            }

        }

        $photo = $newPhoto;

    } else {

        die("خطا در آپلود تصویر.");

    }

}

/*
| بروزرسانی اطلاعات
*/
$sql = "SELECT id
        FROM contacts
        WHERE user_id=?
        AND mobile=?
        AND id<>?";

$stmt = mysqli_prepare($conn,$sql);


mysqli_stmt_bind_param(
    $stmt,
    "isi",
    $user_id,
    $mobile,
    $id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result)>0){
    die("این شماره موبایل قبلاً ثبت شده است.");
}

if(!empty($email)){

    $sql = "SELECT id
            FROM contacts
            WHERE user_id=?
            AND email=?
            AND id<>?";

    $stmt = mysqli_prepare($conn,$sql);

    if(!$stmt){
        die(mysqli_error($conn));
    }

    mysqli_stmt_bind_param(
        $stmt,
        "isi",
        $user_id,
        $email,
        $id
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($result)>0){
        die("این ایمیل قبلاً ثبت شده است.");
    }

}



$sql = "UPDATE contacts SET

fullname=?,
phone=?,
mobile=?,
email=?,
address=?,
photo=?

WHERE id=? AND user_id=?";

$stmt = mysqli_prepare($conn, $sql);
if(!$stmt){
    die("خطا در آماده‌سازی کوئری: " . mysqli_error($conn));
}

mysqli_stmt_bind_param(
    $stmt,
    "ssssssii",
    $fullname,
    $phone,
    $mobile,
    $email,
    $address,
    $photo,
    $id,
    $user_id
);

if (mysqli_stmt_execute($stmt)) {

    header("Location: ../dashboard.php");
    exit;

} 
if(mysqli_errno($conn)==1062){
    die("شماره موبایل یا ایمیل قبلاً ثبت شده است.");
}

die(mysqli_error($conn));