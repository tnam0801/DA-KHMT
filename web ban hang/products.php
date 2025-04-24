<?php
session_start();
require_once 'db_connection.php';

// Khởi tạo session giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Xử lý thêm sản phẩm vào giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id']) && isset($_POST['size'])) {
    // Kiểm tra đăng nhập
    if (!isset($_SESSION['user_id'])) {
        // Lưu URL hiện tại vào session để sau khi đăng nhập có thể quay lại
        $_SESSION['redirect_after_login'] = 'products.php';
        header("Location: auth.php");
        exit();
    }

    $productId = $_POST['product_id'];
    $size = $_POST['size'];
    
    // Nếu đã đăng nhập, xử lý thêm vào giỏ hàng
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Truy vấn thông tin sản phẩm
    $query = "SELECT * FROM products WHERE product_id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $cartKey = $productId . '-' . $size; // Tạo key riêng cho mỗi combination sản phẩm-size
        if (isset($_SESSION['cart'][$cartKey])) {
            $_SESSION['cart'][$cartKey]['quantity'] += 1;
        } else {
            $_SESSION['cart'][$cartKey] = [
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => 1,
                'size' => $size,
                'product_id' => $productId
            ];
        }
        $_SESSION['success_message'] = "Sản phẩm đã được thêm vào giỏ hàng!";
    }
    
    header("Location: products.php");
    exit();
}

// Lấy danh sách sản phẩm từ cơ sở dữ liệu
$query = "SELECT * FROM products";
$stmt = $conn->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sản phẩm</title>
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
        <div class="cart-button-container">
            <a href="cart.php" class="btn cart-btn">
                <img src="/pic/cart-icon.png" alt="Cart" class="cart-icon">
            </a>
        </div>
    </div>

    <!-- Danh sách sản phẩm -->
    <div id="products">
        <h1>Danh sách sản phẩm</h1>

        <!-- Hiển thị thông báo nếu có -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert-success">
                <?= htmlspecialchars($_SESSION['success_message']) ?>
            </div>
            <?php unset($_SESSION['success_message']); // Xóa thông báo sau khi hiển thị ?>
        <?php endif; ?>

        <ul id="dssanp">
            <?php foreach ($products as $product): ?>
                <li class="item">
                    <div class="img-box">
                        <a href="product_detail.php?id=<?= $product['product_id'] ?>">
                            <img src="<?= htmlspecialchars($product['image_url']) ?>" width="300" alt="">
                        </a>
                    </div>
                    <div class="tensp">
                        <a href="product_detail.php?id=<?= $product['product_id'] ?>" class="product-link">
                            <?= htmlspecialchars($product['name']) ?>
                        </a>
                    </div>
                    <div class="gia"><?= number_format($product['price'], 0, ',', '.') ?> VNĐ</div>
                    <form method="POST" action="" class="add-to-cart-form">
                        <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                        <div class="size-selection">
                            <select name="size" id="size-<?= $product['product_id'] ?>" required>
                                <option value="">Chọn size</option>
                                <option value="39">39</option>
                                <option value="40">40</option>
                                <option value="41">41</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-add-cart" <?php echo !isset($_SESSION['user_id']) ? 'onclick="redirectToLogin(event)"' : ''; ?>>
                            Thêm vào giỏ
                        </button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Thêm script JavaScript -->
    <script>
        function redirectToLogin(event) {
            event.preventDefault();
            window.location.href = 'auth.php';
        }
    </script>
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