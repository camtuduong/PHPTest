<?php
session_start();
include '../config/db.php'; // Đảm bảo đường dẫn đúng với cấu trúc thư mục

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($conn)) {
        die("Lỗi: Không thể kết nối với cơ sở dữ liệu.");
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Truy vấn kiểm tra user
    $sql = "SELECT * FROM users WHERE username = ? AND password = MD5(?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Lỗi truy vấn: " . $conn->error);
    }

    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] == 'admin') {
            header("Location: ../views/admin/admin_dashboard.php"); // Chuyển hướng admin
        } else {
            header("Location: ../views/user/user_dashboard.php"); // Chuyển hướng user
        }
        exit();
    } else {
        $error = "Sai tên đăng nhập hoặc mật khẩu!";
    }
}

// Đóng kết nối sau khi sử dụng xong (nếu cần)
if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
</head>

<body>
    <h2>Đăng nhập</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
        <label>Tên đăng nhập:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Mật khẩu:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Đăng nhập</button>
    </form>
</body>

</html>