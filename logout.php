<?php
include_once('classes/user.php');

Session::destroySession($_COOKIE['uid'], $_COOKIE['token']);

setcookie("uid", '', 0);
setcookie("username", '', 0);
setcookie("token", '', 0);
setcookie("sid", '', 0);

header("Location: ./login.php");
die();
?>
