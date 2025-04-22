<?php
session_start();
require_once 'db_connection.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: auth.php");
    exit();
}

// Kiểm tra có ID sản phẩm được truyền vào không
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    
    try {
        // Lấy thông tin ảnh sản phẩm trước khi xóa
        $stmt = $conn->prepare("SELECT image_url FROM products WHERE product_id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();

        // Xóa sản phẩm từ database
        $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
        if ($stmt->execute([$product_id])) {
            // Xóa file ảnh nếu tồn tại
            if ($product && $product['image_url'] && file_exists($product['image_url'])) {
                unlink($product['image_url']);
            }
            
            $_SESSION['message'] = "Xóa sản phẩm thành công!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Không thể xóa sản phẩm!";
            $_SESSION['message_type'] = "error";
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "Lỗi khi xóa sản phẩm: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }
} else {
    $_SESSION['message'] = "Không tìm thấy ID sản phẩm!";
    $_SESSION['message_type'] = "error";
}

// Chuyển hướng về trang dashboard
header("Location: admin_dashboard.php");
exit();
?>
