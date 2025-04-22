<?php
$host = "localhost"; // Tên máy chủ
$dbname = "SaleWN";  // Tên cơ sở dữ liệu
$username = "root";  // Tên người dùng (thường là "root" trên localhost)
$password = "";      // Mật khẩu (để trống nếu sử dụng XAMPP)

try {
    // Kết nối tới cơ sở dữ liệu bằng PDO
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

    // Thiết lập chế độ lỗi của PDO
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Hiển thị lỗi nếu kết nối thất bại
    die("Kết nối CSDL thất bại: " . $e->getMessage());
}
?>