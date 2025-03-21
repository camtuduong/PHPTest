<?php session_start();
include '../../config/db.php'; // Database connection

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../../views/login.php");
    exit();
}

// Set number of employees per page
$limit = 5;

// Determine current page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Get employee data from database with pagination
$sql = "SELECT nv.Ma_NV, nv.Ten_NV, nv.Phai, nv.Noi_Sinh, pb.Ten_Phong, nv.Luong
         FROM NHANVIEN nv
         JOIN PHONGBAN pb ON nv.Ma_Phong = pb.Ma_Phong
         LIMIT $start, $limit";
$result = $conn->query($sql);

// Calculate total pages
$total_sql = "SELECT COUNT(*) AS total FROM NHANVIEN";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_pages = ceil($total_row['total'] / $limit);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin nhân viên</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #4e73df;
            --secondary: #858796;
            --success: #1cc88a;
            --info: #36b9cc;
            --warning: #f6c23e;
            --danger: #e74a3b;
            --light: #f8f9fc;
            --dark: #5a5c69;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8f9fc;
            color: #444;
            line-height: 1.6;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 15px;
        }

        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 25px;
            overflow: hidden;
        }

        .card-header {
            background-color: #4e73df;
            color: white;
            padding: 15px 20px;
            font-weight: bold;
            font-size: 1.2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-body {
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e3e6f0;
        }

        th {
            background-color: #f8f9fc;
            color: #4e73df;
            font-weight: bold;
        }

        tr:hover {
            background-color: #f1f3ff;
        }

        .gender-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            overflow: hidden;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin: 20px 0;
            flex-wrap: wrap;
        }

        .pagination a {
            padding: 8px 16px;
            text-decoration: none;
            margin: 0 4px;
            color: #4e73df;
            border-radius: 4px;
            transition: all 0.3s;
        }

        .pagination a:hover {
            background-color: #4e73df;
            color: white;
        }

        .pagination a.active {
            background-color: #4e73df;
            color: white;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: #4e73df;
            text-decoration: none;
        }

        .navbar-right {
            display: flex;
            align-items: center;
        }

        .user-info {
            margin-right: 20px;
            color: #5a5c69;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background-color: #4e73df;
            color: white;
        }

        .btn-danger {
            background-color: #e74a3b;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .money {
            font-family: monospace;
            text-align: right;
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: #858796;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <div class="navbar">
        <a href="#" class="navbar-brand">Tu'System</a>
        <div class="navbar-right">
            <div class="user-info">
                <i class="fas fa-user-circle"></i> <?php echo $_SESSION['username']; ?>
            </div>
            <a href="../logout.php" class="btn btn-danger">
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </a>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <span>THÔNG TIN NHÂN VIÊN</span>

            </div>
            <div class="card-body">
                <table>
                    <thead>
                        <tr>
                            <th>Mã NV</th>
                            <th>Tên Nhân Viên</th>
                            <th>Giới tính</th>
                            <th>Nơi Sinh</th>
                            <th>Phòng Ban</th>
                            <th>Lương</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $row["Ma_NV"]; ?></td>
                                <td><?php echo $row["Ten_NV"]; ?></td>
                                <td>
                                    <?php
                                    if ($row["Phai"] == "NAM") {
                                        echo "<img src='../../images/man.jpg' class='gender-icon' alt='Nam'>";
                                    } else {
                                        echo "<img src='../../images/woman.jpg' class='gender-icon' alt='Nữ'>";
                                    }
                                    ?>
                                </td>
                                <td><?php echo $row["Noi_Sinh"]; ?></td>
                                <td><?php echo $row["Ten_Phong"]; ?></td>
                                <td class="money"><?php echo number_format($row["Luong"], 0, ',', '.') . ' $'; ?></td>

                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="pagination">
                    <?php if ($page > 1) : ?>
                        <a href="?page=<?php echo ($page - 1); ?>">
                            <i class="fas fa-chevron-left"></i> Trang trước
                        </a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                        <a href="?page=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages) : ?>
                        <a href="?page=<?php echo ($page + 1); ?>">
                            Trang sau <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>© <?php echo date('Y'); ?> Tu'System. All rights reserved.</p>
    </div>
</body>

</html>