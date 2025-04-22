<?php
session_start();
// Kết nối tới CSDL
require_once 'db_connection.php';

$successMessage = "";
// Xử lý khi người dùng gửi form liên hệ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['contactName'] ?? '';
    $email = $_POST['contactEmail'] ?? '';
    $phone = $_POST['contactPhone'] ?? '';
    $message = $_POST['contactMessage'] ?? '';

    // Thêm thông tin liên hệ vào cơ sở dữ liệu
    $query = "INSERT INTO contacts (name, email, phone, message) VALUES (:name, :email, :phone, :message)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':message', $message);
    $stmt->execute();

    // Xác nhận gửi thông tin thành công
    $successMessage = "Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất có thể.";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên hệ</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div id="header">
        <a href="index.php" class="logo">
            <img src="/pic/logo.jpg" width="135" height="75">
        </a>
        <div id="menu">
            <div class="thanhp">
                <a href="index.php">Trang chủ</a>
            </div>
            <div class="thanhp">
                <a href="products.php">Sản phẩm</a>
            </div>
            <div class="thanhp">
                <a href="contact.php">Liên hệ</a>
            </div>
            <div class="thanhp">
                <?php if(isset($_SESSION['username'])): ?>
                    <div class="user-menu">
                        <span>Xin chào, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        <a href="logout.php" class="btn-logout">Đăng xuất</a>
                    </div>
                <?php else: ?>
                    <a href="auth.php">Đăng nhập</a>
                <?php endif; ?>
            </div>
        </div>
        </div>
    </div>


        <div class="contact-container">
            <h2>LIÊN HỆ VỚI CHÚNG TÔI</h2>
            <div class="contact-content">
                <div class="contact-form">
                    <h3>Gửi tin nhắn cho chúng tôi</h3>
                    <?php if ($successMessage): ?>
                        <p class="success"><?= htmlspecialchars($successMessage) ?></p>
                    <?php endif; ?>
                    <form id="contactForm" method="POST" action="">
                        <div class="form-group">
                            <input type="text" id="contactName" name="contactName" placeholder="Họ và tên" required>
                        </div>
                        <div class="form-group">
                            <input type="email" id="contactEmail" name="contactEmail" placeholder="Email" required>
                        </div>
                        <div class="form-group">
                            <input type="tel" id="contactPhone" name="contactPhone" placeholder="Số điện thoại">
                        </div>
                        <div class="form-group">
                            <textarea id="contactMessage" name="contactMessage" rows="5" placeholder="Nội dung tin nhắn" required></textarea>
                        </div>
                        <button type="submit">Gửi tin nhắn</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>Liên hệ</h3>
                <p>Địa chỉ: 123 Đường ABC, Quận HD, TP.Hà Nội</p>
                <p>Điện thoại: 090 123 4567</p>
                <p>Email: info@salewn.com</p>
                <p>Giờ làm việc: 8:00 - 20:00 hàng ngày</p>
            </div>
        </div>
    </footer>
</body>
</html>