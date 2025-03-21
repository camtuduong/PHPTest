<?php session_start();
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

// Đóng kết nối sau khi sử dụng xong
if (isset($stmt)) {
    $stmt->close();
}
if (isset($conn)) {
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập | Hệ thống quản lý</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
        }

        body {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            width: 420px;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        .logo-container {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo {
            width: 80px;
            height: 80px;
            background-color: var(--primary-color);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            margin-bottom: 15px;
        }

        .login-container h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            font-weight: 500;
            color: #555;
            margin-bottom: 8px;
            display: block;
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            left: 15px;
            top: 13px;
            color: var(--secondary-color);
        }

        .form-control {
            padding: 12px 15px 12px 45px;
            border-radius: 8px;
            border: 1px solid #ddd;
            transition: all 0.3s;
        }

        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.25);
            border-color: var(--primary-color);
        }

        .btn-login {
            background-color: var(--primary-color);
            color: white;
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s;
            border: none;
        }

        .btn-login:hover {
            background-color: #3a5dd9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.3);
        }

        .forgot-password {
            text-align: right;
            margin-bottom: 20px;
        }

        .forgot-password a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 14px;
        }

        .alert {
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
            border-left: 4px solid transparent;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-left-color: #dc3545;
            color: #721c24;
        }

        .footer-text {
            text-align: center;
            margin-top: 30px;
            color: var(--secondary-color);
            font-size: 14px;
        }

        /* Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-container {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="logo-container">
            <div class="logo">
                <i class="fas fa-user-shield"></i>
            </div>
            <h2>Đăng nhập hệ thống</h2>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="username">Tên đăng nhập:</label>
                <div class="input-icon">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" class="form-control"
                        placeholder="Nhập tên đăng nhập" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu:</label>
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" class="form-control"
                        placeholder="Nhập mật khẩu" required>
                </div>
            </div>

            <div class="forgot-password">
                <a href="#">Quên mật khẩu?</a>
            </div>

            <button type="submit" class="btn btn-login">
                <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
            </button>
        </form>

        <div class="footer-text">
            <p>&copy; <?php echo date('Y'); ?> - Hệ thống quản lý</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>