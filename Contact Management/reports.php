<?php

include 'includes/auth.php';
include 'config/database.php';


$user_id = $_SESSION['user_id'];

$sql = "SELECT COUNT(*) total
        FROM contacts
        WHERE user_id=?";

$stmt = mysqli_prepare($conn,$sql);
if(!$stmt){
    die("خطا در آماده‌سازی کوئری: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt,"i",$user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$totalContacts = mysqli_fetch_assoc($result)['total'];

mysqli_stmt_close($stmt);


$sql = "SELECT COUNT(*) total
        FROM contacts
        WHERE user_id=?
        AND photo IS NOT NULL
        AND photo<>'default.png'
        AND photo<>''";

$stmt = mysqli_prepare($conn,$sql);
if(!$stmt){
    die("خطا در آماده‌سازی کوئری: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt,"i",$user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$totalPhotos = mysqli_fetch_assoc($result)['total'];

mysqli_stmt_close($stmt);



$sql = "SELECT COUNT(*) total
        FROM contacts
        WHERE user_id=?
        AND (photo IS NULL OR photo='default.png' OR photo='')";

$stmt = mysqli_prepare($conn,$sql);
if(!$stmt){
    die("خطا در آماده‌سازی کوئری: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt,"i",$user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$withoutPhotos = mysqli_fetch_assoc($result)['total'];

mysqli_stmt_close($stmt);



$sql = "SELECT COUNT(*) total
        FROM contacts
        WHERE user_id=?
        AND email IS NOT NULL
        AND email<>''";

$stmt = mysqli_prepare($conn,$sql);
if(!$stmt){
    die("خطا در آماده‌سازی کوئری: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt,"i",$user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$totalEmails = mysqli_fetch_assoc($result)['total'];
mysqli_stmt_close($stmt);


$sql = "SELECT COUNT(*) total
        FROM contacts
        WHERE user_id=?
        AND (email IS NULL OR email='')";

$stmt = mysqli_prepare($conn,$sql);
if(!$stmt){
    die("خطا در آماده‌سازی کوئری: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt,"i",$user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$withoutEmails = mysqli_fetch_assoc($result)['total'];
mysqli_stmt_close($stmt);


?>




<main>

<div class="card">

<h1>گزارش آماری مخاطبین</h1>
<p>تاریخ گزارش:
<?= date("Y/m/d H:i"); ?>
</p>    



</div>

<div class="report-grid">

    <div class="report-card">

        <h3>کل مخاطبین</h3>

        <span><?php echo $totalContacts; ?></span>

    </div>

    <div class="report-card">

        <h3>دارای عکس</h3>

        <span><?php echo $totalPhotos; ?></span>

    </div>

    <div class="report-card">

        <h3>بدون عکس</h3>

        <span><?php echo $withoutPhotos; ?></span>

    </div>

    <div class="report-card">

        <h3>دارای ایمیل</h3>

        <span><?php echo $totalEmails; ?></span>

    </div>

    <div class="report-card">

        <h3>بدون ایمیل</h3>

        <span><?php echo $withoutEmails; ?></span>

    </div>

</div>

<button onclick="window.print()" class="btn btn-primary"> چاپ گزارش </button>

<a href="export.php" class="btn btn-primary btn-success"> دانلود فایل CSV </a>

</main>

<?php
mysqli_close($conn);
?>