<?php
session_start();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Trang chủ</title>
</head>

<body>
    <h2>Chào mừng đến với hệ thống quản lý</h2>

    <?php
    if (isset($_SESSION['username'])) {
        echo "<p>Xin chào, <b>" . $_SESSION['username'] . "</b>!</p>";

        // Kiểm tra quyền và điều hướng
        if ($_SESSION['role'] == 'admin') {
            echo "<a href='./views/admin/employee/admin_dashboard.php'>Trang quản trị</a>";
        } else {
            echo "<a href='./views/user/user_dashboard.php'>Trang cá nhân</a>";
        }
        echo " | <a href='./views/logout.php'>Đăng xuất</a>";
    } else {
        echo "<p>Bạn chưa đăng nhập. Vui lòng <a href='./views/login.php'>đăng nhập</a> để tiếp tục.</p>";
    }
    ?>
</body>

</html>