<?php
session_start();
require_once 'db_connection.php';

// Kiểm tra đăng nhập admin
if(!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: auth.php");
    exit();
}

// Lấy ngày hiện tại
$today = date('Y-m-d');

// Lấy thống kê đơn hàng trong ngày
$stmt = $conn->prepare("
    SELECT 
        o.order_id,
        o.fullname,
        o.email,
        o.phone,
        o.address,
        o.total_amount,
        o.order_date,
        GROUP_CONCAT(CONCAT(p.name, ' (Size: ', oi.size, ', SL: ', oi.quantity, ')') SEPARATOR ', ') as order_details
    FROM orders o
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    LEFT JOIN products p ON oi.product_id = p.product_id
    WHERE DATE(o.order_date) = ?
    GROUP BY o.order_id
    ORDER BY o.order_date DESC
");
$stmt->execute([$today]);
$orders = $stmt->fetchAll();

// Tính tổng doanh thu trong ngày
$stmt = $conn->prepare("
    SELECT SUM(total_amount) as daily_total 
    FROM orders 
    WHERE DATE(order_date) = ?
");
$stmt->execute([$today]);
$daily_total = $stmt->fetch()['daily_total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê đơn hàng</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .statistics-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }

        .statistics-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .daily-total {
            background-color: #4CAF50;
            color: white;
            padding: 15px 25px;
            border-radius: 5px;
            font-size: 18px;
        }

        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .order-table th, 
        .order-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .order-table th {
            background-color: burlywood;
            color: white;
            font-weight: 500;
        }

        .order-table tr:hover {
            background-color: #f5f5f5;
        }

        .order-details {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .order-details:hover {
            white-space: normal;
            overflow: visible;
        }

        .admin-nav {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .nav-link {
            padding: 10px 20px;
            background-color: burlywood;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .nav-link:hover {
            background-color: chocolate;
        }

        .empty-message {
            text-align: center;
            padding: 40px;
            color: #666;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="admin-nav">
            <h1>Thống kê đơn hàng</h1>
            <div class="admin-controls">
                <span>Xin chào, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="admin_dashboard.php" class="nav-link">Quản lý sản phẩm</a>
                <a href="logout.php" class="btn-logout">Đăng xuất</a>
            </div>
        </div>
    </div>

    <div class="statistics-container">
        <div class="statistics-header">
            <h2>Đơn hàng ngày <?php echo date('d/m/Y', strtotime($today)); ?></h2>
            <div class="daily-total">
                Doanh thu: <?php echo number_format($daily_total, 0, ',', '.'); ?> VNĐ
            </div>
        </div>

        <?php if (empty($orders)): ?>
            <div class="empty-message">
                Không có đơn hàng nào trong ngày hôm nay.
            </div>
        <?php else: ?>
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Thông tin liên hệ</th>
                        <th>Chi tiết đơn hàng</th>
                        <th>Tổng tiền</th>
                        <th>Thời gian đặt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?php echo $order['order_id']; ?></td>
                        <td><?php echo htmlspecialchars($order['fullname']); ?></td>
                        <td>
                            Email: <?php echo htmlspecialchars($order['email']); ?><br>
                            SĐT: <?php echo htmlspecialchars($order['phone']); ?><br>
                            Địa chỉ: <?php echo htmlspecialchars($order['address']); ?>
                        </td>
                        <td class="order-details">
                            <?php echo htmlspecialchars($order['order_details']); ?>
                        </td>
                        <td><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> VNĐ</td>
                        <td><?php echo date('H:i:s d/m/Y', strtotime($order['order_date'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
