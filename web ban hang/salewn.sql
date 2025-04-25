-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 25, 2025 lúc 03:37 PM
-- Phiên bản máy phục vụ: 10.4.28-MariaDB
-- Phiên bản PHP: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `salewn`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `contacts`
--

CREATE TABLE `contacts` (
  `contact_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `fullname`, `email`, `address`, `phone`, `total_amount`, `order_date`) VALUES
(11, 11, 'Nam', 'risevil@gmail.com', 'èigiwuhdoajo', '0219387129301', 2390000.00, '2025-04-24 14:07:33');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `size` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price`, `size`) VALUES
(13, 11, 2, 1, 1195000.00, '39'),
(14, 11, 1, 1, 1195000.00, '41');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `price`, `image_url`, `created_at`) VALUES
(1, 'ANTA793-4', 'Giày thể thao ANTA793-4 thiết kế đơn giản nhưng không kém phần tinh tế, đôi giày mang lại cảm giác êm ái và ổn định cho mỗi buổi tập. Đế đúc cao su chất lượng và đệm EVA ở phần giữa đế giày tập cung cấp độ linh hoạt và hỗ trợ tối ưu cho đôi chân, giúp giảm thiểu cảm giác mệt mỏi sau thời gian dài sử dụng.', 1195000.00, '/pic/hang1.webp', '2025-04-11 16:37:05'),
(2, 'ANTA8010-3', 'Sản phảm này là sự lựa chọn hoàn hảo cho những ai yêu thích phong cách thể thao và năng động. Với thiết kế đa dạng, màu sắc tươi sáng và chất liệu cao cấp, mẫu giày này mang đến cho bạn sự thoải mái, bền bỉ và thời trang. Bạn có thể kết hợp giày với nhiều loại trang phục khác nhau, từ quần áo thể thao đến quần jeans hay công sở. Siêu phẩm này không chỉ phù hợp cho các hoạt động thể dục thể thao, mà còn cho các dịp đi chơi, đi làm hay đi học.', 1195000.00, '/pic/hang2.webp', '2025-04-11 16:37:05'),
(3, '𝗜𝗶.𝗻𝗶𝗻𝗴 ARSV033-2', 'Thiết kế hiện đại, đường nét thể thao mạnh mẽ cùng tone màu trẻ trung, ARSV033-2 là lựa chọn hoàn hảo cho những ai luôn muốn nổi bật giữa đám đông. Phần upper thoáng khí kết hợp đế giày êm ái giúp giảm chấn hiệu quả, hỗ trợ tối đa khi di chuyển hay vận động mạnh. Đây là đôi giày lý tưởng cho cả luyện tập, đi học, đi làm hay dạo phố – vừa thoải mái vừa thời trang. 𝗜𝗶.𝗻𝗶𝗻𝗴 ARSV033-2 không chỉ là đôi giày, mà là nguồn cảm hứng cho một lối sống năng động mỗi ngày.', 1195000.00, '/pic/hang3.webp', '2025-04-11 16:37:05'),
(4, 'ANTA840S-4', 'Mang đậm tinh thần \"basic nhưng không đơn điệu\", AGCN361-2 là mẫu giày lý tưởng cho những ai yêu thích sự nhẹ nhàng, linh hoạt trong từng bước đi. Thiết kế tối giản kết hợp với lớp lót êm ái và chất liệu thoáng khí, giúp bạn luôn cảm thấy dễ chịu dù hoạt động cả ngày dài. Dễ dàng phối đồ, phù hợp với nhiều phong cách – từ năng động đến thanh lịch. Basic Lining AGCN361-2 không chỉ là một đôi giày, mà còn là tuyên ngôn về sự tinh tế trong đơn giản.\r\n\r\n', 1295000.00, '/pic/hang4.webp', '2025-04-11 16:37:05'),
(5, 'Basic Lining AGCN361-2', 'Mang phong cách tối giản nhưng không kém phần hiện đại, Basic Lining AGCN361-2 là lựa chọn hoàn hảo cho những ai yêu thích sự tiện dụng và thời trang. Với thiết kế thanh lịch, màu sắc trung tính dễ phối đồ, đôi giày này phù hợp cho cả đi làm, đi học hay dạo phố. Chất liệu vải lót êm ái kết hợp cùng đế cao su bền bỉ, giúp bạn luôn cảm thấy thoải mái suốt ngày dài di chuyển. AGCN361-2 không chỉ là một đôi giày – mà còn là điểm nhấn hoàn hảo để hoàn thiện phong cách sống năng động, hiện đại của bạn.', 1195000.00, '/pic/hang5.webp', '2025-04-11 16:37:05'),
(6, '𝗜𝗶.𝗻𝗶𝗻𝗴 ARSU049-3', 'Được thiết kế dành cho những bước chân không ngừng chuyển động, ARSU049-3 mang đến sự kết hợp hoàn hảo giữa hiệu năng và phong cách. Với phần đế nâng cao trợ lực, chất liệu nhẹ và linh hoạt, đôi giày này giúp bạn thoải mái di chuyển suốt cả ngày. Điểm nhấn độc đáo trong thiết kế hiện đại cùng phối màu tinh tế khiến ARSU049-3 trở thành item không thể thiếu cho mọi outfit streetwear cá tính. Sẵn sàng nâng tầm phong cách – chỉ với một bước đi.', 1350000.00, '/pic/hang6.webp', '2025-04-11 16:37:05'),
(9, 'Puma Smash L (356722-11)', 'Lấy cảm hứng từ những đôi giày tennis cổ điển, Puma Smash L mang đến phong cách đơn giản nhưng đầy cuốn hút. Với phần thân giày làm từ da tổng hợp cao cấp, kết hợp cùng logo Puma đặc trưng hai bên, đôi giày này không chỉ bền mà còn dễ dàng phối với mọi outfit từ năng động đến lịch sự. Đế cao su êm ái hỗ trợ bước đi nhẹ nhàng, thích hợp cho cả ngày dài hoạt động. 356722-11 là lựa chọn lý tưởng cho những ai yêu thích sự tối giản nhưng không kém phần cá tính.\n\n', 1395000.00, 'uploads/68027fd3e4e25.webp', '2025-04-18 16:37:39');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_contacts`
--

CREATE TABLE `product_contacts` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `contact_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sizes`
--

CREATE TABLE `sizes` (
  `size_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `size_value` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`, `is_admin`) VALUES
(9, 'admin', 'admin123', '2025-04-18 15:53:43', 1),
(11, 'ablabla09', '123', '2025-04-18 16:23:13', 0);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`contact_id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Chỉ mục cho bảng `product_contacts`
--
ALTER TABLE `product_contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `contact_id` (`contact_id`);

--
-- Chỉ mục cho bảng `sizes`
--
ALTER TABLE `sizes`
  ADD PRIMARY KEY (`size_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `contacts`
--
ALTER TABLE `contacts`
  MODIFY `contact_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `product_contacts`
--
ALTER TABLE `product_contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `sizes`
--
ALTER TABLE `sizes`
  MODIFY `size_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Các ràng buộc cho bảng `product_contacts`
--
ALTER TABLE `product_contacts`
  ADD CONSTRAINT `product_contacts_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_contacts_ibfk_2` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`contact_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `sizes`
--
ALTER TABLE `sizes`
  ADD CONSTRAINT `sizes_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
