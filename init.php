<?php
$pdo = new PDO('mysql:host=localhost', 'root', '');

$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->prepare('SELECT COUNT(SCHEMA_NAME) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = "webcw"');
$stmt->execute();

$data = $stmt->fetchAll();
if($data[0][0] == '0') {

  if(isset($_POST['username']) && isset($_POST['password']) && strlen($_POST['username']) > 3 && strlen($_POST['username']) <= 16 && !preg_match('/[^a-z]/', $_POST['username']) && strlen($_POST['password']) >= 8 && strlen($_POST['password']) <= 512) {
    $stmt = $pdo->prepare('CREATE DATABASE `webcw`');
    $stmt->execute();

    $pdo = new PDO('mysql:host=localhost;dbname=webcw', 'root', '');
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->prepare("CREATE TABLE `accounts` (`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `number` varchar(64) NOT NULL, `name` varchar(128) NOT NULL, `balance` decimal(18,2) NOT NULL, `currency_id` int(11) NOT NULL, `create_time` datetime NOT NULL DEFAULT current_timestamp());")->execute();
    $pdo->prepare("CREATE TABLE `currencies` (`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `name` varchar(45) NOT NULL, `code` varchar(3) NOT NULL);")->execute();
    $pdo->prepare("CREATE TABLE `permissions` (`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `name` varchar(45) NOT NULL, `description` text DEFAULT NULL);")->execute();
    $pdo->prepare("CREATE TABLE `roles` (`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `name` varchar(16) NOT NULL, `description` text DEFAULT NULL);")->execute();
    $pdo->prepare("CREATE TABLE `roles_permissions` (`roles_id` int(11) NOT NULL, `permissions_id` int(11) NOT NULL);")->execute();
    $pdo->prepare("CREATE TABLE `transactions` (`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `uuid` varchar(36) NOT NULL, `account_id` int(11) NOT NULL, `target_account_id` int(11) NOT NULL, `amount` decimal(18,2) NOT NULL, `transaction_type_id` int(11) NOT NULL, `description` text NOT NULL, `creator_session_id` int(11) NOT NULL, `create_time` datetime NOT NULL DEFAULT current_timestamp(), `status` enum('HOLD','AUTHORIZED','CANCELLED','DELETED') NOT NULL DEFAULT 'HOLD', `controller_session_id` int(11) DEFAULT NULL, `controller_time` datetime DEFAULT NULL);")->execute();
    $pdo->prepare("CREATE TABLE `transaction_types` (`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `name` varchar(45) NOT NULL, `description` text NOT NULL);")->execute();
    $pdo->prepare("CREATE TABLE `users` (`uid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `username` varchar(16) NOT NULL, `password` varchar(256) NOT NULL, `role_id` int(11) DEFAULT NULL, `suspended` tinyint(1) NOT NULL DEFAULT 0);")->execute();
    $pdo->prepare("CREATE TABLE `users_log` (`id` bigint(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, `action` enum('STOP_SESSION','SUSPEND_USER','UNSUSPEND_USER','CREATE_USER','GRANT_ROLE_PERMISSION','REMOVE_ROLE_PERMISSION','CREATE_ROLE','ASSIGN_ROLE','CREATE_ACCOUNT','EDIT_ACCOUNT','ROLLBACK_TRANSACTION','PASS_CHANGE') NOT NULL, `action_description` text DEFAULT NULL, `operator_session_id` int(11) NOT NULL, `time` datetime NOT NULL DEFAULT current_timestamp());")->execute();
    $pdo->prepare("CREATE TABLE `users_sessions` (`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `users_id` int(11) NOT NULL, `token` varchar(128) NOT NULL, `stopped` tinyint(1) NOT NULL DEFAULT 0, `address` varchar(64) NOT NULL, `create_time` datetime NOT NULL DEFAULT current_timestamp());")->execute();

    $pdo->prepare("ALTER TABLE `accounts` ADD UNIQUE KEY `number` (`number`), ADD KEY `currency_id` (`currency_id`);")->execute();
    $pdo->prepare("ALTER TABLE `permissions` ADD UNIQUE KEY `name` (`name`);")->execute();
    $pdo->prepare("ALTER TABLE `roles` ADD UNIQUE KEY `name` (`name`);")->execute();
    $pdo->prepare("ALTER TABLE `transactions` ADD UNIQUE KEY `uuid` (`uuid`);")->execute();
    $pdo->prepare("ALTER TABLE `users` ADD UNIQUE KEY `username` (`username`);")->execute();
    $pdo->prepare("ALTER TABLE `users_sessions` ADD UNIQUE KEY `token` (`token`);")->execute();

    $pdo->prepare("INSERT INTO `currencies` (`name`, `code`) VALUES
    ('Долар США', 'USD'),
    ('Українська гривня', 'UAH'),
    ('Євро', 'EUR'),
    ('Золото', 'XAU');")->execute();
    $pdo->prepare("INSERT INTO `permissions` (`name`, `description`) VALUES
    ('VIEW_USERS', 'Дозволяє переглядати користувачів'),
    ('STOP_SESSIONS', 'Дозволяє віддалено зупиняти сесії користувачів'),
    ('SUSPEND_USERS', 'Дозволяє блокувати та розблоковувати користувачів'),
    ('MODIFY_ROLES', 'Дозволяє змінювати ролі, дозволи ролей, а також присвоювати ролі користувачам'),
    ('CREATE_USERS', 'Дозволяє додати нового користувача'),
    ('VIEW_ACCOUNTS', 'Дозволяє переглядати та шукати рахунки'),
    ('CREATE_ACCOUNTS', 'Дозволяє створювати нові рахунки та редагувати вже створені'),
    ('VIEW_TRANSACTIONS', 'Дозволяє переглядати транзакції'),
    ('CREATE_TRANSACTIONS', 'Дозволяє створювати та проводити транзакції '),
    ('AUTH_TRANSACTIONS', 'Дозволяє користувачам авторизовувати транзакції, створених іншими користувачами'),
    ('ROLLBACK_TRANSACTIONS', 'Дозволяє видаляти вже авторизовані транзакції'),
    ('AUTH_SELFTRANSACTIONS', 'Дозволяє користувачам авторизовувати всі транзакції, включаючи свої власні ');")->execute();
    $pdo->prepare("INSERT INTO `roles` (`name`, `description`) VALUES
    ('admin', 'Повні права доступу. Всі можливості розблоковані.');")->execute();
    $pdo->prepare("INSERT INTO `roles_permissions` (`roles_id`, `permissions_id`) VALUES
    (1, 1),
    (1, 2),
    (1, 3),
    (1, 4),
    (1, 5),
    (1, 6),
    (1, 7),
    (1, 8),
    (1, 9),
    (1, 10),
    (1, 11),
    (1, 12);")->execute();
    $pdo->prepare("INSERT INTO `transaction_types` (`name`, `description`) VALUES
    ('WITHDRAWAL', 'Зняття з рахунку'),
    ('REFILL', 'Поповнення рахунку'),
    ('TRANSFER', 'Переведення коштів');")->execute();
    $pdo->prepare("INSERT INTO `users` (`username`, `password`, `role_id`, `suspended`) VALUES
    ('".$_POST['username']."', '".hash('sha512', crypt($_POST['password'], '$5$rounds=5000$salt'.hash('md5', $_POST['password']).'endsalt$'))."', 1, 0);")->execute();

    // $pdo->prepare("")->execute();

    header("Location: ./login.php");
    die();
  } else {
    echo('
    Ініціалізація системи. Введіть дані користувача для першого входу, після чого ви зможете повністю використовувати систему.<br />
    Ім\'я користувача має містити від 3 до 16 латинських символів, а пароль має складатися з принаймні 8 будь-яких символів.
    <br />
    <br />
    <form action="init.php" method="post">
      <label for="username">Ім\'я користувача</label>
      <input id="username" type="text" name="username"><br />
      <label for="password">Пароль</label>
      <input id="password" type="password" name="password"><br />
      <button type="submit">Створити</button>
    </form>
    ');
  }

} else {
  die('Database has already been initialized.');
}
?>
