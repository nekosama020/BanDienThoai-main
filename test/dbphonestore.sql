-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 07, 2025 lúc 04:05 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `dbphonestore`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`) VALUES
(75, 2, 48, 1),
(76, 2, 49, 1),
(77, 2, 50, 1),
(78, 2, 51, 1),
(79, 2, 52, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `status`) VALUES
(1, 'Test Category', 'Active'),
(7, 'LuaDao', 'Active'),
(8, 'Apple', 'Active'),
(9, 'XiaoMi', 'Active'),
(10, 'Oppo', 'Active'),
(11, 'ROG', 'Active'),
(13, 'Nubida', 'Active'),
(15, 'Samsung', 'Active'),
(20, 'UpdateCat', 'Active'),
(21, 'DeleteCat', 'Active');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orderdetails`
--

CREATE TABLE `orderdetails` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orderdetails`
--

INSERT INTO `orderdetails` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(11, 15, 48, 1, 17990000.00),
(12, 16, 48, 1, 17990000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('Processing','Shipped','Completed','Canceled','Pending') DEFAULT 'Processing',
  `payment_method` varchar(50) NOT NULL DEFAULT 'COD',
  `payment_status` varchar(50) NOT NULL DEFAULT 'Unpaid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_date`, `total_price`, `status`, `payment_method`, `payment_status`) VALUES
