<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "ql_nhansu";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $database);

// Kiểm tra lỗi kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// KHÔNG đóng kết nối tại đây
