<?php
session_start();
include '../../../config/db.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../views/login.php");
    exit();
}

// Lấy ID nhân viên từ URL
if (!isset($_GET['id'])) {
    die("Lỗi: Không có ID nhân viên.");
}

$ma_nv = $_GET['id'];
$success = "";
$error = "";

// Lấy dữ liệu nhân viên từ database
$sql = "SELECT * FROM NHANVIEN WHERE Ma_NV = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $ma_nv);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Lỗi: Không tìm thấy nhân viên.");
}

$employee = $result->fetch_assoc();
$stmt->close();

// Xử lý cập nhật dữ liệu
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten_nv = $_POST['Ten_NV'];
    $phai = $_POST['Phai'];
    $noi_sinh = $_POST['Noi_Sinh'];
    $ma_phong = $_POST['Ma_Phong'];
    $luong = $_POST['Luong'];

    // Kiểm tra dữ liệu đầu vào
    if (empty($ten_nv) || empty($phai) || empty($noi_sinh) || empty($ma_phong) || empty($luong)) {
        $error = "Vui lòng nhập đầy đủ thông tin!";
    } else {
        // Cập nhật dữ liệu
        $update_sql = "UPDATE NHANVIEN SET Ten_NV=?, Phai=?, Noi_Sinh=?, Ma_Phong=?, Luong=? WHERE Ma_NV=?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssssis", $ten_nv, $phai, $noi_sinh, $ma_phong, $luong, $ma_nv);

        if ($update_stmt->execute()) {
            $success = "Cập nhật thành công!";
        } else {
            $error = "Lỗi cập nhật: " . $update_stmt->error;
        }
        $update_stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chỉnh sửa Nhân Viên</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center text-warning">✏️ Chỉnh sửa Nhân Viên</h2>

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
                    <input type="text" name="Ma_NV" class="form-control" value="<?php echo $employee['Ma_NV']; ?>"
                        disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tên Nhân Viên:</label>
                    <input type="text" name="Ten_NV" class="form-control" value="<?php echo $employee['Ten_NV']; ?>"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Giới tính:</label>
                    <select name="Phai" class="form-select">
                        <option value="NAM" <?php echo ($employee['Phai'] == 'NAM') ? 'selected' : ''; ?>>Nam</option>
                        <option value="NU" <?php echo ($employee['Phai'] == 'NU') ? 'selected' : ''; ?>>Nữ</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nơi Sinh:</label>
                    <input type="text" name="Noi_Sinh" class="form-control" value="<?php echo $employee['Noi_Sinh']; ?>"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mã Phòng:</label>
                    <select name="Ma_Phong" class="form-select">
                        <option value="QT" <?php echo ($employee['Ma_Phong'] == 'QT') ? 'selected' : ''; ?>>Quản Trị
                        </option>
                        <option value="TC" <?php echo ($employee['Ma_Phong'] == 'TC') ? 'selected' : ''; ?>>Tài Chính
                        </option>
                        <option value="KT" <?php echo ($employee['Ma_Phong'] == 'KT') ? 'selected' : ''; ?>>Kỹ Thuật
                        </option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Lương:</label>
                    <input type="number" name="Luong" class="form-control" value="<?php echo $employee['Luong']; ?>"
                        required>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-warning">Cập Nhật</button>
                    <a href="../admin_dashboard.php" class="btn btn-secondary">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>