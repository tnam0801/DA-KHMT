<?php
session_start();
require_once 'db_connection.php';

// Lấy hoặc tạo giỏ hàng cho người dùng
function getOrCreateCart($userId, $conn) {
    // Kiểm tra giỏ hàng hiện tại của người dùng
    $query = "SELECT * FROM cart WHERE user_id = :user_id AND status = 'active'";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $cart = $stmt->fetch(PDO::FETCH_ASSOC);

    // Nếu không có giỏ hàng, tạo mới
    if (!$cart) {
        $query = "INSERT INTO cart (user_id) VALUES (:user_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        // Lấy giỏ hàng mới tạo
        $cartId = $conn->lastInsertId();
        $cart = ['cart_id' => $cartId, 'user_id' => $userId, 'status' => 'active'];
    }

    return $cart;
}

// Thêm sản phẩm vào giỏ hàng
function addToCart($cartId, $productId, $quantity, $conn) {
    // Kiểm tra sản phẩm đã tồn tại trong giỏ hàng chưa
    $query = "SELECT * FROM cart_items WHERE cart_id = :cart_id AND product_id = :product_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':cart_id', $cartId, PDO::PARAM_INT);
    $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
    $stmt->execute();
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        // Nếu sản phẩm đã tồn tại, cập nhật số lượng
        $query = "UPDATE cart_items SET quantity = quantity + :quantity WHERE item_id = :item_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':item_id', $item['item_id'], PDO::PARAM_INT);
        $stmt->execute();
    } else {
        // Nếu sản phẩm chưa tồn tại, thêm mới
        $query = "INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (:cart_id, :product_id, :quantity)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':cart_id', $cartId, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->execute();
    }
}

// Lấy danh sách sản phẩm trong giỏ hàng
function getCartItems($cartId, $conn) {
    $query = "SELECT ci.*, p.name, p.price, p.image_url 
              FROM cart_items ci
              JOIN products p ON ci.product_id = p.product_id
              WHERE ci.cart_id = :cart_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':cart_id', $cartId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Ví dụ sử dụng
$userId = 1; // Giả sử ID người dùng là 1
$cart = getOrCreateCart($userId, $conn); // Tạo hoặc lấy giỏ hàng hiện tại

// Thêm sản phẩm vào giỏ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['quantity'])) {
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    addToCart($cart['cart_id'], $productId, $quantity, $conn);
    echo "Sản phẩm đã được thêm vào giỏ hàng!";
}

// Hiển thị sản phẩm trong giỏ hàng
$cartItems = getCartItems($cart['cart_id'], $conn);
foreach ($cartItems as $item) {
    echo "<p>";
    echo "Tên sản phẩm: " . htmlspecialchars($item['name']) . "<br>";
    echo "Số lượng: " . $item['quantity'] . "<br>";
    echo "Giá: " . number_format($item['price'], 0, ',', '.') . " VNĐ<br>";
    echo "Tổng: " . number_format($item['price'] * $item['quantity'], 0, ',', '.') . " VNĐ<br>";
    echo "<img src='" . htmlspecialchars($item['image_url']) . "' width='50'><br>";
    echo "</p>";
}
?>