(3, 2, '2025-03-06 05:56:25', 43999998.00, 'Completed', 'COD', 'Unpaid'),
(4, 2, '2025-03-06 06:54:14', 99999999.99, 'Completed', 'COD', 'Unpaid'),
(5, 2, '2025-03-06 08:54:41', 21999999.00, 'Completed', 'COD', 'Unpaid'),
(6, 3, '2025-03-19 12:24:39', 10000000.00, 'Canceled', 'COD', 'Paid'),
(7, 2, '2025-03-19 14:07:30', 21999999.00, 'Pending', 'COD', 'Unpaid'),
(8, 2, '2025-09-29 09:27:10', -100.00, 'Pending', 'COD', 'Unpaid'),
(9, 2, '2025-09-30 03:36:29', -1.00, 'Pending', 'Bank Transfer', 'Unpaid'),
(10, 2, '2025-09-30 03:36:47', -100.00, 'Pending', 'E-Wallet', 'Unpaid'),
(11, 2, '2025-09-30 05:50:02', 1.00, 'Pending', 'Bank Transfer', 'Unpaid'),
(12, 1, '2025-10-02 06:29:32', 1000000.00, 'Processing', 'COD', 'Unpaid'),
(13, 1, '2025-10-02 06:29:32', 2000000.00, 'Processing', 'COD', 'Unpaid'),
(14, 1, '2025-10-02 06:29:32', 3000000.00, 'Processing', 'COD', 'Unpaid'),
(15, 2, '2025-10-02 07:17:33', 17990000.00, 'Pending', 'COD', 'Unpaid'),
(16, 2, '2025-10-02 07:19:14', 17990000.00, '', 'E-Wallet', 'Unpaid'),
(17, 1, '2025-10-02 07:22:14', 1000000.00, 'Processing', 'COD', 'Unpaid'),
(18, 1, '2025-10-02 07:22:14', 2000000.00, 'Processing', 'COD', 'Unpaid'),
(19, 1, '2025-10-02 07:22:14', 3000000.00, 'Processing', 'COD', 'Unpaid'),
(20, 1, '2025-10-07 06:51:54', 1000000.00, 'Processing', 'COD', 'Unpaid'),
(21, 1, '2025-10-07 06:51:54', 2000000.00, 'Processing', 'COD', 'Unpaid'),
(22, 1, '2025-10-07 06:51:54', 3000000.00, 'Processing', 'COD', 'Unpaid'),
(23, 1, '2025-10-07 13:44:04', 1000000.00, 'Processing', 'COD', 'Unpaid'),
(24, 1, '2025-10-07 13:44:04', 2000000.00, 'Processing', 'COD', 'Unpaid'),
(25, 1, '2025-10-07 13:44:04', 3000000.00, 'Processing', 'COD', 'Unpaid');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `category_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `description` text NOT NULL,
  `specifications` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `name`, `image`, `price`, `discount_price`, `stock_quantity`, `category_id`, `created_by`, `status`, `description`, `specifications`) VALUES
(48, 'Red Magic 10 Pro', 'uploads/68db780703538.jpg', 17990000.00, 16990000.00, 50, 13, 2, 'Active', 'Điện thoại chơi game mạnh mẽ từ Nubia với chip Snapdragon hàng đầu.', 'Màn hình AMOLED 6.8\", Snapdragon 8 Gen 3, RAM 12GB, ROM 256GB, Pin 6000mAh, Sạc nhanh 80W'),
(49, 'Xiaomi 13 Pro', 'uploads/68db77f64cfac.png', 19990000.00, 18990000.00, 40, 9, 2, 'Active', 'Flagship Xiaomi hợp tác Leica, camera chụp ảnh xuất sắc.', 'Màn hình AMOLED 6.73\", Snapdragon 8 Gen 2, RAM 12GB, ROM 256GB, Pin 4820mAh, Sạc nhanh 120W'),
(50, 'iPhone 16 Pro Max 256GB', 'uploads/68db77e97d89e.jpg', 33990000.00, 32990000.00, 100, 8, 2, 'Active', 'iPhone mới nhất từ Apple với hiệu năng vượt trội và camera cải tiến.', 'Màn hình Super Retina XDR 6.7\", Apple A18 Bionic, RAM 8GB, ROM 256GB, Camera 48MP, Pin 4500mAh'),
(51, 'Samsung Galaxy Z Flip6', 'uploads/68db77df357a6.jpg', 25990000.00, 24990000.00, 70, 15, 2, 'Active', 'Điện thoại gập nhỏ gọn, độc đáo từ Samsung.', 'Màn hình Dynamic AMOLED 6.7\", Snapdragon 8 Gen 2, RAM 8GB, ROM 256GB, Pin 3700mAh, Sạc nhanh 25W'),
(52, 'OPPO Find N5', 'uploads/68db77d3cfd4c.jpg', 29990000.00, 28990000.00, 60, 10, 2, 'Active', 'Smartphone gập cao cấp từ OPPO.', 'Màn hình gập AMOLED 7.1\", Snapdragon 8 Gen 2, RAM 12GB, ROM 256GB, Pin 4800mAh, Sạc nhanh 80W'),
(53, 'OPPO Reno10 Pro+ 5G 12GB 256GB', 'uploads/68db77c5046ff.jpg', 17990000.00, 16990000.00, 80, 10, 2, 'Active', 'Điện thoại Reno series với camera tele tiềm vọng.', 'Màn hình AMOLED 6.74\", Snapdragon 8+ Gen 1, RAM 12GB, ROM 256GB, Camera 64MP, Pin 4700mAh'),
(54, 'ROG Phone 7 12GB 256GB', 'uploads/68db77b314349.jpg', 23990000.00, 22990000.00, 50, 11, 2, 'Active', 'Điện thoại gaming tối ưu từ ASUS ROG.', 'Màn hình AMOLED 6.78\", Snapdragon 8 Gen 2, RAM 12GB, ROM 256GB, Pin 6000mAh, Sạc nhanh 65W'),
(55, 'Red Magic 10 Pro', 'uploads/68db77a63139c.jpg', 17990000.00, 16990000.00, 50, 13, 2, 'Active', 'Điện thoại chơi game mạnh mẽ từ Nubia.', 'Màn hình AMOLED 6.8\", Snapdragon 8 Gen 3, RAM 12GB, ROM 256GB, Pin 6000mAh, Sạc nhanh 80W'),
(56, 'Xiaomi 13 Pro', 'uploads/68db779777e06.png', 19990000.00, 18990000.00, 40, 9, 2, 'Active', 'Flagship Xiaomi hợp tác Leica.', 'Màn hình AMOLED 6.73\", Snapdragon 8 Gen 2, RAM 12GB, ROM 256GB, Pin 4820mAh, Sạc nhanh 120W'),
(57, 'iPhone 16 Pro Max 256GB', 'uploads/68db777b9900d.jpg', 33990000.00, 32990000.00, 100, 8, 2, 'Active', 'iPhone mới nhất từ Apple.', 'Màn hình Super Retina XDR 6.7\", Apple A18 Bionic, RAM 8GB, ROM 256GB, Camera 48MP, Pin 4500mAh'),
(58, 'iPhone 16 Pro Max 256GB', 'uploads/68db7770453af.jpg', 33990000.00, 32990000.00, 100, 8, 2, 'Active', 'iPhone mới nhất từ Apple.', 'Màn hình Super Retina XDR 6.7\", Apple A18 Bionic, RAM 8GB, ROM 256GB'),
(59, 'iPhone 16 Pro Max 256GB', 'uploads/68db7767a8aae.jpg', 33990000.00, 32990000.00, 100, 8, 2, 'Active', 'iPhone mới nhất từ Apple.', 'Màn hình Super Retina XDR 6.7\", Apple A18 Bionic, RAM 8GB, ROM 256GB'),
(60, 'iPhone 16 Pro Max 256GB', 'uploads/68db775e4b223.jpg', 33990000.00, 32990000.00, 100, 8, 2, 'Active', 'iPhone mới nhất từ Apple.', 'Màn hình Super Retina XDR 6.7\", Apple A18 Bionic, RAM 8GB, ROM 256GB'),
(61, 'iPhone 16 Pro Max 256GB', 'uploads/68db77503b580.jpg', 33990000.00, 32990000.00, 100, 8, 2, 'Active', 'iPhone mới nhất từ Apple.', 'Màn hình Super Retina XDR 6.7\", Apple A18 Bionic, RAM 8GB, ROM 256GB'),
(62, 'iPhone 16 Pro Max 256GB', 'uploads/68db77446a8fe.jpg', 33990000.00, 32990000.00, 100, 8, 2, 'Active', 'iPhone mới nhất từ Apple.', 'Màn hình Super Retina XDR 6.7\", Apple A18 Bionic, RAM 8GB, ROM 256GB'),
(63, 'iPhone 16 Pro Max 256GB', 'uploads/68db77298f5e8.jpg', 33990000.00, 32990000.00, 100, 8, 2, 'Active', 'iPhone mới nhất từ Apple.', 'Màn hình Super Retina XDR 6.7\", Apple A18 Bionic, RAM 8GB, ROM 256GB'),
(64, 'Samsung Galaxy Z Flip6', 'uploads/68db771b2be9a.jpg', 25990000.00, 24990000.00, 70, 15, 2, 'Active', 'Điện thoại gập nhỏ gọn từ Samsung.', 'Màn hình Dynamic AMOLED 6.7\", Snapdragon 8 Gen 2, RAM 8GB, ROM 256GB, Pin 3700mAh'),
(65, 'Samsung Galaxy Z Flip6', 'uploads/68db770b56f4f.jpg', 25990000.00, 24990000.00, 70, 15, 2, 'Active', 'Điện thoại gập nhỏ gọn từ Samsung.', 'Màn hình Dynamic AMOLED 6.7\", Snapdragon 8 Gen 2, RAM 8GB, ROM 256GB, Pin 3700mAh'),
(66, 'OPPO Find N5', 'uploads/68db76f4efef6.jpg', 29990000.00, 28990000.00, 60, 10, 2, 'Active', 'Smartphone gập cao cấp từ OPPO.', 'Màn hình gập AMOLED 7.1\", Snapdragon 8 Gen 2, RAM 12GB, ROM 256GB, Pin 4800mAh'),
(67, 'OPPO Reno10 Pro+ 5G 12GB 256GB', 'uploads/68db75e98fa10.jpg', 17990000.00, 16990000.00, 80, 10, 2, 'Active', 'Điện thoại Reno series với camera tele tiềm vọng.', 'Màn hình AMOLED 6.74\", Snapdragon 8+ Gen 1, RAM 12GB, ROM 256GB, Camera 64MP'),
(68, 'ROG Phone 7 12GB 256GB', 'uploads/68db75d56457b.jpg', 23990000.00, 22990000.00, 50, 11, 2, 'Active', 'Điện thoại gaming tối ưu từ ASUS ROG.', 'Màn hình AMOLED 6.78\", Snapdragon 8 Gen 2, RAM 12GB, ROM 256GB, Pin 6000mAh'),
(73, 'iPhone 16 Pro Max 1TB | Chính hãng VN/A', 'uploads/68db75be1b83b.jpg', 999.00, 99.00, 5, 8, 2, 'Active', '44', '444');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('Admin','Customer') DEFAULT 'Customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `phone`, `address`, `role`, `created_at`) VALUES
(1, 'HeziShieda3', '$2y$10$j3xI9OGDCjtm.9FzhLacgOUyTRo7Zl7pPmcxEgXuyayPTRRmUGskS', 'hezi@gmail.com', NULL, NULL, 'Customer', '2025-03-05 04:12:42'),
(2, 'admin', '$2y$10$WfAhA0lJtyevFkqtgNCcMucrPX9qbTGhYnOj1/qWgOlT0w9n2QUii', 'admin@admin.admin', '0123456789', 'Ài ố sì mà', 'Admin', '2025-03-05 04:13:17'),
(3, 'Đặng Đình Tuấn', '$2y$10$OamGTis8WguEWS7Uxo8svOxUK0BH/Mqs.hLsQhIGsEtpQVS.P8ZA2', 'hezi1@gmail.com', NULL, NULL, 'Customer', '2025-03-19 12:24:23'),
(9, 'HeziShieda5', '$2y$10$0OhC2q74JFYih2O49Qd3peG2hSyP6Vgh8UWARv.rqJn5K2I.zPiya', 'ahaha@gmail.ccc', NULL, NULL, 'Customer', '2025-08-16 07:23:53');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Chỉ mục cho bảng `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT cho bảng `orderdetails`
--
ALTER TABLE `orderdetails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD CONSTRAINT `orderdetails_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orderdetails_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
