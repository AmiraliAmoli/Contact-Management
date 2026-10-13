<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>سیستم مدیریت مخاطبین</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/web2prj/assets/css/style.css">

</head>

<body>

<header class="header">

    <div class="container">

        <div class="logo">

            <a href="/web2prj/index.php">
                سیستم مدیریت مخاطبین
            </a>

        </div>

        <nav>

            <ul class="menu">

                <li>
                    <a href="/web2prj/index.php">
                        خانه
                    </a>
                </li>

                 <?php if(isset($_SESSION['user_id'])){ ?>
                <li>
                    <a href="/web2prj/dashboard.php">
                        داشبورد
                    </a>
                </li>
                <li>
                    <a href="/web2prj/logout.php">
                        خروج
                    </a>
                </li>

                 <?php } else { ?>
                <li>
                    <a href="/web2prj/login.php">
                        ورود
                    </a>
                </li>
                <?php } ?>

            </ul>

        </nav>

    </div>

</header>