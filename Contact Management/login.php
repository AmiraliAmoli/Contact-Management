<?php
session_start();

if(isset($_SESSION['user_id'])){
    header("Location: dashboard.php");
    exit;
}

include 'config/database.php';

$error = "";

if(isset($_POST['login'])){

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM users WHERE username=?";

    $stmt = mysqli_prepare($conn, $sql);

    if(!$stmt){
        die("خطا در آماده‌سازی کوئری: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt,"s",$username);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($result)==1){

        $user = mysqli_fetch_assoc($result);

        if(password_verify($password, $user['password'])){

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['last_activity'] = time();

            header("Location: dashboard.php");
            exit;
    }else{

            $error = "نام کاربری یا رمز عبور اشتباه است.";

        }

    }else{

        $error = "نام کاربری یا رمز عبور اشتباه است.";

    }

}
?>

<?php include 'includes/header.php'; ?>
<main>

    <div class="login-box">

        <h2>ورود به سیستم</h2>

        <?php if(!empty($error)){ ?>
            <div class="alert">
                <?php echo $error; ?>
            </div>
        <?php } ?>

        <form method="post">

            <div class="form-group">

                <label>نام کاربری</label>

                <input
                    type="text"
                    name="username"
                    placeholder="نام کاربری خود را وارد کنید"
                    required>

            </div>

            <div class="form-group">

                <label>رمز عبور</label>

                <input
                    type="password"
                    name="password"
                    placeholder="رمز عبور"
                    required>

            </div>

            <button class="btn-login" name="login">

                ورود به سیستم

            </button>

        </form>

    </div>

</main>

<?php
if (isset($stmt)) {
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);

include 'includes/footer.php';
?>