<?php
session_start();
include '../../../config/db.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../views/login.php");
    exit();
}

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ma_nv = $_POST['Ma_NV'];
    $ten_nv = $_POST['Ten_NV'];
    $phai = $_POST['Phai'];
    $noi_sinh = $_POST['Noi_Sinh'];
    $ma_phong = $_POST['Ma_Phong'];
    $luong = $_POST['Luong'];

    // Kiểm tra dữ liệu đầu vào
    if (empty($ma_nv) || empty($ten_nv) || empty($phai) || empty($noi_sinh) || empty($ma_phong) || empty($luong)) {
        $error = "Vui lòng nhập đầy đủ thông tin!";
    } else {
        // Kiểm tra xem Mã NV đã tồn tại chưa
        $check_sql = "SELECT * FROM NHANVIEN WHERE Ma_NV = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $ma_nv);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error = "Mã nhân viên đã tồn tại!";
        } else {
            // Thêm nhân viên mới vào CSDL
            $sql = "INSERT INTO NHANVIEN (Ma_NV, Ten_NV, Phai, Noi_Sinh, Ma_Phong, Luong) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssi", $ma_nv, $ten_nv, $phai, $noi_sinh, $ma_phong, $luong);

            if ($stmt->execute()) {
                $success = "Thêm nhân viên thành công!";
            } else {
                $error = "Lỗi: " . $stmt->error;
            }
            $stmt->close();
        }
        $check_stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thêm Nhân Viên</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center text-success">➕ Thêm Nhân Viên</h2>

        <!-- Hiển thị thông báo -->
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card p-4">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Mã Nhân Viên:</label>
                    <input type="text" name="Ma_NV" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tên Nhân Viên:</label>
                    <input type="text" name="Ten_NV" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Giới tính:</label>
                    <select name="Phai" class="form-select" required>
                        <option value="NAM">Nam</option>
                        <option value="NU">Nữ</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nơi Sinh:</label>
                    <input type="text" name="Noi_Sinh" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mã Phòng:</label>
                    <select name="Ma_Phong" class="form-select" required>
                        <option value="QT">Quản Trị</option>
                        <option value="TC">Tài Chính</option>
                        <option value="KT">Kỹ Thuật</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Lương:</label>
                    <input type="number" name="Luong" class="form-control" required>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-success">Lưu Nhân Viên</button>
                    <a href="../admin_dashboard.php" class="btn btn-secondary">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>