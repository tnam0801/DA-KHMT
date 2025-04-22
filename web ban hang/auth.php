<?php
session_start();
require_once 'db_connection.php';

// Nếu đã đăng nhập, chuyển hướng về trang chủ
if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

// Xử lý đăng ký
if (isset($_POST['register'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm_password) {
        $error = "Mật khẩu xác nhận không khớp!";
    } else {
        // Kiểm tra username đã tồn tại
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        
        if ($stmt->rowCount() > 0) {
            $error = "Tên tài khoản đã được sử dụng!";
        } else {
            // Thêm người dùng mới không hash mật khẩu
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            if ($stmt->execute([$username, $password])) {
                $success = "Đăng ký thành công! Vui lòng đăng nhập.";
            } else {
                $error = "Có lỗi xảy ra, vui lòng thử lại!";
            }
        }
    }
}

// Xử lý đăng nhập
if (isset($_POST['login'])) {
    $username = $_POST['login_username'] ?? '';
    $password = $_POST['login_password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = $user['is_admin'];
        
        if ($user['is_admin']) {
            header("Location: admin_dashboard.php");
        } else {
            if (isset($_SESSION['redirect_after_login'])) {
                $redirect = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']);
                header("Location: " . $redirect);
            } else {
                header("Location: index.php");
            }
        }
        exit();
    } else {
        $error = "Tên tài khoản hoặc mật khẩu không chính xác!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập & Đăng ký</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .auth-container {
            max-width: 400px; /* Thu nhỏ container lại */
            margin: 120px auto 40px;
            padding: 20px;
        }

        .auth-form {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .form-switch {
            text-align: center;
            margin-top: 15px;
            color: #666;
        }

        .form-switch a {
            color: burlywood;
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
        }

        .form-switch a:hover {
            color: chocolate;
        }

        #registerForm {
            display: none;
        }

        .auth-form h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .btn-auth {
            width: 100%;
            padding: 12px;
            background: burlywood;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }

        .btn-auth:hover {
            background: chocolate;
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
        </div>
        <div class="cart-button-container">
            <a href="cart.php" class="btn cart-btn">
                <img src="/pic/cart-icon.png" alt="Cart" class="cart-icon">
            </a>
        </div>
    </div>

    <div class="auth-container">
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="auth-form" id="loginForm">
            <h2>Đăng nhập</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <input type="text" name="login_username" placeholder="Tên tài khoản" required>
                </div>
                <div class="form-group">
                    <input type="password" name="login_password" placeholder="Mật khẩu" required>
                </div>
                <button type="submit" name="login" class="btn-auth">Đăng nhập</button>
            </form>
            <div class="form-switch">
                Chưa có tài khoản? <a onclick="switchForm('register')">Đăng ký ngay</a>
            </div>
        </div>

        <div class="auth-form" id="registerForm">
            <h2>Đăng ký</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <input type="text" name="username" placeholder="Tên tài khoản" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Mật khẩu" required>
                </div>
                <div class="form-group">
                    <input type="password" name="confirm_password" placeholder="Xác nhận mật khẩu" required>
                </div>
                <button type="submit" name="register" class="btn-auth">Đăng ký</button>
            </form>
            <div class="form-switch">
                Đã có tài khoản? <a onclick="switchForm('login')">Đăng nhập ngay</a>
            </div>
        </div>
    </div>

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

    <script>
        function switchForm(type) {
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            
            if (type === 'register') {
                loginForm.style.display = 'none';
                registerForm.style.display = 'block';
            } else {
                loginForm.style.display = 'block';
                registerForm.style.display = 'none';
            }
        }

        // Nếu có thông báo lỗi đăng ký, hiển thị form đăng ký
        <?php if (isset($_POST['register'])): ?>
            switchForm('register');
        <?php endif; ?>
    </script>
</body>
</html>