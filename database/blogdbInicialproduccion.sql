-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: db
-- Tiempo de generación: 04-01-2026 a las 09:49:44
-- Versión del servidor: 10.4.34-MariaDB-1:10.4.34+maria~ubu2004
-- Versión de PHP: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `blogdb`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `app_logs`
--

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
(53, '172.18.0.1', 1, 'info', 'login_success', '{\"user_id\":1}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-19 16:56:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

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
(5, 'Evolución Humana', 'evolucion-humana');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `etiquetas`
--

CREATE TABLE `etiquetas` (
  `id_etiqueta` int(11) NOT NULL,
  `nombre_etiqueta` varchar(191) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paginas`
--

CREATE TABLE `paginas` (
  `id_pagina` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `contenido` longtext NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `posts`
--

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
(2, 'post-prueba-de-surf', 'Post Prueba de Surf', 'Esto es un tipo de post nuevo que vamos a intentar subir a la página web nueva para comprobar cómo se escribe cómo se plasma dentro de lo que es la arquitectura habitual de la web que estoy diseñando para ver cómo lo plasma. \r\n\r\nDe alguna forma el contenido de la web para nada va a ser lo que estoy narrando ni lo que estoy titulando en este post pero es sencillamente una muestra de un texto de dos párrafos con una imagen asociada para poder juzgar como simplemente que corregir y que está bien en el diseño. \r\n\r\nEscrito desde as veigas a día 27 de julio de 2025 por el petardo de la familia.', 'uploads/1753650574_smoothed_image.jpg', '2025-07-27 21:09:34', 1, 5, '2025-08-27 17:13:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `post_etiquetas`
--

CREATE TABLE `post_etiquetas` (
  `id_post` int(11) NOT NULL,
  `id_etiqueta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rate_limits`
--

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
(1062, 'general', '172.18.0.1', '2026-01-04 09:47:13', '2026-01-04 09:47:00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `settings`
--

CREATE TABLE `settings` (
  `k` varchar(100) NOT NULL,
  `v` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `settings`
--

INSERT INTO `settings` (`k`, `v`, `updated_at`) VALUES
('header_bg_url', '', '2025-09-04 16:40:00'),
('theme_bg_color', '#ffffff', '2025-09-04 16:40:00'),
('theme_primary_color', '#0645ad', '2025-09-04 16:40:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `etiquetas`
--
ALTER TABLE `etiquetas`
  MODIFY `id_etiqueta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `paginas`
--
ALTER TABLE `paginas`
  MODIFY `id_pagina` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `posts`
--
ALTER TABLE `posts`
  MODIFY `id_post` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `rate_limits`
--
ALTER TABLE `rate_limits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1063;

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
-- Filtros para la tabla `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`);

--
-- Filtros para la tabla `post_etiquetas`
--
ALTER TABLE `post_etiquetas`
  ADD CONSTRAINT `post_etiquetas_ibfk_1` FOREIGN KEY (`id_post`) REFERENCES `posts` (`id_post`),
  ADD CONSTRAINT `post_etiquetas_ibfk_2` FOREIGN KEY (`id_etiqueta`) REFERENCES `etiquetas` (`id_etiqueta`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
