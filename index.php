<?php
include_once('classes/page-builder.php');

$page = new Page('Головна');
$page->setContent('Система електронного ведення рахунків.<br />
Версія 1.0<br /><br />
Оберіть дії у меню зліва, щоб розпочати роботу з системою.
<div class="mdl-card__supporting-text">
<h2 class="mdl-card__title-text">Швидкий перехід до дії</h2>
<a href="./accounts.php?create">Створити рахунок</a><br />
<a href="./new-transaction.php">Створити транзакцію</a><br />
<a href="./transactions.php?uuid=&account=&amount=&currency=0&transaction-type=0&status=HOLD&opid=&description=&from-date=&to-date=">Переглянути неавторизовані операції</a>
</div>');
$page->create();
 ?>
