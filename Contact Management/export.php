<?php

include 'includes/auth.php';
include 'config/database.php';

$user_id = $_SESSION['user_id'];

// تنظیم هدرهای دانلود
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="contacts.csv"');

// جلوگیری از نمایش حروف فارسی به صورت ناخوانا در Excel
echo "\xEF\xBB\xBF";

$output = fopen("php://output", "w");

// سطر عنوان
fputcsv($output, [
    'ID',
    'نام',
    'تلفن',
    'موبایل',
    'ایمیل',
    'آدرس'
]);

$sql = "SELECT id,
               fullname,
               phone,
               mobile,
               email,
               address
        FROM contacts
        WHERE user_id=?
        ORDER BY fullname ASC";

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    die("خطا در آماده‌سازی کوئری: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($result)) {

    fputcsv($output, [
        $row['id'],
        $row['fullname'],
        $row['phone'],
        $row['mobile'],
        $row['email'],
        $row['address']
    ]);

}

mysqli_stmt_close($stmt);
mysqli_close($conn);

fclose($output);

exit;