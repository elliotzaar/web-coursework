<?php
include_once('classes/page-builder.php');
include_once('classes/log.php');
include_once('classes/access-rules.php');
$usr_perms = AccessRules::getUsersPermissions($_COOKIE['uid']);

if(!AccessRules::hasPermission('VIEW_TRANSACTIONS', $usr_perms)) {
  NoPermissionPage::display('Транзакції');
}

$page = new Page('Транзакції');
$page->setContent('');
$page->create();
 ?>
