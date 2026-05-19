-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 16, 2026 at 03:07 PM
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
-- Database: `resort_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `amenities`
--

CREATE TABLE `amenities` (
  `amenity_id` smallint(5) UNSIGNED NOT NULL,
  `amenity_name` varchar(100) NOT NULL,
  `icon_class` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `amenities`
--

INSERT INTO `amenities` (`amenity_id`, `amenity_name`, `icon_class`) VALUES
(1, 'Wi-Fi', 'ti-wifi'),
(2, 'Pool Access', 'ti-ripple'),
(3, 'Air Conditioning', 'ti-wind'),
(4, 'Hot Shower', 'ti-droplet'),
(5, 'TV', 'ti-device-tv'),
(6, 'Mini Fridge', 'ti-snowflake'),
(7, 'Breakfast', 'ti-egg'),
(8, 'Parking', 'ti-parking'),
(9, 'Beach Access', 'ti-umbrella'),
(10, 'BBQ Area', 'ti-flame');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `log_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `table_name` varchar(64) DEFAULT NULL,
  `record_id` varchar(50) DEFAULT NULL,
  `old_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_value`)),
  `new_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_value`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`log_id`, `user_id`, `action`, `table_name`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 2, 'Verify Payment', 'payments', '3', NULL, NULL, NULL, NULL, '2026-05-11 22:24:55'),
(2, 2, 'Manual Payment Verification', 'payments', '4', NULL, NULL, NULL, NULL, '2026-05-11 22:38:44');

-- --------------------------------------------------------

--
-- Table structure for table `blackout_dates`
--

