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
    <title>Quản lý nhân viên</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</head>

<body class="bg-light">
    <div class="container mt-4">
        <h2 class="text-center text-primary">QUẢN LÝ NHÂN VIÊN</h2>

        <!-- Nút Thêm Nhân Viên -->
        <div class="text-end mb-3">
            <a href="./employee/add_employee.php" class="btn btn-success">
                ➕ Thêm Nhân Viên
            </a>
        </div>

        <!-- Bảng danh sách nhân viên -->
        <table class="table table-bordered text-center">
            <thead class="table-primary">
                <tr>
                    <th>Mã NV</th>
                    <th>Tên Nhân Viên</th>
                    <th>Giới tính</th>
                    <th>Nơi Sinh</th>
                    <th>Tên Phòng</th>
                    <th>Lương</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row["Ma_NV"]; ?></td>
                        <td><?php echo $row["Ten_NV"]; ?></td>
                        <td>
                            <img src='../../images/<?php echo ($row["Phai"] == "NAM") ? "man.jpg" : "woman.jpg"; ?>'
                                class='rounded-circle' width='40' height='40'>
                        </td>
                        <td><?php echo $row["Noi_Sinh"]; ?></td>
                        <td><?php echo $row["Ten_Phong"]; ?></td>
                        <td><?php echo number_format($row["Luong"]); ?> VND</td>
                        <td>
                            <a href="./employee/edit_employee.php?id=<?php echo $row['Ma_NV']; ?>"
                                class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="./employee/delete_employee.php?id=<?php echo $row['Ma_NV']; ?>"
                                onclick="return confirm('Bạn có chắc chắn muốn xóa?')" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Phân trang -->
        <nav>
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo ($page - 1); ?>">« Trước</a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo ($page + 1); ?>">Sau »</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>

        <!-- Đăng xuất -->
        <div class="text-center mt-3">
            <a href="../logout.php" class="btn btn-danger">🚪 Đăng xuất</a>
        </div>
    </div>
</body>

</html>