<?php
session_start();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Trang chủ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
    body {
        background-color: #f8f9fa;
    }

    .container {
        max-width: 600px;
        margin: 100px auto;
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    h2 {
        color: #007bff;
    }

    .btn-custom {
        width: 100%;
        margin-bottom: 10px;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>Chào mừng đến với hệ thống quản lý</h2>

        <?php if (isset($_SESSION['username'])): ?>
        <p>Xin chào, <b><?php echo $_SESSION['username']; ?></b>!</p>
        <?php if ($_SESSION['role'] == 'admin'): ?>
        <a href="views/admin/employee/admin_dashboard.php" class="btn btn-primary btn-custom">🔧 Trang quản trị</a>
        <?php else: ?>
        <a href="views/user/user_dashboard.php" class="btn btn-secondary btn-custom">👤 Trang cá nhân</a>
        <?php endif; ?>
        <a href="views/logout.php" class="btn btn-danger btn-custom">🚪 Đăng xuất</a>
        <?php else: ?>
        <p>Bạn chưa đăng nhập. Vui lòng đăng nhập để tiếp tục.</p>
        <a href="views/login.php" class="btn btn-success btn-custom">🔑 Đăng nhập</a>
        <?php endif; ?>
    </div>
</body>

</html>