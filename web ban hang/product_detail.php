<?php
session_start();
require_once 'db_connection.php';

// Kiểm tra có ID sản phẩm được truyền vào không
if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

// Lấy thông tin sản phẩm
$product_id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

// Nếu không tìm thấy sản phẩm, chuyển về trang products
if (!$product) {
    header("Location: products.php");
    exit();
}

// Xử lý thêm vào giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['redirect_after_login'] = "product_detail.php?id=" . $product_id;
        header("Location: auth.php");
        exit();
    }

    $size = $_POST['size'];
    $cartKey = $product_id . '-' . $size;

    if (isset($_SESSION['cart'][$cartKey])) {
        $_SESSION['cart'][$cartKey]['quantity'] += 1;
    } else {
        $_SESSION['cart'][$cartKey] = [
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => 1,
            'size' => $size,
            'product_id' => $product_id
        ];
    }
    
    $_SESSION['success_message'] = "Sản phẩm đã được thêm vào giỏ hàng!";
    header("Location: product_detail.php?id=" . $product_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        .product-detail {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
            display: flex;
            gap: 40px;
        }

        .product-image {
            flex: 1;
            text-align: center;
        }

        .product-image img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .product-info {
            flex: 1;
            padding: 20px;
        }

        .product-title {
            font-size: 24px;
            color: #333;
            margin-bottom: 15px;
        }

        .product-price {
            font-size: 28px;
            color: #e74c3c;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .product-description {
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .add-to-cart-section {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
        }

        .size-selection {
            margin-bottom: 20px;
        }

        .size-selection label {
            display: block;
            margin-bottom: 10px;
            color: #333;
            font-weight: 500;
        }

        .size-selection select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .btn-add-to-cart {
            width: 100%;
            padding: 15px;
            background: burlywood;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-add-to-cart:hover {
            background: chocolate;
        }

        .back-to-products {
            display: inline-block;
            margin-bottom: 20px;
            color: #666;
            text-decoration: none;
            padding: 10px 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .back-to-products:hover {
            background: #f5f5f5;
            color: #333;
        }

        .alert-success {
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .product-detail {
                flex-direction: column;
            }
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

    <div class="container">
        <a href="products.php" class="back-to-products">← Quay lại danh sách sản phẩm</a>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert-success">
                <?php 
                    echo $_SESSION['success_message'];
                    unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>

        <div class="product-detail">
            <div class="product-image">
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            
            <div class="product-info">
                <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
                <div class="product-price"><?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ</div>
                <div class="product-description"><?php echo nl2br(htmlspecialchars($product['description'])); ?></div>
                
                <form method="POST" class="add-to-cart-section">
                    <div class="size-selection">
                        <label for="size">Chọn size:</label>
                        <select name="size" id="size" required>
                            <option value="">Chọn size</option>
                            <option value="39">39</option>
                            <option value="40">40</option>
                            <option value="41">41</option>
                        </select>
                    </div>
                    <button type="submit" name="add_to_cart" class="btn-add-to-cart">
                        Thêm vào giỏ hàng
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
