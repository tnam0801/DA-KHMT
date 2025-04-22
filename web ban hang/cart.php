<?php
session_start(); // Bắt đầu session để lưu trạng thái giỏ hàng
require_once 'db_connection.php';

// Kiểm tra đăng nhập trước khi xem giỏ hàng
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

// Thêm thông báo
$notification = "";

// Xử lý đặt hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $user_id = $_SESSION['user_id'];
    $total_amount = 0;

    // Tính tổng tiền
    foreach ($_SESSION['cart'] as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }

    try {
        // Bắt đầu transaction
        $conn->beginTransaction();

        // Thêm thông tin đơn hàng
        $stmt = $conn->prepare("INSERT INTO orders (user_id, fullname, email, address, phone, total_amount) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $fullname, $email, $address, $phone, $total_amount]);
        $order_id = $conn->lastInsertId();

        // Thêm chi tiết đơn hàng
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, size) VALUES (?, ?, ?, ?, ?)");
        foreach ($_SESSION['cart'] as $cartKey => $item) {
            $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price'], $item['size']]);
        }

        // Commit transaction
        $conn->commit();

        // Xóa giỏ hàng sau khi đặt hàng thành công
        unset($_SESSION['cart']);
        $success_message = "Đặt hàng thành công!";
        
    } catch (Exception $e) {
        // Rollback nếu có lỗi
        $conn->rollBack();
        $error_message = "Có lỗi xảy ra khi đặt hàng. Vui lòng thử lại!";
    }
}

// Xóa sản phẩm khỏi giỏ hàng
if (isset($_GET['action']) && $_GET['action'] === 'remove' && isset($_GET['id'])) {
    $productId = $_GET['id'];
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
        $notification = "Sản phẩm đã được xóa khỏi giỏ hàng.";
    }
}

// Xóa toàn bộ giỏ hàng
if (isset($_GET['action']) && $_GET['action'] === 'clear') {
    unset($_SESSION['cart']);
    $notification = "Giỏ hàng đã được xóa hoàn toàn.";
}

// Lấy danh sách sản phẩm trong giỏ hàng
$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng</title>
    <link rel="stylesheet" href="style.css">
    <script>
        // Hiển thị thông báo khi có
        window.onload = function() {
            const notification = "<?= $notification ?>";
            if (notification) {
                alert(notification);
            }
        }
    </script>
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            border-radius: 5px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
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

    <!-- Giỏ hàng -->
    <div id="cart">
        <h1>Giỏ hàng của bạn</h1>
        <a href="products.php" class="btn">Tiếp tục mua sắm</a>
        <?php if (isset($success_message)): ?>
            <div class="alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <div class="alert-error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <?php if (empty($cartItems)): ?>
            <p>Giỏ hàng của bạn đang trống.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Tên sản phẩm</th>
                        <th>Size</th>
                        <th>Số lượng</th>
                        <th>Giá</th>
                        <th>Tổng</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalPrice = 0;
                    foreach ($cartItems as $cartKey => $item):
                        $itemTotal = $item['price'] * $item['quantity'];
                        $totalPrice += $itemTotal;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td>Size <?= htmlspecialchars($item['size']) ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td><?= number_format($item['price'], 0, ',', '.') ?> VNĐ</td>
                            <td><?= number_format($itemTotal, 0, ',', '.') ?> VNĐ</td>
                            <td>
                                <a href="cart.php?action=remove&id=<?= $cartKey ?>" class="btn-remove">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4"><strong>Tổng cộng:</strong></td>
                        <td><strong><?= number_format($totalPrice, 0, ',', '.') ?> VNĐ</strong></td>
                        <td>
                            <button onclick="openCheckoutModal()" class="btn-checkout">Thanh toán</button>
                        </td>
                    </tr>
                </tfoot>
            </table>
        <?php endif; ?>
    </div>

    <!-- Modal đặt hàng -->
    <div id="checkoutModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Thông tin đặt hàng</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="fullname">Họ và tên:</label>
                    <input type="text" id="fullname" name="fullname" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="address">Địa chỉ:</label>
                    <input type="text" id="address" name="address" required>
                </div>
                <div class="form-group">
                    <label for="phone">Số điện thoại:</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
                <button type="submit" name="checkout" class="btn-checkout">Xác nhận đặt hàng</button>
            </form>
        </div>
    </div>

    <script>
        // JavaScript để điều khiển modal
        var modal = document.getElementById("checkoutModal");
        var span = document.getElementsByClassName("close")[0];

        function openCheckoutModal() {
            modal.style.display = "block";
        }

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
<footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>Liên hệ</h3>
                <p>Địa chỉ: 123 Đường ABC, Quận 1, TP.Hà Nội</p>
                <p>Điện thoại: 090 123 4567</p>
                <p>Email: info@salewn.com</p>
                <p>Giờ làm việc: 8:00 - 20:00 hàng ngày</p>
            </div>
        </div>
    </footer>
</html>