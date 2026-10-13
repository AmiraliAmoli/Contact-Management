<div class="card">

    <h2>جستجوی مخاطب</h2>

    <form method="GET">

        <div class="search-box">

            <input
                type="text"
                name="search"
                placeholder="جستجو بر اساس نام، موبایل، تلفن یا ایمیل..."
                value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">

            <button class="btn btn-primary">
                جستجو
            </button>

        </div>

    </form>


<?php


$search = trim($_GET['search'] ?? '');

$limit = 5;

$page = max(1, (int)($_GET['page'] ?? 1));

$keyword = "%{$search}%";

/*
 محاسبه تعداد کل رکوردها
*/

if ($search != "") {

        $sqlCount = "SELECT COUNT(*) AS total
                    FROM contacts
                    WHERE
                    (
                        fullname LIKE ?
                        OR phone LIKE ?
                        OR mobile LIKE ?
                        OR email LIKE ?
                        OR address LIKE ?
                    )";

    $stmtCount = mysqli_prepare($conn, $sqlCount);

    if (!$stmtCount) {
        die(mysqli_error($conn));
    }

        mysqli_stmt_bind_param(
        $stmtCount,
        "sssss",
        $keyword,
        $keyword,
        $keyword,
        $keyword,
        $keyword
    );

} else {

    $sqlCount = "SELECT COUNT(*) AS total
             FROM contacts";

    $stmtCount = mysqli_prepare($conn, $sqlCount);

    if (!$stmtCount) {
        die(mysqli_error($conn));
    }


}

mysqli_stmt_execute($stmtCount);

$countResult = mysqli_stmt_get_result($stmtCount);

$totalRows = mysqli_fetch_assoc($countResult)['total'];

$totalPages = ceil($totalRows / $limit);

/*
 اعتبارسنجی شماره صفحه
*/

if ($page > $totalPages && $totalPages > 0) {
    header("Location: ?page=".$totalPages."&search=".urlencode($search));
    exit;
}

$offset = ($page - 1) * $limit;

/*
 دریافت مخاطبین
*/

if ($search != "") {

    $sql = "SELECT id, fullname, phone, mobile, email, address, photo
        FROM contacts
        WHERE
        (
            fullname LIKE ?
            OR phone LIKE ?
            OR mobile LIKE ?
            OR email LIKE ?
            OR address LIKE ?
        )
        ORDER BY id DESC
        LIMIT ? OFFSET ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        die(mysqli_error($conn));
    }

    mysqli_stmt_bind_param(
    $stmt,
    "sssssii",
    $keyword,
    $keyword,
    $keyword,
    $keyword,
    $keyword,
    $limit,
    $offset
);

} else {

    $sql = "SELECT id, fullname, phone, mobile, email, address, photo
            FROM contacts
            ORDER BY id DESC
            LIMIT ? OFFSET ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        die(mysqli_error($conn));
    }

    mysqli_stmt_bind_param(
        $stmt,
        "ii",
        $limit,
        $offset
    );

}

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    die("نتیجه‌ای برای نمایش وجود ندارد.");
}

?>



    <h2>لیست مخاطبین</h2>

    <table class="table">

        <thead>

        <tr>

            <th>عکس</th>
            <th>نام</th>
            <th>تلفن</th>
            <th>موبایل</th>
            <th>ایمیل</th>
            <th>آدرس</th>
            <?php if($showActions){ ?>

            <th>عملیات</th>

            <?php } ?>

        </tr>

        </thead>

    <tbody>
        

        <?php if(mysqli_num_rows($result) == 0){ ?>

        <tr>
            <td colspan="<?php echo $showActions ? 7 : 6; ?>">
                مخاطبی پیدا نشد.
            </td>
        </tr>

        <?php } ?>


        <?php while($row = mysqli_fetch_assoc($result)){ ?>

        <tr>

            <td>
                <?php
                $image = !empty($row['photo']) ? $row['photo'] : 'default.png';
                ?>

                <img
                src="assets/uploads/contacts/<?php echo htmlspecialchars($image); ?>"
                class="avatar">
            </td>

            <td><?php echo htmlspecialchars($row['fullname']); ?></td>
            <td><?php echo htmlspecialchars($row['phone']); ?></td>
            <td><?php echo htmlspecialchars($row['mobile']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['address']); ?></td>
            <?php if($showActions){ ?>

            <td>

            <a href="dashboard.php?id=<?php echo $row['id']; ?>" class="btn btn-warning">

            ویرایش

            </a>

            <a
            href="contacts/delete.php?id=<?php echo $row['id']; ?>"
            class="btn btn-danger delete-btn">

            حذف

            </a>

            </td>

    <?php } ?>

        </tr>

        <?php } ?>

        </tbody>

    </table>

    <?php if($totalPages > 1){ ?>

<div class="pagination">

    <?php if($page > 1){ ?>

        <a href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>">

            قبلی

        </a>

    <?php } ?>



    <?php for($i=1;$i<=$totalPages;$i++){ ?>

        <a
            href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"
            class="<?php echo ($page==$i)?'active':''; ?>">

            <?php echo $i; ?>

        </a>

    <?php } ?>



    <?php if($page < $totalPages){ ?>

        <a href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>">

            بعدی

        </a>

    <?php } ?>

</div>

<?php } 
mysqli_stmt_close($stmt);
mysqli_stmt_close($stmtCount);
?>

</div>