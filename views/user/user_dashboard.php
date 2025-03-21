<?php
session_start();
include '../../config/db.php'; // Kết nối CSDL

// Kiểm tra xem user đã đăng nhập chưa
if (!isset($_SESSION['username'])) {
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
    <title>Thông tin nhân viên</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }

        table {
            width: 80%;
            margin: auto;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid black;
        }

        th {
            background-color: #f2f2f2;
            color: red;
        }

        .male,
        .female {
            width: 40px;
            height: 40px;
        }

        .pagination {
            margin: 20px;
        }

        .pagination a {
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid #ccc;
            margin: 2px;
            color: blue;
        }

        .pagination a.active {
            background-color: gray;
            color: white;
        }
    </style>
</head>

<body>
    <h2>THÔNG TIN NHÂN VIÊN</h2>
    <table>
        <tr>
            <th>Mã Nhân Viên</th>
            <th>Tên Nhân Viên</th>
            <th>Giới tính</th>
            <th>Nơi Sinh</th>
            <th>Tên Phòng</th>
            <th>Lương</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row["Ma_NV"]; ?></td>
                <td><?php echo $row["Ten_NV"]; ?></td>
                <td>
                    <?php
                    if ($row["Phai"] == "NAM") {
                        echo "<img src='../../images/man.jpg' class='male' alt='Nam'>";
                    } else {
                        echo "<img src='../../images/woman.jpg' class='female' alt='Nữ'>";
                    }
                    ?>
                </td>
                <td><?php echo $row["Noi_Sinh"]; ?></td>
                <td><?php echo $row["Ten_Phong"]; ?></td>
                <td><?php echo $row["Luong"]; ?></td>
            </tr>
        <?php } ?>
    </table>

    <!-- Phân trang -->
    <div class="pagination">
        <?php if ($page > 1) : ?>
            <a href="?page=<?php echo ($page - 1); ?>">« Trang trước</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
            <a href="?page=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $total_pages) : ?>
            <a href="?page=<?php echo ($page + 1); ?>">Trang sau »</a>
        <?php endif; ?>
    </div>

    <br>
    <a href="../logout.php">Đăng xuất</a>
</body>

</html>