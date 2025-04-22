<?php
session_start();
require_once 'db_connection.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: auth.php");
    exit();
}

$message = '';
$product = null;

// Lấy thông tin sản phẩm cần sửa
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if (!$product) {
        $_SESSION['message'] = "Không tìm thấy sản phẩm!";
        $_SESSION['message_type'] = "error";
        header("Location: admin_dashboard.php");
        exit();
    }
}

// Xử lý form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $product_id = $_POST['product_id'];

    try {
        // Kiểm tra có upload ảnh mới không
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
                    // Xóa ảnh cũ nếu tồn tại
                    if ($product['image_url'] && file_exists($product['image_url'])) {
                        unlink($product['image_url']);
                    }

                    // Cập nhật sản phẩm với ảnh mới
                    $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, description = ?, image_url = ? WHERE product_id = ?");
                    $stmt->execute([$name, $price, $description, $upload_path, $product_id]);
                } else {
                    $message = "Lỗi khi upload file ảnh mới.";
                }
            } else {
                $message = "Chỉ chấp nhận file ảnh (jpg, jpeg, png, gif, webp).";
            }
        } else {
            // Cập nhật sản phẩm không thay đổi ảnh
            $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, description = ? WHERE product_id = ?");
            $stmt->execute([$name, $price, $description, $product_id]);
        }

        if (!$message) {
            $_SESSION['message'] = "Cập nhật sản phẩm thành công!";
            $_SESSION['message_type'] = "success";
            header("Location: admin_dashboard.php");
            exit();
        }
    } catch (PDOException $e) {
        $message = "Lỗi khi cập nhật sản phẩm: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa sản phẩm</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-container">
        <h1>Sửa sản phẩm</h1>
        
        <?php if ($message): ?>
            <div class="alert-error"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="product-form">
            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
            
            <div class="form-group">
                <label for="name">Tên sản phẩm:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="price">Giá:</label>
                <input type="number" id="price" name="price" value="<?php echo $product['price']; ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Mô tả:</label>
                <textarea id="description" name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="image">Hình ảnh:</label>
                <?php if ($product['image_url']): ?>
                    <div class="current-image">
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" width="100">
                        <p>Ảnh hiện tại</p>
                    </div>
                <?php endif; ?>
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
                <small>Chỉ chọn ảnh nếu muốn thay đổi ảnh hiện tại</small>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn-submit">Cập nhật</button>
                <a href="admin_dashboard.php" class="btn-cancel">Hủy</a>
            </div>
        </form>
    </div>

    <style>
        .current-image {
            margin: 10px 0;
            text-align: center;
        }
        
        .current-image img {
            max-width: 200px;
            height: auto;
            border: 1px solid #ddd;
            padding: 5px;
            border-radius: 4px;
        }
        
        .current-image p {
            margin-top: 5px;
            color: #666;
        }
        
        small {
            color: #666;
            display: block;
            margin-top: 5px;
        }
        
        .form-buttons {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        
        .btn-submit {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .btn-cancel {
            background: #f44336;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
        }
        
        .alert-error {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
    </style>
</body>
</html>