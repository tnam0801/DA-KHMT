<?php
session_start();
require_once 'db_connection.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: auth.php");
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    
    // Xử lý upload ảnh
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_dir = 'uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif', 'webp');
        
        if (in_array($file_extension, $allowed_extensions)) {
            $new_filename = uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                // Thêm sản phẩm vào database
                $stmt = $conn->prepare("INSERT INTO products (name, price, description, image_url) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$name, $price, $description, $upload_path])) {
                    $_SESSION['message'] = "Thêm sản phẩm thành công!";
                    $_SESSION['message_type'] = "success";
                    header("Location: admin_dashboard.php");
                    exit();
                } else {
                    $message = "Lỗi khi thêm sản phẩm vào database.";
                }
            } else {
                $message = "Lỗi khi upload file.";
            }
        } else {
            $message = "Chỉ chấp nhận file ảnh (jpg, jpeg, png, gif, webp).";
        }
    } else {
        $message = "Vui lòng chọn ảnh sản phẩm.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sản phẩm mới</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-container">
        <h1>Thêm sản phẩm mới</h1>
        
        <?php if ($message): ?>
            <div class="alert-error"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="product-form">
            <div class="form-group">
                <label for="name">Tên sản phẩm:</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="price">Giá:</label>
                <input type="number" id="price" name="price" required>
            </div>

            <div class="form-group">
                <label for="description">Mô tả:</label>
                <textarea id="description" name="description" required></textarea>
            </div>

            <div class="form-group">
                <label for="image">Hình ảnh:</label>
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp" required>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn-submit">Thêm sản phẩm</button>
                <a href="admin_dashboard.php" class="btn-cancel">Hủy</a>
            </div>
        </form>
    </div>
</body>
</html>