CREATE TABLE `blackout_dates` (
  `blackout_id` int(10) UNSIGNED NOT NULL,
  `room_id` smallint(5) UNSIGNED DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(10) UNSIGNED NOT NULL,
  `reference_code` varchar(20) NOT NULL,
  `guest_id` int(10) UNSIGNED NOT NULL,
  `room_id` smallint(5) UNSIGNED NOT NULL,
  `promo_id` int(10) UNSIGNED DEFAULT NULL,
  `booked_by` int(10) UNSIGNED DEFAULT NULL,
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `num_adults` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `num_children` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `nights` tinyint(3) UNSIGNED NOT NULL,
  `base_amount` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','checked_in','checked_out','cancelled','no_show') NOT NULL DEFAULT 'pending',
  `booking_source` enum('online','walk_in','phone','agent') NOT NULL DEFAULT 'online',
  `special_requests` text DEFAULT NULL,
  `internal_notes` text DEFAULT NULL,
  `early_checkin` tinyint(1) NOT NULL DEFAULT 0,
  `late_checkout` tinyint(1) NOT NULL DEFAULT 0,
  `early_checkin_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `late_checkout_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cancelled_at` datetime DEFAULT NULL,
  `cancel_reason` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `reference_code`, `guest_id`, `room_id`, `promo_id`, `booked_by`, `check_in_date`, `check_out_date`, `num_adults`, `num_children`, `nights`, `base_amount`, `discount_amount`, `tax_amount`, `total_amount`, `status`, `booking_source`, `special_requests`, `internal_notes`, `early_checkin`, `late_checkout`, `early_checkin_fee`, `late_checkout_fee`, `cancelled_at`, `cancel_reason`, `created_at`, `updated_at`) VALUES
(3, 'IVR-0D34B90A', 1, 1, NULL, NULL, '2026-05-10', '2026-05-11', 2, 0, 1, 100.00, 0.00, 12.00, 112.00, 'checked_out', 'online', 'sfferfeee', NULL, 0, 0, 0.00, 0.00, NULL, NULL, '2026-05-10 23:04:04', '2026-05-11 22:51:07'),
(4, 'IVR-DEA6EF8E', 1, 2, NULL, NULL, '2026-05-11', '2026-05-12', 1, 0, 1, 2220.00, 0.00, 266.40, 2486.40, 'confirmed', 'online', 'dfddfd', NULL, 0, 0, 0.00, 0.00, NULL, NULL, '2026-05-11 22:37:53', '2026-05-11 22:38:44'),
(5, 'IVR-95A86194', 1, 3, NULL, NULL, '2026-05-11', '2026-05-12', 1, 0, 1, 4000.00, 0.00, 480.00, 4480.00, 'checked_in', 'online', '', NULL, 0, 0, 0.00, 0.00, NULL, NULL, '2026-05-11 22:48:55', '2026-05-11 23:01:47');

-- --------------------------------------------------------

--
-- Table structure for table `booking_groups`
--

CREATE TABLE `booking_groups` (
  `group_id` int(10) UNSIGNED NOT NULL,
  `group_reference` varchar(20) NOT NULL,
  `lead_guest_id` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `booking_group_items`
--

CREATE TABLE `booking_group_items` (
  `group_id` int(10) UNSIGNED NOT NULL,
  `booking_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cancellation_policies`
--

CREATE TABLE `cancellation_policies` (
  `policy_id` int(10) UNSIGNED NOT NULL,
  `policy_name` varchar(100) NOT NULL,
  `days_before` tinyint(3) UNSIGNED NOT NULL,
  `refund_percent` decimal(5,2) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cancellation_policies`
--

INSERT INTO `cancellation_policies` (`policy_id`, `policy_name`, `days_before`, `refund_percent`, `is_default`, `notes`) VALUES
(1, 'Full Refund', 7, 100.00, 0, 'Cancel 7+ days before check-in'),
(2, 'Partial Refund', 3, 50.00, 1, 'Cancel 3â€“6 days before check-in'),
(3, 'No Refund', 0, 0.00, 0, 'Cancel less than 3 days before check-in');

-- --------------------------------------------------------

--
-- Table structure for table `daily_revenue_summary`
--

CREATE TABLE `daily_revenue_summary` (
  `summary_id` int(10) UNSIGNED NOT NULL,
  `report_date` date NOT NULL,
  `total_bookings` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `total_checkins` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `total_checkouts` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `total_revenue` decimal(12,2) NOT NULL DEFAULT 0.00,
  `occupancy_rate` decimal(5,2) DEFAULT NULL,
  `generated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guests`
--

CREATE TABLE `guests` (
  `guest_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `first_name` varchar(80) NOT NULL,
  `last_name` varchar(80) NOT NULL,
  `email` varchar(191) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `id_type` varchar(50) DEFAULT NULL,
  `id_number` varchar(100) DEFAULT NULL,
  `nationality` varchar(80) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `special_requests` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_vip` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `guests`
--

INSERT INTO `guests` (`guest_id`, `user_id`, `first_name`, `last_name`, `email`, `phone`, `address`, `id_type`, `id_number`, `nationality`, `date_of_birth`, `special_requests`, `notes`, `is_vip`, `created_at`) VALUES
(1, 3, 'Francis', 'Auxtero', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-05-09 21:45:26');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_requests`
--

CREATE TABLE `maintenance_requests` (
  `request_id` int(10) UNSIGNED NOT NULL,
  `room_id` smallint(5) UNSIGNED NOT NULL,
  `reported_by` int(10) UNSIGNED DEFAULT NULL,
  `assigned_to` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `status` enum('open','in_progress','resolved','cancelled') NOT NULL DEFAULT 'open',
  `resolved_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notif_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `guest_id` int(10) UNSIGNED DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text DEFAULT NULL,
  `channel` enum('email','sms','in_app') NOT NULL DEFAULT 'in_app',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `sent_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `official_receipts`
--

CREATE TABLE `official_receipts` (
  `or_id` int(10) UNSIGNED NOT NULL,
  `payment_id` int(10) UNSIGNED NOT NULL,
  `or_number` varchar(50) NOT NULL,
  `issued_to` varchar(255) NOT NULL,
  `tin` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `issued_at` datetime NOT NULL DEFAULT current_timestamp(),
  `pdf_url` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(10) UNSIGNED NOT NULL,
  `booking_id` int(10) UNSIGNED NOT NULL,
  `processed_by` int(10) UNSIGNED DEFAULT NULL,
  `payment_method` enum('gcash','maya','paypal','bank_transfer','cash','credit_card') NOT NULL,
  `transaction_ref` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` char(3) NOT NULL DEFAULT 'PHP',
  `status` enum('pending','verified','failed','refunded') NOT NULL DEFAULT 'pending',
  `payment_type` enum('deposit','full_payment','balance','extra_charge','refund') NOT NULL,
  `screenshot_url` varchar(500) DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `booking_id`, `processed_by`, `payment_method`, `transaction_ref`, `amount`, `currency`, `status`, `payment_type`, `screenshot_url`, `verified_at`, `notes`, `created_at`) VALUES
(1, 3, 2, 'cash', NULL, 112.00, 'PHP', 'verified', 'full_payment', NULL, '2026-05-11 22:24:55', NULL, '2026-05-10 23:04:04'),
(2, 4, 2, 'cash', NULL, 2486.40, 'PHP', 'verified', 'full_payment', NULL, '2026-05-11 22:38:44', NULL, '2026-05-11 22:37:53'),
(3, 5, 2, 'cash', 'IVR-95A86194', 4480.00, 'PHP', 'verified', 'full_payment', NULL, '2026-05-11 22:49:23', NULL, '2026-05-11 22:48:55');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `permission_id` smallint(5) UNSIGNED NOT NULL,
  `permission_name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `promo_codes`
--

CREATE TABLE `promo_codes` (
  `promo_id` int(10) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `discount_type` enum('percent','fixed') NOT NULL DEFAULT 'percent',
  `discount_value` decimal(10,2) NOT NULL,
  `min_nights` tinyint(3) UNSIGNED DEFAULT 1,
  `max_uses` int(10) UNSIGNED DEFAULT NULL,
  `used_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `valid_from` date DEFAULT NULL,
  `valid_until` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `refunds`
--

CREATE TABLE `refunds` (
  `refund_id` int(10) UNSIGNED NOT NULL,
  `payment_id` int(10) UNSIGNED NOT NULL,
  `booking_id` int(10) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','processed','rejected') NOT NULL DEFAULT 'pending',
  `processed_by` int(10) UNSIGNED DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `reference_ref` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reschedule_requests`
--

CREATE TABLE `reschedule_requests` (
  `request_id` int(10) UNSIGNED NOT NULL,
  `booking_id` int(10) UNSIGNED NOT NULL,
  `requested_by` int(10) UNSIGNED DEFAULT NULL,
  `new_check_in` date NOT NULL,
  `new_check_out` date NOT NULL,
  `new_room_id` smallint(5) UNSIGNED DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `reviewed_by` int(10) UNSIGNED DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `reject_reason` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(10) UNSIGNED NOT NULL,
  `booking_id` int(10) UNSIGNED NOT NULL,
  `guest_id` int(10) UNSIGNED NOT NULL,
  `room_id` smallint(5) UNSIGNED NOT NULL,
  `overall_rating` tinyint(3) UNSIGNED NOT NULL,
  `cleanliness` tinyint(3) UNSIGNED DEFAULT NULL,
  `service` tinyint(3) UNSIGNED DEFAULT NULL,
  `value_for_money` tinyint(3) UNSIGNED DEFAULT NULL,
  `amenities_rating` tinyint(3) UNSIGNED DEFAULT NULL,
  `review_text` text DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` tinyint(3) UNSIGNED NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `description`) VALUES
(1, 'superadmin', 'Full system access'),
(2, 'admin', 'Resort owner / manager'),
(3, 'staff', 'Front desk / receptionist'),
(4, 'guest', 'Registered guest account');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` tinyint(3) UNSIGNED NOT NULL,
  `permission_id` smallint(5) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` smallint(5) UNSIGNED NOT NULL,
  `room_type_id` smallint(5) UNSIGNED NOT NULL,
  `room_number` varchar(20) NOT NULL,
  `floor_level` tinyint(4) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `room_type_id`, `room_number`, `floor_level`, `description`, `is_active`) VALUES
(1, 1, '101', 1, NULL, 1),
(2, 2, '201', 2, NULL, 1),
(3, 3, '202', 2, NULL, 1),
(4, 4, '203', 3, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `room_current_status`
--

CREATE TABLE `room_current_status` (
  `room_id` smallint(5) UNSIGNED NOT NULL,
  `status` enum('available','occupied','cleaning','dirty','maintenance','blocked') NOT NULL DEFAULT 'available',
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_current_status`
--

INSERT INTO `room_current_status` (`room_id`, `status`, `updated_at`) VALUES
(1, 'available', '2026-05-11 23:13:52'),
(2, 'occupied', '2026-05-11 22:37:53'),
(3, 'occupied', '2026-05-11 23:01:47'),
(4, 'available', '2026-05-10 23:09:56');

-- --------------------------------------------------------

--
-- Table structure for table `room_photos`
--

CREATE TABLE `room_photos` (
  `photo_id` int(10) UNSIGNED NOT NULL,
  `room_id` smallint(5) UNSIGNED DEFAULT NULL,
  `room_type_id` smallint(5) UNSIGNED DEFAULT NULL,
  `photo_url` varchar(500) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `sort_order` tinyint(3) UNSIGNED DEFAULT 0,
  `is_cover` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `room_status_log`
--

CREATE TABLE `room_status_log` (
  `log_id` int(10) UNSIGNED NOT NULL,
  `room_id` smallint(5) UNSIGNED NOT NULL,
  `status` enum('available','occupied','cleaning','dirty','maintenance','blocked') NOT NULL,
  `changed_by` int(10) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `changed_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_status_log`
--

INSERT INTO `room_status_log` (`log_id`, `room_id`, `status`, `changed_by`, `notes`, `changed_at`) VALUES
(1, 1, 'available', 2, 'Manual status update by staff via dashboard.', '2026-05-09 21:39:58'),
(2, 1, 'cleaning', 2, 'Manual status update by staff via dashboard.', '2026-05-09 21:40:32'),
(3, 1, 'maintenance', 2, 'Manual status update by staff via dashboard.', '2026-05-09 21:40:43'),
(4, 1, 'available', 2, 'Manual status update by staff via dashboard.', '2026-05-09 21:40:48'),
(5, 1, 'occupied', NULL, 'Auto-assigned via Online Booking #IVR-0D34B90A', '2026-05-10 23:04:04'),
(6, 1, 'available', 1, 'Updated via booking status change to confirmed', '2026-05-11 22:01:04'),
(7, 1, 'occupied', 2, 'Staff confirmed check-in for Ref #IVR-0D34B90A', '2026-05-11 22:01:45'),
(8, 1, 'cleaning', 2, 'Staff confirmed check-out for Ref #IVR-0D34B90A. Room set to cleaning.', '2026-05-11 22:02:38'),
(9, 1, 'available', 1, 'Updated via booking status change to confirmed', '2026-05-11 22:12:16'),
(10, 1, 'occupied', 2, 'Staff confirmed check-in for Ref #IVR-0D34B90A', '2026-05-11 22:21:36'),
(11, 1, 'cleaning', 2, 'Staff confirmed check-out for Ref #IVR-0D34B90A. Room set to cleaning.', '2026-05-11 22:23:33'),
(12, 1, 'available', 1, 'Updated via booking status change to confirmed', '2026-05-11 22:24:04'),
(13, 1, 'occupied', 2, 'Staff confirmed check-in for Ref #IVR-0D34B90A', '2026-05-11 22:24:41'),
(14, 2, 'occupied', NULL, 'Auto-assigned via Online Booking #IVR-DEA6EF8E', '2026-05-11 22:37:53'),
(15, 1, 'available', 1, 'Updated via booking status change to confirmed', '2026-05-11 22:42:04'),
(16, 1, 'occupied', 1, 'Updated via booking status change to checked_in', '2026-05-11 22:42:09'),
(17, 3, 'occupied', NULL, 'Auto-assigned via Online Booking #IVR-95A86194', '2026-05-11 22:48:55'),
(18, 1, 'occupied', 1, 'Updated via booking status change to checked_in', '2026-05-11 22:50:56'),
(19, 1, 'cleaning', 1, 'Updated via booking status change to checked_out', '2026-05-11 22:51:07'),
(20, 3, 'occupied', 2, 'Staff confirmed check-in for Ref #IVR-95A86194', '2026-05-11 23:01:47'),
(21, 1, 'available', 2, 'Manual status update by staff via dashboard.', '2026-05-11 23:13:52');

-- --------------------------------------------------------

--
-- Table structure for table `room_types`
--

CREATE TABLE `room_types` (
  `room_type_id` smallint(5) UNSIGNED NOT NULL,
  `type_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `max_occupancy` tinyint(3) UNSIGNED NOT NULL DEFAULT 2,
  `base_price` decimal(10,2) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_types`
--

INSERT INTO `room_types` (`room_type_id`, `type_name`, `description`, `max_occupancy`, `base_price`, `is_active`) VALUES
(1, 'Standard Room', 'A functional space with a queen bed, desk, and private bathroom. Suitable for solo travelers or couples.', 2, 100.00, 1),
(2, 'Deluxe Suite', 'Features a separate living area, king bed, and a pull-out sofa. Includes a kitchenette and premium city views.', 4, 2220.00, 1),
(3, 'Family Villa', 'A private three-bedroom unit with a full kitchen, dining area, and outdoor patio. Designed for large groups.', 6, 4000.00, 1),
(4, 'Penthouse', 'scdvvcgdcgscvgdsc', 6, 1000.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `room_type_amenities`
--

CREATE TABLE `room_type_amenities` (
  `room_type_id` smallint(5) UNSIGNED NOT NULL,
  `amenity_id` smallint(5) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seasonal_pricing`
--

CREATE TABLE `seasonal_pricing` (
  `pricing_id` int(10) UNSIGNED NOT NULL,
  `room_type_id` smallint(5) UNSIGNED NOT NULL,
  `label` varchar(100) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `priority` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `role_id` tinyint(3) UNSIGNED NOT NULL,
  `first_name` varchar(80) NOT NULL,
  `last_name` varchar(80) NOT NULL,
  `email` varchar(191) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `avatar_url` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `role_id`, `first_name`, `last_name`, `email`, `phone`, `password_hash`, `avatar_url`, `is_active`, `email_verified`, `created_at`, `updated_at`) VALUES
(1, 1, 'Super', 'Admin', 'admin@resort.ph', NULL, '$2y$10$4j25KBKQjmft03FAgDYGbeVL0bHqh9OTHYFPD29N0OJqgt2cXDlPW', NULL, 1, 1, '2026-05-09 19:15:41', '2026-05-09 19:48:53'),
(2, 3, 'Rudelyn Joy', 'Abanador', 'abanador@gmail.com', NULL, '$2y$10$nL8QK.wgx20MPIVS/NwAPOFxg3UdGbdiMtVWVI02KTsyYcuLA7oku', NULL, 1, 0, '2026-05-09 20:32:29', '2026-05-09 20:32:29'),
(3, 4, 'Francis', 'Auxtero', 'auxtero@gmail.com', '+63 925 4555 278', '$2y$10$PosOzt42669JOoE1v8aMv.IErxhKyCKef56mgi1luAHZsTTJP8FrC', NULL, 1, 0, '2026-05-09 21:09:25', '2026-05-09 21:09:25');

-- --------------------------------------------------------

--
-- Table structure for table `vouchers`
--

CREATE TABLE `vouchers` (
  `voucher_id` int(10) UNSIGNED NOT NULL,
  `booking_id` int(10) UNSIGNED NOT NULL,
  `voucher_code` varchar(50) NOT NULL,
  `issued_at` datetime NOT NULL DEFAULT current_timestamp(),
  `pdf_url` varchar(500) DEFAULT NULL,
  `is_valid` tinyint(1) NOT NULL DEFAULT 1,
  `verified_at` datetime DEFAULT NULL,
  `verified_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_monthly_revenue`
-- (See below for the actual view)
--
CREATE TABLE `vw_monthly_revenue` (
`year` int(4)
,`month` int(2)
,`total_bookings` bigint(21)
,`gross_revenue` decimal(32,2)
,`total_discounts` decimal(32,2)
,`total_tax` decimal(32,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_pending_payments`
-- (See below for the actual view)
--
CREATE TABLE `vw_pending_payments` (
`payment_id` int(10) unsigned
,`reference_code` varchar(20)
,`guest_name` varchar(161)
,`payment_method` enum('gcash','maya','paypal','bank_transfer','cash','credit_card')
,`transaction_ref` varchar(255)
,`amount` decimal(10,2)
,`payment_type` enum('deposit','full_payment','balance','extra_charge','refund')
,`screenshot_url` varchar(500)
,`created_at` datetime
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_room_availability`
-- (See below for the actual view)
--
CREATE TABLE `vw_room_availability` (
`room_id` smallint(5) unsigned
,`room_number` varchar(20)
,`type_name` varchar(100)
,`base_price` decimal(10,2)
,`max_occupancy` tinyint(3) unsigned
,`current_status` enum('available','occupied','cleaning','dirty','maintenance','blocked')
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_todays_checkins`
-- (See below for the actual view)
--
CREATE TABLE `vw_todays_checkins` (
`booking_id` int(10) unsigned
,`reference_code` varchar(20)
,`guest_name` varchar(161)
,`phone` varchar(20)
,`room_number` varchar(20)
,`type_name` varchar(100)
,`num_adults` tinyint(3) unsigned
,`num_children` tinyint(3) unsigned
,`check_out_date` date
,`status` enum('pending','confirmed','checked_in','checked_out','cancelled','no_show')
,`total_amount` decimal(10,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_todays_checkouts`
-- (See below for the actual view)
--
CREATE TABLE `vw_todays_checkouts` (
`booking_id` int(10) unsigned
,`reference_code` varchar(20)
,`guest_name` varchar(161)
,`room_number` varchar(20)
,`type_name` varchar(100)
,`check_in_date` date
,`status` enum('pending','confirmed','checked_in','checked_out','cancelled','no_show')
,`total_amount` decimal(10,2)
);

-- --------------------------------------------------------

--
-- Structure for view `vw_monthly_revenue`
--
DROP TABLE IF EXISTS `vw_monthly_revenue`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_monthly_revenue`  AS SELECT year(`b`.`created_at`) AS `year`, month(`b`.`created_at`) AS `month`, count(`b`.`booking_id`) AS `total_bookings`, sum(`b`.`total_amount`) AS `gross_revenue`, sum(`b`.`discount_amount`) AS `total_discounts`, sum(`b`.`tax_amount`) AS `total_tax` FROM `bookings` AS `b` WHERE `b`.`status` not in ('cancelled','no_show') GROUP BY year(`b`.`created_at`), month(`b`.`created_at`) ;

-- --------------------------------------------------------

--
-- Structure for view `vw_pending_payments`
--
DROP TABLE IF EXISTS `vw_pending_payments`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_pending_payments`  AS SELECT `p`.`payment_id` AS `payment_id`, `b`.`reference_code` AS `reference_code`, concat(`g`.`first_name`,' ',`g`.`last_name`) AS `guest_name`, `p`.`payment_method` AS `payment_method`, `p`.`transaction_ref` AS `transaction_ref`, `p`.`amount` AS `amount`, `p`.`payment_type` AS `payment_type`, `p`.`screenshot_url` AS `screenshot_url`, `p`.`created_at` AS `created_at` FROM ((`payments` `p` join `bookings` `b` on(`p`.`booking_id` = `b`.`booking_id`)) join `guests` `g` on(`b`.`guest_id` = `g`.`guest_id`)) WHERE `p`.`status` = 'pending' ORDER BY `p`.`created_at` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `vw_room_availability`
--
DROP TABLE IF EXISTS `vw_room_availability`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_room_availability`  AS SELECT `r`.`room_id` AS `room_id`, `r`.`room_number` AS `room_number`, `rt`.`type_name` AS `type_name`, `rt`.`base_price` AS `base_price`, `rt`.`max_occupancy` AS `max_occupancy`, `rcs`.`status` AS `current_status` FROM ((`rooms` `r` join `room_types` `rt` on(`r`.`room_type_id` = `rt`.`room_type_id`)) left join `room_current_status` `rcs` on(`r`.`room_id` = `rcs`.`room_id`)) WHERE `r`.`is_active` = 1 ;

-- --------------------------------------------------------

--
-- Structure for view `vw_todays_checkins`
--
DROP TABLE IF EXISTS `vw_todays_checkins`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_todays_checkins`  AS SELECT `b`.`booking_id` AS `booking_id`, `b`.`reference_code` AS `reference_code`, concat(`g`.`first_name`,' ',`g`.`last_name`) AS `guest_name`, `g`.`phone` AS `phone`, `r`.`room_number` AS `room_number`, `rt`.`type_name` AS `type_name`, `b`.`num_adults` AS `num_adults`, `b`.`num_children` AS `num_children`, `b`.`check_out_date` AS `check_out_date`, `b`.`status` AS `status`, `b`.`total_amount` AS `total_amount` FROM (((`bookings` `b` join `guests` `g` on(`b`.`guest_id` = `g`.`guest_id`)) join `rooms` `r` on(`b`.`room_id` = `r`.`room_id`)) join `room_types` `rt` on(`r`.`room_type_id` = `rt`.`room_type_id`)) WHERE `b`.`check_in_date` = curdate() AND `b`.`status` in ('confirmed','checked_in') ;

-- --------------------------------------------------------

--
-- Structure for view `vw_todays_checkouts`
--
DROP TABLE IF EXISTS `vw_todays_checkouts`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_todays_checkouts`  AS SELECT `b`.`booking_id` AS `booking_id`, `b`.`reference_code` AS `reference_code`, concat(`g`.`first_name`,' ',`g`.`last_name`) AS `guest_name`, `r`.`room_number` AS `room_number`, `rt`.`type_name` AS `type_name`, `b`.`check_in_date` AS `check_in_date`, `b`.`status` AS `status`, `b`.`total_amount` AS `total_amount` FROM (((`bookings` `b` join `guests` `g` on(`b`.`guest_id` = `g`.`guest_id`)) join `rooms` `r` on(`b`.`room_id` = `r`.`room_id`)) join `room_types` `rt` on(`r`.`room_type_id` = `rt`.`room_type_id`)) WHERE `b`.`check_out_date` = curdate() AND `b`.`status` = 'checked_in' ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `amenities`
--
ALTER TABLE `amenities`
  ADD PRIMARY KEY (`amenity_id`),
  ADD UNIQUE KEY `amenity_name` (`amenity_name`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `blackout_dates`
--
ALTER TABLE `blackout_dates`
  ADD PRIMARY KEY (`blackout_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD UNIQUE KEY `reference_code` (`reference_code`),
  ADD KEY `guest_id` (`guest_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `promo_id` (`promo_id`),
  ADD KEY `booked_by` (`booked_by`);

--
-- Indexes for table `booking_groups`
--
ALTER TABLE `booking_groups`
  ADD PRIMARY KEY (`group_id`),
  ADD UNIQUE KEY `group_reference` (`group_reference`),
  ADD KEY `lead_guest_id` (`lead_guest_id`);

--
-- Indexes for table `booking_group_items`
--
ALTER TABLE `booking_group_items`
  ADD PRIMARY KEY (`group_id`,`booking_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `cancellation_policies`
--
ALTER TABLE `cancellation_policies`
  ADD PRIMARY KEY (`policy_id`);

--
-- Indexes for table `daily_revenue_summary`
--
ALTER TABLE `daily_revenue_summary`
  ADD PRIMARY KEY (`summary_id`),
  ADD UNIQUE KEY `report_date` (`report_date`);

--
-- Indexes for table `guests`
--
ALTER TABLE `guests`
  ADD PRIMARY KEY (`guest_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `reported_by` (`reported_by`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notif_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `guest_id` (`guest_id`);

--
-- Indexes for table `official_receipts`
--
ALTER TABLE `official_receipts`
  ADD PRIMARY KEY (`or_id`),
  ADD UNIQUE KEY `or_number` (`or_number`),
  ADD KEY `payment_id` (`payment_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `processed_by` (`processed_by`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`permission_id`),
  ADD UNIQUE KEY `permission_name` (`permission_name`);

--
-- Indexes for table `promo_codes`
--
ALTER TABLE `promo_codes`
  ADD PRIMARY KEY (`promo_id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `refunds`
--
ALTER TABLE `refunds`
  ADD PRIMARY KEY (`refund_id`),
  ADD KEY `payment_id` (`payment_id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `processed_by` (`processed_by`);

--
-- Indexes for table `reschedule_requests`
--
ALTER TABLE `reschedule_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `requested_by` (`requested_by`),
  ADD KEY `new_room_id` (`new_room_id`),
  ADD KEY `reviewed_by` (`reviewed_by`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD UNIQUE KEY `booking_id` (`booking_id`),
  ADD KEY `guest_id` (`guest_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD UNIQUE KEY `room_number` (`room_number`),
  ADD KEY `room_type_id` (`room_type_id`);

--
-- Indexes for table `room_current_status`
--
ALTER TABLE `room_current_status`
  ADD PRIMARY KEY (`room_id`);

--
-- Indexes for table `room_photos`
--
ALTER TABLE `room_photos`
  ADD PRIMARY KEY (`photo_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `room_type_id` (`room_type_id`);

--
-- Indexes for table `room_status_log`
--
ALTER TABLE `room_status_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `changed_by` (`changed_by`);

--
-- Indexes for table `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`room_type_id`);

--
-- Indexes for table `room_type_amenities`
--
ALTER TABLE `room_type_amenities`
  ADD PRIMARY KEY (`room_type_id`,`amenity_id`),
  ADD KEY `amenity_id` (`amenity_id`);

--
-- Indexes for table `seasonal_pricing`
--
ALTER TABLE `seasonal_pricing`
  ADD PRIMARY KEY (`pricing_id`),
  ADD KEY `room_type_id` (`room_type_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`voucher_id`),
  ADD UNIQUE KEY `booking_id` (`booking_id`),
  ADD UNIQUE KEY `voucher_code` (`voucher_code`),
  ADD KEY `verified_by` (`verified_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `amenities`
--
ALTER TABLE `amenities`
  MODIFY `amenity_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `log_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `blackout_dates`
--
ALTER TABLE `blackout_dates`
  MODIFY `blackout_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `booking_groups`
--
ALTER TABLE `booking_groups`
  MODIFY `group_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cancellation_policies`
--
ALTER TABLE `cancellation_policies`
  MODIFY `policy_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `daily_revenue_summary`
--
ALTER TABLE `daily_revenue_summary`
  MODIFY `summary_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guests`
--
ALTER TABLE `guests`
  MODIFY `guest_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  MODIFY `request_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notif_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `official_receipts`
--
ALTER TABLE `official_receipts`
  MODIFY `or_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permission_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `promo_codes`
--
ALTER TABLE `promo_codes`
  MODIFY `promo_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `refunds`
--
ALTER TABLE `refunds`
  MODIFY `refund_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reschedule_requests`
--
ALTER TABLE `reschedule_requests`
  MODIFY `request_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `room_photos`
--
ALTER TABLE `room_photos`
  MODIFY `photo_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `room_status_log`
--
ALTER TABLE `room_status_log`
  MODIFY `log_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `room_type_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `seasonal_pricing`
--
ALTER TABLE `seasonal_pricing`
  MODIFY `pricing_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `voucher_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `blackout_dates`
--
ALTER TABLE `blackout_dates`
  ADD CONSTRAINT `blackout_dates_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `blackout_dates_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`guest_id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`),
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`promo_id`) REFERENCES `promo_codes` (`promo_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bookings_ibfk_4` FOREIGN KEY (`booked_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `booking_groups`
--
ALTER TABLE `booking_groups`
  ADD CONSTRAINT `booking_groups_ibfk_1` FOREIGN KEY (`lead_guest_id`) REFERENCES `guests` (`guest_id`);

--
-- Constraints for table `booking_group_items`
--
ALTER TABLE `booking_group_items`
  ADD CONSTRAINT `booking_group_items_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `booking_groups` (`group_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_group_items_ibfk_2` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE;

--
-- Constraints for table `guests`
--
ALTER TABLE `guests`
  ADD CONSTRAINT `guests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  ADD CONSTRAINT `maintenance_requests_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`),
  ADD CONSTRAINT `maintenance_requests_ibfk_2` FOREIGN KEY (`reported_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `maintenance_requests_ibfk_3` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`guest_id`) ON DELETE CASCADE;

--
-- Constraints for table `official_receipts`
--
ALTER TABLE `official_receipts`
  ADD CONSTRAINT `official_receipts_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`payment_id`);

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`processed_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `promo_codes`
--
ALTER TABLE `promo_codes`
  ADD CONSTRAINT `promo_codes_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `refunds`
--
ALTER TABLE `refunds`
  ADD CONSTRAINT `refunds_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`payment_id`),
  ADD CONSTRAINT `refunds_ibfk_2` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`),
  ADD CONSTRAINT `refunds_ibfk_3` FOREIGN KEY (`processed_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `reschedule_requests`
--
ALTER TABLE `reschedule_requests`
  ADD CONSTRAINT `reschedule_requests_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`),
  ADD CONSTRAINT `reschedule_requests_ibfk_2` FOREIGN KEY (`requested_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reschedule_requests_ibfk_3` FOREIGN KEY (`new_room_id`) REFERENCES `rooms` (`room_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reschedule_requests_ibfk_4` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`guest_id`),
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`);

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`) ON DELETE CASCADE;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`room_type_id`);

--
-- Constraints for table `room_current_status`
--
ALTER TABLE `room_current_status`
  ADD CONSTRAINT `room_current_status_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE;

--
-- Constraints for table `room_photos`
--
ALTER TABLE `room_photos`
  ADD CONSTRAINT `room_photos_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `room_photos_ibfk_2` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`room_type_id`) ON DELETE CASCADE;

--
-- Constraints for table `room_status_log`
--
ALTER TABLE `room_status_log`
  ADD CONSTRAINT `room_status_log_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`),
  ADD CONSTRAINT `room_status_log_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `room_type_amenities`
--
ALTER TABLE `room_type_amenities`
  ADD CONSTRAINT `room_type_amenities_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`room_type_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `room_type_amenities_ibfk_2` FOREIGN KEY (`amenity_id`) REFERENCES `amenities` (`amenity_id`) ON DELETE CASCADE;

--
-- Constraints for table `seasonal_pricing`
--
ALTER TABLE `seasonal_pricing`
  ADD CONSTRAINT `seasonal_pricing_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`room_type_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `seasonal_pricing_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);

--
-- Constraints for table `vouchers`
--
ALTER TABLE `vouchers`
  ADD CONSTRAINT `vouchers_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`),
  ADD CONSTRAINT `vouchers_ibfk_2` FOREIGN KEY (`verified_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
