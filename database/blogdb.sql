-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 31-01-2026 a las 16:09:48
-- Versión del servidor: 11.8.3-MariaDB-log
-- Versión de PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `u939521020_blogdb`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `app_logs`
--

DROP TABLE IF EXISTS `app_logs`;
CREATE TABLE `app_logs` (
  `id` int(11) NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `level` varchar(20) NOT NULL,
  `event` varchar(50) NOT NULL,
  `details` text DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `app_logs`
--

INSERT INTO `app_logs` (`id`, `ip`, `user_id`, `level`, `event`, `details`, `user_agent`, `created_at`) VALUES
(1, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', NULL, 'error', 'post_view_failed', '{\"error\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'p.slug\' in \'field list\'\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 16:38:21'),
(2, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', NULL, 'error', 'post_view_failed', '{\"error\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'p.slug\' in \'field list\'\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 16:38:26'),
(3, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', NULL, 'error', 'post_view_failed', '{\"error\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'p.slug\' in \'field list\'\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 16:38:32'),
(4, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', NULL, 'error', 'post_view_failed', '{\"error\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'p.slug\' in \'field list\'\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 16:40:56'),
(5, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', NULL, 'error', 'post_view_failed', '{\"error\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'p.slug\' in \'field list\'\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 16:41:00'),
(6, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', NULL, 'error', 'post_view_failed', '{\"error\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'p.slug\' in \'field list\'\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 16:42:33'),
(7, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', NULL, 'error', 'post_view_failed', '{\"error\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'p.slug\' in \'field list\'\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 16:47:15'),
(8, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', NULL, 'error', 'post_view_failed', '{\"error\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'p.slug\' in \'field list\'\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 16:47:18'),
(9, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', NULL, 'error', 'post_view_failed', '{\"error\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'p.slug\' in \'field list\'\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 16:47:26'),
(10, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', NULL, 'error', 'post_view_failed', '{\"error\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'p.slug\' in \'field list\'\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 16:49:59'),
(11, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', NULL, 'error', 'post_view_failed', '{\"error\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'p.slug\' in \'field list\'\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 16:50:03'),
(12, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', NULL, 'error', 'post_view_failed', '{\"error\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'p.slug\' in \'field list\'\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 16:50:37'),
(13, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', NULL, 'error', 'post_view_failed', '{\"error\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'p.slug\' in \'field list\'\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 16:51:00'),
(14, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', NULL, 'error', 'post_view_failed', '{\"error\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'p.slug\' in \'field list\'\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 16:58:47'),
(15, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', NULL, 'error', 'search_failed', '{\"q\":\"skatepark\",\"error\":\"SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'\\\\\'\' at line 3\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 16:58:54'),
(16, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', NULL, 'error', 'search_failed', '{\"q\":\"fifilosofia\",\"error\":\"SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'\\\\\'\' at line 3\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 16:59:07'),
(17, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', NULL, 'error', 'search_failed', '{\"q\":\"fifilosofilosofiafia\",\"error\":\"SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'\\\\\'\' at line 3\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 16:59:14'),
(18, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', NULL, 'error', 'search_failed', '{\"q\":\"filosofiaosofiafia\",\"error\":\"SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'\\\\\'\' at line 3\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 16:59:23'),
(19, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', NULL, 'error', 'search_failed', '{\"q\":\"filosofiaosofiafia\",\"error\":\"SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'\\\\\'\' at line 3\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 16:59:28'),
(20, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', NULL, 'error', 'search_failed', '{\"q\":\"filosoasdfasdfasdafasdffiaosofiafia\",\"error\":\"SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'\\\\\'\' at line 3\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 16:59:31'),
(21, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', NULL, 'error', 'search_failed', '{\"q\":\"filosoasdfasdfasdafasdffiaosofiafia\",\"error\":\"SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'\\\\\'\' at line 3\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 16:59:34'),
(22, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', NULL, 'error', 'search_failed', '{\"q\":\"filosofia\",\"error\":\"SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'\\\\\'\' at line 3\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 16:59:41'),
(23, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', NULL, 'error', 'search_failed', '{\"q\":\"etica\",\"error\":\"SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'\\\\\'\' at line 3\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 16:59:49'),
(24, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', NULL, 'error', 'search_failed', '{\"q\":\"surf\",\"error\":\"SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'\\\\\'\' at line 3\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 16:59:53'),
(25, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', NULL, 'error', 'search_failed', '{\"q\":\"Surf\",\"error\":\"SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'\\\\\'\' at line 3\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 17:00:01'),
(26, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', NULL, 'error', 'post_view_failed', '{\"error\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'p.slug\' in \'field list\'\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 17:00:10'),
(27, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', NULL, 'error', 'post_view_failed', '{\"error\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'p.slug\' in \'field list\'\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 17:00:19'),
(28, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', NULL, 'error', 'post_view_failed', '{\"error\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'p.slug\' in \'field list\'\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 17:03:23'),
(29, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', NULL, 'error', 'post_view_failed', '{\"error\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'p.slug\' in \'field list\'\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 17:03:25'),
(30, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', NULL, 'error', 'post_view_failed', '{\"error\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'p.slug\' in \'field list\'\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 17:03:26'),
(31, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', NULL, 'error', 'post_view_failed', '{\"error\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'p.slug\' in \'field list\'\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 17:03:27'),
(32, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', NULL, 'error', 'post_view_failed', '{\"error\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'p.slug\' in \'field list\'\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 17:07:56'),
(33, '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', NULL, 'error', 'post_view_failed', '{\"error\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'p.slug\' in \'field list\'\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 17:08:44'),
(34, '::1', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-27 17:32:56'),
(35, '::1', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-28 21:24:06'),
(36, '::1', 1, 'error', 'search_failed', '{\"q\":\"surf\",\"error\":\"SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'\\\\\'\' at line 3\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-28 21:41:24'),
(37, '::1', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-29 15:09:30'),
(38, '::1', 1, 'error', 'search_failed', '{\"q\":\"asdf\",\"error\":\"SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'\\\\\'\' at line 3\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-29 15:15:38'),
(39, '::1', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-29 21:35:12'),
(40, '::1', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-29 21:49:23'),
(41, '::1', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-29 23:02:06'),
(42, '::1', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-02 15:31:56'),
(43, '::1', NULL, 'error', 'search_failed', '{\"q\":\"filosofia\",\"error\":\"SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'\\\\\')\' at line 3\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-04 18:28:45'),
(44, '::1', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-05 15:17:09'),
(45, '::1', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-06 15:54:39'),
(46, '::1', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-10 22:01:53'),
(47, '::1', NULL, 'error', 'search_failed', '{\"q\":\"biologia\",\"error\":\"SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'\\\\\')\' at line 3\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-10 22:07:47'),
(48, '::1', NULL, 'error', 'search_failed', '{\"q\":\"filo\",\"error\":\"SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'\\\\\')\' at line 3\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-26 00:39:51'),
(49, '::1', NULL, 'error', 'search_failed', '{\"q\":\"filo\",\"error\":\"SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'\\\\\')\' at line 3\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-26 00:40:54'),
(50, '::1', NULL, 'error', 'search_failed', '{\"q\":\"filo\",\"error\":\"SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'\\\\\')\' at line 3\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-26 00:40:56'),
(51, '172.18.0.1', NULL, 'error', 'search_failed', '{\"q\":\"po\",\"error\":\"SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'\\\\\')\' at line 3\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-09 21:16:49'),
(52, '172.18.0.1', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-09 21:32:54'),
(53, '172.18.0.1', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-19 16:56:20'),
(54, '172.18.0.1', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-04 19:08:59'),
(55, '172.18.0.1', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-04 21:46:54'),
(56, '172.18.0.1', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-04 22:03:27'),
(57, '172.18.0.1', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-04 22:05:07'),
(58, '172.18.0.1', 1, 'error', 'page_create_failed', '{\"error\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'id_usuario\' in \'field list\'\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-04 22:24:29'),
(59, '172.18.0.1', 1, 'error', 'page_create_failed', '{\"error\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'id_usuario\' in \'field list\'\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-04 22:25:50'),
(60, '172.18.0.1', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-04 22:43:30'),
(61, '172.18.0.1', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-05 09:46:10'),
(62, '172.18.0.1', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-05 09:48:14'),
(63, '172.18.0.1', 1, 'error', 'search_failed', '{\"q\":\"platon\",\"error\":\"SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'\\\\\')\' at line 3\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-05 10:15:46'),
(64, '172.18.0.1', 1, 'error', 'search_failed', '{\"q\":\"inmunitaria\",\"error\":\"SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'\\\\\')\' at line 3\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-05 10:15:53'),
(65, '172.18.0.1', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-05 15:15:14'),
(66, '172.18.0.1', 1, 'error', 'search_failed', '{\"q\":\"surf\",\"error\":\"SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'\\\\\')\' at line 3\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-05 15:54:48'),
(67, '172.18.0.1', NULL, 'security', 'login_failed', '{\"username\":\"AnaLopezS1963\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-05 16:50:41'),
(68, '172.18.0.1', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-05 16:50:51'),
(69, '172.18.0.1', 1, 'error', 'search_failed', '{\"q\":\"ana\",\"error\":\"SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'\\\\\')\' at line 3\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-05 17:28:10'),
(70, '172.18.0.1', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-05 21:47:52'),
(71, '172.18.0.1', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-05 22:57:01'),
(72, '172.18.0.1', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-05 23:24:02'),
(73, '172.18.0.1', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-05 23:27:04'),
(74, '172.18.0.1', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-06 00:10:51'),
(75, '172.18.0.1', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-06 00:32:58'),
(76, '81.9.192.200', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.2 Safari/605.1.15', '2026-01-06 08:32:14'),
(77, '81.9.192.200', NULL, 'security', 'login_failed', '{\"username\":\"AnaLopezS1963\"}', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '2026-01-06 10:09:36'),
(78, '81.9.192.200', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '2026-01-06 10:09:51'),
(79, '81.9.192.200', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-06 12:49:29'),
(80, '81.9.192.200', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-06 12:57:03'),
(81, '31.221.236.82', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-26 18:45:44'),
(82, '83.165.112.227', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 22:44:11'),
(83, '83.165.112.227', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 23:01:58'),
(84, '83.165.112.227', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', '2026-01-27 23:10:58'),
(85, '83.165.112.227', 1, 'error', 'post_create_failed', '{\"error\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'etiquetas\' in \'INSERT INTO\'\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 23:50:33'),
(86, '83.165.112.227', 1, 'error', 'post_create_failed', '{\"error\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'etiquetas\' in \'INSERT INTO\'\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 23:58:53'),
(87, '83.165.112.227', 1, 'error', 'post_create_failed', '{\"error\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'etiquetas\' in \'INSERT INTO\'\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 00:01:33'),
(88, '83.165.112.227', 1, 'error', 'post_create_failed', '{\"error\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'etiquetas\' in \'INSERT INTO\'\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 00:02:14'),
(89, '83.165.112.227', 1, 'error', 'post_create_failed', '{\"error\":\"SQLSTATE[21S01]: Insert value list does not match column list: 1136 Column count doesn\'t match value count at row 1\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 00:10:25'),
(90, '81.9.192.200', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.2 Safari/605.1.15', '2026-01-28 10:01:47'),
(91, '81.9.192.200', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.4.1 Safari/605.1.15', '2026-01-28 10:50:08'),
(92, '81.9.192.200', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.4.1 Safari/605.1.15', '2026-01-28 10:53:13'),
(93, '81.9.192.200', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.4.1 Safari/605.1.15', '2026-01-28 10:54:59'),
(94, '81.9.192.200', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.4.1 Safari/605.1.15', '2026-01-28 10:55:29'),
(95, '83.165.112.227', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 22:47:33'),
(96, '81.9.192.200', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.2 Safari/605.1.15', '2026-01-29 13:07:49'),
(97, '81.9.192.200', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.2 Safari/605.1.15', '2026-01-30 09:35:11'),
(98, '83.165.112.227', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-30 20:02:25'),
(99, '83.165.112.227', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', '2026-01-30 20:04:12'),
(100, '83.165.112.227', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-31 12:04:11'),
(101, '83.165.112.227', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-31 14:47:12'),
(102, '83.165.112.227', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', '2026-01-31 14:48:21'),
(103, '83.165.112.227', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', '2026-01-31 15:29:08'),
(104, '83.165.112.227', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-31 15:38:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nombre_categoria` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre_categoria`, `slug`) VALUES
(1, 'Antropología Biológica', 'antropologia-biologica'),
(2, 'Antropología Social y Cultural', 'antropologia-social-y-cultural'),
(3, 'Arqueología', 'arqueologia'),
(4, 'Prehistoria', 'prehistoria'),
(5, 'Evolución Humana', 'evolucion-humana'),
(6, 'Historia', 'historia');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `etiquetas`
--

DROP TABLE IF EXISTS `etiquetas`;
CREATE TABLE `etiquetas` (
  `id_etiqueta` int(11) NOT NULL,
  `nombre_etiqueta` varchar(191) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `etiquetas`
--

INSERT INTO `etiquetas` (`id_etiqueta`, `nombre_etiqueta`) VALUES
(1, 'actividad'),
(2, 'interactivo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paginas`
--

DROP TABLE IF EXISTS `paginas`;
CREATE TABLE `paginas` (
  `id_pagina` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `orden` int(11) DEFAULT 0,
  `contenido` longtext NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `paginas`
--

INSERT INTO `paginas` (`id_pagina`, `titulo`, `slug`, `orden`, `contenido`, `fecha_creacion`, `actualizado_en`) VALUES
(1, 'Historia de la Filosofía', 'historia-de-la-filosofia', 0, '<p data-path-to-node=\"2\"><strong data-path-to-node=\"2\" data-index-in-node=\"0\">Breve Historia de la Filosof&iacute;a Occidental</strong></p>\r\n<p data-path-to-node=\"2\">&nbsp;</p>\r\n<p style=\"text-align: justify;\" data-path-to-node=\"3\">La historia del pensamiento occidental nace en Grecia (s. VI a.C.) con el paso del <strong data-path-to-node=\"3\" data-index-in-node=\"83\">mito al logos</strong>: el intento de explicar la naturaleza (<em data-path-to-node=\"3\" data-index-in-node=\"136\">physis</em>) mediante la raz&oacute;n y no mediante dioses. Los presocr&aacute;ticos buscaron el <em data-path-to-node=\"3\" data-index-in-node=\"214\">arch&eacute;</em> o principio material de todo. Con <strong data-path-to-node=\"3\" data-index-in-node=\"254\">S&oacute;crates</strong>, el foco gir&oacute; hacia el ser humano y la &eacute;tica, m&eacute;todo que perfeccion&oacute; su disc&iacute;pulo <strong data-path-to-node=\"3\" data-index-in-node=\"345\">Plat&oacute;n</strong>, quien dividi&oacute; la realidad en dos mundos: el sensible (material) y el de las Ideas (la verdad eterna). <strong data-path-to-node=\"3\" data-index-in-node=\"455\">Arist&oacute;teles</strong>, m&aacute;s emp&iacute;rico, baj&oacute; esas ideas a la tierra, sistematizando la l&oacute;gica, la biolog&iacute;a y la &eacute;tica.</p>\r\n<p style=\"text-align: justify;\" data-path-to-node=\"3\">&nbsp;</p>\r\n<p style=\"text-align: justify;\" data-path-to-node=\"4\">Tras el declive de la polis, el <strong data-path-to-node=\"4\" data-index-in-node=\"32\">Helenismo</strong> (estoicos, epic&uacute;reos) se centr&oacute; en la \"vida buena\" y la ataraxia. La <strong data-path-to-node=\"4\" data-index-in-node=\"111\">Edad Media</strong> supuso un largo di&aacute;logo entre Fe y Raz&oacute;n. San Agust&iacute;n cristianiz&oacute; a Plat&oacute;n, mientras que siglos despu&eacute;s, Santo Tom&aacute;s de Aquino recuper&oacute; a Arist&oacute;teles para construir la gran escol&aacute;stica: la raz&oacute;n como pre&aacute;mbulo de la fe.</p>\r\n<p style=\"text-align: justify;\" data-path-to-node=\"4\">&nbsp;</p>\r\n<p style=\"text-align: justify;\" data-path-to-node=\"5\">El <strong data-path-to-node=\"5\" data-index-in-node=\"3\">Renacimiento</strong> trajo el humanismo, pero fue <strong data-path-to-node=\"5\" data-index-in-node=\"45\">Descartes</strong> quien inaugur&oacute; la <strong data-path-to-node=\"5\" data-index-in-node=\"73\">Modernidad</strong> en el siglo XVII. Su <em data-path-to-node=\"5\" data-index-in-node=\"105\">\"Pienso, luego existo\"</em> coloc&oacute; al sujeto y a la raz&oacute;n matem&aacute;tica en el centro (Racionalismo). En respuesta, el <strong data-path-to-node=\"5\" data-index-in-node=\"215\">Empirismo</strong> brit&aacute;nico (Hume, Locke) defendi&oacute; que no hay ideas innatas y que todo conocimiento nace de los sentidos. <strong data-path-to-node=\"5\" data-index-in-node=\"329\">Kant</strong>, en la Ilustraci&oacute;n, sintetiz&oacute; ambas corrientes analizando los l&iacute;mites de lo que podemos conocer: no vemos el mundo como es, sino como somos.</p>\r\n<p style=\"text-align: justify;\" data-path-to-node=\"5\">&nbsp;</p>\r\n<p style=\"text-align: justify;\" data-path-to-node=\"6\">El siglo XIX reaccion&oacute; a Kant con el Idealismo absoluto de <strong data-path-to-node=\"6\" data-index-in-node=\"59\">Hegel</strong> (la historia como progreso del Esp&iacute;ritu), que fue r&aacute;pidamente contestado por los \"maestros de la sospecha\": <strong data-path-to-node=\"6\" data-index-in-node=\"173\">Marx</strong> (la historia es lucha de clases), <strong data-path-to-node=\"6\" data-index-in-node=\"212\">Nietzsche</strong> (la moral es resentimiento, Dios ha muerto) y Freud (el yo no manda en su propia casa).</p>\r\n<p style=\"text-align: justify;\" data-path-to-node=\"6\">&nbsp;</p>\r\n<p style=\"text-align: justify;\" data-path-to-node=\"7\">El siglo XX se dividi&oacute; en dos grandes bloques: la <strong data-path-to-node=\"7\" data-index-in-node=\"50\">filosof&iacute;a anal&iacute;tica</strong> (anglosajona), obsesionada con la l&oacute;gica y el lenguaje (Wittgenstein); y la <strong data-path-to-node=\"7\" data-index-in-node=\"146\">continental</strong>, centrada en la existencia humana, la libertad y el poder, abarcando desde la fenomenolog&iacute;a y el existencialismo de <strong data-path-to-node=\"7\" data-index-in-node=\"274\">Sartre</strong> y <strong data-path-to-node=\"7\" data-index-in-node=\"283\">Heidegger</strong>, hasta la hermen&eacute;utica y la Posmodernidad, que decret&oacute; el fin de las verdades absolutas.</p>', '2026-01-04 22:45:17', '2026-01-05 16:51:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagina_posts`
--

DROP TABLE IF EXISTS `pagina_posts`;
CREATE TABLE `pagina_posts` (
  `id` int(11) NOT NULL,
  `id_pagina` int(11) NOT NULL,
  `id_post` int(11) NOT NULL,
  `orden` int(11) DEFAULT 0,
  `tipo_visualizacion` enum('embebido','card') DEFAULT 'card',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `posts`
--

DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
  `id_post` int(11) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `contenido` text NOT NULL,
  `imagen_destacada_url` varchar(255) DEFAULT NULL,
  `fecha_publicacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_usuario` int(11) DEFAULT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `actualizado_en` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `posts`
--

INSERT INTO `posts` (`id_post`, `slug`, `titulo`, `contenido`, `imagen_destacada_url`, `fecha_publicacion`, `id_usuario`, `id_categoria`, `actualizado_en`) VALUES
(1, 'respuesta-inmunitaria-y-sus-costes:-un-enfoque-desde-la-ecología-del-comportamiento', 'Respuesta inmunitaria y sus costes: un enfoque desde la ecología del comportamiento', 'El sistema inmunitario es el conjunto de células, teijdos y moléculas que nos defienden de los patógenos. Su función es crucial para la supervivencia, pero mantenerlo y activarlo tiene costes energéticos y nutricionales. Hoy en día sabemos que la activación inmunitaria no es un proceso que ocurra de forma aislada, sino que interacciona con otros procesos fisiológicos como el crecimiento, la reproducción o el mantenimiento somático. Para optimizar la eficacia biológica, los organismos deben distribuir los recursos limitados entre estos procesos que compiten entre sí. Este reparto de recursos se conoce como trade-off y la ecología del comportamiento es el marco teórico que lo estudia...', 'https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEhLgZ9L0M5i-963_T5w727d2W-c_bC9t_pG_aH8W8s3Q/w400-h266/inmune.jpg', '2024-07-12 19:00:00', 1, 1, '2025-08-27 17:13:15'),
(2, 'post-prueba-de-surf', 'Post Prueba de Surf', 'Esto es un tipo de post nuevo que vamos a intentar subir a la página web nueva para comprobar cómo se escribe cómo se plasma dentro de lo que es la arquitectura habitual de la web que estoy diseñando para ver cómo lo plasma. \r\n\r\nDe alguna forma el contenido de la web para nada va a ser lo que estoy narrando ni lo que estoy titulando en este post pero es sencillamente una muestra de un texto de dos párrafos con una imagen asociada para poder juzgar como simplemente que corregir y que está bien en el diseño. \r\n\r\nEscrito desde as veigas a día 27 de julio de 2025 por el petardo de la familia.', 'uploads/1753650574_smoothed_image.jpg', '2025-07-27 21:09:34', 1, 5, '2025-08-27 17:13:15'),
(3, 'post-de-historia', 'Post de Historia', '<div id=\"post-body-6139889512538066483\" class=\"post-body entry-content\">\r\n<div style=\"text-align: center;\">\r\n<div style=\"text-align: center;\">\r\n<h3 class=\"post-title entry-title\" style=\"text-align: justify;\">HISTORIA DA FILOSOF&Iacute;A</h3>\r\n<div id=\"post-body-6139889512538066483\" class=\"post-body entry-content\">\r\n<div style=\"text-align: justify;\">&nbsp;</div>\r\n<div style=\"text-align: justify;\">&nbsp;PARA FACER UNHA CORRECTA<strong>&nbsp;COMPOSICI&Oacute;N FILOS&Oacute;FICA&nbsp;</strong>-como a pedida nos exames de selectividade- &Eacute; IMPRESCINDIBLE DOMINAR A T&Eacute;CNICA DOS COMENTARIOS DE TEXTO.&nbsp;</div>\r\n<br><br>\r\n<div class=\"separator\" style=\"text-align: justify;\"><a href=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEhIVjBfo7TPddSxu1lHGCSlf9cRzlBWmN2HcnZVXH3bB5PiTNAVXTlnGdeGPTzvqTLAWhNBa4jTvUSyUD8SU4PQYcuiXs4tzgOPhSYQv9rK-EFTP72cLGxCbxTdp_dwkN4RhjpJXw1P2pcm/s1600/Gui%25CC%2581a+comentario+texto-imaxe.png\"><img src=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEhIVjBfo7TPddSxu1lHGCSlf9cRzlBWmN2HcnZVXH3bB5PiTNAVXTlnGdeGPTzvqTLAWhNBa4jTvUSyUD8SU4PQYcuiXs4tzgOPhSYQv9rK-EFTP72cLGxCbxTdp_dwkN4RhjpJXw1P2pcm/s400/Gui%25CC%2581a+comentario+texto-imaxe.png\" width=\"400\" height=\"52\" border=\"0\"></a></div>\r\n<br>\r\n<div style=\"text-align: justify;\">\r\n<div style=\"text-align: justify;\">Neste enlace atopar&aacute;s a axuda necesaria as&iacute; como un varios comentarios que podes utilizar como modelo:&nbsp;</div>\r\n<div style=\"text-align: justify;\"><strong><a href=\"http://filex.es/index.php/aula-de-filosofia/comentario-texto/78-guia-para-el-comentario-de-texto-filosofico\" target=\"_blank\" rel=\"noopener\">Gu&iacute;a para o comentario de texto filos&oacute;fico</a></strong></div>\r\n<div style=\"text-align: justify;\"><br>LECTURA. <strong>DI&Oacute;GENES LAERCIO</strong>:&nbsp;<em>VIDAS, OPINIONES Y SENTENCIAS DE LOS FIL&Oacute;SOFOS M&Aacute;S ILUSTRES</em></div>\r\n<br>\r\n<div style=\"text-align: justify;\">\r\n<div class=\"separator\"><a href=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEjgZp7Y2glC9xBTk11cJg9ZnydzACDHBCB0W4DTOkMTrTSB06CMDoUmectcPJW7-lJMvJkwrlVSGUVZkgm-P_piLhESH_o-keZjxtzgO-oWOMGs2axIFpWFYZ-aaERtBFtwE7psa64i9Xi5/s1600/dio%CC%81genes+laercio.png\"><img src=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEjgZp7Y2glC9xBTk11cJg9ZnydzACDHBCB0W4DTOkMTrTSB06CMDoUmectcPJW7-lJMvJkwrlVSGUVZkgm-P_piLhESH_o-keZjxtzgO-oWOMGs2axIFpWFYZ-aaERtBFtwE7psa64i9Xi5/s1600/dio%CC%81genes+laercio.png\" width=\"320\" height=\"230\" border=\"0\"></a></div>\r\n<div class=\"separator\"><a href=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEg4TOZEduT_ZKOH2RaYd0emupcIc-C1h-IDzwQwhtLjjiVHrR7OSsZ-yUfayuJlvWQtTke0doj48wiEcjX6SGtRGyaXHKDvlpjkTpepQ8YWYFClbQ9VwWpQYM-u5l3NTcbBzl4bbSep9BCD/s1600/Wiki-+dio%CC%81genes+laercio.png\"><img src=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEg4TOZEduT_ZKOH2RaYd0emupcIc-C1h-IDzwQwhtLjjiVHrR7OSsZ-yUfayuJlvWQtTke0doj48wiEcjX6SGtRGyaXHKDvlpjkTpepQ8YWYFClbQ9VwWpQYM-u5l3NTcbBzl4bbSep9BCD/s1600/Wiki-+dio%CC%81genes+laercio.png\" width=\"400\" height=\"180\" border=\"0\"></a></div>\r\n</div>\r\n<br><a href=\"http://www.nueva-acropolis.es/filiales/libros/Diogenes_Laercio-Vida_de_los_filosofos_mas_ilustres.pdf\" target=\"_blank\" rel=\"noopener\"><span class=\"Apple-style-span\">DI&Oacute;GENES LAERCIO: Vida de los fil&oacute;sofos m&aacute;s ilustres</span></a><span class=\"Apple-style-span\"><strong><br></strong></span><br>\r\n<p style=\"text-align: justify;\"><span class=\"Apple-style-span\"><strong>ORIXE HIST&Oacute;RICA DA FILOSOF&Iacute;A: GRECIA NO S&Eacute;CULO VI</strong></span></p>\r\n<br><a href=\"http://es.calameo.com/read/002570365b3d8f4070b5d\" target=\"_blank\" rel=\"noopener\"><span class=\"Apple-style-span\">Podes lelo aqu&iacute;</span></a><br><span class=\"Apple-style-span\"><br></span><br>\r\n<p style=\"text-align: justify;\"><span class=\"Apple-style-span\"><strong>Cronolox&iacute;a:&nbsp;<a href=\"http://es.calameo.com/read/002570365c19473a40b09\" target=\"_blank\" rel=\"noopener\">Da civilizaci&oacute;n minoica &aacute; ca&iacute;da do Imperio Romano</a></strong></span></p>\r\n<p style=\"text-align: justify;\"><span class=\"Apple-style-span\"><strong>FILOSOF&Iacute;A PRESOCR&Aacute;TICA.&nbsp;&nbsp;</strong></span></p>\r\n<div style=\"text-align: justify;\">Un v&iacute;deo de Jes&uacute;s Palomar sobre Her&aacute;clito \"o escuro\":</div>\r\n<div style=\"text-align: justify;\">&nbsp;</div>\r\n<div class=\"separator\" style=\"text-align: justify;\"><iframe class=\"YOUTUBE-iframe-video\" src=\"https://www.youtube.com/embed/6RbZKPRUCBY?feature=player_embedded\" width=\"320\" height=\"266\" frameborder=\"0\" allowfullscreen=\"allowfullscreen\" data-thumbnail-src=\"https://i.ytimg.com/vi/6RbZKPRUCBY/0.jpg\"></iframe></div>\r\n<span class=\"Apple-style-span\"><strong><br></strong><strong>Resumo en power-point:&nbsp;<a href=\"http://es.calameo.com/read/002570365f1e31edeee53\" target=\"_blank\" rel=\"noopener\">O NACEMENTO DA FILOSOF&Iacute;A EN OCCIDENTE</a></strong></span><br><span class=\"Apple-style-span\"><br></span><span class=\"Apple-style-span\">Tam&eacute;n pode ser l&uacute;dico; aqu&iacute; tes \"O Rap dos Presocr&aacute;ticos\":</span><br>\r\n<div class=\"separator\" style=\"text-align: justify;\">&nbsp;</div>\r\n<iframe class=\"YOUTUBE-iframe-video\" src=\"https://www.youtube.com/embed/fPqkwtRXdYY?feature=player_embedded\" width=\"320\" height=\"266\" frameborder=\"0\" allowfullscreen=\"allowfullscreen\" data-thumbnail-src=\"https://i.ytimg.com/vi/fPqkwtRXdYY/0.jpg\"></iframe><br><br>\r\n<p style=\"text-align: justify;\"><span class=\"Apple-style-span\"><strong>A FILOSOF&Iacute;A EN ATENAS.&nbsp;</strong></span></p>\r\n<br><span class=\"Apple-style-span\">Unha perla<strong>:&nbsp;<a href=\"http://pedroolalla.com/images/stories/pdfs/Atenas%20la%20ciudad%20de%20los%20filosofos.pdf\" target=\"_blank\" rel=\"noopener\">Atenas, la ciudad de fil&oacute;sofos (Pedro Olalla)</a>. Un percorrido pola Atenas actual evocando os tempos dos fil&oacute;sofos antigos e a filosof&iacute;a como&nbsp;actitude.</strong></span><br><span class=\"Apple-style-span\"><strong><br></strong><strong>Resumo en powerpoint: &nbsp;<a href=\"http://es.calameo.com/read/00257036537c124ad3350\" target=\"_blank\" rel=\"noopener\">SOFISTAS E S&Oacute;CRATES</a></strong></span><br><br>\r\n<p style=\"text-align: justify;\"><span class=\"Apple-style-span\"><strong>PLAT&Oacute;N</strong></span></p>\r\n<br><span class=\"Apple-style-span\"><strong><br></strong><a href=\"https://youtu.be/zM9zD0p1JJM\" target=\"_blank\" rel=\"noopener\">Plat&oacute;n en \"La aventura del pensamiento\"</a><strong>&nbsp;</strong>(v&iacute;deo conducido por Fernando Savater)</span><br><span class=\"Apple-style-span\"><strong><br></strong>LECTURA. PLAT&Oacute;N: \"APOLOX&Iacute;A DE S&Oacute;CRATES\"</span><br><span class=\"Apple-style-span\">&nbsp;(<a href=\"http://www.nueva-acropolis.es/filiales/libros/Platon-Apologia_de_Socrates.pdf\" target=\"_blank\" rel=\"noopener\">Plat&oacute;n: Apolox&iacute;a de S&oacute;crates</a>)</span><br><span class=\"Apple-style-span\"><br></span><span class=\"Apple-style-span\"><strong><em>Apolox&iacute;a de S&oacute;crates:</em>&nbsp;<a href=\"https://drive.google.com/file/d/0B7E8SC_Y7Me2Sm43VE1kYlNUZ2s/edit?usp=sharing\">Cuestionario</a></strong></span>&nbsp;<br><br>\r\n<div style=\"text-align: justify;\">\r\n<div class=\"separator\">&nbsp;</div>\r\n<div class=\"separator\"><iframe class=\"YOUTUBE-iframe-video\" src=\"https://www.youtube.com/embed/TT1_UWbm9sc?feature=player_embedded\" width=\"320\" height=\"266\" frameborder=\"0\" allowfullscreen=\"allowfullscreen\" data-thumbnail-src=\"https://i.ytimg.com/vi/TT1_UWbm9sc/0.jpg\"></iframe></div>\r\n<div class=\"separator\">&nbsp;</div>\r\n<h3><span class=\"Apple-style-span\"><a href=\"http://www.calameo.com/read/00257036502fbbd91a1c6\" target=\"_blank\" rel=\"noopener\">Plat&oacute;n</a>&nbsp;&nbsp;</span></h3>\r\n<div class=\"separator\"><a href=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEhC96o2XyMKlcsh4ZNIqbyVcfDtl2FklWFd_dWXLpDFF6iWgbKYR4iEIAz1bcwpMeaaxMqWtdgypNmczg_glUfmXpWvMPgcifQYYB5oWG2bc68X1k380UKDwRLjKmsWTka1ceq2uxZJoMKn/s1600/Plato%CC%81n.png\"><img src=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEhC96o2XyMKlcsh4ZNIqbyVcfDtl2FklWFd_dWXLpDFF6iWgbKYR4iEIAz1bcwpMeaaxMqWtdgypNmczg_glUfmXpWvMPgcifQYYB5oWG2bc68X1k380UKDwRLjKmsWTka1ceq2uxZJoMKn/s1600/Plato%CC%81n.png\" width=\"320\" height=\"244\" border=\"0\"></a></div>\r\n<div class=\"separator\">&nbsp;</div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\"><strong><a href=\"http://www.acropolis.org/es/recursos/fondo-documental-de-jorge-angel-livraga?id=757:la-educacion-segun-platon\">A educaci&oacute;n segundo Plat&oacute;n</a>&nbsp;(Jorge &Aacute;ngel Livraga)</strong></span></div>\r\n<br><span class=\"Apple-style-span\">&nbsp;LECTURA. PLAT&Oacute;N:&nbsp;<a href=\"http://www.nueva-acropolis.es/filiales/libros/Platon-La_Republica.pdf\" target=\"_blank\" rel=\"noopener\">\"REP&Uacute;BLICA\"</a></span><br><span class=\"Apple-style-span\"><br></span><a href=\"http://boj.pntic.mec.es/jgomez46/documentos/hfia/texto-caverna.pdf\" target=\"_blank\" rel=\"noopener\"><span class=\"Apple-style-span\">Plat&oacute;n: O mito da caverna (Libro VII da \"Rep&uacute;blica\")</span></a><br><br>\r\n<div class=\"separator\"><a href=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEjOVnA7mFDrmUpwywCBd0h_viy7dnQoAU2-KQgYGRpYg-ccHVvUzoX9ET4rvLuTMrP16uBHAO447G5lav6pwo32m7ZP4CI-vjo7LkUY5v2b657JGjwggtBVipF-SEN_5LqhyphenhyphenSqKnRTruaDa/s1600/Mito+caverna.png\"><img src=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEjOVnA7mFDrmUpwywCBd0h_viy7dnQoAU2-KQgYGRpYg-ccHVvUzoX9ET4rvLuTMrP16uBHAO447G5lav6pwo32m7ZP4CI-vjo7LkUY5v2b657JGjwggtBVipF-SEN_5LqhyphenhyphenSqKnRTruaDa/s200/Mito+caverna.png\" width=\"197\" height=\"200\" border=\"0\"></a></div>\r\n<br><span class=\"Apple-style-span\">Alegor&iacute;a da caverna,&nbsp;<a href=\"http://es.calameo.com/read/0025703654704ca0c5545\" target=\"_blank\" rel=\"noopener\">interpretaci&oacute;n</a></span><br><br>\r\n<div class=\"separator\"><a href=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEieyGonUigUj6Ni3ecag4XoyZLq3yeQENLJy1zE63ZYcqhoN_PN4Vs83k7dclQGvjtrrIMp72z0czgPyHSrbPWQFF-fSB0SPQ31QulHauv5KBWCroFsLacmoVmCePhPuxmv5K5q6BWX1QQG/s1600/Imaxe-interpretacio%CC%81n+caverna.png\"><img src=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEieyGonUigUj6Ni3ecag4XoyZLq3yeQENLJy1zE63ZYcqhoN_PN4Vs83k7dclQGvjtrrIMp72z0czgPyHSrbPWQFF-fSB0SPQ31QulHauv5KBWCroFsLacmoVmCePhPuxmv5K5q6BWX1QQG/s400/Imaxe-interpretacio%CC%81n+caverna.png\" width=\"400\" height=\"376\" border=\"0\"></a></div>\r\n</div>\r\n<div style=\"text-align: justify;\">&nbsp;</div>\r\n<div style=\"text-align: justify;\"><br><span class=\"Apple-style-span\"><a href=\"https://www.blubbr.tv/fb-game-embed.php?game_id=22675\" target=\"_blank\" rel=\"noopener\">Matrix e a caverna de Plat&oacute;n</a> (cortes&iacute;a da profesora do IES de Ames Eva Garea Traba)</span></div>\r\n<div style=\"text-align: justify;\">\r\n<div class=\"separator\">&nbsp;</div>\r\n<div class=\"separator\">&nbsp;</div>\r\n<div class=\"separator\"><a href=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEjgwpaMZJvJovwc-aI-eBAW78658SYRt2tUE-CmRoeeCTdM5x2jxdnxbVmSXcObZkyB2NgpIkbEtULaeUxydhR_J-KIHVOlY_z9UsHNLVO7CCAJqpPXzsmpkKjMWwyzRBci4B3pIp3KRq4S/s1600/matrix1.jpg\"><img src=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEjgwpaMZJvJovwc-aI-eBAW78658SYRt2tUE-CmRoeeCTdM5x2jxdnxbVmSXcObZkyB2NgpIkbEtULaeUxydhR_J-KIHVOlY_z9UsHNLVO7CCAJqpPXzsmpkKjMWwyzRBci4B3pIp3KRq4S/s1600/matrix1.jpg\" width=\"146\" height=\"200\" border=\"0\"></a></div>\r\n<div class=\"separator\">&nbsp;</div>\r\n<div class=\"separator\">&nbsp;</div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\">Outra pel&iacute;cula para reflexionar sobre o mito da caverna:&nbsp;<a href=\"http://vimeo.com/61818837\" target=\"_blank\" rel=\"noopener\">\"El show de Truman\"</a></span></div>\r\n<div class=\"separator\">&nbsp;</div>\r\n<div class=\"separator\">&nbsp;</div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\"><a href=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEi_iPFvNjH0lL3koOodLAWrwBqTKLss0JoJDByoeaafQHKmdCEESMrin_gGkuIG7nZH531KzMPw5KdLuki3VGKOcLwXpLyRnRII3wvN4N_lpLz3jkgGDFjkaatQOMJZ9zfeTJQwpi_5_y9S/s1600/Cara%CC%81tula+el+show+de+Truman.png\"><img src=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEi_iPFvNjH0lL3koOodLAWrwBqTKLss0JoJDByoeaafQHKmdCEESMrin_gGkuIG7nZH531KzMPw5KdLuki3VGKOcLwXpLyRnRII3wvN4N_lpLz3jkgGDFjkaatQOMJZ9zfeTJQwpi_5_y9S/s200/Cara%CC%81tula+el+show+de+Truman.png\" width=\"115\" height=\"200\" border=\"0\"></a></span></div>\r\n<div class=\"separator\">&nbsp;</div>\r\n<div class=\"separator\">&nbsp;</div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\">Se queres traballar sobre isto:&nbsp;<a href=\"http://es.calameo.com/read/002570365c1013d1046bb\" target=\"_blank\" rel=\"noopener\">Cuesti&oacute;ns para a reflexi&oacute;n</a></span></div>\r\n<div class=\"separator\">&nbsp;</div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\">&nbsp;</span></div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\">&nbsp;</span><span class=\"Apple-style-span\"><strong>V&iacute;deo:&nbsp;<em>Apolox&iacute;a de S&oacute;crates:</em></strong></span></div>\r\n<br>\r\n<p><strong><span class=\"Apple-style-span\">ARIST&Oacute;TELES</span></strong></p>\r\n</div>\r\n<div style=\"text-align: justify;\">\r\n<div class=\"separator\"><iframe class=\"YOUTUBE-iframe-video\" src=\"https://www.youtube.com/embed/QuByLb83ZbQ?feature=player_embedded\" width=\"320\" height=\"266\" frameborder=\"0\" allowfullscreen=\"allowfullscreen\" data-thumbnail-src=\"https://i.ytimg.com/vi/QuByLb83ZbQ/0.jpg\"></iframe></div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\">Introduci&oacute;n a Arist&oacute;teles:</span></div>\r\n<div class=\"separator\">&nbsp;</div>\r\n<div>\r\n<div class=\"separator\"><iframe class=\"YOUTUBE-iframe-video\" src=\"https://www.youtube.com/embed/TT1bmmLtwII?feature=player_embedded\" width=\"320\" height=\"266\" frameborder=\"0\" allowfullscreen=\"allowfullscreen\" data-thumbnail-src=\"https://i.ytimg.com/vi/TT1bmmLtwII/0.jpg\"></iframe></div>\r\n</div>\r\n<br><span class=\"Apple-style-span\">Arist&oacute;teles, aspectos biogr&aacute;ficos:</span><br>\r\n<div class=\"separator\">&nbsp;</div>\r\n<div><iframe class=\"YOUTUBE-iframe-video\" src=\"https://www.youtube.com/embed/--GGHb4p93A?feature=player_embedded\" width=\"320\" height=\"266\" frameborder=\"0\" allowfullscreen=\"allowfullscreen\" data-thumbnail-src=\"https://i.ytimg.com/vi/--GGHb4p93A/0.jpg\"></iframe></div>\r\n<div class=\"separator\">&nbsp;</div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\">Fernando Savater, sobre Arist&oacute;teles, na \"Aventura do pensamento\":</span></div>\r\n<div class=\"separator\">&nbsp;</div>\r\n<div>\r\n<div class=\"separator\"><iframe class=\"YOUTUBE-iframe-video\" src=\"https://www.youtube.com/embed/R06dfKOGKiU?feature=player_embedded\" width=\"320\" height=\"266\" frameborder=\"0\" allowfullscreen=\"allowfullscreen\" data-thumbnail-src=\"https://i.ytimg.com/vi/R06dfKOGKiU/0.jpg\"></iframe></div>\r\n<br>\r\n<div><strong>Hylemorfismo:</strong></div>\r\n<div class=\"separator\">&nbsp;</div>\r\n<iframe class=\"YOUTUBE-iframe-video\" src=\"https://www.youtube.com/embed/7jxAkHf50Bc?feature=player_embedded\" width=\"320\" height=\"266\" frameborder=\"0\" allowfullscreen=\"allowfullscreen\" data-thumbnail-src=\"https://i.ytimg.com/vi/7jxAkHf50Bc/0.jpg\"></iframe><br>\r\n<div><strong>&nbsp;</strong></div>\r\n</div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\">&nbsp;</span></div>\r\n<div><span class=\"Apple-style-span\"><strong><u>Arist&oacute;teles:&nbsp;</u><a href=\"http://es.calameo.com/read/002570365c5c5dd07d80b\" target=\"_blank\" rel=\"noopener\">Resumo (primeira parte)</a></strong></span><br><span class=\"Apple-style-span\"><br></span><strong><a href=\"http://www.calameo.com/read/0025703650f632b11280d\" target=\"_blank\" rel=\"noopener\">F&Iacute;SICA E METAF&Iacute;SICA EN ARIST&Oacute;TELES</a></strong><br><br>\r\n<div class=\"separator\"><a href=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEgTxPw3yVAuZdNbCymiQd1PP3eX5e9zeY-iw-TFxhRN2Xh74cBxl6DTd09_Na1MgMxl6BNy2XyRjw_9YIW86cjZR5cWFQnHFAlzbImpFoeYhXraRlW8P34iBDb4yIbRZLLMSJeb40PjQInX/s1600/Portada.+Fi%CC%81sica+e+Metafi%CC%81sica+en+Aristo%CC%81teles.png\"><img src=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEgTxPw3yVAuZdNbCymiQd1PP3eX5e9zeY-iw-TFxhRN2Xh74cBxl6DTd09_Na1MgMxl6BNy2XyRjw_9YIW86cjZR5cWFQnHFAlzbImpFoeYhXraRlW8P34iBDb4yIbRZLLMSJeb40PjQInX/s1600/Portada.+Fi%CC%81sica+e+Metafi%CC%81sica+en+Aristo%CC%81teles.png\" width=\"320\" height=\"238\" border=\"0\"></a></div>\r\n<div class=\"separator\">&nbsp;</div>\r\n</div>\r\n<div class=\"separator\">&nbsp;</div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\"><strong><a href=\"http://es.calameo.com/read/0025703658ed82ea077e4\" target=\"_blank\" rel=\"noopener\">Arist&oacute;teles:&nbsp;Esquema da teor&iacute;a do co&ntilde;ecemento</a></strong></span></div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\">&nbsp;</span></div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\"><u><strong>Arist&oacute;teles:</strong>&nbsp;</u><strong><a href=\"http://es.calameo.com/read/002570365ca6b7da1d9d3\" target=\"_blank\" rel=\"noopener\">Cosmolox&iacute;a</a></strong></span></div>\r\n<div class=\"separator\"><strong>As Escolas Helen&iacute;sticas</strong></div>\r\n<div class=\"separator\"><iframe class=\"YOUTUBE-iframe-video\" src=\"https://www.youtube.com/embed/VY2osqEbvfY?feature=player_embedded\" width=\"320\" height=\"266\" frameborder=\"0\" allowfullscreen=\"allowfullscreen\" data-thumbnail-src=\"https://i.ytimg.com/vi/VY2osqEbvfY/0.jpg\"></iframe></div>\r\n<div class=\"separator\">&nbsp;</div>\r\n<p><a href=\"http://es.calameo.com/read/0025703657d786e992ce3?cid=.%2Fdir%3Drtlasid%3D05815378b8b5566495a17abb1e1dde91\" target=\"_blank\" rel=\"noopener\"><strong><span class=\"Apple-style-span\">A noci&oacute;n de Escol&aacute;stica e o nacemento da Universidade</span></strong></a></p>\r\n<br>\r\n<div class=\"separator\"><span class=\"Apple-style-span\"><strong><a href=\"http://filex.es/historia/filosofiamedieval/index.html\" target=\"_blank\" rel=\"noopener\">Filosof&iacute;a medieval</a>:&nbsp;&nbsp;Relaci&oacute;ns fe-raz&oacute;n</strong></span></div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\"><strong>&nbsp;</strong></span></div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\">-&nbsp;<a href=\"http://www.calameo.com/read/002570365af62299d3531\" target=\"_blank\" rel=\"noopener\">Cristianismo: as novas ideas fronte ao mundo grego</a></span></div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\">-&nbsp;<a href=\"http://www.calameo.com/read/002570365544e83518cd7\" target=\"_blank\" rel=\"noopener\">Helenismo: Filosof&iacute;a e Cristianismo</a></span></div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\">-&nbsp;<a href=\"http://www.calameo.com/read/0025703656f3c8dd5e1de\" target=\"_blank\" rel=\"noopener\">Fe e raz&oacute;n en Santo Agosti&ntilde;o de Hipona</a></span></div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\">-&nbsp;<a href=\"http://www.calameo.com/read/0025703659f3487d0371c\" target=\"_blank\" rel=\"noopener\">A Escol&aacute;stica e o nacemento das Universidades</a></span></div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\">-&nbsp;<a href=\"http://www.calameo.com/read/002570365b28bacea3a66\" target=\"_blank\" rel=\"noopener\">Averroes e o averro&iacute;smo latino</a></span></div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\">-&nbsp;<a href=\"http://www.calameo.com/read/0025703650e55dffc68bb\" target=\"_blank\" rel=\"noopener\">Raz&oacute;n e fe en San Tom&eacute; de Aquino</a></span></div>\r\n<div class=\"separator\">-&nbsp;<a href=\"http://www.calameo.com/read/002570365ebdf5c0ff7e5\" target=\"_blank\" rel=\"noopener\">Lei divina, lei natural, lei positiva en San Tom&eacute; de Aquino</a></div>\r\n<div class=\"separator\">&nbsp;</div>\r\n<p><strong>Un v&iacute;deo de transici&oacute;n: Guillermo de Ockham y Maquiavelo&nbsp;</strong>(Unboxing Philosophy)</p>\r\n<div class=\"separator\"><iframe class=\"YOUTUBE-iframe-video\" src=\"https://www.youtube.com/embed/hkcw2sO0jJ4?feature=player_embedded\" width=\"320\" height=\"266\" frameborder=\"0\" allowfullscreen=\"allowfullscreen\" data-thumbnail-src=\"https://i.ytimg.com/vi/hkcw2sO0jJ4/0.jpg\"></iframe></div>\r\n<div>&nbsp;</div>\r\n<p><strong><span class=\"Apple-style-span\">Humanismo e Renacemento en \"<a href=\"http://www.webdianoia.com/moderna/renhum/renhum.htm\" target=\"_blank\" rel=\"noopener\">webdianioia</a>\"</span></strong></p>\r\n<p>&nbsp;</p>\r\n<p><strong><span class=\"Apple-style-span\">Introduci&oacute;n &aacute; Idade Moderna:&nbsp;<a href=\"http://es.calameo.com/read/00257036564554459a832\">Renacemento e Reforma</a>&nbsp;</span></strong></p>\r\n<br><br>\r\n<div>\r\n<div class=\"separator\"><a href=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEhJQqazoPoJxtvuSP-cAXBH3BYaW6RjGDxfW50HjgERMk5nVJYHiEPZN-Ds5XnuXTDrAVu1r5Xv_B8_08eCZOG_pZU5Be-n3R7dwz7AYkMJtDTuCij2xueYwmYooXZtsHoUbWhIN-5v2gMq/s1600/Filosofi%25CC%2581a+Moderna.png\"><img src=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEhJQqazoPoJxtvuSP-cAXBH3BYaW6RjGDxfW50HjgERMk5nVJYHiEPZN-Ds5XnuXTDrAVu1r5Xv_B8_08eCZOG_pZU5Be-n3R7dwz7AYkMJtDTuCij2xueYwmYooXZtsHoUbWhIN-5v2gMq/s1600/Filosofi%CC%81a+Moderna.png\" width=\"320\" height=\"164\" border=\"0\"></a></div>\r\n<div class=\"separator\">&nbsp;</div>\r\n<p><strong>En<a href=\"http://filex.es/index.php/aula-de-filosofia/h-filosofia/la-filosofia-moderna-racionalismo-y-empirismo\" target=\"_blank\" rel=\"noopener\">&nbsp;filex.es</a>&nbsp;encontramos un interesante desenvolvemento do tema \"La Filosof&iacute;a Moderna. Racionalismo y Empirismo\"</strong></p>\r\n<br>\r\n<div class=\"separator\"><a href=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEimUDowQWvJVoj0OocN0oltJkF7lx0E2BzxbgTa5Q8vuOJRQMTrIPJLkDCOdF8eVCozC3gpA0C0Nm7rQd7ceTpHDnzxqXOA0xUOdmMKowAdp9l3B1BNnqR45Cf3e7byHed2CTOhHwHWWZYS/s1600/La+filosofi%25CC%2581a+moderna.Racionalismo+y+empirismo.+Imaxe.png\"><img src=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEimUDowQWvJVoj0OocN0oltJkF7lx0E2BzxbgTa5Q8vuOJRQMTrIPJLkDCOdF8eVCozC3gpA0C0Nm7rQd7ceTpHDnzxqXOA0xUOdmMKowAdp9l3B1BNnqR45Cf3e7byHed2CTOhHwHWWZYS/s320/La+filosofi%25CC%2581a+moderna.Racionalismo+y+empirismo.+Imaxe.png\" width=\"320\" height=\"147\" border=\"0\"></a></div>\r\n<span class=\"Apple-style-span\"><strong><br></strong></span><span class=\"Apple-style-span\"><strong><br></strong></span><span class=\"Apple-style-span\"><strong>A IDADE MODERNA (I): &nbsp;</strong></span><br><br>\r\n<div class=\"separator\"><a href=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEjlZJ7XLJ8F9TUOZUeEwtlOLwye6ddZgguS8XGQ6fbUppUJ2xiz0W6PfmWqCYIXcSO5gnbijj3i0SkR4fwH6ahuqOyxk-nJ941MBB8bB3hqLq0TGj4bsEHOZu6RdadmehuPyH4KK2SpTPeO/s1600/Pienso+luego+estorbo.png\"><img src=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEjlZJ7XLJ8F9TUOZUeEwtlOLwye6ddZgguS8XGQ6fbUppUJ2xiz0W6PfmWqCYIXcSO5gnbijj3i0SkR4fwH6ahuqOyxk-nJ941MBB8bB3hqLq0TGj4bsEHOZu6RdadmehuPyH4KK2SpTPeO/s1600/Pienso+luego+estorbo.png\" width=\"320\" height=\"257\" border=\"0\"></a></div>\r\n<div class=\"separator\">&nbsp;</div>\r\n<div><strong>O canal Unboxing Philosophy, presenta os seguintes v&iacute;deos introdutorios ao pensamento de Descartes:&nbsp;</strong></div>\r\n<div>&nbsp;</div>\r\n<div class=\"separator\"><iframe class=\"YOUTUBE-iframe-video\" src=\"https://www.youtube.com/embed/9BMXwjKOSyk?feature=player_embedded\" width=\"320\" height=\"266\" frameborder=\"0\" allowfullscreen=\"allowfullscreen\" data-thumbnail-src=\"https://i.ytimg.com/vi/9BMXwjKOSyk/0.jpg\"></iframe></div>\r\n</div>\r\n<div>&nbsp;</div>\r\n<div><br>\r\n<div class=\"separator\"><iframe class=\"YOUTUBE-iframe-video\" src=\"https://www.youtube.com/embed/ScAQqBUAdfY?feature=player_embedded\" width=\"320\" height=\"266\" frameborder=\"0\" allowfullscreen=\"allowfullscreen\" data-thumbnail-src=\"https://i.ytimg.com/vi/ScAQqBUAdfY/0.jpg\"></iframe></div>\r\n<div>&nbsp;</div>\r\n<p><strong><a href=\"http://es.calameo.com/read/0025703653696c43208d5\" target=\"_blank\" rel=\"noopener\">Descartes</a>&nbsp;(power-point)</strong></p>\r\n<p><strong>Descartes: <a href=\"https://drive.google.com/file/d/0B7E8SC_Y7Me2elExNzBqdGJQVWc/view?usp=sharing\" target=\"_blank\" rel=\"noopener\">Resumo para a selectividade</a></strong></p>\r\n</div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\"><strong>&nbsp;</strong></span></div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\"><strong>S&eacute;culo XVII-O Racionalismo-Descartes:&nbsp;<a href=\"https://drive.google.com/file/d/0B7E8SC_Y7Me2S0xNT0NxenFkblU/edit?usp=sharing\">Resumo</a></strong></span></div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\"><strong>Descartes en \"esquemas\":</strong></span></div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\"><strong>a)&nbsp;<a href=\"http://es.calameo.com/read/00257036551257080295e\" target=\"_blank\" rel=\"noopener\">Esquema 1</a></strong></span></div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\"><strong>b)&nbsp;<a href=\"http://es.calameo.com/read/002570365731ec4de9e46\" target=\"_blank\" rel=\"noopener\">Esquema 2</a></strong></span></div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\"><strong>c)&nbsp;<a href=\"http://es.calameo.com/read/0025703656b60bb29d2ed\" target=\"_blank\" rel=\"noopener\">Esquema 3</a></strong></span></div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\"><strong>d)&nbsp;<a href=\"http://es.calameo.com/read/0025703656659f728414f\" target=\"_blank\" rel=\"noopener\">Esquema 4</a></strong></span></div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\"><strong>e)&nbsp;<a href=\"http://es.calameo.com/read/00257036521c8fc00f8ed\" target=\"_blank\" rel=\"noopener\">Esquema 5</a></strong></span></div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\"><strong>f)&nbsp;<a href=\"http://es.calameo.com/read/002570365618faf9bb53b\" target=\"_blank\" rel=\"noopener\">Esquema 6</a></strong></span></div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\"><strong>g)&nbsp;<a href=\"http://es.calameo.com/read/002570365eab0c11c1147\" target=\"_blank\" rel=\"noopener\">Esquema 7</a></strong></span></div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\">&nbsp;</span></div>\r\n<div class=\"separator\"><a href=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEgpXBhMv1CArz-EJnkHZ0OZuyLlCBzLEpNWvSmJLcoBZqBhEGmNhh8E8Q3zeRde9gO6EevOMTM8U5E7LhWmZ6qMk2QgZUQyAI8ABm-5zFWJdvQA1pwc7NR2XMo503A6JwVNe5-Dr4lrloME/s1600/Descartes+y+los+blogs.png\"><img src=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEgpXBhMv1CArz-EJnkHZ0OZuyLlCBzLEpNWvSmJLcoBZqBhEGmNhh8E8Q3zeRde9gO6EevOMTM8U5E7LhWmZ6qMk2QgZUQyAI8ABm-5zFWJdvQA1pwc7NR2XMo503A6JwVNe5-Dr4lrloME/s1600/Descartes+y+los+blogs.png\" width=\"320\" height=\"175\" border=\"0\"></a></div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\">&nbsp;</span></div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\"><strong>Descartes:&nbsp;<a href=\"http://www.e-torredebabel.com/Historia-de-la-filosofia/Ejercicios/Filosofia-Medieval-Moderna/Descartes-Imprimible-Cuestiones.htm\">Actividades</a></strong></span></div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\">&nbsp;</span></div>\r\n<div class=\"separator\"><strong>Co&ntilde;eces a Descartes? Ponte a proba con este<a href=\"http://www.paginasobrefilosofia.com/html/testDes.html\" target=\"_blank\" rel=\"noopener\">&nbsp;TEST</a></strong></div>\r\n<div class=\"separator\">&nbsp;</div>\r\n<p><strong>Contextualizando:&nbsp;<a href=\"https://drive.google.com/file/d/0B7E8SC_Y7Me2aVU4NWd2X0txekE/edit?usp=sharing\">Racionalismo versus Empirismo</a></strong></p>\r\n<br>\r\n<div class=\"separator\"><span class=\"Apple-style-span\">&nbsp;</span></div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\"><strong>John Locke:</strong></span></div>\r\n<div class=\"separator\">&nbsp;</div>\r\n<div class=\"separator\">&nbsp;</div>\r\n<div class=\"separator\"><a href=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEhYb3mJu6ymostFz1aE2LI-EwAgx8dJkNvBYpulBJbB7ZFhQohonBNZwVDWu5o9B_T2zTu_4tHlUEncnkqCQtc0q2zQuMhW0IAbx1tvFIO1J1C0ArZTjyKS_Dk5uqsxkwLHwLfhVrE3bdHa/s1600/John+Locke.png\">&nbsp;<img src=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEhYb3mJu6ymostFz1aE2LI-EwAgx8dJkNvBYpulBJbB7ZFhQohonBNZwVDWu5o9B_T2zTu_4tHlUEncnkqCQtc0q2zQuMhW0IAbx1tvFIO1J1C0ArZTjyKS_Dk5uqsxkwLHwLfhVrE3bdHa/s1600/John+Locke.png\" width=\"320\" height=\"271\" border=\"0\"></a></div>\r\n<div class=\"separator\">&nbsp;</div>\r\n<div class=\"separator\">&nbsp;</div>\r\n<div class=\"separator\"><a href=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEhYb3mJu6ymostFz1aE2LI-EwAgx8dJkNvBYpulBJbB7ZFhQohonBNZwVDWu5o9B_T2zTu_4tHlUEncnkqCQtc0q2zQuMhW0IAbx1tvFIO1J1C0ArZTjyKS_Dk5uqsxkwLHwLfhVrE3bdHa/s1600/John+Locke.png\"><span class=\"Apple-style-span\"><strong>A aventura do pensamento: John Locke</strong></span></a></div>\r\n<div class=\"separator\">&nbsp;</div>\r\n<div class=\"separator\"><iframe class=\"YOUTUBE-iframe-video\" src=\"https://www.youtube.com/embed/V9IOw_ztyXY?feature=player_embedded\" width=\"320\" height=\"266\" frameborder=\"0\" allowfullscreen=\"allowfullscreen\" data-thumbnail-src=\"https://i.ytimg.com/vi/V9IOw_ztyXY/0.jpg\"></iframe></div>\r\n<div class=\"separator\">&nbsp;</div>\r\n<div class=\"separator\">&nbsp;</div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\"><strong>David Hume:</strong></span></div>\r\n<div class=\"separator\">&nbsp;</div>\r\n<div class=\"separator\"><a href=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEgZ_6GwznGkbXXRLd0V0CwFz6R0N6zX3h20ZxPPe1SLWpJYx_mrMRh4nQoZcqHsRMQBgVigwon5OGtZOAe3dA-5SPBzh_Sbk1dL_LeDHnL28v9s2A1cuF6CiG84Z_52acESOULbC2cZwdRh/s1600/David+Hume.png\"><img src=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEgZ_6GwznGkbXXRLd0V0CwFz6R0N6zX3h20ZxPPe1SLWpJYx_mrMRh4nQoZcqHsRMQBgVigwon5OGtZOAe3dA-5SPBzh_Sbk1dL_LeDHnL28v9s2A1cuF6CiG84Z_52acESOULbC2cZwdRh/s1600/David+Hume.png\" width=\"203\" height=\"320\" border=\"0\"></a></div>\r\n<div class=\"separator\"><strong>Autobiograf&iacute;a:<a href=\"https://drive.google.com/file/d/0B7E8SC_Y7Me2bUNTQ0RtaWdJOWc/view?usp=sharing\" target=\"_blank\" rel=\"noopener\">&nbsp;<em>My own life</em></a></strong></div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\"><strong>&nbsp;</strong></span></div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\"><strong>A aventura do pensamento: David Hume</strong></span></div>\r\n<div class=\"separator\">&nbsp;</div>\r\n<div class=\"separator\"><iframe class=\"YOUTUBE-iframe-video\" src=\"https://www.youtube.com/embed/u9zx_VtKVBs?feature=player_embedded\" width=\"320\" height=\"266\" frameborder=\"0\" allowfullscreen=\"allowfullscreen\" data-thumbnail-src=\"https://i.ytimg.com/vi/u9zx_VtKVBs/0.jpg\"></iframe></div>\r\n<div class=\"separator\">&nbsp;</div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\"><strong>Hume, Shrek e as cebolas...</strong></span></div>\r\n<div class=\"separator\">&nbsp;</div>\r\n<div class=\"separator\"><span class=\"Apple-style-span\"><strong>&nbsp;</strong></span><iframe class=\"YOUTUBE-iframe-video\" src=\"https://www.youtube.com/embed/Dcg4MRmOsJ4?feature=player_embedded\" width=\"320\" height=\"266\" frameborder=\"0\" allowfullscreen=\"allowfullscreen\" data-thumbnail-src=\"https://i.ytimg.com/vi/Dcg4MRmOsJ4/0.jpg\"></iframe></div>\r\n<strong><br></strong><span class=\"Apple-style-span\"><strong>Descartes e Hume: powerpoints (selectividade)</strong></span><br><span class=\"Apple-style-span\"><strong><br></strong></span><br>\r\n<div class=\"separator\"><a href=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEiTgNzGMOvKFWDvMcCzOKk6HaaD7a8YaNEwHKEWheGgVsT9r2jm54P4KLBApIRW49Y3sX0sv7vWJwtadM_tnqQaaJLHfRChnXoVaN0xErxPf2-FFNvR0dpQjU_jCM-cVnxxiS7zqnBl5gey/s1600/Selectivo-forges.png\"><img src=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEiTgNzGMOvKFWDvMcCzOKk6HaaD7a8YaNEwHKEWheGgVsT9r2jm54P4KLBApIRW49Y3sX0sv7vWJwtadM_tnqQaaJLHfRChnXoVaN0xErxPf2-FFNvR0dpQjU_jCM-cVnxxiS7zqnBl5gey/s1600/Selectivo-forges.png\" width=\"320\" height=\"254\" border=\"0\"></a></div>\r\n<span class=\"Apple-style-span\"><strong><br></strong></span><span class=\"Apple-style-span\"><strong><a href=\"http://www.calameo.com/read/0025703659edac65d5ab4\">Descartes (powerpoint)</a></strong></span><br><span class=\"Apple-style-span\"><strong><a href=\"http://www.calameo.com/read/00257036543a0437e983e\">Hume (powerpoint)</a></strong></span><br><br>\r\n<p><strong><span class=\"Apple-style-span\">A ILUSTRACI&Oacute;N</span></strong></p>\r\n<p><span class=\"Apple-style-span\">LECTURAS. ARIST&Oacute;TELES: <a href=\"http://www.cervantesvirtual.com/servlet/SirveObras/12482398660132622976846/index.htm\" target=\"_blank\" rel=\"noopener\">\"&Eacute;TICA A NIC&Oacute;MACO\"</a></span><br><span class=\"Apple-style-span\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;ARIST&Oacute;TELES:&nbsp;<a href=\"http://www.cervantesvirtual.com/servlet/SirveObras/13561630989134941976613/index.htm\" target=\"_blank\" rel=\"noopener\">\"POL&Iacute;TICA\"</a></span><br><span class=\"Apple-style-span\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;ARIST&Oacute;TELES:&nbsp;<a href=\"http://www.uruguaypiensa.org.uy/noticia_145_1.html\" target=\"_blank\" rel=\"noopener\">\"F&Iacute;SICA\"</a></span><br><span class=\"Apple-style-span\">V&Iacute;DEO: Arist&oacute;teles e Alexandre Magno:</span></p>\r\n<p><strong><span class=\"Apple-style-span\">Immanuel KANT: Resposta &aacute; pregunta: <a href=\"https://drive.google.com/file/d/0B7E8SC_Y7Me2WEFscXdha2hld2M/edit?usp=sharing\">Que &eacute; a Ilustraci&oacute;n?</a></span></strong></p>\r\n<p><br><a href=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEjGlpjABT0Z0BPZGLMzX5vDBI4Qt24qPSemAenSlB9MiUinYLtIHJwxEt0BZxJtGSd3YRbBU03xnHOiAwYjq76JImx0jWLCawxWigwlJgWozgbS_uMYeI_TgzhcgdQ1FtPFmIAvUormy1n0/s1600/Kant+Imagen.png\"><img src=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEjGlpjABT0Z0BPZGLMzX5vDBI4Qt24qPSemAenSlB9MiUinYLtIHJwxEt0BZxJtGSd3YRbBU03xnHOiAwYjq76JImx0jWLCawxWigwlJgWozgbS_uMYeI_TgzhcgdQ1FtPFmIAvUormy1n0/s1600/Kant+Imagen.png\" width=\"305\" height=\"320\" border=\"0\"></a></p>\r\n</div>\r\n<div style=\"text-align: justify;\">\r\n<div class=\"separator\" style=\"text-align: justify;\"><a href=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEiVk8P-DeU9VT2qn91sHMnaX5SopDfdfqBUvdJUhKJmFOF-RB07MH9S-GxnzlYkn9oJrlOJ7ZQiQf6_Xkn0UADwHGHLKyPLxqbuiOjS7XZuamIn6Txwptw4TfDrnLL3LtdlLssgH3hfA6c4/s1600/Texto+du%CC%81as+cousas....png\"><img src=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEiVk8P-DeU9VT2qn91sHMnaX5SopDfdfqBUvdJUhKJmFOF-RB07MH9S-GxnzlYkn9oJrlOJ7ZQiQf6_Xkn0UADwHGHLKyPLxqbuiOjS7XZuamIn6Txwptw4TfDrnLL3LtdlLssgH3hfA6c4/s1600/Texto+du%CC%81as+cousas....png\" width=\"320\" height=\"139\" border=\"0\"></a></div>\r\n<div class=\"separator\" style=\"text-align: justify;\">&nbsp;</div>\r\n<span class=\"Apple-style-span\"><strong><a href=\"http://es.calameo.com/read/002570365b147804dab86\">A Ilustraci&oacute;n: I.Kant</a>&nbsp;(un resumo)</strong></span><br><br><strong><em>Resposta &aacute; pregunta, Que &eacute; a Ilustraci&oacute;n?<a href=\"http://www.paginasobrefilosofia.com/html/kantpre/iluskant.html\" target=\"_blank\" rel=\"noopener\">&nbsp;</a></em><a href=\"http://www.paginasobrefilosofia.com/html/kantpre/iluskant.html\" target=\"_blank\" rel=\"noopener\">Esquema e estrutura&nbsp;</a>&nbsp;</strong><br><br><a href=\"https://drive.google.com/file/d/0B7E8SC_Y7Me2MzRoOWZLV1ZMRlk/edit?usp=sharing\"><strong>Kant, en esquema</strong></a><br><br><span class=\"Apple-style-span\"><strong>Immanuel Kant:&nbsp;<a href=\"https://drive.google.com/file/d/0B7E8SC_Y7Me2TGpHdFhzaXRhN3c/edit?usp=sharing\">Apuntes de clase</a></strong></span><br><br><span class=\"Apple-style-span\"><strong>Un \'prezzi\' coa&nbsp;<a href=\"http://prezi.com/yux2t0eol-dt/?utm_campaign=share&amp;utm_medium=copy&amp;rc=ex0share\">&Eacute;TICA KANTIANA</a>&nbsp;</strong></span><br><span class=\"Apple-style-span\"><strong><br></strong></span><span class=\"Apple-style-span\"><strong>Ideas para&nbsp;<a href=\"http://auladefilosofia.net/2008/10/28/ideas-para-relacionar-la-filosofia-de-kant-con-la-de-otros-autores/\" target=\"_blank\" rel=\"noopener\">relacionar a filosof&iacute;a kantiana coa doutros autores</a></strong></span><br><br>\r\n<p style=\"text-align: justify;\"><span class=\"Apple-style-span\"><strong>KARL MARX en&nbsp;<em>\"La aventura del pensamiento\"</em>:</strong></span></p>\r\n<div class=\"separator\" style=\"text-align: justify;\"><iframe class=\"YOUTUBE-iframe-video\" src=\"https://www.youtube.com/embed/8ef6BX2FdCk?feature=player_embedded\" width=\"320\" height=\"266\" frameborder=\"0\" allowfullscreen=\"allowfullscreen\" data-thumbnail-src=\"https://i.ytimg.com/vi/8ef6BX2FdCk/0.jpg\"></iframe></div>\r\n<span class=\"Apple-style-span\"><strong><br></strong></span><span class=\"Apple-style-span\"><strong>Karl MARX:&nbsp;<a href=\"https://drive.google.com/file/d/0B7E8SC_Y7Me2MERna0J0TnluTFE/edit?usp=sharing\">Apuntamentos</a></strong></span><br><span class=\"Apple-style-span\"><strong>MARX:&nbsp;<a href=\"https://drive.google.com/file/d/0B7E8SC_Y7Me2dndkMml1eDhWc3M/edit?usp=sharing\">Mapa conceptual</a></strong></span><br><span class=\"Apple-style-span\"><strong><br></strong></span><br>\r\n<div class=\"separator\" style=\"text-align: justify;\"><a href=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEjneUvH8Sae-UoRMrGQHjy5AN7OFaNa3TgFmilOvugWYP3JC4IprdT4ufJwyx9Vhp6ElyGdm3SJ07rHRRwpncX0baYm1ruJoVcVtUYyu5vXSVU4G4UszyTN2hKi5EEKse-g47xfckipYn23/s1600/Marx-+mapa+conceptual.png\"><img src=\"https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEjneUvH8Sae-UoRMrGQHjy5AN7OFaNa3TgFmilOvugWYP3JC4IprdT4ufJwyx9Vhp6ElyGdm3SJ07rHRRwpncX0baYm1ruJoVcVtUYyu5vXSVU4G4UszyTN2hKi5EEKse-g47xfckipYn23/s1600/Marx-+mapa+conceptual.png\" width=\"302\" height=\"320\" border=\"0\"></a></div>\r\n<span class=\"Apple-style-span\"><strong><br></strong></span><span class=\"Apple-style-span\"><br></span><br>\r\n<p style=\"text-align: justify;\"><strong>ARTIGO SOBRE MAQUIAVELO:&nbsp;<a href=\"http://elpais.com/elpais/2013/05/21/opinion/1369148964_042657.html\" target=\"_blank\" rel=\"noopener\">\"Las manos sucias de Maquiavelo\"</a></strong></p>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>', NULL, '2026-01-28 00:19:34', 1, 6, '2026-01-28 23:50:23'),
(4, 'historia-2', 'Historia 2', '<h2>Un v&iacute;deo de Jes&uacute;s Palomar sobre Her&aacute;clito \"o escuro\":</h2>\r\n<p style=\"text-align: center;\">&nbsp;<iframe style=\"display: table; margin-left: auto; margin-right: auto;\" src=\"https://www.youtube.com/embed/fPqkwtRXdYY\" width=\"560\" height=\"314\"></iframe> <iframe style=\"display: table; margin-left: auto; margin-right: auto;\" src=\"https://www.youtube.com/embed/6RbZKPRUCBY?feature=player_embedded\" width=\"320\" height=\"266\" frameborder=\"0\"></iframe> &nbsp;</p>\r\n<p style=\"text-align: center;\"><strong><br></strong><strong>Resumo en power-point:&nbsp;<a href=\"http://es.calameo.com/read/002570365f1e31edeee53\" target=\"_blank\" rel=\"noreferrer noopener\">O NACEMENTO DA FILOSOF&Iacute;A EN OCCIDENTE</a></strong></p>', NULL, '2026-01-28 00:43:39', 1, 6, '2026-01-28 22:49:09'),
(5, 'arist-teles', 'ARISTÓTELES', '<div style=\"width: 100%;\">\r\n<div style=\"position: relative; padding-bottom: 56.25%; padding-top: 0; height: 0;\"><iframe style=\"position: absolute; top: 0; left: 0; width: 100%; height: 100%;\" title=\"ARIST&Oacute;TELES\" src=\"https://view.genially.com/61a101bfa9b0a30de3a0395c\" width=\"1200px\" height=\"675px\" frameborder=\"0\" scrolling=\"yes\" allowfullscreen=\"allowfullscreen\"></iframe></div>\r\n</div>', NULL, '2026-01-28 10:11:56', 1, 6, '2026-01-28 23:34:44'),
(6, 'pon-a-prueba-tus-conocimientos-sobre-arist-teles-quiz-arist-teles', 'Pon a prueba tus conocimientos sobre Aristóteles: Quiz \"Aristóteles\"', '\n<iframe title=\"QUIZ ARISTÓTELES\" src=\"https://view.genially.com/61efe8b69af65500134bd827\" width=\"1200\" height=\"675\" frameborder=\"0\"></iframe>\n', NULL, '2026-01-29 13:10:43', 1, 6, NULL),
(7, 'escape-room-repasando-filosof-a-aristot-lica-ejercicio-interactivo-con-premio', 'ESCAPE ROOM repasando filosofía aristotélica. Ejercicio interactivo con premio.', '<p><iframe title=\"ESCAPE ROOM MUSEO\" src=\"https://view.genially.com/61eff0335f60e30012ad01fa\" width=\"1200\" height=\"675\" frameborder=\"0\"></iframe></p>', NULL, '2026-01-30 09:37:36', 1, 6, '2026-01-30 09:39:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `post_etiquetas`
--

DROP TABLE IF EXISTS `post_etiquetas`;
CREATE TABLE `post_etiquetas` (
  `id_post` int(11) NOT NULL,
  `id_etiqueta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `post_etiquetas`
--

INSERT INTO `post_etiquetas` (`id_post`, `id_etiqueta`) VALUES
(6, 1),
(6, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rate_limits`
--

DROP TABLE IF EXISTS `rate_limits`;
CREATE TABLE `rate_limits` (
  `id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT current_timestamp(),
  `bucket_start` datetime NOT NULL,
  `hits` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `rate_limits`
--

INSERT INTO `rate_limits` (`id`, `action`, `ip`, `ts`, `bucket_start`, `hits`) VALUES
(4040, 'general', '83.165.112.227', '2026-01-31 16:02:19', '2026-01-31 16:02:00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `k` varchar(100) NOT NULL,
  `v` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `settings`
--

INSERT INTO `settings` (`k`, `v`, `updated_at`) VALUES
('header_bg_url', 'uploads/theme/header_1767649968.png', '2026-01-05 21:52:48'),
('theme_bg_color', '#ff0000', '2026-01-05 22:59:16'),
('theme_primary_color', '#000000', '2026-01-05 22:58:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre_usuario` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contrasena_hash` varchar(255) NOT NULL,
  `rol` enum('administrador','autor','usuario') NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre_usuario`, `email`, `contrasena_hash`, `rol`, `fecha_registro`) VALUES
(1, 'AnaLopezS1963', 'analosampedro@gmail.com', '$2y$10$dcnI3e785FHK6ycG5A8adui34KDKsO7Pb6LmJxdsb36BwlCDCgSA2', 'autor', '2025-07-14 21:23:19');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `app_logs`
--
ALTER TABLE `app_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_level` (`level`),
  ADD KEY `idx_event` (`event`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`),
  ADD UNIQUE KEY `nombre_categoria` (`nombre_categoria`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `uq_categorias_nombre` (`nombre_categoria`),
  ADD UNIQUE KEY `uq_categorias_slug` (`slug`);

--
-- Indices de la tabla `etiquetas`
--
ALTER TABLE `etiquetas`
  ADD PRIMARY KEY (`id_etiqueta`),
  ADD UNIQUE KEY `nombre_etiqueta` (`nombre_etiqueta`),
  ADD UNIQUE KEY `uq_etiquetas_nombre` (`nombre_etiqueta`);

--
-- Indices de la tabla `paginas`
--
ALTER TABLE `paginas`
  ADD PRIMARY KEY (`id_pagina`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `uq_paginas_slug` (`slug`);

--
-- Indices de la tabla `pagina_posts`
--
ALTER TABLE `pagina_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_pagina_post` (`id_pagina`,`id_post`),
  ADD KEY `idx_orden` (`id_pagina`,`orden`),
  ADD KEY `idx_pagina` (`id_pagina`),
  ADD KEY `idx_post` (`id_post`);

--
-- Indices de la tabla `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id_post`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `uq_posts_titulo_usuario` (`titulo`,`id_usuario`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_categoria` (`id_categoria`),
  ADD KEY `idx_posts_fecha_publicacion` (`fecha_publicacion`),
  ADD KEY `idx_posts_cat_fecha` (`id_categoria`,`fecha_publicacion`);

--
-- Indices de la tabla `post_etiquetas`
--
ALTER TABLE `post_etiquetas`
  ADD PRIMARY KEY (`id_post`,`id_etiqueta`),
  ADD UNIQUE KEY `uq_post_tag` (`id_post`,`id_etiqueta`),
  ADD KEY `id_etiqueta` (`id_etiqueta`);

--
-- Indices de la tabla `rate_limits`
--
ALTER TABLE `rate_limits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_action_ip_ts` (`action`,`ip`,`ts`);

--
-- Indices de la tabla `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`k`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `nombre_usuario` (`nombre_usuario`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `app_logs`
--
ALTER TABLE `app_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `etiquetas`
--
ALTER TABLE `etiquetas`
  MODIFY `id_etiqueta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `paginas`
--
ALTER TABLE `paginas`
  MODIFY `id_pagina` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `pagina_posts`
--
ALTER TABLE `pagina_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `posts`
--
ALTER TABLE `posts`
  MODIFY `id_post` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `rate_limits`
--
ALTER TABLE `rate_limits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4041;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `app_logs`
--
ALTER TABLE `app_logs`
  ADD CONSTRAINT `fk_app_logs_user` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL;

--
-- Filtros para la tabla `pagina_posts`
--
ALTER TABLE `pagina_posts`
  ADD CONSTRAINT `fk_pagina_posts_pagina` FOREIGN KEY (`id_pagina`) REFERENCES `paginas` (`id_pagina`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pagina_posts_post` FOREIGN KEY (`id_post`) REFERENCES `posts` (`id_post`) ON DELETE CASCADE;

--
-- Filtros para la tabla `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`);

--
-- Filtros para la tabla `post_etiquetas`
--
ALTER TABLE `post_etiquetas`
  ADD CONSTRAINT `post_etiquetas_ibfk_1` FOREIGN KEY (`id_post`) REFERENCES `posts` (`id_post`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_etiquetas_ibfk_2` FOREIGN KEY (`id_etiqueta`) REFERENCES `etiquetas` (`id_etiqueta`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
