-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Мар 19 2021 г., 15:20
-- Версия сервера: 10.4.16-MariaDB
-- Версия PHP: 7.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `pyxis`
--

-- --------------------------------------------------------

--
-- Структура таблицы `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `number` varchar(64) NOT NULL,
  `name` varchar(128) NOT NULL,
  `balance` decimal(18,2) NOT NULL,
  `currency_id` int(11) NOT NULL,
  `create_time` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `accounts`
--

INSERT INTO `accounts` (`id`, `number`, `name`, `balance`, `currency_id`, `create_time`) VALUES
(1, 'UA212', 'Заар Елліот Юрійович', '2435.00', 2, '2021-03-17 16:13:44'),
(3, 'UA211', 'Заар Елліот Юрійович', '1.10', 3, '2021-03-19 08:46:57'),
(4, 'UA210', 'Заар Елліот Юрійович', '0.00', 2, '2021-03-19 08:49:21');

-- --------------------------------------------------------

--
-- Структура таблицы `currencies`
--

CREATE TABLE `currencies` (
  `id` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  `code` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `currencies`
--

INSERT INTO `currencies` (`id`, `name`, `code`) VALUES
(1, 'Долар США', 'USD'),
(2, 'Українська гривня', 'UAH'),
(3, 'Євро', 'EUR'),
(4, 'Золото', 'XAU');

-- --------------------------------------------------------

--
-- Структура таблицы `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `description`) VALUES
(1, 'VIEW_USERS', 'Дозволяє переглядати користувачів'),
(2, 'STOP_SESSIONS', 'Дозволяє віддалено зупиняти сесії користувачів'),
(3, 'SUSPEND_USERS', 'Дозволяє блокувати та розблоковувати користувачів'),
(4, 'MODIFY_ROLES', 'Дозволяє змінювати ролі, дозволи ролей, а також присвоювати ролі користувачам'),
(5, 'CREATE_USERS', 'Дозволяє додати нового користувача'),
(6, 'VIEW_ACCOUNTS', 'Дозволяє переглядати та шукати рахунки'),
(7, 'CREATE_ACCOUNTS', 'Дозволяє створювати нові рахунки та редагувати вже створені'),
(8, 'VIEW_TRANSACTIONS', 'Дозволяє переглядати транзакції'),
(9, 'CREATE_TRANSACTIONS', 'Дозволяє створювати та проводити транзакції ');

-- --------------------------------------------------------

--
-- Структура таблицы `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(16) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'admin', 'Повні права доступу. Всі можливості розблоковані.'),
(4, 'accountant', 'Роль бухгалтера. Є можливість створювати та проводити операції');

-- --------------------------------------------------------

--
-- Структура таблицы `roles_permissions`
--

CREATE TABLE `roles_permissions` (
  `roles_id` int(11) NOT NULL,
  `permissions_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `roles_permissions`
--

INSERT INTO `roles_permissions` (`roles_id`, `permissions_id`) VALUES
(1, 2),
(1, 1),
(1, 5),
(1, 4),
(1, 3),
(4, 6),
(1, 6),
(1, 7),
(4, 7),
(1, 8),
(1, 9),
(4, 8),
(4, 9);

-- --------------------------------------------------------

--
-- Структура таблицы `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `uuid` varchar(36) NOT NULL,
  `account_id` int(11) NOT NULL,
  `target_account_id` int(11) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `transaction_type_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `creator_session_id` int(11) NOT NULL,
  `create_time` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `transactions`
--

INSERT INTO `transactions` (`id`, `uuid`, `account_id`, `target_account_id`, `amount`, `transaction_type_id`, `description`, `creator_session_id`, `create_time`) VALUES
(1, '4ebcdf39-a0af-4eed-9d37-4b215edf', 1, 4, '866.00', 2, 'AAA', 45, '2021-03-19 16:10:52'),
(2, 'fb4d4b34-91c9-4fc6-a08c-0ed427c3ca49', 1, 4, '866.00', 1, 'AAA', 45, '2021-03-19 16:12:02'),
(3, '94e8a460-39d7-495c-9e0f-b276f08e8599', 1, 4, '866.00', 2, 'AAA', 45, '2021-03-19 16:13:16'),
(4, '454f2a06-6714-49e2-b900-66ea2d96a173', 1, 4, '866.00', 2, 'AAA', 45, '2021-03-19 16:13:18'),
(5, 'd449b39e-b1ff-493a-8aa8-59adff816093', 1, 4, '866.00', 2, 'AAA', 45, '2021-03-19 16:13:18'),
(6, '73548a9f-fe26-43d5-a809-62335991879e', 1, 4, '866.00', 2, 'AAA', 45, '2021-03-19 16:14:29');

