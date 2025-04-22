<?php
session_start();
require_once 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SalewN</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div id="nory">
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
            <div class="cart-button-container">
                <a href="cart.php" class="btn cart-btn">
                    <img src="/pic/cart-icon.png" alt="Cart" class="cart-icon">
                </a>
            </div>
        </div>

        <!-- Trang chủ -->
        <div id="trangchu">
            <div id="nhan">
                <div class="box-left">
                    <h2>
                        <span>Giày thể thao</span>
                        <br>
                        <span>Chất lượng</span>
                    </h2>
                    <p>
                        Mua ngay các phiên bản giày Pickedball và Tennis
                        mới nhất đến từ các thương hiệu Asics, Nike, Lacoste,… chính hãng <?php echo date("Y"); ?>
                    </p>
                    <!-- Thay button bằng thẻ <a> để điều hướng -->
                    <a href="products.php" class="btn-buy-now">Mua ngay</a>
                </div>
                <div class="box-right">
                    <img src="/pic/giay2.png" width="382" alt="">
                    <img src="/pic/giay-removebg-preview.png" width="382" alt="">
                </div>
            </div>
        </div>
        <div id="giamgia">
                <div class="box-left">
                    <h1>
                        <span>GIẢM GIÁ LÊN TỚI</span>
                        <span>50%</span>
                    </h1>
                </div>
                <div class="box-right"></div>
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