<?php
session_start();
include '../../config/db.php'; // Kết nối CSDL

// Kiểm tra quyền admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../views/login.php");
    exit();
}

// Thiết lập số nhân viên hiển thị trên mỗi trang
$limit = 5;

// Xác định trang hiện tại (nếu không có, mặc định là trang 1)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Lấy dữ liệu nhân viên từ database có phân trang
$sql = "SELECT nv.Ma_NV, nv.Ten_NV, nv.Phai, nv.Noi_Sinh, pb.Ten_Phong, nv.Luong 
        FROM NHANVIEN nv 
        JOIN PHONGBAN pb ON nv.Ma_Phong = pb.Ma_Phong
        LIMIT $start, $limit";
$result = $conn->query($sql);

// Tính tổng số trang
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
    <title>Quản lý nhân viên</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
        }

        .navbar {
            background-color: var(--primary-color);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: white !important;
        }

        .user-info {
            color: white;
            padding: 0 1rem;
            border-right: 1px solid rgba(255, 255, 255, 0.3);
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 1.25rem;
        }

        .card-header h5 {
            color: var(--primary-color);
            font-weight: 600;
            margin: 0;
        }

        .btn-add {
            background-color: var(--success-color);
            border-color: var(--success-color);
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .btn-add:hover {
            background-color: #169c6c;
            border-color: #169c6c;
            transform: translateY(-2px);
        }

        .table {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        thead {
            background-color: var(--primary-color);
            color: white;
        }

        .table th {
            font-weight: 600;
            border: none;
            padding: 12px 15px;
            text-transform: uppercase;
            font-size: 0.875rem;
            letter-spacing: 0.5px;
        }

        .table td {
            vertical-align: middle;
            border-bottom: 1px solid #e3e6f0;
            padding: 12px 15px;
        }

        .table tr:hover {
            background-color: #f8f9fc;
        }

        .employee-image {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 3px;
            transition: all 0.2s;
        }

        .action-btn:hover {
            transform: translateY(-3px);
        }

        .btn-edit {
            background-color: var(--warning-color);
            border: none;
            color: white;
        }

        .btn-delete {
            background-color: var(--danger-color);
            border: none;
            color: white;
        }

        .pagination {
            margin-top: 1.5rem;
        }

        .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .page-link {
            color: var(--primary-color);
            padding: 0.5rem 0.75rem;
            margin: 0 3px;
            border-radius: 5px;
        }

        .footer {
            margin-top: 2rem;
            padding: 1rem 0;
            text-align: center;
            color: var(--secondary-color);
            font-size: 0.875rem;
        }

        .salary {
            font-family: monospace;
            font-weight: 500;
            color: #2d2d2d;
        }

        .location-badge {
            display: inline-block;
            padding: 0.35rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            background-color: #e8f4ff;
            color: #4a89dc;
            border-radius: 4px;
        }

        .department-badge {
            display: inline-block;
            padding: 0.35rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            background-color: #e9f7ef;
            color: #27ae60;
            border-radius: 4px;
        }

        .logout-btn {
            transition: all 0.3s;
        }

        .logout-btn:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }

        .btn-back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1000;
            opacity: 0;
            transition: all 0.3s;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .show-btn {
            opacity: 1;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-users-cog me-2"></i> QUẢN LÝ NHÂN VIÊN
            </a>
            <div class="d-flex align-items-center">
                <div class="user-info me-3">
                    <i class="fas fa-user-shield me-1"></i> <?php echo $_SESSION['username']; ?>
                </div>
                <a href="../logout.php" class="btn btn-light btn-sm logout-btn">
                    <i class="fas fa-sign-out-alt me-1"></i> Đăng xuất
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Card chính -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-table me-2"></i> Danh sách nhân viên</h5>
                <a href="./employee/add_employee.php" class="btn btn-add text-white">
                    <i class="fas fa-plus-circle me-1"></i> Thêm nhân viên mới
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">Mã NV</th>
                                <th>Tên nhân viên</th>
                                <th class="text-center">Giới tính</th>
                                <th>Nơi sinh</th>
                                <th>Phòng ban</th>
                                <th class="text-end">Lương</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td class="text-center"><?php echo $row["Ma_NV"]; ?></td>
                                    <td><?php echo $row["Ten_NV"]; ?></td>
                                    <td class="text-center">
                                        <img src='../../images/<?php echo ($row["Phai"] == "NAM") ? "man.jpg" : "woman.jpg"; ?>'
                                            class='employee-image' alt='<?php echo $row["Phai"]; ?>'>
                                    </td>
                                    <td>
                                        <span class="location-badge">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            <?php echo $row["Noi_Sinh"]; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="department-badge">
                                            <i class="fas fa-building me-1"></i>
                                            <?php echo $row["Ten_Phong"]; ?>
                                        </span>
                                    </td>
                                    <td class="text-end salary">
                                        <?php echo number_format($row["Luong"], 0, ',', '.'); ?> <small>VND</small>
                                    </td>
                                    <td class="text-center">
                                        <a href="./employee/edit_employee.php?id=<?php echo $row['Ma_NV']; ?>"
                                            class="action-btn btn-edit" title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="./employee/delete_employee.php?id=<?php echo $row['Ma_NV']; ?>"
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa nhân viên này?')"
                                            class="action-btn btn-delete" title="Xóa">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <!-- Phân trang -->
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo ($page - 1); ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo ($page + 1); ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="container">
            <p>© <?php echo date('Y'); ?> Hệ thống Quản lý Nhân viên. Đã đăng nhập với quyền Admin.</p>
        </div>
    </div>

    <!-- Back to top button -->
    <div class="btn-back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </div>

    <!-- Scripts -->
    <script>
        // Back to top button functionality
        window.onscroll = function() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                document.getElementById("backToTop").classList.add("show-btn");
            } else {
                document.getElementById("backToTop").classList.remove("show-btn");
            }
        };

        document.getElementById("backToTop").addEventListener("click", function() {
            document.body.scrollTop = 0; // For Safari
            document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
        });
    </script>
</body>

</html>