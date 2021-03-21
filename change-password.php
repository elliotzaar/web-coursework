<?php
include_once('classes/page-builder.php');
include_once('classes/user.php');
include_once('classes/log.php');

$page = new Page('Змінити пароль');

if(isset($_POST['current-password']) && isset($_POST['new-password'])) {
  $cpr = UserAccount::changePassword($_COOKIE['uid'], $_COOKIE['username'], $_POST['current-password'], $_POST['new-password']);

  if($cpr == -1) {
    UsersLog::record($_COOKIE['sid'], UsersLog::PASS_CHANGE, 'Невдала спроба зміни паролю');
    header("Location: ./change-password.php?invalid");
    die();
  } else {
    UsersLog::record($_COOKIE['sid'], UsersLog::PASS_CHANGE, 'Змінено пароль');
    header("Location: ./change-password.php?success");
    die();
  }
}

$content = '';

if(isset($_GET['success'])) {
  $page->setContent('Пароль користувача змінено');
  $page->create();
} else {
  if(isset($_GET['invalid'])) {
    $content .= '<div>Неправильно вказано дані.</div>';
  }
  $content .= '<div><form action="change-password.php" method="post">
  <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
    <input class="mdl-textfield__input" type="password" id="changeusrpassword" name="current-password" minlength="8">
    <label class="mdl-textfield__label" for="changeusrpassword">Поточний пароль</label>
  </div>
  <br />
  <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
    <input onchange="processPasswordChange()" class="mdl-textfield__input" type="password" id="changeusrnewpassword" name="new-password" minlength="8">
    <label class="mdl-textfield__label" for="changeusrnewpassword">Новий пароль</label>
    <span class="mdl-textfield__error">Довжина паролю має бути не менше 8 символів!</span>
  </div>
  <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
    <input onchange="processPasswordChange()" class="mdl-textfield__input" type="password" id="changeusrnewpasswordc">
    <label class="mdl-textfield__label" for="changeusrnewpasswordc">Підтвердження нового пароля</label>
  </div>
  <br />
  <button id="changeusrpassword-btn" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" type="submit" disabled>Змінити</button>
  </form></div>';

  $page->setContent($content);
  $page->create();
}
?>
