<?php
session_start();
include '../../../config/db.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../views/login.php");
    exit();
}

// Kiểm tra ID nhân viên
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Lỗi: Không có ID nhân viên.");
}

$ma_nv = $_GET['id'];

// Thêm xác nhận xóa bằng trang HTML thay vì xóa trực tiếp
if (!isset($_GET['confirm']) || $_GET['confirm'] !== 'yes') {
    // Lấy thông tin nhân viên để hiển thị
    $info_sql = "SELECT Ma_NV, Ten_NV FROM NHANVIEN WHERE Ma_NV = ?";
    $info_stmt = $conn->prepare($info_sql);
    $info_stmt->bind_param("s", $ma_nv);
    $info_stmt->execute();
    $result = $info_stmt->get_result();
    
    if ($result->num_rows == 0) {
        die("Lỗi: Không tìm thấy nhân viên.");
    }
    
    $employee = $result->fetch_assoc();
    $info_stmt->close();
    
    // Hiển thị màn hình xác nhận xóa
    ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận xóa nhân viên</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
    body {
        background-color: #f8f9fa;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .confirmation-card {
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border: none;
        max-width: 550px;
        margin: 0 auto;
    }

    .btn {
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 500;
        transition: all 0.3s;
    }

    .btn:hover {
        transform: translateY(-2px);
    }

    .btn-danger {
        background-color: #dc3545;
    }

    .btn-secondary {
        background-color: #6c757d;
    }

    .icon-warning {
        font-size: 48px;
        color: #dc3545;
    }

    h2 {
        color: #dc3545;
        font-weight: 600;
    }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="card confirmation-card p-4 text-center">
            <div class="card-body">
                <i class="fas fa-exclamation-triangle icon-warning mb-3"></i>
                <h2 class="mb-4">Xác nhận xóa nhân viên</h2>

                <p class="lead mb-4">Bạn có chắc chắn muốn xóa nhân viên sau đây?</p>

                <div class="alert alert-secondary mb-4">
                    <p><strong>Mã nhân viên:</strong> <?php echo htmlspecialchars($employee['Ma_NV']); ?></p>
                    <p class="mb-0"><strong>Tên nhân viên:</strong> <?php echo htmlspecialchars($employee['Ten_NV']); ?>
                    </p>
                </div>

                <p class="text-danger mb-4">Hành động này không thể hoàn tác!</p>

                <div class="d-flex justify-content-center gap-3">
                    <a href="delete_employee.php?id=<?php echo urlencode($ma_nv); ?>&confirm=yes"
                        class="btn btn-danger">
                        <i class="fas fa-trash-alt me-2"></i>Xác nhận xóa
                    </a>
                    <a href="../admin_dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Hủy bỏ
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<?php
    exit();
}

// Người dùng đã xác nhận, tiến hành xóa
$sql = "DELETE FROM NHANVIEN WHERE Ma_NV = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $ma_nv);

if ($stmt->execute()) {
    echo "<script>
            alert('Xóa nhân viên thành công!');
            window.location.href = '../admin_dashboard.php';
          </script>";
} else {
    echo "<script>
            alert('Lỗi khi xóa nhân viên: " . $stmt->error . "');
            window.location.href = '../admin_dashboard.php';
          </script>";
}

$stmt->close();
$conn->close();
?>