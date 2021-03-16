<?php
include_once('./classes/user.php');
include_once('./classes/access-rules.php');
include_once('./classes/log.php');

if(!isset($_COOKIE['uid']) || !isset($_COOKIE['token'])) {
  http_response_code(403);
  die('Unauthorized');
}

$sess = Session::checkSession($_COOKIE['uid'], $_COOKIE['token']);
if($sess == false || $_COOKIE['username'] != $sess['username'] || $_COOKIE['sid'] != $sess['session_id']) {
  http_response_code(403);
  die('Unauthorized');
}

$usr_perms = AccessRules::getUsersPermissions($_COOKIE['uid']);

if(isset($_GET['change-role-perm-status'])) {
  if(!AccessRules::hasPermission('MODIFY_ROLES', $usr_perms)) {
    http_response_code(401);
    die('Not enough permissions');
  } else {
    if(AccessRules::roleExists($_GET['role']) && AccessRules::permissionExists($_GET['perm'])) {
      if($_GET['status'] == 'true') {
        AccessRules::addPermission($_GET['role'], $_GET['perm']);
        UsersLog::record($_COOKIE['sid'], UsersLog::GRANT_ROLE_PERMISSION, 'Додано дозвіл '.AccessRules::getPermission($_GET['perm'])['name'].' (id'.$_GET['perm'].') до ролі '.AccessRules::getRole($_GET['role'])['name'].' (id'.$_GET['role'].')');
      } else if($_GET['status'] == 'false') {
        AccessRules::removePermission($_GET['role'], $_GET['perm']);
        UsersLog::record($_COOKIE['sid'], UsersLog::REMOVE_ROLE_PERMISSION, 'Видалено дозвіл '.AccessRules::getPermission($_GET['perm'])['name'].' (id'.$_GET['perm'].') з ролі '.AccessRules::getRole($_GET['role'])['name'].' id'.$_GET['role'].')');
      } else {
        http_response_code(400);
        die('Incorrect status');
      }
    } else {
      http_response_code(400);
      die('Incorrect role or permission');
    }
  }
} else {
  http_response_code(501);
  die('Unknown method');
}
?>
