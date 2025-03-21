<?php
session_start();
include '../../../config/db.php';

// Initialize variables to avoid undefined variable warnings
$success = "";
$error = "";

// Kiểm tra quyền admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../views/login.php");
    exit();
}

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Nhân Viên</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid #ced4da;
            transition: all 0.3s;
        }

        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
            border-color: #28a745;
        }

        .btn {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-success {
            background-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }

        h2 {
            color: #28a745;
            font-weight: 600;
            margin-bottom: 30px;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
        }

        .header-icon {
            color: #28a745;
            margin-right: 10px;
        }

        .alert {
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2 class="text-center">
                    <i class="fas fa-user-plus header-icon"></i>Thêm Nhân Viên Mới
                </h2>

                <!-- Hiển thị thông báo -->
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card p-4 mb-4">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-id-card me-2"></i>Mã Nhân Viên:</label>
                                <input type="text" name="Ma_NV" class="form-control" required
                                    placeholder="Nhập mã nhân viên">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-user me-2"></i>Tên Nhân Viên:</label>
                                <input type="text" name="Ten_NV" class="form-control" required
                                    placeholder="Nhập tên nhân viên">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-venus-mars me-2"></i>Giới tính:</label>
                                <select name="Phai" class="form-select" required>
                                    <option value="" disabled selected>Chọn giới tính</option>
                                    <option value="NAM">Nam</option>
                                    <option value="NU">Nữ</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-map-marker-alt me-2"></i>Nơi Sinh:</label>
                                <input type="text" name="Noi_Sinh" class="form-control" required
                                    placeholder="Nhập nơi sinh">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-building me-2"></i>Phòng Ban:</label>
                                <select name="Ma_Phong" class="form-select" required>
                                    <option value="" disabled selected>Chọn phòng ban</option>
                                    <option value="QT">Quản Trị</option>
                                    <option value="TC">Tài Chính</option>
                                    <option value="KT">Kỹ Thuật</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="fas fa-money-bill-alt me-2"></i>Lương:</label>
                                <div class="input-group">
                                    <input type="number" name="Luong" class="form-control" required
                                        placeholder="Nhập lương">
                                    <span class="input-group-text">VNĐ</span>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-success me-2">
                                <i class="fas fa-save me-2"></i>Lưu Nhân Viên
                            </button>
                            <a href="../admin_dashboard.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Quay lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>