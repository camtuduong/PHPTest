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

// Xóa nhân viên khỏi database
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
            alert('Lỗi khi xóa nhân viên!');
            window.location.href = '../admin_dashboard.php';
          </script>";
}

$stmt->close();
$conn->close();
