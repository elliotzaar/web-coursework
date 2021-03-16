<?php
include_once('classes/page-builder.php');
include_once('classes/user.php');
include_once('classes/log.php');
include_once('classes/access-rules.php');
$usr_perms = AccessRules::getUsersPermissions($_COOKIE['uid']);

if(!AccessRules::hasPermission('VIEW_USERS', $usr_perms)) {
  NoPermissionPage::display('Користувачі');
}

$page = new Page('Користувачі');

if(isset($_GET['view'])) {
  $view_id = $_GET['view'];

  if(!UserAccount::userExists($view_id)) {
    $page->setContent('Помилка: користувача з ідентифікатором '.$view_id.' не знайдено.');
  } else {
    $usr_status = UserAccount::isSuspended($view_id);
    if($usr_status) {
      $usr_status = '<font color="#b71c1c">заблокований</font>';
    } else {
      $usr_status = 'не заблокований';
    }

    $all_sess = Session::countAllSessions($view_id);

    $tmp_usr_role = AccessRules::getRole($view_id);

    if($tmp_usr_role == -1) {
      $tmp_usr_role = 'не присвоєно';
    } else {
      $tmp_usr_role = AccessRules::getRole($view_id)['name'];
    }

    $content = '<h2 class="mdl-card__title-text">Інформація про користувача '.UserAccount::getUsername($view_id).'</h2>
    <div class="mdl-card__supporting-text">';
    $content .= '<p>Ідентифікатор користувача: '.$view_id.'<br />';
    $content .= 'Статус: '.$usr_status.'<br />';
    $content .= 'Роль: '.$tmp_usr_role.'<br />';
    $content .= '</p>';
    $content .= '<p>Кількість сесій: '.$all_sess.' (з них активних - '.Session::countSessions($view_id).')</p>';

    if($all_sess > 0) {
      $content .= '<div class="page-list mdl-list scrollable-list">';

      $sess_list = Session::getAllSessions($view_id);

      foreach ($sess_list as $k) {
        $tmp_i_class_list = '';
        if(!Session::isActive($k['id'])) {
          $tmp_i_class_list = ' non-active';
        } else if($k['id'] == $_COOKIE['sid']) {
          $tmp_i_class_list = ' selected';
        }

        $content .= '<div class="mdl-list__item'.$tmp_i_class_list.'">';
        $content .= '<span class="mdl-list__item-primary-content">';
        $content .= '<span>ID: '.$k['id'].', IP: '.($k['address'] == '::1' ? 'local' : $k['address']).', час створення: '.$k['create_time'].'</span>';
        $content .= '</span>';
        $content .= '<a id="sesslist-view-elem-'.$k['id'].'" class="mdl-button mdl-js-button mdl-button--icon mdl-button--colored mdl-button--accent" href="users.php?view-session-details='.$k['id'].'"><span class="material-icons">history</span></a>';
        $content .= '<div class="mdl-tooltip" for="sesslist-view-elem-'.$k['id'].'">Переглянути дії</div>';
        if(Session::isActive($k['id'])) {
          $content .= '<button class="mdl-button mdl-js-button mdl-button--primary" onclick="location.href=\'users.php?stop-session='.$k['id'].'&rv='.$view_id.'\'">Зупинити</button>';
        }
        $content .= '</div>';
      }

      $content .= '</div>';
    }

    $content .= '</div>';
    $page->setContent($content);
  }
} else if(isset($_GET['stop-session'])) {
  if(!AccessRules::hasPermission('STOP_SESSIONS', $usr_perms)) {
    NoPermissionPage::display('Користувачі');
  }

  $sid = $_GET['stop-session'];
  if(Session::isActive($sid)) {
    Session::destroySessionById($sid);
    UsersLog::record($_COOKIE['sid'], UsersLog::STOP_SESSION, 'Зупинено сесію ID '.$sid);
  }

  if(isset($_GET['rv'])) {
    header("Location: ./users.php?view=".$_GET['rv']);
    die();
  }
} else if(isset($_GET['suspend'])) {
  $s_uid = $_GET['suspend'];
  if(!UserAccount::userExists($s_uid)) {
    $page->setContent('Користувача з таким ID не існує.');
  } else if(UserAccount::isSuspended($s_uid) == 1) {
    $page->setContent('Користувача вже було заблоковано.');
  } else {
    if(isset($_GET['confirmed'])) {
      UserAccount::setSuspended($s_uid, 1);
      UsersLog::record($_COOKIE['sid'], UsersLog::SUSPEND_ACCOUNT, 'Заблоковано користувача ID '.$s_uid);
      header("Location: ./users.php");
      die();
    } else {
      $content = 'Ви впевнені що хочете заблокувати користувача '.UserAccount::getUsername($s_uid).' (ID '.$s_uid.')?<br />Доступ користувача буде одразу зупинено та користувач більше не зможе увійти до системи до розблокування.';
      $content .= '<div class="mdl-card__actions"><button class="mdl-button mdl-js-button mdl-js-ripple-effect" onclick="location.href=\'users.php?suspend='.$s_uid.'&confirmed\'">Підтвердити</button><button class="mdl-button mdl-js-button mdl-button--raised mdl-button--accent" onclick="location.href=\'users.php\'">Відхилити</button></div>';
      $page->setContent($content);
    }
  }
} else if(isset($_GET['unsuspend'])) {
  $s_uid = $_GET['unsuspend'];
  if(!UserAccount::userExists($s_uid)) {
    $page->setContent('Користувача з таким ID не існує.');
  } else if(UserAccount::isSuspended($s_uid) == 0) {
    $page->setContent('Користувач не заблокований.');
  } else {
    if(isset($_GET['confirmed'])) {
      UserAccount::setSuspended($s_uid, 0);
      UsersLog::record($_COOKIE['sid'], UsersLog::UNSUSPEND_ACCOUNT, 'Поновлено доступ користувача ID '.$s_uid);
      header("Location: ./users.php");
      die();
    } else {
      $content = 'Ви впевнені що хочете розблокувати користувача '.UserAccount::getUsername($s_uid).' (ID '.$s_uid.')?<br />Доступ користувача до системи буде відновлено.';
      $content .= '<div class="mdl-card__actions"><button class="mdl-button mdl-js-button mdl-js-ripple-effect" onclick="location.href=\'users.php?unsuspend='.$s_uid.'&confirmed\'">Підтвердити</button><button class="mdl-button mdl-js-button mdl-button--raised mdl-button--accent" onclick="location.href=\'users.php\'">Відхилити</button></div>';
      $page->setContent($content);
    }
  }
} else if(isset($_GET['view-session-details'])) {
  $tmp_sid = $_GET['view-session-details'];
  if(!Session::sessionExists($tmp_sid)) {
    $page->setContent('Сесії з таким ID не існує.');
  } else {
    $sess_info = Session::getSession($tmp_sid);

    $sess_status = !Session::isActive($tmp_sid);
    if($sess_status) {
      $sess_status = '<font color="#b71c1c">зупинено</font>';
    } else {
      $sess_status = 'активна';
    }

    $content = '<h2 class="mdl-card__title-text">Деталі сесії ID '.$tmp_sid.' користувача '.UserAccount::getUsername($sess_info['users_id']).'</h2>';
    $content .= '<div class="mdl-card__supporting-text">';
    $content .= '<p>Ідентифікатор користувача: '.$sess_info['users_id'].'<br />';
    $content .= 'Статус: '.$sess_status.'<br />';
    $content .= 'Дата та час створення: '.$sess_info['create_time'];
    $content .= '</p>';

    $sess_actions = UsersLog::getSessionActions($tmp_sid);

    if(count($sess_actions) == 0) {
      $content .= 'Немає записів дій у цій сесії.';
    } else {
      $content .= '<table class="mdl-data-table page-table">
  <thead>
    <tr>
      <th class="mdl-data-table__cell--non-numeric">Дія</th>
      <th class="mdl-data-table__cell--non-numeric">Опис</th>
      <th>Дата і час</th>
    </tr>
  </thead>
  <tbody>
  ';

      foreach($sess_actions as $si) {
        $content .= '<tr>
      <td class="mdl-data-table__cell--non-numeric">'.$si['action'].'</td>
      <td class="mdl-data-table__cell--non-numeric">'.$si['action_description'].'</td>
      <td>'.$si['time'].'</td>
    </tr>';
      }

      $content .= '</tbody>
</table>';
    }

    $content .= '</div>';
    $page->setContent($content);
  }
} else if(isset($_GET['create'])) {
  if(!AccessRules::hasPermission('CREATE_USERS', $usr_perms)) {
    NoPermissionPage::display('Користувачі');
  }

  if(isset($_GET['success'])) {
    $page->setContent('Користувача створено.<div class="carditem-border-top"><button class="mdl-button mdl-js-button mdl-button--accent" onclick="location.href=\'users.php?view='.$_GET['success'].'\'">Перейти до перегляду</button></div>');
    $page->create();
  } else {
    if(isset($_POST['createusr-username']) && isset($_POST['createusr-password'])) {
      if(strlen($_POST['createusr-username']) < 3 || strlen($_POST['createusr-username']) > 16 || preg_match('/[^a-z]/', $_POST['createusr-username']) || UserAccount::usernameExists($_POST['createusr-username'])) {
        header("Location: ./users.php?create&invalidu");
        die();
      } else if(strlen($_POST['createusr-password']) < 8 || strlen($_POST['createusr-password']) > 512) {
        header("Location: ./users.php?create&invalidp");
        die();
      } else {
        $tmp_new_uid = UserAccount::createUser($_POST['createusr-username'], $_POST['createusr-password']);
        UsersLog::record($_COOKIE['sid'], UsersLog::CREATE_USER, 'Створено користувача '.$tmp_new_uid.' з ім\'ям '.$_POST['createusr-username']);
        header("Location: ./users.php?create&success=".$tmp_new_uid);
        die();
      }
    }

    $content = '<h2 class="mdl-card__title-text">Додати нового користувача</h2>';
    $content .= '<div class="mdl-card__supporting-text">';

    $content .= '<form action="users.php?create" method="post">
    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label'.(isset($_GET['invalidu']) ? ' is-invalid' : '').'">
      <input class="mdl-textfield__input" type="text" id="createusr-username" name="createusr-username" minlength="3" maxlength="16" pattern="-?[a-z]*(\.[a-z]+)?">
      <label class="mdl-textfield__label" for="createusr-username">Ім\'я користувача</label>
    </div>
    <br />
    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label'.(isset($_GET['invalidp']) ? ' is-invalid' : '').'">
      <input class="mdl-textfield__input" type="password" id="createusr-password" name="createusr-password" minlength="8">
      <label class="mdl-textfield__label" for="createusr-password">Пароль</label>
      <span class="mdl-textfield__error">Довжина паролю має бути не менше 8 символів!</span>
    </div>
    <br />
    <button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" type="submit">Створити</button>
  </form>';

    $content .= '</div>';

    $page->setContent($content);
    $page->create();
  }
} else {
  $users_list = array();

  if(isset($_GET['role'])) {
    $users_list = AccessRules::getRoleUsersList($_GET['role']);
  } else {
    $users_list = UserAccount::getUsersList();
  }

  $content = '';

  if(AccessRules::hasPermission('CREATE_USERS', $usr_perms)) {
    $content .= '<div class="carditem-border-bottom"><button onclick="location.href=\'users.php?create\'" class="mdl-button mdl-js-button mdl-button--primary mdl-js-ripple-effect btn-align-right">Створити</button></div>';
  }

  $content .= ' <div class="page-list mdl-list">';
  foreach($users_list as $u) {
    $tmp_active_sess_amnt = Session::countSessions($u['uid']);
    $tmp_usr_sess_txt = '';
    $tmp_usr_role = AccessRules::getRole($u['uid']);

    if($tmp_usr_role == -1) {
      $tmp_usr_role = 'Роль відсутня';
    } else {
      $tmp_usr_role = 'Роль '.AccessRules::getRole($u['uid'])['name'];
    }

    if($tmp_active_sess_amnt > 0) {
      $tmp_usr_sess_txt = $tmp_usr_role.', активних сесій - '.$tmp_active_sess_amnt.'. Останній вхід '.Session::getLastSession($u['uid'])['create_time'];
    } else {
      $tmp_usr_sess_txt = $tmp_usr_role.', немає активних сесій';
    }

    $tmp_us_btn_type = ['block', 'Заблокувати', 'suspend'];
    if($u['suspended'] == 1) {
      $tmp_us_btn_type = ['how_to_reg', 'Розблокувати', 'unsuspend'];
    }

    $content .= '<div class="mdl-list__item mdl-list__item--two-line">
      <span class="mdl-list__item-primary-content">
        <i class="material-icons mdl-list__item-avatar">person</i>
        <span>'.$u['username'].'</span>
        <span class="mdl-list__item-sub-title">'.$tmp_usr_sess_txt.'</span>
      </span>
      <button id="usrslist-det-elem-'.$u['uid'].'" class="mdl-button mdl-js-button mdl-button--icon mdl-button--accent" onclick="location.href=\'users.php?view='.$u['uid'].'\'"><i class="material-icons">visibility</i></button>
      <div class="mdl-tooltip" for="usrslist-det-elem-'.$u['uid'].'">Переглянути детальну інформацію</div>
      <div style="width: 10px"></div>
      <button id="usrslist-block-elem-'.$u['uid'].'" class="mdl-button mdl-js-button mdl-button--icon mdl-button--colored"'.((AccessRules::hasPermission('SUSPEND_USERS', $usr_perms)) ? '' : 'disabled').' onclick="location.href=\'users.php?'.$tmp_us_btn_type[2].'='.$u['uid'].'\'"><i class="material-icons">'.$tmp_us_btn_type[0].'</i></button>
      <div class="mdl-tooltip" for="usrslist-block-elem-'.$u['uid'].'">'.$tmp_us_btn_type[1].'<br />користувача</div>
    </div>';
  }
  $content .= '
  </div>';
  $page->setContent($content);
}

$page->create();
?>
