<?php
include_once('./classes/user.php');

$errs = [];

if(isset($_POST['username']) && isset($_POST['password'])) {
  if(UserAccount::checkCredentials($_POST['username'], $_POST['password']) == false) {
    $uid = UserAccount::getUserId($_POST['username']);
    if($uid == -1 || UserAccount::isSuspended($uid)) {
      array_push($errs, 'username');
    } else {
      array_push($errs, 'password');
    }
  } else {
    $sess = Session::createSession($_POST['username'], $_POST['password'], $_SERVER['REMOTE_ADDR']);
    if($sess == -1) {
      array_push($errs, 'username');
    } else {
      setcookie("uid", $sess['uid'], time() + 60 * 30);
      setcookie("username", $sess['username'], time() + 60 * 30);
      setcookie("token", $sess['token'], time() + 60 * 30);
      setcookie("sid", $sess['sid'], time() + 60 * 30);
      header("Location: ./index.php");
      die();
    }
  }
}

include_once('classes/page-builder.php');

PageBuilder::setupPage('Увійти в систему');
?>
  <div class="mdl-layout mdl-js-layout">
    <section class="container">
      <div class="show-front">
        <figure class="front">
          <div class="mdl-card mdl-shadow--4dp align-page-center">
            <div class="mdl-card__title mdl-color--primary mdl-color-text--white relative">
              <h2 class="mdl-card__title-text">Увійдіть в систему</h2>
            </div>

            <form action="login.php" method="post">
              <div class="mdl-card__supporting-text">
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label<?php if(in_array('username', $errs)) {echo(' is-invalid');}  ?>">
                  <input name="username" class="mdl-textfield__input" id="username" />
                  <label class="mdl-textfield__label" for="username">Ім'я користувача</label>
                </div>
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label<?php if(in_array('password', $errs)) {echo(' is-invalid');}  ?>">
                  <input name="password" class="mdl-textfield__input" type="password" id="password" />
                  <label class="mdl-textfield__label" for="password">Пароль</label>
                </div>
              </div>

              <div class="mdl-card__actions mdl-card--border">
                <div class="mdl-grid">
                  <button class="mdl-cell mdl-cell--12-col mdl-button mdl-button--raised mdl-button--colored mdl-js-button mdl-js-ripple-effect mdl-color-text--white">Увійти</button>
                </div>
              </div>
            </form>
          </div>
        </figure>
      </div>
    </section>
  </div><?php PageBuilder::endPage(); ?>
