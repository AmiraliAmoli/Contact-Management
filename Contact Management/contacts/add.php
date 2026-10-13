<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: ../dashboard.php");
    exit;
}



    $user_id  = $_SESSION['user_id'];

    $fullname = trim($_POST['fullname']);
    $phone    = trim($_POST['phone']);
    $mobile   = trim($_POST['mobile']);
    $email    = trim($_POST['email']);
    $address  = trim($_POST['address']);

$photo = "default.png";


if (isset($_FILES['photo'])) {

    // بررسی خطاهای آپلود
    if ($_FILES['photo']['error'] != UPLOAD_ERR_OK &&
        $_FILES['photo']['error'] != UPLOAD_ERR_NO_FILE) {

        die("خطا در بارگذاری فایل.");
    }

    // اگر کاربر فایلی انتخاب کرده باشد
    if ($_FILES['photo']['error'] == UPLOAD_ERR_OK) {

        // پسوندهای مجاز
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        // استخراج پسوند
        $extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

        // بررسی پسوند
        if (!in_array($extension, $allowedExtensions)) {
            die("فرمت عکس مجاز نیست.");
        }

        // بررسی حجم
        if ($_FILES['photo']['size'] > 5 * 1024 * 1024) {
            die("حجم فایل نباید بیشتر از 5 مگابایت باشد.");
        }

        // بررسی واقعی بودن تصویر
        if (getimagesize($_FILES['photo']['tmp_name']) === false) {
            die("فایل انتخاب شده تصویر نیست.");
        }

        // نام یکتا
        $fileName = date("YmdHis") . "_" . uniqid() . "." . $extension;

        // مسیر ذخیره
        $target = "../assets/uploads/contacts/" . $fileName;

        // انتقال فایل
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {

            $photo = $fileName;

        } else {

            die("خطا در ذخیره فایل.");

        }

    }

}

    if(empty($fullname)){
    die("نام الزامی است.");
}

if(strlen($fullname) < 3){
    die("نام باید حداقل ۳ کاراکتر باشد.");
}

if(strlen($fullname) > 100){
    die("نام بیش از حد مجاز است.");
}

if(!preg_match('/^09[0-9]{9}$/',$mobile)){
    die("شماره موبایل معتبر نیست.");
}

if(!empty($phone)){

    if(!preg_match('/^[0-9]{8,15}$/',$phone)){

        die("شماره تلفن معتبر نیست.");

    }

}

if(!empty($email)){

    if(!filter_var($email,FILTER_VALIDATE_EMAIL)){

        die("ایمیل معتبر نیست.");

    }

}

if(strlen($address)>500){

    die("آدرس بیش از حد مجاز است.");

}

    $sql = "SELECT id
        FROM contacts
        WHERE user_id=?
        AND mobile=?";

        $stmt = mysqli_prepare($conn,$sql);

        mysqli_stmt_bind_param(
            $stmt,
            "is",
            $user_id,
            $mobile
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if(mysqli_num_rows($result)>0){
            
            $file = "../assets/uploads/contacts/".$photo;
            if($photo != "default.png" && file_exists($file) ){
            unlink($file);
            }   

            die("این شماره موبایل قبلاً ثبت شده است.");

        }


        if(!empty($email)){

            $sql = "SELECT id
                    FROM contacts
                    WHERE user_id=?
                    AND email=?";

            $stmt = mysqli_prepare($conn,$sql);

            mysqli_stmt_bind_param(
                $stmt,
                "is",
                $user_id,
                $email
            );

            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            if(mysqli_num_rows($result)>0){

                $file = "../assets/uploads/contacts/".$photo;

                if($photo != "default.png" && file_exists($file)){
                    unlink($file);
                }

                die("این ایمیل قبلاً ثبت شده است.");

            }

        }

    $sql = "INSERT INTO contacts
    (user_id, fullname, phone, mobile, email, address, photo)
    VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            die("خطا در آماده‌سازی کوئری: " . mysqli_error($conn));
        }

    mysqli_stmt_bind_param(
        $stmt,
        "issssss",
        $user_id,
        $fullname,
        $phone,
        $mobile,
        $email,
        $address,
        $photo
    );

    if(mysqli_stmt_execute($stmt)){

        header("Location: ../dashboard.php");

        exit;

    }
    if(mysqli_errno($conn) == 1062){
    die("شماره موبایل یا ایمیل قبلاً ثبت شده است.");
}

die("خطا در ثبت اطلاعات: " . mysqli_error($conn));

