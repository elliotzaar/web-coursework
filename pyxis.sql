-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Мар 17 2021 г., 15:27
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
  `creator_session_id` int(11) NOT NULL,
  `create_time` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `accounts`
--

INSERT INTO `accounts` (`id`, `number`, `name`, `balance`, `currency_id`, `creator_session_id`, `create_time`) VALUES
(1, 'UA3829483988439994', 'Заар Елліот Юрійович', '2435.43', 2, 1, '2021-03-17 16:13:44');

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
(6, 'VIEW_ACCOUNTS', 'Дозволяє переглядати та шукати рахунки');

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
(1, 6);

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
(2, 'admin', 'a756e25891ef18437e01b6a81969a01b21f6bf095cbe2b85e30f9aa4a8d33729fd8eaecfca5c6d2c1166c1e2ba0d524ce01e57a065f0a8c65222a6d69123570b', 4, 1),
(3, 'test', '3e8ddffee0774d21fac027dc9d5b2c1e5f6ba64bf6b853d82067de3c2d117ca11110f6b22d097b7045d3a9d31ae0cdfd1f9f5bce107e5b3e7eb90fed70e6e203', 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `users_log`
--

CREATE TABLE `users_log` (
  `id` bigint(11) UNSIGNED NOT NULL,
  `action` enum('STOP_SESSION','SUSPEND_ACCOUNT','UNSUSPEND_ACCOUNT','CREATE_USER','GRANT_ROLE_PERMISSION','REMOVE_ROLE_PERMISSION','CREATE_ROLE','ASSIGN_ROLE') NOT NULL,
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
(3, 'SUSPEND_ACCOUNT', 'Заблоковано користувача ID 1', 19, '2021-02-22 14:36:42'),
(4, 'SUSPEND_ACCOUNT', 'Заблоковано користувача ID 1', 20, '2021-02-22 14:39:23'),
(5, 'UNSUSPEND_ACCOUNT', 'Розблоковано користувача ID 2', 21, '2021-02-22 14:42:53'),
(6, 'SUSPEND_ACCOUNT', 'Заблоковано користувача ID 2', 21, '2021-02-22 14:43:01'),
(7, 'UNSUSPEND_ACCOUNT', 'Розблоковано користувача ID 2', 21, '2021-02-22 14:43:03'),
(8, 'SUSPEND_ACCOUNT', 'Заблоковано користувача ID 2', 21, '2021-02-22 14:43:10'),
(9, 'UNSUSPEND_ACCOUNT', 'Розблоковано користувача ID 2', 21, '2021-02-22 14:43:15'),
(10, 'SUSPEND_ACCOUNT', 'Заблоковано користувача ID 2', 21, '2021-02-22 14:44:02'),
(11, 'UNSUSPEND_ACCOUNT', 'Розблоковано користувача ID 2', 21, '2021-02-22 14:44:04'),
(12, 'SUSPEND_ACCOUNT', 'Заблоковано користувача ID 2', 21, '2021-02-22 14:44:29'),
(13, 'UNSUSPEND_ACCOUNT', 'Поновлено доступ користувача ID 2', 21, '2021-02-22 14:44:31'),
(14, 'CREATE_USER', 'Створено користувача 3 з ім\'ям test', 24, '2021-03-15 13:43:34'),
(15, 'SUSPEND_ACCOUNT', 'Заблоковано користувача ID 3', 24, '2021-03-15 14:26:23'),
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
(76, 'SUSPEND_ACCOUNT', 'Заблоковано користувача ID 2', 34, '2021-03-17 13:38:52'),
(77, 'UNSUSPEND_ACCOUNT', 'Поновлено доступ користувача ID 2', 34, '2021-03-17 13:38:57'),
(78, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл SUSPEND_USERS (id3) з ролі admin id1)', 34, '2021-03-17 13:39:02'),
(79, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл SUSPEND_USERS (id3) до ролі admin (id1)', 34, '2021-03-17 13:39:09'),
(80, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл SUSPEND_USERS (id3) з ролі admin id1)', 34, '2021-03-17 13:39:09'),
(81, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл SUSPEND_USERS (id3) до ролі admin (id1)', 34, '2021-03-17 13:39:10'),
(82, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл VIEW_ACCOUNTS (id6) до ролі admin (id1)', 34, '2021-03-17 13:40:57'),
(83, 'ASSIGN_ROLE', 'Встановлено роль id1 користувачеві 3', 34, '2021-03-17 13:44:32'),
(84, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл VIEW_ACCOUNTS (id6) до ролі accountant (id4)', 34, '2021-03-17 13:44:56'),
(85, 'REMOVE_ROLE_PERMISSION', 'Видалено дозвіл VIEW_ACCOUNTS (id6) з ролі admin id1)', 34, '2021-03-17 13:49:04'),
(86, 'GRANT_ROLE_PERMISSION', 'Додано дозвіл VIEW_ACCOUNTS (id6) до ролі admin (id1)', 34, '2021-03-17 13:49:09'),
(87, 'SUSPEND_ACCOUNT', 'Заблоковано користувача ID 2', 34, '2021-03-17 13:58:08');

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
(35, 1, '773bd397f02f548b7b7b99bdbd76ccde2c70b4fd6dd4f68d4887a645a2116966ce2d2ff888221fb32e71e5ce3c01572907dd33e2fde7c55186efb38796cd931e', 0, '::1', '2021-03-17 15:27:07');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `users_log`
--
ALTER TABLE `users_log`
  MODIFY `id` bigint(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT для таблицы `users_sessions`
--
ALTER TABLE `users_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
