<?php
include 'includes/auth.php';
include 'includes/header.php';
include 'config/database.php';


$edit=false;
$user_id = $_SESSION['user_id'];


if(isset($_GET['id'])){

    $edit=true;

    $id = (int)$_GET['id'];

    $sql="SELECT *
          FROM contacts
          WHERE id=? AND user_id=?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        die(mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt,"ii",$id,$user_id);

    mysqli_stmt_execute($stmt);

    $result=mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) != 1) {
    header("Location: dashboard.php");
    exit;
    }

    $contact=mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

}
?>

<main>

<div class="card">

    <h2><?php echo $edit ? "ویرایش مخاطب" : "افزودن مخاطب"; ?></h2>

    <form
        action="<?php echo $edit ? 'contacts/edit.php' : 'contacts/add.php'; ?>"
        method="POST"
        enctype="multipart/form-data">

        <?php if($edit){ ?>

            <input
                type="hidden"
                name="id"
                value="<?php echo $contact['id']; ?>">

        <?php } ?>

        <div class="row">

            <div class="form-group">

                <label>نام مخاطب</label>

                <input
                    type="text"
                    name="fullname"
                    required
                    minlength="3"
                    maxlength="100"
                    value="<?php echo $edit ? htmlspecialchars($contact['fullname']) : ''; ?>">

            </div>

            <div class="form-group">

                <label>تلفن</label>

                <input
                    type="text"
                    name="phone"
                    pattern="[0-9]{8,15}"
                    maxlength="11"
                    value="<?php echo $edit ? htmlspecialchars($contact['phone']) : ''; ?>">

            </div>

        </div>


        <div class="row">

            <div class="form-group">

                <label>موبایل</label>

                <input
                    type="text"
                    name="mobile"
                    required
                    pattern="09[0-9]{9}"
                    maxlength="11"
                    value="<?php echo $edit ? htmlspecialchars($contact['mobile']) : ''; ?>">

            </div>

            <div class="form-group">

                <label>ایمیل</label>

                <input
                    type="email"
                    name="email"
                    value="<?php echo $edit ? htmlspecialchars($contact['email']) : ''; ?>">

            </div>

        </div>


        <div class="form-group">

            <label>آدرس</label>

            <textarea
                name="address"
                rows="4"
                maxlength="500"><?php echo $edit ? htmlspecialchars($contact['address']) : ''; ?></textarea>

        </div>


        

            <?php if($edit){ ?>

            <div class="form-group">

                <label>عکس فعلی</label>

                <?php
                $image = !empty($contact['photo'])
                    ? $contact['photo']
                    : 'default.png';
                ?>

                <img
                    src="assets/uploads/contacts/<?php echo htmlspecialchars($image); ?>"
                    class="avatar-preview">

            </div>

            <?php } ?>


        <div class="form-group">

            <label>عکس مخاطب</label>

            <input
                type="file"
                name="photo"
                accept=".jpg,.jpeg,.png,.webp">

            <?php if($edit){ ?>

                <small>
                    اگر عکس جدید انتخاب نکنید، عکس قبلی حفظ خواهد شد.
                </small>

            <?php } ?>

        </div>


        <button class="btn btn-primary">

            <?php echo $edit ? "ویرایش مخاطب" : "ثبت مخاطب"; ?>

        </button>

    </form>

</div>




    <?php 

    $showActions = true;

    include 'contacts/view.php';
    ?>





</main>

<?php
include 'reports.php';
include 'includes/footer.php';
?>