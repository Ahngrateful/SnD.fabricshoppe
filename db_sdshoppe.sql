-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 08, 2024 at 04:07 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_sdshoppe`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `adminID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `pics` varchar(255) NOT NULL,
  `login_attempts` int(11) DEFAULT 0,
  `lockout_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`adminID`, `name`, `password`, `position`, `pics`, `login_attempts`, `lockout_time`) VALUES
(1, 'Sharleen', 'sharleenadmin', 'IT Tech', 'images\\shao.jpg', 0, NULL),
(2, 'Shaima', 'ownerofhaus', 'OWNER', 'images\\shai.jpg', 0, NULL),
(3, 'Danica', 'slayable', 'Admin', 'images\\dani.jpg', 0, NULL),
(4, 'Francis ', 'dandadan', 'Admin', 'images\\raf.jpg', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bulk_order_details`
--

CREATE TABLE `bulk_order_details` (
  `bulk_order_id` int(8) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `grand_total` decimal(10,2) NOT NULL,
  `delivery_method` varchar(255) NOT NULL,
  `delivery_date` date NOT NULL,
  `payment_id` int(11) NOT NULL,
  `status` enum('To Pay','To Ship','Ship Out','Completed','Cancelled') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bulk_order_details`
--

INSERT INTO `bulk_order_details` (`bulk_order_id`, `customer_id`, `order_date`, `grand_total`, `delivery_method`, `delivery_date`, `payment_id`, `status`) VALUES
(50000027, 1, '2024-12-01 14:27:45', 50500.00, 'Pick Up', '2024-02-01', 123, 'Completed'),
(50000028, 1, '2024-12-01 15:32:06', 40500.00, 'Contact Seller', '2024-12-01', 124, 'To Pay'),
(50000029, 1, '2024-12-01 15:42:12', 40500.00, 'Pick Up', '2004-12-01', 3212104, 'To Pay'),
(50000030, 1, '2024-12-01 16:11:01', 45000.00, 'Pick Up', '2024-12-12', 3212105, 'To Pay'),
(50000031, 1, '2024-12-01 16:22:15', 40500.00, 'Pick Up', '2024-12-01', 125, 'To Pay'),
(50000032, 1, '2024-12-01 16:42:34', 56000.00, 'Contact Seller', '2024-12-02', 126, 'To Pay'),
(50000033, 123513, '2024-12-01 17:12:31', 36000.00, 'Pick Up', '2024-12-12', 127, 'To Pay'),
(50000034, 123513, '2024-12-01 17:33:48', 36000.00, 'Contact Seller', '2024-12-12', 128, 'To Pay'),
(50000035, 123513, '2024-12-01 18:33:42', 36000.00, 'Pick Up', '2024-12-12', 129, 'To Pay'),
(50000036, 123513, '2024-12-01 18:50:32', 180000.00, 'Contact Seller', '2024-12-12', 3212106, 'To Pay'),
(50000037, 123513, '2024-12-01 19:04:13', 40500.00, 'Contact Seller', '2024-12-12', 130, 'To Pay'),
(50000038, 123513, '2024-12-01 19:08:23', 60000.00, 'Pick Up', '2024-12-12', 131, 'To Pay'),
(50000039, 123513, '2024-12-01 19:20:38', 36000.00, 'Contact Seller', '2024-12-12', 132, 'To Pay'),
(50000040, 123513, '2024-12-01 19:25:52', 40500.00, 'Contact Seller', '2024-12-12', 134, 'To Pay'),
(50000041, 123513, '2024-12-02 05:52:39', 30500.00, 'Pick Up', '2025-02-20', 135, 'To Pay'),
(50000042, 123513, '2024-12-02 06:19:05', 54000.00, 'Contact Seller', '2024-12-12', 136, 'To Pay'),
(50000047, 1, '2024-12-02 10:01:10', 12000.00, 'Contact Seller', '2024-12-10', 137, 'To Pay'),
(50000048, 123475, '2024-12-04 16:59:43', 40500.00, 'Pick Up', '2024-12-06', 11220000, 'To Pay'),
(50000049, 123470, '2024-12-05 03:07:32', 82500.00, 'Pick Up', '2024-12-25', 11220000, 'To Pay'),
(50000050, 123513, '2024-12-06 02:59:39', 36000.00, 'Contact Seller', '0566-06-04', 11220000, 'To Pay'),
(50000051, 123470, '2024-12-06 22:28:50', 60000.00, 'Contact Seller', '2024-12-15', 11220000, 'To Pay'),
(50000053, 123515, '2024-12-07 05:37:57', 136000.00, 'Contact Seller', '1222-12-12', 139, 'To Pay');

-- --------------------------------------------------------

--
-- Table structure for table `bulk_order_items`
--

CREATE TABLE `bulk_order_items` (
  `bulk_order_items` int(11) NOT NULL,
  `bulk_order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL,
  `yards` int(11) NOT NULL,
  `rolls` int(11) NOT NULL,
  `item_subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bulk_order_items`
--

INSERT INTO `bulk_order_items` (`bulk_order_items`, `bulk_order_id`, `product_id`, `product_name`, `color`, `yards`, `rolls`, `item_subtotal`) VALUES
(26, 50000020, 3, 'Spark Fly-Caviar', 'Fuchsia Pink', 0, 5, 0.00),
(27, 50000022, 4, 'Bejeweled-Beaded', 'Champagne', 33, 2, 0.00),
(28, 50000022, 1, 'Enchanted-Beaded', 'White', 33, 0, 0.00),
(29, 50000023, 2, 'Midnight-Corded', 'Blush Pink', 34, 4, 0.00),
(30, 50000023, 4, 'Bejeweled-Beaded', 'Champagne', 43, 0, 0.00),
(31, 50000024, 2, 'Midnight-Corded', 'Emerald Green', 34, 4, 0.00),
(32, 50000024, 1, 'Enchanted-Beaded', 'Red', 30, 5, 0.00),
(33, 50000024, 8, 'Style Corded Lace', 'No colors available', 42, 0, 0.00),
(34, 50000025, 2, 'Midnight-Corded', 'Lavender', 37, 4, 0.00),
(35, 50000025, 1, 'Enchanted-Beaded', 'White', 46, 0, 0.00),
(36, 50000025, 6, 'Epiphany Candy Crush', 'Fuchsia Pink', 37, 10, 0.00),
(37, 50000026, 4, 'Bejeweled-Beaded', 'Pink-Champagne', 45, 0, 0.00),
(38, 50000026, 3, 'Spark Fly-Caviar', 'Fuchsia Pink', 35, 0, 0.00),
(39, 50000026, 3, 'Spark Fly-Caviar', 'Magenta', 0, 8, 0.00),
(40, 50000027, 6, 'Epiphany Candy Crush', 'Pink', 30, 2, 50500.00),
(41, 50000028, 11, 'Style Panel Lace ', 'Blue', 30, 1, 40500.00),
(42, 50000029, 11, 'Style Panel Lace ', 'Red', 30, 1, 40500.00),
(43, 50000030, 12, 'Wannabe Panel Lace', 'Dirty White', 30, 1, 45000.00),
(44, 50000031, 14, 'Glam Panel Lace ', 'Cyan', 30, 1, 40500.00),
(45, 50000032, 4, 'Bejeweled-Beaded', 'Pink-Champagne', 30, 1, 56000.00),
(46, 50000033, 16, 'Queendom Velvet Embroider ', 'Yellow', 30, 1, 36000.00),
(47, 50000034, 17, 'Campfire Velvet Embroider ', 'Maroon', 30, 1, 36000.00),
(48, 50000035, 19, 'Sour Grapes Velvet Embroider ', 'Black', 30, 1, 36000.00),
(49, 50000036, 15, 'Primadona Heavy Beaded Lace', 'Pink', 30, 1, 180000.00),
(50, 50000037, 10, 'Corded Embroider Lace All Over ', 'Baby Pink', 30, 1, 40500.00),
(51, 50000038, 1, 'Enchanted-Beaded', 'Red', 30, 1, 60000.00),
(52, 50000039, 23, 'Winter Velvet Embroider', 'White', 30, 1, 36000.00),
(53, 50000040, 13, 'Little Freak Panel All Over Lace', 'Lilac', 30, 1, 40500.00),
(54, 50000041, 6, 'Epiphany Candy Crush', 'Fuchsia Pink', 30, 1, 30500.00),
(55, 50000042, 21, 'Circles Velvet Embroider', 'Red', 30, 1, 36000.00),
(56, 50000042, 8, 'Love Talk Corded 3D Lace ', 'Baby Blue', 30, 0, 18000.00),
(57, 50000047, 16, 'Queendom Velvet Embroider ', 'Blue', 30, 0, 12000.00),
(58, 50000048, 11, 'Style Panel Lace ', 'White', 30, 1, 40500.00),
(59, 50000049, 26, 'Croissant', 'gren', 30, 2, 82500.00),
(60, 50000050, 21, 'Circles Velvet Embroider', 'Red', 30, 1, 36000.00),
(61, 50000051, 1, 'Enchanted-Beaded', 'Cyan', 30, 1, 60000.00),
(62, 50000053, 4, 'Bejeweled-Beaded', 'Champagne', 30, 5, 136000.00);

-- --------------------------------------------------------

--
-- Table structure for table `bulk_payment`
--

CREATE TABLE `bulk_payment` (
  `payment_id` int(8) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `number` int(11) NOT NULL,
  `acc_name` varchar(255) NOT NULL,
  `method` enum('GCash','Maya','COD','') NOT NULL,
  `ref_num` varchar(255) NOT NULL,
  `proof` varchar(255) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `confirmation` enum('Not yet confirmed','Confirmed','','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bulk_payment`
--

INSERT INTO `bulk_payment` (`payment_id`, `customer_id`, `customer_name`, `number`, `acc_name`, `method`, `ref_num`, `proof`, `payment_date`, `confirmation`) VALUES
(3212102, 123514, 'Meldridge Pingco', 2147483647, 'dani', 'GCash', 'fgdgsdgfe2343ref', 'uploads/67499e8f59937_70a92c5e-cf1e-49f6-940e-035a409a06a1.jpg', '2024-11-29 10:59:27', 'Not yet confirmed'),
(3212103, 123514, 'Meldridge Pingco', 2147483647, 'Meldridge', 'GCash', 'cefdfdsfdsfd', 'uploads/6749a411b9897_70a92c5e-cf1e-49f6-940e-035a409a06a1.jpg', '2024-11-29 11:22:57', 'Not yet confirmed'),
(3212104, 1, 'Sharleen Olaguir', 2324876, 'chao', 'GCash', '24135486', 'uploads/674c83d471913_Chart.jpeg', '2024-12-01 15:42:12', 'Confirmed'),
(3212105, 1, 'Sharleen Olaguir', 2324876, 'chao', 'GCash', '24135486', 'uploads/674c8a95de8dc_Copy-of-fall-canada.png', '2024-12-01 16:11:01', 'Not yet confirmed'),
(3212106, 123513, 'Yzekiel Cooper', 2324876, 'yzzek', 'GCash', '54564', 'uploads/674caff8b8c5e_Shrek_(character).png', '2024-12-01 18:50:32', 'Not yet confirmed');

-- --------------------------------------------------------

--
-- Table structure for table `bulk_shopping_cart`
--

CREATE TABLE `bulk_shopping_cart` (
  `bulk_cart_id` int(6) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product` varchar(255) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `yards` int(11) DEFAULT NULL,
  `rolls` int(11) DEFAULT NULL,
  `color` varchar(255) NOT NULL,
  `color_id` int(11) NOT NULL,
  `delivery_method` varchar(255) NOT NULL,
  `delivery_date` date NOT NULL,
  `roll_price` decimal(10,2) NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `item_subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bulk_shopping_cart`
--

INSERT INTO `bulk_shopping_cart` (`bulk_cart_id`, `product_id`, `product`, `customer_id`, `firstname`, `lastname`, `unit_price`, `yards`, `rolls`, `color`, `color_id`, `delivery_method`, `delivery_date`, `roll_price`, `payment_method`, `item_subtotal`) VALUES
(120183, 2, 'Midnight-Corded', 123514, 'Meldridge', 'Pingco', 350.00, NULL, NULL, '', 0, '', '0000-00-00', 20000.00, '', 0.00),
(120238, 1, 'Enchanted-Beaded', 123513, 'Yzekiel', 'Cooper', 1000.00, 40, 5, 'Cyan', 1, 'Pick Up', '1222-12-12', 30000.00, 'Maya', 190000.00);

-- --------------------------------------------------------

--
-- Table structure for table `cod_payment`
--

CREATE TABLE `cod_payment` (
  `cod_payment_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `method` set('COD','','','') NOT NULL,
  `confirmation` enum('Not yet confirmed','Confirmed','','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cod_payment`
--

INSERT INTO `cod_payment` (`cod_payment_id`, `customer_id`, `method`, `confirmation`) VALUES
(123, 1, 'COD', 'Not yet confirmed'),
(124, 1, 'COD', 'Not yet confirmed'),
(125, 1, 'COD', 'Not yet confirmed'),
(126, 1, 'COD', 'Not yet confirmed'),
(127, 123513, 'COD', 'Confirmed'),
(128, 123513, 'COD', 'Not yet confirmed'),
(129, 123513, 'COD', 'Not yet confirmed'),
(130, 123513, 'COD', 'Not yet confirmed'),
(131, 123513, 'COD', 'Not yet confirmed'),
(132, 123513, 'COD', 'Not yet confirmed'),
(134, 123513, 'COD', 'Not yet confirmed'),
(135, 123513, 'COD', 'Not yet confirmed'),
(136, 123513, 'COD', 'Not yet confirmed'),
(137, 1, 'COD', 'Not yet confirmed'),
(138, 123470, 'COD', 'Not yet confirmed'),
(139, 123515, 'COD', 'Not yet confirmed');

-- --------------------------------------------------------

--
-- Table structure for table `income`
--

CREATE TABLE `income` (
  `income_id` int(11) NOT NULL,
  `1st_Week` decimal(10,2) NOT NULL,
  `2nd_Week` decimal(10,2) NOT NULL,
  `3rd_Week` decimal(10,2) NOT NULL,
  `4th_Week` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `income`
--

INSERT INTO `income` (`income_id`, `1st_Week`, `2nd_Week`, `3rd_Week`, `4th_Week`) VALUES
(202411, 45000.00, 32045.00, 63874.00, 56381.00);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notif_id` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notif_id`, `id`, `message`, `is_read`, `created_at`) VALUES
(1, 1, 'Admin is currently working on your order. Your order #200063 is now \"To Receive\".', 0, '2024-12-02 18:02:38'),
(2, 1, 'Admin is currently working on your order. Your order #200063 is now \"To Ship\".', 1, '2024-12-02 18:34:04'),
(3, 1, 'Admin is currently working on your order. Your order #200062 is now \"To Receive\".', 0, '2024-12-03 10:07:35'),
(4, 1, 'Your Order #200062 is now completed! Thank you for buying our product, suki!', 0, '2024-12-03 10:17:12'),
(5, 123474, 'Your Order #200064 is now on their way! Thank you for your patience.', 0, '2024-12-03 10:56:35'),
(6, 1, 'Your Order #200063 is now on their way! Thank you for your patience.', 0, '2024-12-03 10:56:49'),
(7, 1, 'Your Order #200063 is now on their way! Thank you for your patience.', 0, '2024-12-03 10:59:21'),
(8, 1, 'Your Order #200063 is now completed! Thank you for buying our product.', 0, '2024-12-03 11:30:25'),
(9, 1, 'Your Order #200063 is now completed! Thank you for buying our product.', 0, '2024-12-03 12:03:39'),
(10, 123470, 'Your Order #200081 is now on its way! Thank you for your patience.', 1, '2024-12-03 12:04:51'),
(11, 1, 'Your Order #200077 is now on its way! Thank you for your patience.', 0, '2024-12-03 12:07:18'),
(12, 1, 'Your Order #200077 is now completed! Thank you for buying our product.', 0, '2024-12-03 12:08:30'),
(13, 1, 'Your Order #200084 is now on its way! Thank you for your patience.', 1, '2024-12-03 12:15:56'),
(14, 1, 'Your Order #200084 is now on its way! Thank you for your patience.', 1, '2024-12-03 12:27:41'),
(15, 123470, 'Your Order #200082 is now on its way! Thank you for your patience.', 1, '2024-12-03 12:30:45'),
(16, 123475, 'Your Order #200087 is now to ship! We\'re preparing your items for shipment.', 0, '2024-12-04 17:01:53'),
(17, 123475, 'Your Order #200087 is now on its way! Thank you for your patience.', 0, '2024-12-04 17:03:01'),
(18, 123475, 'Your Order #200087 is now on its way! Thank you for your patience.', 0, '2024-12-04 17:17:50'),
(19, 123475, 'Your Order #200087 is now on its way! Thank you for your patience.', 0, '2024-12-04 17:24:49'),
(20, 123475, 'Your Order #200087 is now on its way! Thank you for your patience.', 0, '2024-12-04 17:25:16'),
(21, 123475, 'Your Order #200087 is now on its way! Thank you for your patience.', 0, '2024-12-04 17:25:43'),
(22, 123475, 'Your Order #200087 is now on its way! Thank you for your patience.', 0, '2024-12-04 17:27:00'),
(23, 123475, 'Your Order #200087 is now on its way! Thank you for your patience.', 0, '2024-12-04 17:27:58'),
(24, 1, 'Your Order #200085 is now on its way! Thank you for your patience.', 0, '2024-12-04 17:28:11'),
(25, 123475, 'Your Order #200087 is now on its way! Thank you for your patience.', 0, '2024-12-04 17:30:05'),
(26, 123475, 'Your Order #200087 is now on its way! Thank you for your patience.', 1, '2024-12-04 17:32:22'),
(27, 123470, 'Your Order #200088 is now to ship! We\'re preparing your items for shipment.', 0, '2024-12-05 03:11:53'),
(28, 123470, 'Your Order #200088 is now on its way! Thank you for your patience.', 1, '2024-12-05 03:12:35'),
(29, 123470, 'Your Order #200088 is now on its way! Thank you for your patience.', 0, '2024-12-05 03:14:59'),
(30, 123470, 'Your Order #200088 is now completed! Thank you for buying our product.', 0, '2024-12-05 03:15:18'),
(31, 123474, 'Your Order #200089 is now to ship! We\'re preparing your items for shipment.', 0, '2024-12-05 05:54:39'),
(32, 123474, 'Your Order #200089 is now on its way! Thank you for your patience.', 0, '2024-12-05 05:55:38'),
(33, 123474, 'Your Order #200089 is now completed! Thank you for buying our product.', 0, '2024-12-05 05:56:21'),
(34, 123516, 'Your Order #200113 is now to ship! We\'re preparing your items for shipment.', 0, '2024-12-07 08:30:14'),
(35, 123516, 'Your Order #200113 is now on its way! Thank you for your patience.', 0, '2024-12-07 08:33:53'),
(36, 123516, 'Your Order #200113 is now to ship! We\'re preparing your items for shipment.', 0, '2024-12-08 04:29:23'),
(37, 123516, 'Your Order #200113 is now to ship! We\'re preparing your items for shipment.', 0, '2024-12-08 04:33:17'),
(38, 123516, 'Your Order #200113 is now to ship! We\'re preparing your items for shipment.', 0, '2024-12-08 04:39:23'),
(39, 123516, 'Your Order #200113 is now on its way! Thank you for your patience.', 0, '2024-12-08 04:44:35'),
(40, 123515, 'Your order #200114 has been placed successfully. Please wait for your payment confirmation within 12-24 hours. Thank you for shopping with us!', 1, '2024-12-08 04:54:37');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `order_num` int(15) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `sub_total` decimal(10,2) NOT NULL,
  `shipping_fee` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `delivery_option` varchar(50) NOT NULL,
  `payment` int(15) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `Status` enum('To Pay','To Ship','Ship out','Completed') NOT NULL,
  `track_num` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`order_num`, `customer_id`, `sub_total`, `shipping_fee`, `total_price`, `delivery_option`, `payment`, `order_date`, `Status`, `track_num`) VALUES
(200062, 1, 1550.00, 44.00, 1594.00, 'lbc', 100066, '2024-11-22 14:08:46', 'Completed', '12345688'),
(200063, 1, 6000.00, 60.00, 6060.00, 'ninja-van', 100067, '2024-11-23 06:41:04', 'Completed', '12345689'),
(200064, 123474, 10000.00, 0.00, 10000.00, 'ninja-van', 100068, '2024-11-23 08:40:26', 'To Ship', NULL),
(200065, 123513, 1550.00, 44.00, 1594.00, 'lbc', 100069, '2024-11-25 03:49:47', 'Ship out', '102503'),
(200076, 123513, 600.00, 60.00, 660.00, 'ninja-van', 100082, '2024-11-29 06:07:12', 'To Pay', NULL),
(200077, 1, 1200.00, 44.00, 1244.00, 'lbc', 100083, '2024-11-29 13:41:19', 'Completed', NULL),
(200081, 123470, 5850.00, 90.00, 5940.00, 'ninja-van', 100090, '2024-11-29 17:26:06', 'To Ship', '010101'),
(200082, 123470, 450.00, 60.00, 510.00, 'jnt', 100091, '2024-11-29 17:31:07', 'To Ship', NULL),
(200083, 123470, 600.00, 64.00, 664.00, 'lbc', 100092, '2024-11-29 17:37:55', 'To Pay', NULL),
(200084, 1, 450.00, 60.00, 510.00, 'ninja-van', 100093, '2024-12-01 15:22:37', 'Ship out', NULL),
(200085, 1, 450.00, 44.00, 494.00, 'lbc', 100094, '2024-12-01 16:46:56', 'Ship out', NULL),
(200086, 123513, 800.00, 40.00, 840.00, 'jnt', 100095, '2024-12-01 16:47:47', 'To Pay', NULL),
(200087, 123475, 900.00, 44.00, 944.00, 'lbc', 100096, '2024-12-04 17:00:49', 'To Ship', '170V3Y0U'),
(200088, 123470, 1100.00, 90.00, 1190.00, 'ninja-van', 100097, '2024-12-05 03:10:46', 'Completed', '6876248'),
(200089, 123474, 10000.00, 74.00, 10074.00, 'lbc', 100098, '2024-12-05 05:53:29', 'Completed', '546578'),
(200090, 1, 600.00, 40.00, 640.00, 'jnt', 100099, '2024-12-06 01:19:32', 'To Pay', NULL),
(200091, 1, 3000.00, 40.00, 3040.00, 'jnt', 100100, '2024-12-06 01:26:26', 'To Pay', NULL),
(200092, 123513, 1750.00, 60.00, 1810.00, 'ninja-van', 100101, '2024-12-06 01:28:50', 'To Pay', NULL),
(200104, 123513, 700.00, 60.00, 760.00, 'ninja-van', 100113, '2024-12-06 02:13:17', 'To Pay', NULL),
(200109, 123513, 8400.00, 60.00, 8460.00, 'ninja-van', 100118, '2024-12-06 04:24:20', 'To Pay', NULL),
(200111, 123513, 1600.00, 60.00, 1660.00, 'ninja-van', 100120, '2024-12-06 05:07:11', 'To Pay', NULL),
(200112, 123470, 350.00, 64.00, 414.00, 'lbc', 100121, '2024-12-06 22:34:12', 'To Pay', NULL),
(200113, 123516, 5000.00, 64.00, 5064.00, 'lbc', 100122, '2024-12-07 08:28:27', 'Ship out', NULL),
(200114, 123515, 450.00, 44.00, 494.00, 'lbc', 100123, '2024-12-08 04:54:37', 'To Pay', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_items_id` int(11) NOT NULL,
  `order_num` varchar(255) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `color` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_items_id`, `order_num`, `product_id`, `product_name`, `color`, `quantity`) VALUES
(20, '200062', 3, 'Spark Fly-Caviar', 'Fuchsia Pink', 2),
(21, '200062', 6, 'Epiphany Candy Crush', 'Pink', 1),
(22, '200063', 4, 'Bejeweled-Beaded', 'Purple-Pink', 5),
(23, '200064', 5, 'Wonderland Beaded Lace', 'Royal Blue', 2),
(24, '200064', 19, 'gumamela', 'Others', 1),
(25, '200065', 4, 'Bejeweled-Beaded', 'Purple-Pink', 1),
(26, '200065', 6, 'Epiphany Candy Crush', 'Pink', 1),
(38, '200076', 3, 'Spark Fly-Caviar', '', 1),
(39, '200077', 8, 'Love Talk Corded 3D Lace ', 'Blush Pink', 2),
(43, '200081', 1, 'Enchanted-Beaded', 'Red', 3),
(44, '200081', 4, 'Bejeweled-Beaded', 'Pink', 1),
(45, '200081', 3, 'Spark Fly-Caviar', 'Emerald Green', 2),
(46, '200081', 14, 'Glam Panel Lace ', '', 1),
(47, '200082', 9, 'Diva Corded All Over Lace ', '', 1),
(48, '200083', 8, 'Love Talk Corded 3D Lace ', 'Green', 1),
(49, '200084', 13, 'Little Freak Panel All Over Lace', 'Lilac', 1),
(50, '200085', 9, 'Diva Corded All Over Lace ', 'Baby Blue', 1),
(51, '200086', 23, 'Winter Velvet Embroider', 'White', 2),
(52, '200087', 11, 'Style Panel Lace ', 'Blue', 2),
(53, '200088', 26, 'Croissant', 'penk', 2),
(54, '200089', 5, 'Wonderland Beaded Lace', 'Champagne', 10),
(55, '200090', 3, 'Spark Fly-Caviar', 'Red', 1),
(56, '200091', 3, 'Spark Fly-Caviar', 'Fuchsia Pink', 5),
(57, '200092', 2, 'Midnight-Corded', 'Emerald Green', 5),
(58, '200104', 2, 'Midnight-Corded', 'Emerald Green', 2),
(59, '200109', 1, 'Enchanted-Beaded', 'White', 6),
(60, '200109', 3, 'Spark Fly-Caviar', 'Fuchsia Pink', 1),
(61, '200109', 3, 'Spark Fly-Caviar', 'Magenta', 1),
(62, '200109', 4, 'Bejeweled-Beaded', 'Pink-Champagne', 1),
(63, '200111', 1, 'Enchanted-Beaded', 'White', 1),
(64, '200111', 3, 'Spark Fly-Caviar', 'Fuchsia Pink', 1),
(65, '200112', 2, 'Midnight-Corded', 'Lavender', 1),
(66, '200113', 1, 'Enchanted-Beaded', 'Cyan', 5),
(67, '200114', 29, 'Snow Moon Ahri Panel ', 'Baby Blue', 1);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(15) NOT NULL,
  `customer_id` int(15) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `number` varchar(11) NOT NULL,
  `acc_name` varchar(255) NOT NULL,
  `method` enum('Maya','Gcash','','') NOT NULL,
  `ref_num` varchar(255) NOT NULL,
  `proof` varchar(255) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `confirmation` enum('Not yet confirmed','Confirmed','','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`payment_id`, `customer_id`, `customer_name`, `number`, `acc_name`, `method`, `ref_num`, `proof`, `payment_date`, `confirmation`) VALUES
(100001, 1, 'Sharleen Olaguir', '09989333165', 'Sharleen Olaguir', 'Gcash', '4634873156753', 'edfdsfds', '2024-11-21 15:51:41', 'Not yet confirmed'),
(100066, 1, 'Sharleen Olaguir', '09989333165', 'shao', 'Gcash', '24135486', 'uploads/6740906ed0e49_410945053_1072793487371854_6481595374348650915_n.png', '2024-12-03 15:35:18', 'Not yet confirmed'),
(100067, 1, 'Sharleen Olaguir', '09989333165', 'shao', 'Maya', '24135486', 'uploads/674179005ec1f_heneh.png', '2024-12-03 15:51:36', 'Confirmed'),
(100068, 123474, 'Danica Kassandra Lepardo', '09989333165', 'chao', 'Maya', '24135486', 'uploads/674194fad4e29_III-CINS_POSTER.jpg', '2024-12-03 15:55:05', 'Confirmed'),
(100069, 123513, 'Yzekiel Cooper', '2324876', 'sadasdsad', 'Maya', '24135486', 'uploads/6743f3dbacccb_business-report-pie.png', '2024-11-25 03:49:47', 'Not yet confirmed'),
(100082, 123513, 'Yzekiel Cooper', '78787', 'yzzek', 'Gcash', '32', 'uploads/67495a10a856c_Chart.jpeg', '2024-11-29 06:07:12', 'Not yet confirmed'),
(100083, 1, 'Sharleen Olaguir', '546', 'chao', 'Maya', '54564', 'uploads/6749c47f76f63_Shrek_(character).png', '2024-11-29 13:41:19', 'Not yet confirmed'),
(100084, 123470, 'Shaima Mangadang', '232', 'shai', 'Gcash', '32', 'uploads/6749f78965df0_green.jpg', '2024-11-29 17:19:05', 'Not yet confirmed'),
(100085, 123470, 'Shaima Mangadang', '232', 'shai', 'Gcash', '32', 'uploads/6749f7acde52e_green.jpg', '2024-11-29 17:19:40', 'Not yet confirmed'),
(100086, 123470, 'Shaima Mangadang', '232', 'shai', 'Gcash', '32', 'uploads/6749f87322ad3_green.jpg', '2024-11-29 17:22:59', 'Not yet confirmed'),
(100087, 123470, 'Shaima Mangadang', '232', 'shai', 'Gcash', '32', 'uploads/6749f8b2170d5_green.jpg', '2024-11-29 17:24:02', 'Not yet confirmed'),
(100088, 123470, 'Shaima Mangadang', '232', 'shai', 'Gcash', '32', 'uploads/6749f8daaa5ec_green.jpg', '2024-11-29 17:24:42', 'Not yet confirmed'),
(100089, 123470, 'Shaima Mangadang', '232', 'shai', 'Gcash', '32', 'uploads/6749f8f9e0685_green.jpg', '2024-11-29 17:25:13', 'Not yet confirmed'),
(100090, 123470, 'Shaima Mangadang', '232', 'shai', 'Gcash', '32', 'uploads/6749f92e01329_green.jpg', '2024-11-29 17:26:06', 'Not yet confirmed'),
(100091, 123470, 'Shaima Mangadang', '215', 'shai', 'Maya', '32', 'uploads/6749fa5b427ee_455703904_1029671688950731_4237392567667379515_n.jpg', '2024-11-29 17:31:07', 'Not yet confirmed'),
(100092, 123470, 'Shaima Mangadang', '78787', 'shai', 'Gcash', '32', 'uploads/6749fbf3c7b60_Homemade-Croissants-Recipe60.jpg', '2024-11-29 17:37:55', 'Not yet confirmed'),
(100093, 1, 'Sharleen Olaguir', '2324876', 'chao', 'Gcash', '32', 'uploads/674c7f3d52e70_maxresdefault.jpg', '2024-12-01 15:22:37', 'Not yet confirmed'),
(100094, 1, 'Sharleen Olaguir', '09989333165', 'chao', 'Gcash', '8787', 'uploads/674c93005391c_sign-out-alt.png', '2024-12-01 16:46:56', 'Not yet confirmed'),
(100095, 123513, 'Yzekiel Cooper', '215', 'yzzek', 'Maya', '8787', 'uploads/674c93335ca0f_product-sales-comparison-chart.png', '2024-12-01 16:47:47', 'Not yet confirmed'),
(100096, 123475, 'la gasmen', '654897', 'la', 'Gcash', '987354', 'uploads/67508ac1cfe24_e66e283b-2a4c-4cf1-8c6c-49e7ad41a866.jpg', '2024-12-04 17:01:27', 'Confirmed'),
(100097, 123470, 'Shaima Mangadang', '2324876', 'shai', 'Gcash', '54564', 'uploads/675119b6d68b9_lacherpatisserie-petitgateaux-01.jpg', '2024-12-05 03:11:48', 'Confirmed'),
(100098, 123474, 'Danica Kassandra Lepardo', '78787', 'danicuh', 'Gcash', '213452', 'uploads/67513fd96ead7_Shrek_(character).png', '2024-12-05 05:54:33', 'Confirmed'),
(100099, 1, 'Sharleen  Olaguir', '54654', 'chao', 'Gcash', '213452', 'uploads/6752512475933_LOGO.png', '2024-12-06 01:19:32', 'Not yet confirmed'),
(100100, 1, 'Sharleen  Olaguir', '089898', 'chao', 'Gcash', '5464', 'uploads/675252c263366_455704431_828886922333701_6765299808160741205_n.jpg', '2024-12-06 01:26:26', 'Not yet confirmed'),
(100101, 123513, 'Yzekiel Cooper', '546', 'yzzek', 'Maya', '54564', 'uploads/6752535230444_a02fa0ee-b25e-4859-b526-cc1a9daea444.jpg', '2024-12-06 01:28:50', 'Not yet confirmed'),
(100102, 123513, 'Yzekiel Cooper', '215', 'yzzek', 'Gcash', '87987987', 'uploads/675255f0daae3_a02fa0ee-b25e-4859-b526-cc1a9daea444.jpg', '2024-12-06 01:40:00', 'Not yet confirmed'),
(100103, 123513, 'Yzekiel Cooper', '215', 'yzzek', 'Gcash', '87987987', 'uploads/675257b2d43ca_a02fa0ee-b25e-4859-b526-cc1a9daea444.jpg', '2024-12-06 01:47:30', 'Not yet confirmed'),
(100104, 123513, 'Yzekiel Cooper', '215', 'yzzek', 'Gcash', '87987987', 'uploads/675257c764fff_a02fa0ee-b25e-4859-b526-cc1a9daea444.jpg', '2024-12-06 01:47:51', 'Not yet confirmed'),
(100105, 123513, 'Yzekiel Cooper', '215', 'yzzek', 'Gcash', '87987987', 'uploads/675258b68cf78_a02fa0ee-b25e-4859-b526-cc1a9daea444.jpg', '2024-12-06 01:51:50', 'Not yet confirmed'),
(100106, 123513, 'Yzekiel Cooper', '215', 'yzzek', 'Gcash', '87987987', 'uploads/67525acd06a06_a02fa0ee-b25e-4859-b526-cc1a9daea444.jpg', '2024-12-06 02:00:45', 'Not yet confirmed'),
(100107, 123513, 'Yzekiel Cooper', '215', 'yzzek', 'Gcash', '87987987', 'uploads/67525b0680ded_a02fa0ee-b25e-4859-b526-cc1a9daea444.jpg', '2024-12-06 02:01:42', 'Not yet confirmed'),
(100108, 123513, 'Yzekiel Cooper', '215', 'yzzek', 'Gcash', '87987987', 'uploads/67525b3661986_a02fa0ee-b25e-4859-b526-cc1a9daea444.jpg', '2024-12-06 02:02:30', 'Not yet confirmed'),
(100109, 123513, 'Yzekiel Cooper', '215', 'yzzek', 'Gcash', '87987987', 'uploads/67525b66be367_a02fa0ee-b25e-4859-b526-cc1a9daea444.jpg', '2024-12-06 02:03:18', 'Not yet confirmed'),
(100110, 123513, 'Yzekiel Cooper', '215', 'yzzek', 'Gcash', '87987987', 'uploads/67525c0a7243a_a02fa0ee-b25e-4859-b526-cc1a9daea444.jpg', '2024-12-06 02:06:02', 'Not yet confirmed'),
(100111, 123513, 'Yzekiel Cooper', '215', 'yzzek', 'Gcash', '87987987', 'uploads/67525c152b847_a02fa0ee-b25e-4859-b526-cc1a9daea444.jpg', '2024-12-06 02:06:13', 'Not yet confirmed'),
(100112, 123513, 'Yzekiel Cooper', '215', 'yzzek', 'Gcash', '87987987', 'uploads/67525c2542c14_a02fa0ee-b25e-4859-b526-cc1a9daea444.jpg', '2024-12-06 02:06:29', 'Not yet confirmed'),
(100113, 123513, 'Yzekiel Cooper', '215', 'yzzek', 'Gcash', '87987987', 'uploads/67525dbd1d924_a02fa0ee-b25e-4859-b526-cc1a9daea444.jpg', '2024-12-06 02:13:17', 'Not yet confirmed'),
(100114, 123513, 'Yzekiel Cooper', '2324876', 'yzzek', 'Gcash', '54564', 'uploads/67527b78c7130_Untitled design (1).png', '2024-12-06 04:20:08', 'Not yet confirmed'),
(100115, 123513, 'Yzekiel Cooper', '78787', 'chao', 'Maya', '32', 'uploads/67527be899f46_Shrek_(character).png', '2024-12-06 04:22:00', 'Not yet confirmed'),
(100116, 123513, 'Yzekiel Cooper', '78787', 'chao', 'Maya', '32', 'uploads/67527bf852686_Shrek_(character).png', '2024-12-06 04:22:16', 'Not yet confirmed'),
(100117, 123513, 'Yzekiel Cooper', '78787', 'chao', 'Maya', '32', 'uploads/67527bfe8a667_Shrek_(character).png', '2024-12-06 04:22:22', 'Not yet confirmed'),
(100118, 123513, 'Yzekiel Cooper', '215', 'yzzek', 'Gcash', '8787', 'uploads/67527c74d659e_69e40c16-1278-4a94-9625-3b6f27f1bb1d.jpg', '2024-12-06 04:24:20', 'Not yet confirmed'),
(100119, 123513, 'Yzekiel Cooper', '546', 'sadas', 'Gcash', '8787', 'uploads/6752865d3cfb8_6c5c9047-6419-4908-a34c-86bc628a38a7.jpg', '2024-12-06 05:06:37', 'Not yet confirmed'),
(100120, 123513, 'Yzekiel Cooper', '215', 'yzzek', 'Maya', '8787', 'uploads/6752867fb3377_69e40c16-1278-4a94-9625-3b6f27f1bb1d.jpg', '2024-12-06 05:07:11', 'Not yet confirmed'),
(100121, 123470, 'Shaima Mangadang', '215', 'shai', 'Maya', '213452', 'uploads/67537be44983d_6c5c9047-6419-4908-a34c-86bc628a38a7.jpg', '2024-12-07 06:56:57', 'Confirmed'),
(100122, 123516, 'Shaima Marie Mangadang', '546546', 'asdasd', 'Gcash', '54654', 'uploads/6754072baeaa4_Midnight-corded EMERALD GREEN.JPG', '2024-12-07 08:30:05', 'Confirmed'),
(100123, 123515, 'Chappell Roan', '09989333165', 'chao', 'Gcash', '213452', 'uploads/6755268d7b0e1_a02fa0ee-b25e-4859-b526-cc1a9daea444.jpg', '2024-12-08 04:54:37', 'Not yet confirmed');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `category` enum('beaded lace','corded lace','caviar','candy crush','panel','velvet') NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `Status` varchar(255) NOT NULL,
  `product_image` varchar(100) DEFAULT NULL,
  `product_descript` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `roll_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `category`, `price`, `Status`, `product_image`, `product_descript`, `date`, `roll_price`) VALUES
(1, 'Enchanted-Beaded', 'beaded lace', 1000.00, 'In Stock', 'Assets\\inventory\\Enchanted-beaded CYAN.JPG', '', '2024-12-02 07:03:16', 30000.00),
(2, 'Midnight-Corded', 'corded lace', 350.00, 'In Stock', 'Assets\\inventory\\Midnight-corded.JPG', '', '2024-11-26 13:52:36', 20000.00),
(3, 'Spark Fly-Caviar', 'caviar', 600.00, 'In Stock', 'Assets\\inventory\\Spark Fly-caviar.JPG', '', '2024-12-06 01:19:32', 30000.00),
(4, 'Bejeweled-Beaded', 'beaded lace', 1200.00, 'In Stock', 'Assets\\inventory\\Bejeweled-beaded.JPG', '', '2024-12-01 16:42:34', 20000.00),
(5, 'Wonderland Beaded Lace', 'beaded lace', 1000.00, 'In Stock', 'Assets\\inventory\\Wonderland beaded lace.JPG', '', '2024-12-05 05:53:29', 30000.00),
(6, 'Epiphany Candy Crush', 'candy crush', 350.00, 'In Stock', 'Assets\\inventory\\Epiphany candy crush.JPG', '', '2024-12-02 05:52:39', 20000.00),
(7, 'Juno Corded Spanish Lace ', 'corded lace', 350.00, 'In Stock', 'Assets\\inventory\\Juno Spanish Lace.JPG', 'low', '2024-11-29 09:42:33', 21000.00),
(8, 'Love Talk Corded 3D Lace ', 'corded lace', 600.00, 'In Stock', 'Assets\\inventory\\Love Talk 3D Lace.JPG', '', '2024-12-02 06:19:05', 36000.00),
(9, 'Diva Corded All Over Lace ', 'corded lace', 450.00, 'In Stock', 'Assets\\inventory\\DIVA.JPG', '', '2024-12-01 16:46:56', 27000.00),
(10, 'Corded Embroider Lace All Over ', 'corded lace', 450.00, 'In Stock', 'Assets\\inventory\\corded embroider lace.JPG', '', '2024-12-01 19:04:13', 27000.00),
(11, 'Style Panel Lace ', 'panel', 450.00, 'In Stock', 'Assets\\inventory\\style panel.JPG', '', '2024-12-04 17:00:49', 27000.00),
(12, 'Wannabe Panel Lace', 'panel', 500.00, 'In Stock', 'Assets\\inventory\\wannabe.JPG', '', '2024-12-01 16:11:01', 30000.00),
(13, 'Little Freak Panel All Over Lace', 'panel', 450.00, 'In Stock', 'Assets\\inventory\\little freak.JPG', '', '2024-12-01 19:25:52', 27000.00),
(14, 'Glam Panel Lace ', 'panel', 450.00, 'In Stock', 'Assets\\inventory\\glam panel.JPG', '', '2024-12-01 16:22:15', 27000.00),
(15, 'Primadona Heavy Beaded Lace', 'beaded lace', 2000.00, 'In Stock', 'Assets\\inventory\\primadona.JPG', '', '2024-12-01 18:50:32', 120000.00),
(16, 'Queendom Velvet Embroider ', 'velvet', 400.00, 'In Stock', 'Assets\\inventory\\queendom.JPG', '', '2024-12-02 10:01:10', 24000.00),
(17, 'Campfire Velvet Embroider ', 'velvet', 400.00, 'In Stock', 'Assets\\inventory\\campfire.JPG', '', '2024-12-01 17:33:48', 24000.00),
(18, 'Caramel Velvet Embroiderer', 'velvet', 400.00, 'In Stock', 'Assets\\inventory\\caramel.JPG', '', '2024-11-29 09:44:54', 24000.00),
(19, 'Sour Grapes Velvet Embroider ', 'velvet', 400.00, 'In Stock', 'Assets\\inventory\\sour grapes.JPG', '', '2024-12-01 18:33:42', 24000.00),
(20, 'Kingdom Velvet Embroider', 'velvet', 400.00, 'In Stock', 'Assets\\inventory\\kingdom.JPG', '', '2024-11-29 09:45:22', 24000.00),
(21, 'Circles Velvet Embroider', 'velvet', 400.00, 'In Stock', 'Assets\\inventory\\circles.JPG', '', '2024-12-02 06:19:05', 24000.00),
(22, 'Coffee Velvet Embroider', 'velvet', 400.00, 'In Stock', 'Assets\\inventory\\coffee.JPG', '', '2024-11-29 09:45:49', 24000.00),
(23, 'Winter Velvet Embroider', 'velvet', 400.00, 'In Stock', 'Assets\\inventory\\winter.JPG', '', '2024-12-01 19:20:38', 24000.00),
(26, 'Croissant', 'panel', 550.00, '', 'new_products/674eb8b889af8_Homemade-Croissants-Recipe60.jpg', '', '2024-12-05 06:06:51', 33000.00),
(29, 'Snow Moon Ahri Panel ', 'panel', 450.00, 'In Stock', 'Assets\\inventory\\ahri.PNG', '', '2024-12-08 01:17:14', 27000.00),
(30, 'Azalea Panel', 'panel', 450.00, 'In Stock', 'Assets\\inventory\\azalea.PNG', '', '2024-12-08 01:17:14', 27000.00),
(31, 'Dream Velvet', 'velvet', 600.00, 'In stock', 'Assets\\inventory\\dream.jpeg', '', '2024-12-08 01:20:11', 36000.00),
(32, 'Popstar Candy Crush', 'candy crush', 200.00, 'In stock', 'Assets\\inventory\\popstar.PNG', '', '2024-12-08 01:20:11', 12000.00),
(33, 'Paramore Candy Crush', 'candy crush', 200.00, 'In Stock', 'Assets\\inventory\\paramore.PNG', '', '2024-12-08 01:22:06', 12000.00),
(34, 'Honeymoon Velvet Sequins', 'velvet', 500.00, 'In Stock', 'Assets\\inventory\\honeymoon.PNG', '', '2024-12-08 01:22:06', 30000.00),
(35, 'Sweetener Velvet Sequins', 'velvet', 500.00, 'In Stock', 'Assets\\inventory\\sweetener.PNG', '', '2024-12-08 01:26:24', 30000.00),
(36, 'Starships Caviar', 'caviar', 500.00, 'In Stock', 'Assets\\inventory\\starships.PNG', '', '2024-12-08 01:26:24', 30000.00),
(37, 'Cupid Caviar', 'caviar', 500.00, 'In Stock', 'Assets\\inventory\\cupid.PNG', '', '2024-12-08 01:30:10', 36000.00),
(38, 'Cherry Caviar', 'caviar', 500.00, 'In Stock', 'Assets\\inventory\\cherry.PNG', '', '2024-12-08 01:30:10', 30000.00),
(39, 'MCR Vines Beaded', 'beaded lace', 1200.00, 'In Stock', 'Assets\\inventory\\mcr.jpg', '', '2024-12-08 01:32:33', 72000.00),
(40, 'Glittery Sk8er Beaded', 'beaded lace', 1200.00, 'In Stock', 'Assets\\inventory\\sk8er.jpg', '', '2024-12-08 01:38:07', 72000.00),
(41, 'Fall Garden Beaded', 'beaded lace', 1200.00, 'In Stock', 'Assets\\inventory\\fall garden.jpg', '', '2024-12-08 01:34:19', 72000.00),
(42, 'Carly Corded', 'corded lace', 500.00, 'In Stock', 'Assets\\inventory\\carly.jpg', '', '2024-12-08 01:37:53', 30000.00),
(43, 'Rainbow Candy Crush ', 'candy crush', 350.00, 'In Stock', 'Assets\\inventory\\rainbow.PNG', '', '2024-12-08 01:37:53', 21000.00),
(44, 'Rainbow Winter Candy Crush', 'candy crush', 350.00, 'In Stock', 'Assets\\inventory\\rainbow winter.PNG', '', '2024-12-08 01:40:31', 21000.00),
(45, 'Sparkle Caviar', 'caviar', 450.00, 'In Stock', 'Assets\\inventory\\sparkle.PNG', '', '2024-12-08 01:40:31', 27000.00),
(46, 'New Jeans Caviar', 'caviar', 500.00, '', 'Assets\\inventory\\newjeans.PNG', '', '2024-12-08 01:41:48', 30000.00);

-- --------------------------------------------------------

--
-- Table structure for table `product_colors`
--

CREATE TABLE `product_colors` (
  `color_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `color_name` varchar(50) NOT NULL,
  `product_pic` varchar(255) DEFAULT NULL,
  `yards` int(11) NOT NULL DEFAULT 350,
  `rolls` int(11) NOT NULL DEFAULT 50
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_colors`
--

INSERT INTO `product_colors` (`color_id`, `product_id`, `color_name`, `product_pic`, `yards`, `rolls`) VALUES
(1, 1, 'Cyan', 'Assets\\inventory\\1 Enchanted-beaded P1000\\Enchanted-beaded CYAN.JPG', 315, 349),
(2, 1, 'Red', 'Assets\\inventory\\1 Enchanted-beaded P1000\\Enchanted-beaded RED.JPG', 5, 5),
(3, 1, 'Royal Blue', 'Assets\\inventory\\1 Enchanted-beaded P1000\\Enchanted-beaded ROYALBLUE.JPG', 30, 0),
(4, 1, 'White', 'Assets\\inventory\\1 Enchanted-beaded P1000\\Enchanted-beaded WHITE.JPG', 9, 0),
(5, 2, 'Blush Pink', 'Assets\\inventory\\2 Midnight-corded\\Midnight-corded BLUSHPINK.JPG', 15, 25),
(6, 2, 'Champagne', 'Assets\\inventory\\2 Midnight-corded\\Midnight-corded CHAMPAGNE.JPG', 15, 50),
(7, 2, 'Emerald Green', 'Assets\\inventory\\2 Midnight-corded\\Midnight-corded EMERALD GREEN(1).JPG', 13, 50),
(8, 2, 'Lavender', 'Assets\\inventory\\2 Midnight-corded\\Midnight-corded LAVENDER.JPG', 14, 50),
(9, 2, 'Red', 'Assets\\inventory\\2 Midnight-corded\\Midnight-corded RED.JPG', 50, 0),
(10, 3, 'Champagne', 'Assets\\inventory\\3 Spark Fly-caviar P600\\Spark Fly-caviar CHAMPAGNE.JPG', 30, 50),
(11, 3, 'Emerald Green', 'Assets\\inventory\\3 Spark Fly-caviar P600\\Spark Fly-caviar EMERALD GREEN.JPG', 30, 50),
(12, 3, 'Fuchsia Pink', 'Assets\\inventory\\3 Spark Fly-caviar P600\\Spark Fly-caviar FUCHSIA PINK.JPG', 28, 50),
(13, 3, 'Magenta', 'Assets\\inventory\\3 Spark Fly-caviar P600\\Spark Fly-caviar MAGENTA.JPG', 29, 50),
(14, 3, 'Red', 'Assets\\inventory\\3 Spark Fly-caviar P600\\Spark Fly-caviar RED.JPG', 30, 50),
(15, 4, 'Champagne', 'Assets\\inventory\\4 Bejeweled-Beaded\\Bejeweled-beaded CHAMPAGNE.JPG', 20, 45),
(16, 4, 'Dark Green', 'Assets\\inventory\\4 Bejeweled-Beaded\\Bejeweled-beaded DARKGREEN.JPG', 10, 50),
(17, 4, 'Pink-Champagne', 'Assets\\inventory\\4 Bejeweled-Beaded\\Bejeweled-beaded PINK-CHAMPAGNE.JPG', 69, 50),
(18, 4, 'Pink', 'Assets\\inventory\\4 Bejeweled-Beaded\\Bejeweled-beaded PINK.JPG', 23, 50),
(19, 4, 'Purple-Pink', 'Assets\\inventory\\4 Bejeweled-Beaded\\Bejeweled-beaded PURPLE-PINK.JPG', 12, 50),
(20, 5, 'Champagne', 'Assets\\inventory\\5 wonderland beaded lace\\Wonderland-beaded lace  CHAMPAGNE(1).JPG', 50, 50),
(21, 5, 'Royal Blue', 'Assets\\inventory\\5 wonderland beaded lace\\Wonderland-beaded lace ROYALBLUE(1).JPG', 10, 50),
(22, 5, 'Silver', 'Assets\\inventory\\5 wonderland beaded lace\\Wonderland-beaded lace SILVER.JPG', 80, 50),
(23, 5, 'White-Purple', 'Assets\\inventory\\5 wonderland beaded lace\\Wonderland-beaded lace WHITE-PURPLE.JPG', 100, 50),
(24, 6, 'Red', 'Assets\\inventory\\6 Epiphany candy crush\\Epiphany candy crush  RED.JPG', 120, 50),
(25, 6, 'Silver', 'Assets\\inventory\\6 Epiphany candy crush\\Epiphany candy crush SILVER.JPG', 15, 50),
(26, 6, 'Aqua Blue', 'Assets\\inventory\\6 Epiphany candy crush\\Epiphany candy crush AQUABLUE.JPG', 50, 50),
(27, 6, 'Champagne', 'Assets\\inventory\\6 Epiphany candy crush\\Epiphany candy crush CHAMPAGNE.JPG', 50, 50),
(28, 6, 'Emerald Green', 'Assets\\inventory\\6 Epiphany candy crush\\Epiphany candy crush EMERALD GREEN.JPG', 50, 50),
(29, 6, 'Fuchsia Pink', 'Assets\\inventory\\6 Epiphany candy crush\\Epiphany candy crush FUCHSIA PINK.JPG', 50, 50),
(30, 6, 'Green', 'Assets\\inventory\\6 Epiphany candy crush\\Epiphany candy crush GREEN.JPG', 0, 50),
(31, 6, 'Orange', 'Assets\\inventory\\6 Epiphany candy crush\\Epiphany candy crush ORANGE.JPG', 0, 50),
(32, 6, 'Pink', 'Assets\\inventory\\6 Epiphany candy crush\\Epiphany candy crush PINK.JPG', 0, 50),
(36, 7, 'Aqua Blue', 'Assets\\inventory\\7 Juno (CORDED) Spanish Lace 350\\Juno Spanish Lace AQUA BLUE.JPG', 0, 50),
(37, 7, 'Blush Pink', 'Assets\\inventory\\7 Juno (CORDED) Spanish Lace 350\\Juno Spanish Lace BLUSH PINK.JPG', 0, 50),
(38, 7, 'Orange', 'Assets\\inventory\\7 Juno (CORDED) Spanish Lace 350\\Juno Spanish Lace ORANGE.JPG', 0, 50),
(39, 7, 'White', 'Assets\\inventory\\7 Juno (CORDED) Spanish Lace 350\\Juno Spanish Lace WHITE.JPG', 0, 50),
(40, 8, 'Baby Blue', 'Assets\\inventory\\8 Love Talk (CORDED) 3D Lace 600\\Love Talk 3D Lace BABY BLUE.JPG', 0, 50),
(41, 8, 'Blue', 'Assets\\inventory\\8 Love Talk (CORDED) 3D Lace 600\\Love Talk 3D Lace BLUE.JPG', 0, 50),
(42, 8, 'Blush Pink', 'Assets\\inventory\\8 Love Talk (CORDED) 3D Lace 600\\Love Talk 3D Lace BLUSH PINK.JPG', 0, 50),
(43, 8, 'Emerald Green', 'Assets\\inventory\\8 Love Talk (CORDED) 3D Lace 600\\Love Talk 3D Lace EMERALD GREEN.JPG', 0, 50),
(44, 8, 'Green', 'Assets\\inventory\\8 Love Talk (CORDED) 3D Lace 600\\Love Talk 3D Lace GREEN.JPG', 0, 50),
(45, 8, 'Grey', 'Assets\\inventory\\8 Love Talk (CORDED) 3D Lace 600\\Love Talk 3D Lace GREY.JPG', 0, 50),
(46, 8, 'Yellow', 'Assets\\inventory\\8 Love Talk (CORDED) 3D Lace 600\\Love Talk 3D Lace YELLOW.JPG', 0, 50),
(47, 9, 'Baby Blue', 'Assets\\inventory\\9 Diva (CORDED) All Over Lace 450\\baby blue.JPG', 0, 50),
(48, 9, 'Gold', 'Assets\\inventory\\9 Diva (CORDED) All Over Lace 450\\gold.JPG', 0, 50),
(49, 9, 'Pink', 'Assets\\inventory\\9 Diva (CORDED) All Over Lace 450\\pink.JPG', 0, 50),
(50, 10, 'Blue', 'Assets\\inventory\\10 (CORDED) Embroider Lace All Over 450\\blue.JPG', 0, 50),
(51, 10, 'Baby Pink', 'Assets\\inventory\\10 (CORDED) Embroider Lace All Over 450\\pink.jpeg', 0, 50),
(52, 10, 'White', 'Assets\\inventory\\10 (CORDED) Embroider Lace All Over 450\\white.JPG', 0, 50),
(53, 11, 'Blue', 'Assets\\inventory\\11 Style Panel Lace 450\\blue.JPG', 0, 50),
(54, 11, 'Red', 'Assets\\inventory\\11 Style Panel Lace 450\\red.JPG', 0, 50),
(55, 11, 'White', 'Assets\\inventory\\11 Style Panel Lace 450\\white.JPG', 0, 50),
(56, 12, 'Dirty White', 'Assets\\inventory\\12 Wannabe (PANEL) Lace 500\\dirty white.JPG', 0, 50),
(57, 12, 'Khaki', 'Assets\\inventory\\12 Wannabe (PANEL) Lace 500\\khaki.JPG', 0, 50),
(58, 12, 'Yellow', 'Assets\\inventory\\12 Wannabe (PANEL) Lace 500\\yellow.JPG', 0, 50),
(59, 13, 'Red', 'Assets\\inventory\\13 Little Freak (PANEL) All Over Lace 450\\red.JPG', 0, 50),
(60, 13, 'Lilac', 'Assets\\inventory\\13 Little Freak (PANEL) All Over Lace 450\\violet.JPG', 0, 50),
(61, 13, 'White', 'Assets\\inventory\\13 Little Freak (PANEL) All Over Lace 450\\white.JPG', 0, 50),
(62, 14, 'Cyan', 'Assets\\inventory\\14 Glam Panel Lace 450\\cyan.JPG', 0, 50),
(63, 14, 'Silver', 'Assets\\inventory\\14 Glam Panel Lace 450\\silver.JPG', 0, 50),
(64, 14, 'White', 'Assets\\inventory\\14 Glam Panel Lace 450\\white.JPG', 0, 50),
(65, 15, 'Blue', 'Assets\\inventory\\15 Primadona (BEADED) Heavy Beaded Lace 2000\\blue.JPG', 0, 50),
(66, 15, 'Green', 'Assets\\inventory\\15 Primadona (BEADED) Heavy Beaded Lace 2000\\green.JPG', 0, 50),
(67, 15, 'Pink', 'Assets\\inventory\\15 Primadona (BEADED) Heavy Beaded Lace 2000\\pink.JPG', 0, 50),
(68, 16, 'Blue', 'Assets\\inventory\\16 Queendom Velvet Embroider 400\\a.blue.JPG', 0, 50),
(69, 16, 'Yellow', 'Assets\\inventory\\16 Queendom Velvet Embroider 400\\a.yellow.JPG', 0, 50),
(70, 17, 'Blue Green', 'Assets\\inventory\\17 Campfire Velvet Embroider\\blue green.JPG', 0, 50),
(71, 17, 'Maroon', 'Assets\\inventory\\17 Campfire Velvet Embroider\\maroon.JPG', 0, 50),
(72, 18, 'White', 'Assets\\inventory\\18 Caramel Velvet Embroiderer\\white.JPG', 0, 50),
(73, 19, 'Black', 'Assets\\inventory\\19 Sour Grapes Velvet Embroider\\black.JPG', 0, 50),
(74, 20, 'Violet', 'Assets\\inventory\\20 Kindom Velvet Embroider\\violet.JPG', 0, 50),
(75, 21, 'Red', 'Assets\\inventory\\21 Circles Velvet Embroider\\red.JPG', 20, 49),
(76, 22, 'White', 'Assets\\inventory\\22 Coffee Velvet Embroider\\white.JPG', 0, 50),
(77, 23, 'White', 'Assets\\inventory\\23 Winter Velvet Embroider\\white.JPG', 50, 50),
(83, 26, 'gren', 'new_products\\colors\\674ebaf437b73_Shrek_(character).png', 0, 50),
(85, 26, 'choco', 'new_products\\colors\\674ebb2898d4d_lacherpatisserie-petitgateaux-01.jpg', 0, 50),
(90, 26, 'penk', 'new_products\\colors\\6754de91e1fc1_maxresdefault.jpg', 50, 50),
(91, 29, 'Baby Blue', 'Assets\\inventory\\24 Ahri Panel 2 450\\baby blue.PNG', 349, 50),
(92, 29, 'Fuchsia Pink', 'Assets\\inventory\\24 Ahri Panel 2 450\\Fuchsia Pink.PNG', 350, 50),
(93, 29, 'Grey', 'Assets\\inventory\\24 Ahri Panel 2 450\\grey.PNG', 350, 50),
(94, 29, 'Red', 'Assets\\inventory\\24 Ahri Panel 2 450\\red.PNG', 350, 50),
(95, 29, 'White', 'Assets\\inventory\\24 Ahri Panel 2 450\\white.jpeg', 350, 50),
(96, 30, 'Dark Green', 'Assets\\inventory\\25 Azalea Panel 1 450\\dark green.PNG', 350, 50),
(97, 30, 'Green', 'Assets\\inventory\\25 Azalea Panel 1 450\\green.PNG', 350, 50),
(98, 30, 'Grey', 'Assets\\inventory\\25 Azalea Panel 1 450\\grey.PNG', 350, 50),
(99, 30, 'Lime Green', 'Assets\\inventory\\25 Azalea Panel 1 450\\lime green.PNG', 350, 50),
(100, 30, 'Pink', 'Assets\\inventory\\25 Azalea Panel 1 450\\pink.PNG', 350, 50),
(101, 30, 'White', 'Assets\\inventory\\25 Azalea Panel 1 450\\white.PNG', 350, 50),
(102, 30, 'Yellow', 'Assets\\inventory\\25 Azalea Panel 1 450\\yellow.PNG', 350, 50),
(103, 31, 'Blue', 'Assets\\inventory\\26 dream velvet1 600\\blue.PNG', 350, 50),
(104, 31, 'Red', 'Assets\\inventory\\26 dream velvet1 600\\red.PNG', 350, 50),
(105, 31, 'Violet-Leaf', 'Assets\\inventory\\26 dream velvet1 600\\violet-leaf.jpeg', 350, 50),
(106, 31, 'Violet', 'Assets\\inventory\\26 dream velvet1 600\\violet.PNG', 350, 50),
(107, 31, 'White', 'Assets\\inventory\\26 dream velvet1 600\\white.PNG', 350, 50),
(108, 32, 'Light Green', 'Assets\\inventory\\27 popstar candycrush1 200\\light green.PNG', 350, 50),
(109, 32, 'Maroon', 'Assets\\inventory\\27 popstar candycrush1 200\\maroon.PNG', 350, 50),
(110, 32, 'Navy Blue', 'Assets\\inventory\\27 popstar candycrush1 200\\navy blue.PNG', 350, 50),
(111, 32, 'Pink', 'Assets\\inventory\\27 popstar candycrush1 200\\pink.PNG', 350, 50),
(112, 32, 'Violet', 'Assets\\inventory\\27 popstar candycrush1 200\\violet.PNG', 350, 50),
(113, 33, 'White', 'Assets\\inventory\\27 popstar candycrush1 200\\white.PNG', 350, 50),
(114, 33, 'Black', 'Assets\\inventory\\28 paramore candycrush2 200\\black.PNG', 350, 50),
(115, 33, 'Blue', 'Assets\\inventory\\28 paramore candycrush2 200\\blue.PNG', 350, 50),
(116, 33, 'Cyan', 'Assets\\inventory\\28 paramore candycrush2 200\\cyan.PNG', 350, 50),
(117, 33, 'Fuchsia Pink', 'Assets\\inventory\\28 paramore candycrush2 200\\fuchsia pink.PNG', 350, 50),
(118, 33, 'Green', 'Assets\\inventory\\28 paramore candycrush2 200\\green.PNG', 350, 50),
(119, 34, 'Orange', 'Assets\\inventory\\29 honeymoon velvet sequins1 500\\orange.PNG', 350, 50),
(120, 34, 'Pink', 'Assets\\inventory\\29 honeymoon velvet sequins1 500\\pink.PNG', 350, 50),
(121, 34, 'Red', 'Assets\\inventory\\29 honeymoon velvet sequins1 500\\red.PNG', 350, 50),
(122, 34, 'White w/ Black', 'Assets\\inventory\\29 honeymoon velvet sequins1 500\\white with black.PNG', 350, 50),
(123, 34, 'White w/ Blue', 'Assets\\inventory\\29 honeymoon velvet sequins1 500\\white with blue.PNG', 350, 50),
(124, 34, 'White', 'Assets\\inventory\\29 honeymoon velvet sequins1 500\\white.PNG', 350, 50),
(125, 34, 'Yellow', 'Assets\\inventory\\29 honeymoon velvet sequins1 500\\yellow.PNG', 350, 50),
(126, 35, 'Black', 'Assets\\inventory\\30 sweetener velvet sequins2 500\\black.PNG', 350, 50),
(127, 35, 'Brown', 'Assets\\inventory\\30 sweetener velvet sequins2 500\\brown.PNG', 350, 50),
(128, 35, 'Cream', 'Assets\\inventory\\30 sweetener velvet sequins2 500\\cream.PNG', 350, 50),
(129, 35, 'Dark Violet', 'Assets\\inventory\\30 sweetener velvet sequins2 500\\dark violet.PNG', 350, 50),
(130, 35, 'Fuchsia Pink', 'Assets\\inventory\\30 sweetener velvet sequins2 500\\fuchsia pink.PNG', 350, 50),
(131, 35, 'Ivory', 'Assets\\inventory\\30 sweetener velvet sequins2 500\\Ivory.PNG', 350, 50),
(132, 35, 'Lime', 'Assets\\inventory\\30 sweetener velvet sequins2 500\\lime.PNG', 350, 50),
(133, 36, 'Orange', 'Assets\\inventory\\31 starships caviar1 500\\orange.PNG', 350, 50),
(134, 36, 'Pale Blue', 'Assets\\inventory\\31 starships caviar1 500\\pale blue.PNG', 350, 50),
(135, 36, 'Red', 'Assets\\inventory\\31 starships caviar1 500\\red.PNG', 350, 50),
(136, 36, 'White', 'Assets\\inventory\\31 starships caviar1 500\\white.PNG', 350, 50),
(137, 36, 'Yellow', 'Assets\\inventory\\31 starships caviar1 500\\yellow.PNG', 350, 50),
(138, 37, 'Bright Pink', 'Assets\\inventory\\32 cupid caviar1 500\\bright pink.PNG', 350, 50),
(139, 37, 'Gold', 'Assets\\inventory\\32 cupid caviar1 500\\gold.PNG', 350, 50),
(140, 37, 'Green', 'Assets\\inventory\\32 cupid caviar1 500\\green_.PNG', 350, 50),
(141, 37, 'Maroon', 'Assets\\inventory\\32 cupid caviar1 500\\maroon.PNG', 350, 50),
(142, 37, 'White', 'Assets\\inventory\\32 cupid caviar1 500\\white(1).PNG', 350, 50),
(143, 38, 'Amethyst', 'Assets\\inventory\\33 cherry caviar1 500\\amethyst.PNG', 350, 50),
(144, 38, 'Baby Green', 'Assets\\inventory\\33 cherry caviar1 500\\baby green.PNG', 350, 50),
(145, 38, 'Blush Pink', 'Assets\\inventory\\33 cherry caviar1 500\\baby pink.PNG', 350, 50),
(146, 38, 'Black', 'Assets\\inventory\\33 cherry caviar1 500\\black.PNG', 350, 50),
(147, 38, 'Blue', 'Assets\\inventory\\33 cherry caviar1 500\\blue.PNG', 350, 50),
(148, 39, 'Red', 'Assets\\inventory\\34 MCR beaded1 1200\\red.jpg', 350, 50),
(149, 39, 'White', 'Assets\\inventory\\34 MCR beaded1 1200\\white.jpg', 350, 50),
(150, 39, 'Yellow', 'Assets\\inventory\\34 MCR beaded1 1200\\Yellow.jpg', 350, 50),
(151, 40, 'Purple Pink', 'Assets\\inventory\\35 glittery sk8er beaded2 1200\\purple_pink.jpg', 350, 50),
(152, 40, 'Violet', 'Assets\\inventory\\35 glittery sk8er beaded2 1200\\violet.jpg', 350, 50),
(153, 41, 'Baby Blue', 'Assets\\inventory\\36 fall garden beaded3 1200\\baby_blue.jpg', 350, 50),
(154, 41, 'Navy Blue', 'Assets\\inventory\\36 fall garden beaded3 1200\\dark_blue.jpg', 350, 50),
(155, 41, 'Ivory', 'Assets\\inventory\\36 fall garden beaded3 1200\\ivory.jpg', 350, 50),
(156, 41, 'Violet', 'Assets\\inventory\\36 fall garden beaded3 1200\\violet.jpg', 350, 50),
(157, 42, 'Gold', 'Assets\\inventory\\37 carly corded1 500\\gold.jpg', 350, 50),
(158, 42, 'Purple', 'Assets\\inventory\\37 carly corded1 500\\purple.jpg', 350, 50),
(159, 42, 'Silver', 'Assets\\inventory\\37 carly corded1 500\\silver.jpg', 350, 50),
(160, 42, 'White', 'Assets\\inventory\\37 carly corded1 500\\white.jpg', 350, 50),
(161, 43, 'Ivory', 'Assets\\inventory\\38 rainbow candycrush2 350\\ivory.PNG', 350, 50),
(162, 43, 'Maroon', 'Assets\\inventory\\38 rainbow candycrush2 350\\maroon.PNG', 350, 50),
(163, 43, 'Navy Blue', 'Assets\\inventory\\38 rainbow candycrush2 350\\navy blue.PNG', 350, 50),
(164, 43, 'Red', 'Assets\\inventory\\38 rainbow candycrush2 350\\red.PNG', 350, 50),
(165, 43, 'Rose', 'Assets\\inventory\\38 rainbow candycrush2 350\\rose.PNG', 350, 50),
(166, 44, 'Baby Blue', 'Assets\\inventory\\39 rainbow winter candycrush3 350\\baby blue.PNG', 350, 50),
(167, 44, 'Blush Pink', 'Assets\\inventory\\39 rainbow winter candycrush3 350\\baby pink.PNG', 350, 50),
(168, 44, 'Blue', 'Assets\\inventory\\39 rainbow winter candycrush3 350\\blue.PNG', 350, 50),
(169, 44, 'Brown', 'Assets\\inventory\\39 rainbow winter candycrush3 350\\brown.PNG', 350, 50),
(170, 44, 'Green', 'Assets\\inventory\\39 rainbow winter candycrush3 350\\green.PNG', 350, 50),
(171, 45, 'black', 'Assets\\inventory\\40sparkle caviar 450\\black.PNG', 350, 50),
(172, 45, 'Gold', 'Assets\\inventory\\40sparkle caviar 450\\gold.PNG', 350, 50),
(173, 45, 'Green', 'Assets\\inventory\\40sparkle caviar 450\\green.PNG', 350, 50),
(174, 45, 'White', 'Assets\\inventory\\40sparkle caviar 450\\white.PNG', 350, 50),
(175, 46, 'Black', 'Assets\\inventory\\41 new jeans caviar2 500\\black.PNG', 350, 50),
(176, 46, 'Cream', 'Assets\\inventory\\41 new jeans caviar2 500\\cream.PNG', 350, 50),
(177, 46, 'Green', 'Assets\\inventory\\41 new jeans caviar2 500\\green.PNG', 350, 50),
(178, 46, 'Pink', 'Assets\\inventory\\41 new jeans caviar2 500\\pink.PNG', 350, 50),
(179, 46, 'Red', 'Assets\\inventory\\41 new jeans caviar2 500\\red.PNG', 350, 50),
(180, 45, 'White', 'Assets\\inventory\\41 new jeans caviar2 500\\white.PNG', 350, 50),
(181, 46, 'Yellow', 'Assets\\inventory\\41 new jeans caviar2 500\\yellow.PNG', 350, 50),
(182, 46, 'White', 'Assets\\inventory\\41 new jeans caviar2 500\\white.PNG', 350, 50);

-- --------------------------------------------------------

--
-- Table structure for table `product_ratings`
--

CREATE TABLE `product_ratings` (
  `post_id` bigint(12) NOT NULL,
  `user_id` int(6) NOT NULL,
  `user_firstname` varchar(255) NOT NULL,
  `user_lastname` varchar(255) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rating` varchar(1) NOT NULL,
  `title` varchar(50) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_ratings`
--

INSERT INTO `product_ratings` (`post_id`, `user_id`, `user_firstname`, `user_lastname`, `product_id`, `rating`, `title`, `description`, `time`) VALUES
(122300000059, 123507, 'mel', 'cutie', 1, '5', 'Danica', 'Lepardo', '2024-11-20 09:17:07'),
(122300000060, 123470, 'Shaima', 'Mangadang', 1, '4', 'dddddddddddddddddddddd', 'dsatrgrgdgdfgdgrrgr', '2024-11-20 09:27:46'),
(122300000061, 123470, 'Shaima', 'Mangadang', 1, '5', 'sfdfsdf', 'cccccccccccc', '2024-11-20 09:28:46'),
(122300000062, 123474, 'Danica Kassandra', 'Lepardo', 3, '1', 'HAYNAKOOOOOOOOOOOOOOOOOOOOOOOOOO', 'skadhsjkfhdsjfhdsfjdshfjsdhfdksjfsdkjfhdsjfhf', '2024-11-20 09:29:52'),
(122300000063, 123474, 'Danica Kassandra', 'Lepardo', 1, '4', 'goggoogog', 'dddddddd', '2024-11-20 09:31:27'),
(122300000064, 123474, 'Danica Kassandra', 'Lepardo', 1, '1', 'EHHEHEHEHE', 'huhuhuhuhu', '2024-11-20 09:31:45'),
(122300000065, 123474, 'Danica Kassandra', 'Lepardo', 1, '4', 'kaboggg', 'FSFfsdddsdfsdfddddddddddddddddddsdfsgsdgdgdgdhsgbfsajkdfhjkfhdjkfbdsjdnfjkdnfdjfnsdjkfsdjkfbnsdjfkndsfjnsfjsnfjsdnfjksdfbndsjfbsdjfndsffdsjvndskjvndsfjdnfdjfndsfjkdsnvjsdnvsjdfnsdjfndsfjdsfjsdfsdfnsfksfasleifhaweoiftafklsdjf;leirupsefjsdlkvjsld;knfmweklnfklsvnsdmnvx,vkdfjkdjfkjfkjf', '2024-11-20 09:32:20'),
(122300000066, 123474, 'Danica Kassandra', 'Lepardo', 1, '5', 'sjxhjshdjsahdjashd', '11111111111211332143244234', '2024-11-20 09:34:27'),
(122300000067, 123474, 'Danica Kassandra', 'Lepardo', 1, '5', 'end the sem', 'please!!!!!!!!!', '2024-11-20 09:37:44'),
(122300000068, 123474, 'Danica Kassandra', 'Lepardo', 1, '2', 'ayaw q na', 'sdfsdfsdfsdf', '2024-11-20 09:38:04'),
(122300000069, 123474, 'Danica Kassandra', 'Lepardo', 1, '4', 'last oneee', 'haysttttt', '2024-11-20 09:39:07'),
(122300000070, 123474, 'Danica Kassandra', 'Lepardo', 1, '5', 'last one ULIT', 'HAYNAKO', '2024-11-20 09:39:32'),
(122300000071, 123474, 'Danica Kassandra', 'Lepardo', 1, '1', 'sssssssss', 'fsfgsdfdsfs', '2024-11-20 09:53:53'),
(122300000072, 123474, 'Danica Kassandra', 'Lepardo', 1, '5', 'sdsfdsfds', 'sdvdsdfs', '2024-11-20 09:54:10'),
(122300000073, 123474, 'Danica Kassandra', 'Lepardo', 1, '5', 'sssssssssssssssssss', 'aadasfdsaf', '2024-11-20 09:56:30'),
(122300000074, 123474, 'Danica Kassandra', 'Lepardo', 1, '3', 'dddsdsfsdfd', 'dsfsdfddddddddddd', '2024-11-20 09:56:41'),
(122300000075, 123474, 'Danica Kassandra', 'Lepardo', 1, '5', 'Great Fabrics', 'I really like this', '2024-11-20 10:14:23'),
(122300000078, 123470, 'Shaima', 'Mangadang', 1, '3', 'Shipped a little late than expected', '', '2024-11-21 11:05:51'),
(122300000079, 123470, 'Shaima', 'Mangadang', 1, '5', 'Wow!!!', 'The fabrics are very high quality!!', '2024-11-21 11:11:41'),
(122300000080, 123491, 'Juan', 'Dela Cruz', 1, '4', 'Late Shipping', 'Item was shipped a little late but great quality', '2024-11-21 11:14:07'),
(122300000114, 123513, 'Yzekiel', 'Cooper', 6, '5', 'nice', 'angas ng tela lods. will buy again', '2024-11-24 07:37:26'),
(122300000115, 123513, 'Yzekiel', 'Cooper', 3, '3', 'okay lang', 'ganda sana ng tela kayo yung name favorites song ng ex ko yan kaya 3 star muna ', '2024-11-24 07:38:36'),
(122300000116, 123513, 'Yzekiel', 'Cooper', 5, '5', 'naks', 'ganda po maam', '2024-11-24 15:10:29'),
(122300000117, 123470, 'Shaima', 'Mangadang', 26, '5', 'Wow', 'bakit po bastos', '2024-12-05 06:47:05'),
(122300000118, 1, 'Sharleen ', 'Olaguir', 6, '4', 'try', 'what is this bootiful produk', '2024-12-06 20:27:06'),
(122300000119, 1, 'Sharleen ', 'Olaguir', 3, '5', 'woww', 'taylor swift inspired po ba to? sana may olivia rodrigo next', '2024-12-06 21:07:50');

-- --------------------------------------------------------

--
-- Table structure for table `product_revenue`
--

CREATE TABLE `product_revenue` (
  `revenue_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `items_sold` int(11) NOT NULL,
  `total_revenue` double(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_revenue`
--

INSERT INTO `product_revenue` (`revenue_id`, `product_id`, `items_sold`, `total_revenue`) VALUES
(1, 1, 76, 76000.00),
(2, 2, 8, 2800.00),
(3, 3, 14, 8400.00),
(4, 4, 68, 201600.00),
(5, 5, 12, 12000.00),
(6, 6, 32, 31200.00),
(7, 7, 0, 0.00),
(8, 8, 33, 19800.00),
(9, 9, 2, 900.00),
(10, 10, 30, 40500.00),
(11, 11, 32, 41400.00),
(12, 12, 30, 45000.00),
(13, 13, 31, 40950.00),
(14, 14, 31, 40950.00),
(15, 15, 30, 180000.00),
(16, 16, 60, 48000.00),
(17, 17, 30, 36000.00),
(18, 18, 0, 0.00),
(19, 19, 30, 36000.00),
(20, 20, 0, 0.00),
(21, 21, 60, 72000.00),
(22, 22, 30, 36000.00),
(23, 23, 32, 36800.00),
(24, 26, 2, 1100.00),
(25, 29, 1, 450.00);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `sales_id` int(11) NOT NULL,
  `week_start` date DEFAULT NULL,
  `week_end` date DEFAULT NULL,
  `monday` decimal(10,2) NOT NULL,
  `tuesday` decimal(10,2) NOT NULL,
  `wednesday` decimal(10,2) NOT NULL,
  `thursday` decimal(10,2) NOT NULL,
  `friday` decimal(10,2) NOT NULL,
  `saturday` decimal(10,2) NOT NULL,
  `sunday` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`sales_id`, `week_start`, `week_end`, `monday`, `tuesday`, `wednesday`, `thursday`, `friday`, `saturday`, `sunday`) VALUES
(10202411, NULL, NULL, 10000.00, 15365.00, 12365.00, 10689.00, 15365.00, 14896.00, 16000.00),
(10202416, '2024-11-25', '2024-12-01', 1550.00, 0.00, 0.00, 1800.00, 6300.00, 0.00, 72450.00);

-- --------------------------------------------------------

--
-- Table structure for table `shopping_cart`
--

CREATE TABLE `shopping_cart` (
  `cart_id` int(255) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product` varchar(255) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `color` varchar(255) NOT NULL,
  `color_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shopping_cart`
--

INSERT INTO `shopping_cart` (`cart_id`, `product_id`, `product`, `customer_id`, `firstname`, `lastname`, `unit_price`, `quantity`, `color`, `color_id`, `total_price`) VALUES
(100011, 5, 'Wonderland Beaded Lace', 123507, 'Yzekiel', 'Cooper', 1000.00, 2, 'Royal Blue', 20, 2000.00),
(100120, 6, 'Epiphany Candy Crush', 123513, 'Yzekiel', 'Cooper', 350.00, 1, 'Red', 24, 350.00);

-- --------------------------------------------------------

--
-- Table structure for table `statistics`
--

CREATE TABLE `statistics` (
  `stats_id` int(11) NOT NULL,
  `total_sales` decimal(10,2) NOT NULL,
  `total_icnome` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `statistics`
--

INSERT INTO `statistics` (`stats_id`, `total_sales`, `total_icnome`) VALUES
(98765, 150486.00, 1698583.00);

-- --------------------------------------------------------

--
-- Table structure for table `users_credentials`
--

CREATE TABLE `users_credentials` (
  `ID` int(6) NOT NULL,
  `FIRSTNAME` varchar(255) NOT NULL,
  `LASTNAME` varchar(255) NOT NULL,
  `EMAIL` varchar(255) NOT NULL,
  `PASSWORD` varchar(15) NOT NULL,
  `BIRTHDATE` date NOT NULL,
  `GENDER` varchar(10) NOT NULL,
  `ADDRESS` varchar(255) NOT NULL,
  `SUBDIVISION` varchar(255) DEFAULT NULL,
  `BARANGAY` varchar(255) NOT NULL,
  `POSTAL` varchar(255) NOT NULL,
  `CITY` varchar(255) NOT NULL,
  `PLACE` varchar(255) NOT NULL,
  `PHONE` varchar(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `login_attempts` int(11) DEFAULT 0,
  `lockout_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_credentials`
--

INSERT INTO `users_credentials` (`ID`, `FIRSTNAME`, `LASTNAME`, `EMAIL`, `PASSWORD`, `BIRTHDATE`, `GENDER`, `ADDRESS`, `SUBDIVISION`, `BARANGAY`, `POSTAL`, `CITY`, `PLACE`, `PHONE`, `date`, `login_attempts`, `lockout_time`) VALUES
(1, 'Sharleen ', 'Olaguir', 'shaolaguir@gmail.com', 'sharleenadmin', '2003-10-25', 'female', '11 Suha St.', 'Post Proper', 'Southside', '1200', 'Taguig', 'Metro Manila', '09989333165', '2024-12-05 04:39:51', 0, NULL),
(123470, 'Shaima', 'Mangadang', 'shaimamangadang@gmail.com', 'shaima123', '1987-10-25', 'Female', 'Manila', '', '', '', '', 'Luzon', '09874514531', '2024-12-05 06:45:22', 0, NULL),
(123474, 'Danica Kassandra', 'Lepardo', 'd2nica12@gmail.com', 'danicuh!23', '2003-06-12', 'Female', 'sa ayala malls davao', '', '', '', '', 'Mindanao', '09865745127', '2024-12-05 05:53:16', 0, NULL),
(123475, 'la', 'gasmen', 'lrncndrw@gmail.com', 'lagasmen123', '2003-06-01', 'Male', 'Pasig', '', '', '', '', 'Metro Manila', '2147483647', '2024-12-04 17:28:23', 0, NULL),
(123476, 'xiao', 'lin', 'xiao@gmail.com', 'xiaoxiao123', '0005-02-10', 'Female', 'Sa genshin', '', '', '', '', '', '2147483647', '2024-11-25 15:34:46', 0, NULL),
(123482, 'Satoru', 'Gojo', 'gojo@gmail.com', 'gojojo12', '1989-07-12', 'male', 'jjk', '', '', '', '', '', '09123456789', '2024-11-25 15:34:46', 0, NULL),
(123484, 'Suguru', 'Geto', 'sugs@gmail.com ', 'sugurugeto', '2000-10-29', 'male', 'jjk', '', '', '', '', '', '09326545126', '2024-12-04 18:02:01', 3, '2024-12-04 19:05:01'),
(123486, 'Juan', 'Dela Cruz', 'juan123@gmai.com', 'juan123', '2001-02-10', 'female', 'MAkati', '', '', '', '', '', '12345678912', '2024-12-04 18:01:15', 0, NULL),
(123496, 'franz', 'mangao', 'franz123@gmail.com', 'qwertyu', '2024-11-08', 'male', 'Makati City', '', '', '', '', '', '0965234875', '2024-11-25 15:34:46', 0, NULL),
(123513, 'Yzekiel', 'Cooper', 'yzekeilcooper@gmail.com', 'yzekpogi!45', '2003-10-25', 'Male', '11 Pomelo St.', 'Panam ', 'Pinagsama', '1630', 'Taguig', 'Metro Manila', '09989331654', '2024-11-25 15:34:46', 0, NULL),
(123515, 'Chappell', 'Roan', 'chaps@gmail.com', 'chapel', '2024-12-15', 'Female', '11', 'dsas', 'asdasd', 'asdasd', 'asdsad', 'Metro Manila', '8798654', '2024-12-07 07:08:03', 0, NULL),
(123516, 'Shaima Marie', 'Mangadang', 'dkdlepardo12@gmail.com', 'shaima1234', '1212-02-21', 'Female', 'sadasd', 'sadas', 'dasd', 'sdsa', 'asdsa', 'Luzon', '54654654', '2024-12-07 08:27:31', 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`adminID`);

--
-- Indexes for table `bulk_order_details`
--
ALTER TABLE `bulk_order_details`
  ADD PRIMARY KEY (`bulk_order_id`);

--
-- Indexes for table `bulk_order_items`
--
ALTER TABLE `bulk_order_items`
  ADD PRIMARY KEY (`bulk_order_items`);

--
-- Indexes for table `bulk_payment`
--
ALTER TABLE `bulk_payment`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `bulk_shopping_cart`
--
ALTER TABLE `bulk_shopping_cart`
  ADD PRIMARY KEY (`bulk_cart_id`),
  ADD KEY `bulk_customer_id` (`customer_id`),
  ADD KEY `bulk_product_id` (`product_id`);

--
-- Indexes for table `cod_payment`
--
ALTER TABLE `cod_payment`
  ADD PRIMARY KEY (`cod_payment_id`);

--
-- Indexes for table `income`
--
ALTER TABLE `income`
  ADD PRIMARY KEY (`income_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notif_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`order_num`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_items_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `product_colors`
--
ALTER TABLE `product_colors`
  ADD PRIMARY KEY (`color_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_ratings`
--
ALTER TABLE `product_ratings`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `user_id` (`user_id`) USING BTREE,
  ADD KEY `product_id` (`product_id`),
  ADD KEY `userF` (`user_firstname`);

--
-- Indexes for table `product_revenue`
--
ALTER TABLE `product_revenue`
  ADD PRIMARY KEY (`revenue_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`sales_id`);

--
-- Indexes for table `shopping_cart`
--
ALTER TABLE `shopping_cart`
  ADD PRIMARY KEY (`cart_id`);

--
-- Indexes for table `statistics`
--
ALTER TABLE `statistics`
  ADD PRIMARY KEY (`stats_id`);

--
-- Indexes for table `users_credentials`
--
ALTER TABLE `users_credentials`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `adminID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `bulk_order_details`
--
ALTER TABLE `bulk_order_details`
  MODIFY `bulk_order_id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50000054;

--
-- AUTO_INCREMENT for table `bulk_order_items`
--
ALTER TABLE `bulk_order_items`
  MODIFY `bulk_order_items` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `bulk_payment`
--
ALTER TABLE `bulk_payment`
  MODIFY `payment_id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3212107;

--
-- AUTO_INCREMENT for table `bulk_shopping_cart`
--
ALTER TABLE `bulk_shopping_cart`
  MODIFY `bulk_cart_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120248;

--
-- AUTO_INCREMENT for table `cod_payment`
--
ALTER TABLE `cod_payment`
  MODIFY `cod_payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;

--
-- AUTO_INCREMENT for table `income`
--
ALTER TABLE `income`
  MODIFY `income_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=202413;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notif_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `order_num` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200115;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_items_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100124;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `product_colors`
--
ALTER TABLE `product_colors`
  MODIFY `color_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=183;

--
-- AUTO_INCREMENT for table `product_ratings`
--
ALTER TABLE `product_ratings`
  MODIFY `post_id` bigint(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122300000120;

--
-- AUTO_INCREMENT for table `product_revenue`
--
ALTER TABLE `product_revenue`
  MODIFY `revenue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `sales_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10202417;

--
-- AUTO_INCREMENT for table `shopping_cart`
--
ALTER TABLE `shopping_cart`
  MODIFY `cart_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100126;

--
-- AUTO_INCREMENT for table `statistics`
--
ALTER TABLE `statistics`
  MODIFY `stats_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98766;

--
-- AUTO_INCREMENT for table `users_credentials`
--
ALTER TABLE `users_credentials`
  MODIFY `ID` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123518;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `product_colors`
--
ALTER TABLE `product_colors`
  ADD CONSTRAINT `product_colors_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_revenue`
--
ALTER TABLE `product_revenue`
  ADD CONSTRAINT `product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
