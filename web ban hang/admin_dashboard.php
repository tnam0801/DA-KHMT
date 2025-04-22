<?php
session_start();
require_once 'db_connection.php';

// Kiểm tra đăng nhập admin
if(!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: auth.php");
    exit();
}

// Lấy danh sách sản phẩm
$stmt = $conn->prepare("SELECT * FROM products ORDER BY product_id DESC");
$stmt->execute();
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-header">
        <div class="admin-nav">
            <h1>Quản lý sản phẩm</h1>
            <div class="admin-controls">
                <span>Xin chào, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="add_product.php" class="btn-add">Thêm sản phẩm mới</a>
                <a href="logout.php" class="btn-logout">Đăng xuất</a>
            </div>
        </div>
    </div>

    <div class="admin-container">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert-<?php echo $_SESSION['message_type']; ?>">
                <?php 
                    echo $_SESSION['message'];
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']);
                ?>
            </div>
        <?php endif; ?>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Hình ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá</th>
                    <th>Mô tả</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo $product['product_id']; ?></td>
                    <td><img src="<?php echo htmlspecialchars($product['image_url']); ?>" width="50" height="50" style="object-fit: cover;"></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ</td>
                    <td><?php echo htmlspecialchars($product['description']); ?></td>
                    <td>
                        <a href="edit_product.php?id=<?php echo $product['product_id']; ?>" class="btn-edit">Sửa</a>
                        <a href="delete_product.php?id=<?php echo $product['product_id']; ?>" class="btn-delete" onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">Xóa</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
