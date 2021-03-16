<?php
class PageBuilder {

  public static function setupPage($title) {
    if(isset($_COOKIE['uid']) && isset($_COOKIE['token']) && isset($_COOKIE['username']) && isset($_COOKIE['sid'])) {
      setcookie("uid", $_COOKIE['uid'], time() + 60 * 30);
      setcookie("username", $_COOKIE['username'], time() + 60 * 30);
      setcookie("token", $_COOKIE['token'], time() + 60 * 30);
      setcookie("sid", $_COOKIE['sid'], time() + 60 * 30);
    }

    echo('<!doctype html>
<html>
<head>
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-blue.min.css" />
  <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
  <title>'.$title.'</title>
  <link rel="stylesheet" href="./style.css">
</head>
<body>
');
  }

  public static function endPage() {
    echo('
  <script src="script.js"></script>
</body>
</html>');
  }
}

class Page {

  protected $title;
  protected $content;
  protected $requirelogin;

  public function __construct($title, $requirelogin = true) {
    $this->title = $title;
    $this->requirelogin = $requirelogin;
  }

  public function setTitle($str) {
    $this->title = $str;
  }

  public function setContent($str) {
    $this->content = $str;
  }

  public function create() {
    if($this->requirelogin) {
      include_once('./classes/user.php');
      $sess = Session::checkSession($_COOKIE['uid'], $_COOKIE['token']);
      if($sess == false || $_COOKIE['username'] != $sess['username'] || $_COOKIE['sid'] != $sess['session_id']) {
        header("Location: ./login.php");
        die();
      }
    }

    include_once('./classes/access-rules.php');
    $usr_perms = AccessRules::getUsersPermissions($_COOKIE['uid']);

    PageBuilder::setupPage($this->title);
    echo('<div class="mdl-layout mdl-js-layout mdl-layout--fixed-drawer mdl-layout--fixed-header">
    <header class="mdl-layout__header">
      <div class="mdl-layout__header-row">
        <div class="mdl-layout-spacer"></div>
        <button id="menu-lower-right" class="mdl-button mdl-js-button mdl-button--icon"><i class="material-icons">more_vert</i></button>
        <ul class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect" for="menu-lower-right">
          <li disabled class="mdl-menu__item">Ви увійшли як '.$_COOKIE['username'].'</li>
          <a style="text-decoration: none" href="logout.php"><li class="mdl-menu__item">Вийти</li></a>
        </ul>
      </div>
    </header>
    <div class="mdl-layout__drawer">
      <span class="mdl-layout-title">'.$this->title.'</span>
      <nav class="mdl-navigation">
        <a class="mdl-navigation__link" href="index.php"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">home</i>Головна</a>
        <a class="mdl-navigation__link" href="accounts.php"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">account_balance_wallet</i>Рахунки</a>
        <a class="mdl-navigation__link" href="transactions.php"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">payments</i>Транзакції</a>');
        if(AccessRules::hasPermission('VIEW_USERS', $usr_perms)){
          echo('        <a class="mdl-navigation__link" href="users.php"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">group</i>Користувачі</a>');
        }
        if(AccessRules::hasPermission('MODIFY_ROLES', $usr_perms)){
          echo('        <a class="mdl-navigation__link" href="access.php"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">style</i>Ролі</a>');
        }
      echo('
      </nav>
    </div>
    <main class="mdl-layout__content mdl-color--grey-100">
      <div class="mdl-grid content-container">
        <div class="mdl-color--white mdl-shadow--2dp mdl-cell mdl-cell--12-col mdl-grid">
          '.$this->content.'
        </div>
      </div>
    </main>
  </div>');
    PageBuilder::endPage();
  }
}

class NoPermissionPage {

  public static function display($title) {
    $page = new Page($title);
    $page->setContent('У вас недостатньо прав доступу для перегляду цієї сторінки. <br />Якщо ви вважаєте це помилкою, будь ласка, зверніться до адміністратора системи.');
    $page->create();
    die();
  }
}
?>