-- --------------------------------------------------------

--
-- Структура таблицы `transaction_status`
--

CREATE TABLE `transaction_status` (
  `transaction_uuid` varchar(36) NOT NULL,
  `status` enum('HOLD','AUTHORIZED','CANCELLED','DELETED') NOT NULL DEFAULT 'HOLD',
  `controller_session_id` int(11) DEFAULT NULL,
  `controller_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `transaction_status`
--

INSERT INTO `transaction_status` (`transaction_uuid`, `status`, `controller_session_id`, `controller_time`) VALUES
('4', 'HOLD', NULL, NULL),
('454f2a06-6714-49e2-b900-66ea2d96a173', 'HOLD', NULL, NULL),
('73548a9f-fe26-43d5-a809-62335991879e', 'HOLD', NULL, NULL),
('94e8a460-39d7-495c-9e0f-b276f08e8599', 'HOLD', NULL, NULL),
('d449b39e-b1ff-493a-8aa8-59adff816093', 'HOLD', NULL, NULL),
('fb4d4b34-91c9-4fc6-a08c-0ed427c3ca49', 'HOLD', NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `transaction_types`
--

CREATE TABLE `transaction_types` (
  `id` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `transaction_types`
--

INSERT INTO `transaction_types` (`id`, `name`, `description`) VALUES
(1, 'WITHDRAWAL', 'Зняття з рахунку'),
(2, 'REFILL', 'Поповнення рахунку'),
(3, 'ACCOUNT_TRANSFER', 'Переведення коштів');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `uid` int(11) NOT NULL,
  `username` varchar(16) NOT NULL,
  `password` varchar(256) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `suspended` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`uid`, `username`, `password`, `role_id`, `suspended`) VALUES
(1, 'zaar', 'a756e25891ef18437e01b6a81969a01b21f6bf095cbe2b85e30f9aa4a8d33729fd8eaecfca5c6d2c1166c1e2ba0d524ce01e57a065f0a8c65222a6d69123570b', 1, 0),
(2, 'admin', 'a756e25891ef18437e01b6a81969a01b21f6bf095cbe2b85e30f9aa4a8d33729fd8eaecfca5c6d2c1166c1e2ba0d524ce01e57a065f0a8c65222a6d69123570b', 1, 0),
(3, 'test', '3e8ddffee0774d21fac027dc9d5b2c1e5f6ba64bf6b853d82067de3c2d117ca11110f6b22d097b7045d3a9d31ae0cdfd1f9f5bce107e5b3e7eb90fed70e6e203', 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `users_log`
--

CREATE TABLE `users_log` (
  `id` bigint(11) UNSIGNED NOT NULL,
  `action` enum('STOP_SESSION','SUSPEND_USER','UNSUSPEND_USER','CREATE_USER','GRANT_ROLE_PERMISSION','REMOVE_ROLE_PERMISSION','CREATE_ROLE','ASSIGN_ROLE','CREATE_ACCOUNT','EDIT_ACCOUNT') NOT NULL,
  `action_description` text DEFAULT NULL,
  `operator_session_id` int(11) NOT NULL,
  `time` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `users_log`
--

INSERT INTO `users_log` (`id`, `action`, `action_description`, `operator_session_id`, `time`) VALUES
(1, 'STOP_SESSION', 'Зупинено сесію ID 14', 15, '2021-02-18 16:15:09'),
(2, 'STOP_SESSION', 'Зупинено сесію ID 18', 19, '2021-02-22 13:46:07'),
(3, '', 'Заблоковано користувача ID 1', 19, '2021-02-22 14:36:42'),
(4, '', 'Заблоковано користувача ID 1', 20, '2021-02-22 14:39:23'),
(5, '', 'Розблоковано користувача ID 2', 21, '2021-02-22 14:42:53'),
(6, '', 'Заблоковано користувача ID 2', 21, '2021-02-22 14:43:01'),
(7, '', 'Розблоковано користувача ID 2', 21, '2021-02-22 14:43:03'),
(8, '', 'Заблоковано користувача ID 2', 21, '2021-02-22 14:43:10'),
(9, '', 'Розблоковано користувача ID 2', 21, '2021-02-22 14:43:15'),
(10, '', 'Заблоковано користувача ID 2', 21, '2021-02-22 14:44:02'),
(11, '', 'Розблоковано користувача ID 2', 21, '2021-02-22 14:44:04'),
(12, '', 'Заблоковано користувача ID 2', 21, '2021-02-22 14:44:29'),
(13, '', 'Поновлено доступ користувача ID 2', 21, '2021-02-22 14:44:31'),
(14, 'CREATE_USER', 'Створено користувача 3 з ім\'ям test', 24, '2021-03-15 13:43:34'),
(15, '', 'Заблоковано користувача ID 3', 24, '2021-03-15 14:26:23'),
(16, 'REMOVE_ROLE_PERMISSION', '1', 27, '2021-03-16 10:59:07'),
(17, 'GRANT_ROLE_PERMISSION', '1', 27, '2021-03-16 10:59:09'),
(18, 'REMOVE_ROLE_PERMISSION', '1', 27, '2021-03-16 10:59:10'),
(19, 'GRANT_ROLE_PERMISSION', '1', 27, '2021-03-16 11:01:23'),
(20, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл id2 з ролі 1', 27, '2021-03-16 11:02:45'),
(21, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл id2 до ролі 1', 27, '2021-03-16 11:02:46'),
(22, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл id4 з ролі 1', 27, '2021-03-16 11:02:52'),
(23, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл id2 з ролі 1', 27, '2021-03-16 11:03:08'),
(24, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл id1 з ролі 1', 27, '2021-03-16 11:03:08'),
(25, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл id1 до ролі 1', 27, '2021-03-16 11:03:16'),
(26, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл id5 з ролі 1', 27, '2021-03-16 11:03:20'),
(27, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл STOP_SESSIONS id2 до ролі 1', 27, '2021-03-16 11:04:32'),
(28, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл CREATE_USERS id5 до ролі 1', 27, '2021-03-16 11:04:32'),
(29, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл CREATE_USERS id5 з ролі admin id1', 27, '2021-03-16 11:05:56'),
(30, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл CREATE_USERS (id5) до ролі admin (id1)', 27, '2021-03-16 11:06:46'),
(31, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл STOP_SESSIONS (id2) з ролі admin id1)', 28, '2021-03-16 14:52:16'),
(32, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл STOP_SESSIONS (id2) до ролі admin (id1)', 28, '2021-03-16 14:52:17'),
(33, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл CREATE_USERS (id5) з ролі admin id1)', 28, '2021-03-16 14:53:15'),
(34, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл CREATE_USERS (id5) до ролі admin (id1)', 28, '2021-03-16 14:53:16'),
(35, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл VIEW_USERS (id1) з ролі admin id1)', 28, '2021-03-16 15:08:59'),
(36, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл VIEW_USERS (id1) до ролі admin (id1)', 28, '2021-03-16 15:09:01'),
(37, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл CREATE_USERS (id5) з ролі admin id1)', 28, '2021-03-16 15:25:54'),
(38, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл CREATE_USERS (id5) до ролі admin (id1)', 28, '2021-03-16 15:26:03'),
(39, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл CREATE_USERS (id5) з ролі admin id1)', 28, '2021-03-16 15:30:13'),
(40, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл CREATE_USERS (id5) до ролі admin (id1)', 28, '2021-03-16 15:30:14'),
(41, 'STOP_SESSION', 'Зупинено сесію ID 31', 31, '2021-03-17 09:01:00'),
(42, 'STOP_SESSION', 'Зупинено сесію ID 27', 32, '2021-03-17 09:01:08'),
(43, 'CREATE_ROLE', 'Створено роль id3 з назвою accountant', 32, '2021-03-17 09:58:02'),
(44, 'CREATE_ROLE', 'Створено роль id4 з назвою accountant', 32, '2021-03-17 10:02:03'),
(45, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл SUSPEND_USERS (id3) до ролі  (id4)', 32, '2021-03-17 10:14:36'),
(46, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл SUSPEND_USERS (id3) з ролі  id4)', 32, '2021-03-17 10:14:36'),
(47, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл MODIFY_ROLES (id4) з ролі admin id1)', 34, '2021-03-17 12:49:59'),
(48, 'ASSIGN_ROLE', 'Встановлено роль id4 користувачеві 2', 34, '2021-03-17 13:27:22'),
(49, 'ASSIGN_ROLE', 'Встановлено роль id4 користувачеві 2', 34, '2021-03-17 13:27:27'),
(50, 'ASSIGN_ROLE', 'Встановлено роль id1 користувачеві 2', 34, '2021-03-17 13:27:51'),
(51, 'ASSIGN_ROLE', 'Встановлено роль id-1 користувачеві 2', 34, '2021-03-17 13:29:44'),
(52, 'ASSIGN_ROLE', 'Встановлено роль id-1 користувачеві 2', 34, '2021-03-17 13:29:45'),
(53, 'ASSIGN_ROLE', 'Встановлено роль id1 користувачеві 2', 34, '2021-03-17 13:29:48'),
(54, 'ASSIGN_ROLE', 'Встановлено роль id-1 користувачеві 2', 34, '2021-03-17 13:29:50'),
(55, 'ASSIGN_ROLE', 'Встановлено роль id4 користувачеві 2', 34, '2021-03-17 13:30:12'),
(56, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл SUSPEND_USERS (id3) до ролі  (id4)', 34, '2021-03-17 13:30:22'),
(57, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл SUSPEND_USERS (id3) з ролі  id4)', 34, '2021-03-17 13:30:43'),
(58, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл SUSPEND_USERS (id3) з ролі  id4)', 34, '2021-03-17 13:30:53'),
(59, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл SUSPEND_USERS (id3) з ролі accountant id4)', 34, '2021-03-17 13:31:43'),
(60, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл SUSPEND_USERS (id3) з ролі accountant id4)', 34, '2021-03-17 13:31:43'),
(61, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл SUSPEND_USERS (id3) з ролі accountant id4)', 34, '2021-03-17 13:31:44'),
(62, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл SUSPEND_USERS (id3) з ролі accountant id4)', 34, '2021-03-17 13:31:44'),
(63, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл SUSPEND_USERS (id3) з ролі accountant id4)', 34, '2021-03-17 13:31:45'),
(64, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл SUSPEND_USERS (id3) з ролі accountant id4)', 34, '2021-03-17 13:31:45'),
(65, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл SUSPEND_USERS (id3) з ролі accountant id4)', 34, '2021-03-17 13:31:46'),
(66, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл STOP_SESSIONS (id2) до ролі accountant (id4)', 34, '2021-03-17 13:31:49'),
(67, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл SUSPEND_USERS (id3) до ролі accountant (id4)', 34, '2021-03-17 13:33:42'),
(68, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл SUSPEND_USERS (id3) до ролі accountant (id4)', 34, '2021-03-17 13:33:58'),
(69, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл SUSPEND_USERS (id3) до ролі accountant (id4)', 34, '2021-03-17 13:34:15'),
(70, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл SUSPEND_USERS (id3) з ролі accountant id4)', 34, '2021-03-17 13:34:17'),
(71, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл STOP_SESSIONS (id2) з ролі accountant id4)', 34, '2021-03-17 13:34:17'),
(72, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл MODIFY_ROLES (id4) до ролі accountant (id4)', 34, '2021-03-17 13:34:23'),
(73, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл MODIFY_ROLES (id4) з ролі accountant id4)', 34, '2021-03-17 13:34:23'),
(74, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл MODIFY_ROLES (id4) до ролі accountant (id4)', 34, '2021-03-17 13:34:24'),
(75, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл MODIFY_ROLES (id4) з ролі accountant id4)', 34, '2021-03-17 13:34:33'),
(76, '', 'Заблоковано користувача ID 2', 34, '2021-03-17 13:38:52'),
(77, '', 'Поновлено доступ користувача ID 2', 34, '2021-03-17 13:38:57'),
(78, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл SUSPEND_USERS (id3) з ролі admin id1)', 34, '2021-03-17 13:39:02'),
(79, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл SUSPEND_USERS (id3) до ролі admin (id1)', 34, '2021-03-17 13:39:09'),
(80, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл SUSPEND_USERS (id3) з ролі admin id1)', 34, '2021-03-17 13:39:09'),
(81, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл SUSPEND_USERS (id3) до ролі admin (id1)', 34, '2021-03-17 13:39:10'),
(82, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл VIEW_ACCOUNTS (id6) до ролі admin (id1)', 34, '2021-03-17 13:40:57'),
(83, 'ASSIGN_ROLE', 'Встановлено роль id1 користувачеві 3', 34, '2021-03-17 13:44:32'),
(84, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл VIEW_ACCOUNTS (id6) до ролі accountant (id4)', 34, '2021-03-17 13:44:56'),
(85, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл VIEW_ACCOUNTS (id6) з ролі admin id1)', 34, '2021-03-17 13:49:04'),
(86, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл VIEW_ACCOUNTS (id6) до ролі admin (id1)', 34, '2021-03-17 13:49:09'),
(87, '', 'Заблоковано користувача ID 2', 34, '2021-03-17 13:58:08'),
(88, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл CREATE_ACCOUNTS (id7) до ролі admin (id1)', 36, '2021-03-18 11:39:32'),
(89, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл CREATE_ACCOUNTS (id7) з ролі admin id1)', 37, '2021-03-18 12:34:03'),
(90, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл CREATE_ACCOUNTS (id7) до ролі admin (id1)', 37, '2021-03-18 12:34:13'),
(91, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл CREATE_ACCOUNTS (id7) з ролі admin id1)', 37, '2021-03-18 13:09:54'),
(92, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл CREATE_ACCOUNTS (id7) до ролі admin (id1)', 37, '2021-03-18 13:10:07'),
(93, 'CREATE_ACCOUNT', 'Створено рахунок id2 (num UA43342233223, name Заар Елліот Юрійович, bal 1.32:4)', 39, '2021-03-19 08:46:10'),
(94, 'CREATE_ACCOUNT', 'Створено рахунок id3 (num UA211, name Заар Елліот Юрійович, bal 1.1:4)', 39, '2021-03-19 08:46:57'),
(95, 'CREATE_ACCOUNT', 'Створено рахунок id4 (num AAA, name Заар Елліот Юрійович, bal 0:3)', 39, '2021-03-19 08:49:21'),
(96, 'UNSUSPEND_USER', 'Поновлено доступ користувача ID 2', 39, '2021-03-19 09:11:42'),
(97, 'SUSPEND_USER', 'Заблоковано користувача ID 1', 39, '2021-03-19 09:11:45'),
(98, 'UNSUSPEND_USER', 'Поновлено доступ користувача ID 1', 40, '2021-03-19 09:12:58'),
(99, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл CREATE_ACCOUNTS (id7) до ролі accountant (id4)', 41, '2021-03-19 09:13:20'),
(100, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл VIEW_TRANSACTIONS (id8) до ролі admin (id1)', 41, '2021-03-19 09:15:09'),
(101, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл VIEW_TRANSACTIONS (id8) з ролі admin id1)', 41, '2021-03-19 09:15:15'),
(102, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл VIEW_TRANSACTIONS (id8) до ролі admin (id1)', 41, '2021-03-19 09:16:03'),
(103, 'EDIT_ACCOUNT', 'Модифіковано рахунок id1', 41, '2021-03-19 10:28:57'),
(104, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл CREATE_TRANSACTIONS (id9) до ролі admin (id1)', 42, '2021-03-19 12:19:41'),
(105, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл CREATE_TRANSACTIONS (id9) з ролі admin id1)', 42, '2021-03-19 13:46:43'),
(106, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл CREATE_TRANSACTIONS (id9) до ролі admin (id1)', 42, '2021-03-19 13:46:57'),
(107, 'EDIT_ACCOUNT', 'Модифіковано рахунок id1', 42, '2021-03-19 14:40:53'),
(108, 'EDIT_ACCOUNT', 'Модифіковано рахунок id4', 42, '2021-03-19 14:42:02'),
(109, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл VIEW_TRANSACTIONS (id8) до ролі accountant (id4)', 42, '2021-03-19 14:50:32'),
(110, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл CREATE_TRANSACTIONS (id9) до ролі accountant (id4)', 42, '2021-03-19 14:50:32'),
(111, 'EDIT_ACCOUNT', 'Модифіковано рахунок id3', 42, '2021-03-19 14:51:23'),
(112, 'SUSPEND_USER', 'Заблоковано користувача ID 2', 43, '2021-03-19 14:52:23'),
(113, 'UNSUSPEND_USER', 'Поновлено доступ користувача ID 2', 43, '2021-03-19 14:52:32'),
(114, 'STOP_SESSION', 'Зупинено сесію ID 38', 43, '2021-03-19 14:53:16'),
(115, 'STOP_SESSION', 'Зупинено сесію ID 39', 43, '2021-03-19 14:53:16'),
(116, 'STOP_SESSION', 'Зупинено сесію ID 41', 43, '2021-03-19 14:53:17'),
(117, 'STOP_SESSION', 'Зупинено сесію ID 43', 44, '2021-03-19 14:54:03'),
(118, 'SUSPEND_USER', 'Заблоковано користувача ID 1', 44, '2021-03-19 14:54:34'),
(119, 'UNSUSPEND_USER', 'Поновлено доступ користувача ID 1', 46, '2021-03-19 14:54:46');

-- --------------------------------------------------------

--
-- Структура таблицы `users_sessions`
--

CREATE TABLE `users_sessions` (
  `id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `token` varchar(128) NOT NULL,
  `stopped` tinyint(1) NOT NULL DEFAULT 0,
  `address` varchar(64) NOT NULL,
  `create_time` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `users_sessions`
--

INSERT INTO `users_sessions` (`id`, `users_id`, `token`, `stopped`, `address`, `create_time`) VALUES
(1, 1, '83b8e0ed517c3dc5570fc931050bd6590c5553ca488ad89856eb8a6a260899f9fb2a63a9af3f69b10674bdf1950de474dfa07d074e442bac47190001bbc6a2be', 1, '::1', '2021-02-16 15:43:17'),
(2, 1, '0fe137714b6af03aa68f07a42838212e56542ff132a23ba0bb3f4cfb5a2c2d8751930d2c3281bdd8187fe83ce0b10ca9c42e859a762aff687361d5eb30e80b3c', 0, '::1', '2021-02-16 15:50:09'),
(3, 1, '505145af5334baf91e6a793de525ce9edb25eb291d40f56638b5e57e747c7b0fdf6effaaa910c9d6bfeb9bb2ad3cbaebd22851c42994d1ece4e41a141000104b', 0, '::1', '2021-02-16 15:59:20'),
(4, 1, 'd16ad13d49b57577ec72b32b35fb6b89667b6785c7847140d940bbbd367f3e8cedbfcd2fcee3d6d3537c11a9f1713ddbc778fd6433b2c48c6ff26ae169303917', 1, '::1', '2021-02-17 07:56:05'),
(5, 1, '5b85c77abe1850533a5ecc0454d17ebc670fa738fd2e7d898049a7451f6261cc08be4241007b1731e60db2fe3180ab0a563a2d586165e3a85619e25a99f5757b', 0, '::1', '2021-02-17 09:13:19'),
(6, 1, '21f7959f7d6e192123ff83f93b270fb29444439ef14b259e45b1e4f81439faa1f188be507f4f6769b0ab5c8c6c4a2f9d99dc719086ab054bb97f0226fdab24b8', 1, '::1', '2021-02-17 12:08:32'),
(7, 1, '0f75a6ce4db886929dfa8f1d71cad2365eb3d452b09fed9a9e88ce026456694d95d894f7f0c5d5d88b35aa54d6b4c0cab45509bf57e33f9459fc77df72903c3e', 1, '::1', '2021-02-17 12:13:10'),
(8, 1, 'b513c15abe98bd99982550a08b7609a8edb22841b294b5419a085c3dfbbb946ce4b07d1ed9869bab75cb8986060af4908c00462bd7f33064b999ad04350216f1', 1, '::1', '2021-02-17 12:24:31'),
(9, 1, '1aa5791fd08b57b24d9ad27c729b6e0b0f3bf4d6073126209259677c1e4f792d2346a2c7d141a9527a26ec5b352a956a0f44cb608e07deb870abb00e81d19cb6', 1, '::1', '2021-02-17 12:24:40'),
(10, 1, '9e53927959ef7033c649d3cbe4f930b10d21cc4a573c8efa12cb0f2031d242193ad8ea7a25b7ac86ab7d83618976e91a83c5a27d8d8d30fca9ad39f624b0355b', 1, '::1', '2021-02-17 12:26:43'),
(11, 1, 'f981a39d3a6b212d791f768cc7b3cbf46f43a95c90ba5102f3449ec5b77fe42d027f68041404c46ec33b03d8da6971ab927bd390e51b8d1d2bb41522e99f8b0a', 1, '::1', '2021-02-18 09:26:00'),
(12, 1, '3471c5afa68c9ef8c6fb379ae33b1935eff7a6c909dd355c0ebead71372b7be34eea5b65b75f658138f80fc99d013bc95a9c2ab4ce74ecc3d7a7e18458f10091', 1, '::1', '2021-02-18 11:29:37'),
(13, 1, 'e88135b9ea2e4f338eac907ac01f646e21b0098d0f9e9fe04887f7b185a8f0ebba27178fcb8493fb2984c88125e1ee778b3ddd6098e919e5b5c02275fc223532', 1, '::1', '2021-02-18 11:52:16'),
(14, 1, 'eacb0ff2f1093da1b32a63f1006fff6685d9297179d1bbc7144dbc97ea4fae3e62aff80af116c17eefe5f9004d74adbf5d18a6e00823c971706f4d0761d983f3', 1, '::1', '2021-02-18 15:33:14'),
(15, 1, '1aa00892720dd58e558903109930ada9db0b0f46168e705fa1bee595ead8ad593839c912ea57fd264a28794b134a628b013e9392b340d168eb98bb3a50c81c9f', 0, '::1', '2021-02-18 16:15:03'),
(16, 1, 'b166072a5e247f9482f323fd18cb058a2444be78bbd8e435da3c34b4fadf4af25286e9961f749a1c60d85b191c7cb097826eb9a16010ddd7a016942d00303167', 0, '::1', '2021-02-18 16:15:40'),
(17, 1, 'e69ac4904b84ff3c6cbe7b2c76be126f11d0a90e25c551dfbb6e57cdf33dadbb59242442a2aaba16d25f93716f1b0d56df436abc929297fdf559f6e350c718f9', 0, '::1', '2021-02-19 08:25:08'),
(18, 1, '1e4591674f4923c7b0e963dc221d178e3ce431094e46757c8edc0e46db09861e8201d8b55c6f38ceccf528eccdab70221c7006d41e139bcd4debdc7ce0b43906', 1, '::1', '2021-02-22 12:46:01'),
(19, 1, 'd92ed84204eba74688f7522b3999eb473099774c4b11b21dc175f848ce6a26f66cfdc7b20b319971226234df38d25b30ef67ea15255840270919b420d025ab01', 0, '::1', '2021-02-22 12:50:02'),
(20, 1, 'ab47cd77da3c832ec78290a53ee9f9d2033791e8eb0202528bf84da82177404352aa6dc0418a6d1fc7aa96da2e756d6fe58aa99cd23d94d1df1d3e322446732a', 0, '::1', '2021-02-22 14:37:40'),
(21, 1, '27bf71c6810f74eb2dc7d8906db693d6774148302785f9ccfe6c0ea206b3703f6a01bebe16833f31f692def3459af6965956e662df18a17ece7b663b384b5162', 0, '::1', '2021-02-22 14:39:50'),
(22, 1, 'da800b442e5ca86f84f0b6e165a7771cf09d9994435908406ff38e6136217c419d48991f5990bc7a3ca5b82d6ae4eaabe424c9ffe19029a60f5cb3e75c420374', 0, '::1', '2021-03-15 08:09:37'),
(23, 1, 'aa7869b440c48937e3821e1d9ca4e20455efc0d82baaf9fa1c94d28ebf43dc64191cac9652c1c6884d3d075aa90a3f74b6d5ac0a9413db4cbe93cfada0c1a436', 0, '::1', '2021-03-15 11:43:10'),
(24, 1, '08a3cf635d3e420a069cd0531082a2ac98ca9defe4f54891f567263dbf9c6c93e6f4db23dd84adeccfce51f3306fd0e6304f6de056495d9520660ea9966f8a29', 0, '::1', '2021-03-15 13:17:51'),
(25, 1, 'a468785486a76f4fe5f6078f2edcb7c9847454d7e7c1cdd455d4a8c7662b75620f3fbc0f5ec44c5d33da7aa2f95482f801885fa8d6a6bd65c9a7c515ef54c64a', 0, '::1', '2021-03-15 15:49:43'),
(26, 1, 'd8b0ece656155a9cf1b640930d567bb5cc0be5c5967203cb2386e425ad15cff29f6cb70858f37d667fad4066805658697af54326c06eec3bbb7f38c70a61517b', 0, '::1', '2021-03-16 07:58:03'),
(27, 1, '02be455c3db1f0c1b4641cdb31028d23818a35eca1836a4c8ca1c7f005f634457bd4fb31bfa68f365efa8007c2b415a9cd648dcc874c5baff9b03eb3b99485d2', 1, '::1', '2021-03-16 09:08:09'),
(28, 1, '5f2a55a1341e6ac5b1310329d87fa3c8717d9839d6f0a193bc6d861732e95ff7b90606ab08673e4b001d34522fbb55a9ba1469fab738c53a4a931dd5e615f71c', 0, '::1', '2021-03-16 14:52:12'),
(29, 1, '4ab0fccf5d9631f87cf311614581714ff9de5da78002e91ebf2faf8acae2d37a98d9e20254cdd55933ce7eb7e60e89b2607f77ea16e267d8aaac4dd979609bc5', 0, '::1', '2021-03-16 16:17:57'),
(30, 1, 'ab6dbbcc6e5d3475d8ac35fd6479de71c9923e00e4f755375f248e7b08cee3bf75e20e5470c0723f952e642d5492bfd2d241c09ae184e0de249858d2d6e7f303', 0, '127.0.0.1', '2021-03-17 08:16:16'),
(31, 1, '41cce3922ab2d52674ffc05341e55d311178f4b3a1fc226d58dcde798a4cd29d57f247315a96946159ff3c1771ba457044eb951dd1adcca2ba65845cdf89b712', 1, '::1', '2021-03-17 08:58:41'),
(32, 1, '6e3581ed4880c722052d1d5037ca26c17177c37f3f9dcd86d2e1fcb58e46f319fb08532788ff4ae153b755cdfbdc7499a2e60450dd64d75007208cda0109706c', 0, '::1', '2021-03-17 09:01:04'),
(33, 1, '78bbe0497bf0add31505ed3bc7de27b3c188b2eb354731aa2559b2a0d7fe0a7f2fae6eb84394fbdd1c5244607ab25d1b38850a3429ca55ef483f7f044926fafa', 0, '::1', '2021-03-17 12:03:15'),
(34, 1, '6aec145998258165db7dd805f5d9d5aab1b7a18205688019e0f65a9059f8784f895c785ed2a4348a830320bf36a8026487cdd407cbbefba214f46d5674bfc812', 0, '::1', '2021-03-17 12:35:05'),
(35, 1, '773bd397f02f548b7b7b99bdbd76ccde2c70b4fd6dd4f68d4887a645a2116966ce2d2ff888221fb32e71e5ce3c01572907dd33e2fde7c55186efb38796cd931e', 0, '::1', '2021-03-17 15:27:07'),
(36, 1, '093e2f3296f1b88dff3335619d9e9b19318516d453f1474a5d7a4fd811c37720627d4e6f3d82820c98edc419273103e299fd7a1e13899d4b80aa3161e4376faf', 0, '::1', '2021-03-18 11:27:09'),
(37, 1, '92de7b4c29e8642dfd0c42d8444505cd65e2ef903c5444e0853b8c090b1d3ed00781a5d1147f28e2e9b088ff352696cc7fde47ce7b3680789cf62ea29c5b7e2f', 0, '::1', '2021-03-18 12:31:04'),
(38, 1, '1b3743c7c1c9e56173491e260875041393098757e9d1105bc3f3544b576cb8764f9a98c1f688b14390a22b5be1bd2db1f503fbcf7bc27b7c909cb1560af17596', 1, '::1', '2021-03-18 15:38:36'),
(39, 1, '6fbce9bfa053f0f7ad211eeb3cb0db318b0322c8ce65bf07741a9dc5760f4b587ceaceacb910cc5655892fd88c0e610b825b25087ba64c1c7d2e7abf5ec0507f', 1, '::1', '2021-03-19 08:05:30'),
(40, 2, '3a30d1bc59972bfd4f412502a6bfae4fc9b830cf9c4b696a14f65498abf1fcf690ffc4a79b09f496921bad4f9afe4508e782dd215fa6f970c0ba9d89e520f5d9', 1, '::1', '2021-03-19 09:12:32'),
(41, 1, 'd6227c3e7107a29b2c859b15fcf52a29c9b7e9dfc3f32726d4a3979130a2a2a73b1e58131e600a9da35eb65939265c3f1af01505e320c7584b05bf3faab5918d', 1, '::1', '2021-03-19 09:13:04'),
(42, 1, '3de9945431607e374c8c31626a8e917c38614a6f0b837a9db8fdca0e306302874dcd6de4c6ce87ea99a7eba24dee14a02712b76fe22a6710f8237930fdb7b65f', 1, '::1', '2021-03-19 12:16:39'),
(43, 1, '92f582d5a9f20c860d07a0267f4fdcc01d212ddf647010190644a72f0b2922a9a29d9ad1d6eeed7d7f954659267ab0d594d0c90c265c9cebcd0cc9b0ab28967f', 1, '::1', '2021-03-19 14:52:04'),
(44, 1, '8fbd2eebe11ca610b5d59d9232743eb000ec5e89895c19a423f12336b13799f310ceed9f3fd3c2df37eb3d1b1d03c30fde03a96e7404152ef034cb515328bc21', 0, '::1', '2021-03-19 14:53:39'),
(45, 1, 'fedbd4daf74752951d4d9248d2d62fed086f51b1bab3eb26e3adfbbfff271fc8cec0a45af4d68ad1f9dbe09e4f5d9f1599a7b83c3f37ffac2618a50777e498c2', 0, '::1', '2021-03-19 14:54:12'),
(46, 2, '92969f09ac528d9af881bfcb48e2895380c173b0d89126d714161f0aa2a07a9fb826d7adf428204bbeceb064da33ed5aa41da8cea9ae103841744e94f4e8359d', 1, '::1', '2021-03-19 14:54:43'),
(47, 1, 'be814b74814023c435475da4208e8ba622ae37b0bb0e68abe8110d2f4c4969d76dc034b4190db28c5ba8fc3758010553a7945f60cb5f8c01f72c00d5fcc71597', 0, '::1', '2021-03-19 14:54:55');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `number` (`number`),
  ADD KEY `currency_id` (`currency_id`);

--
-- Индексы таблицы `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`);

--
-- Индексы таблицы `transaction_status`
--
ALTER TABLE `transaction_status`
  ADD UNIQUE KEY `transaction_uuid` (`transaction_uuid`);

--
-- Индексы таблицы `transaction_types`
--
ALTER TABLE `transaction_types`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Индексы таблицы `users_log`
--
ALTER TABLE `users_log`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users_sessions`
--
ALTER TABLE `users_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `transaction_types`
--
ALTER TABLE `transaction_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `users_log`
--
ALTER TABLE `users_log`
  MODIFY `id` bigint(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT для таблицы `users_sessions`
--
ALTER TABLE `users_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
