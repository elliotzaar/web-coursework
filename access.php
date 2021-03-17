<?php
include_once('classes/page-builder.php');
include_once('classes/access-rules.php');
include_once('classes/log.php');

$usr_perms = AccessRules::getUsersPermissions($_COOKIE['uid']);

if(!AccessRules::hasPermission('MODIFY_ROLES', $usr_perms)) {
  NoPermissionPage::display('Ролі');
  die();
}

$page = new Page('Ролі');

if(isset($_GET['role'])) {
  $role_id = $_GET['role'];

  if(!AccessRules::roleExists($role_id)) {
    $page->setContent('Помилка: роль з ідентифікатором '.$role_id.' не знайдено.');
  } else {
    $content = '<h2 class="mdl-card__title-text">Налаштування ролі '.AccessRules::getRoleRow($role_id)['name'].'</h2>';
    $content .= '<div class="mdl-card__supporting-text">';
    $content .= 'Кількість користувачів, що мають цю роль: <a id="roleinfo-users-amount" href="./users.php?role='.$role_id.'">'.AccessRules::getRoleUsersAmount($role_id).'</a>';
    $content .= '<div class="mdl-tooltip" data-mdl-for="roleinfo-users-amount">Переглянути</div>';

    $content .= '<br /><br /><table class="mdl-data-table page-table">
    <thead>
    <tr>
    <th class="mdl-data-table__cell--non-numeric">Назва дозволу</th>
    <th class="mdl-data-table__cell--non-numeric">Опис</th>
    <th class="mdl-data-table__cell--non-numeric">Статус</th>
    </tr>
    </thead>
    <tbody>
    ';

    $role_perms_list = AccessRules::getAllPermissionsList($role_id);

    foreach($role_perms_list as $pi) {
      $content .= '<tr>
    <td class="mdl-data-table__cell--non-numeric">'.$pi['name'].'</td>
    <td class="mdl-data-table__cell--non-numeric">'.$pi['description'].'</td>
    <td class="mdl-data-table__cell--non-numeric">
    <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="role-'.$role_id.'-switch-perm-'.$pi['id'].'">
      <input type="checkbox" id="role-'.$role_id.'-switch-perm-'.$pi['id'].'" class="mdl-switch__input role-switch-perms"'.($pi[3] ? ' checked' : '').'>
      <span class="mdl-switch__label"></span>
    </label>
    </td>
    </tr>';
    }

    $content .= '</tbody>
    </table>';

    $content .= '</div>';

    $page->setContent($content);
  }
} else if(isset($_GET['create'])) {
  if(isset($_GET['success'])) {
    $page->setContent('Роль створено.<div class="carditem-border-top"><button class="mdl-button mdl-js-button mdl-button--accent" onclick="location.href=\'access.php?role='.$_GET['success'].'\'">Перейти до налаштування</button></div>');
    $page->create();
  } else {
    if(isset($_POST['createrole-rolename'])) {
      if(strlen($_POST['createrole-rolename']) < 1 || strlen($_POST['createrole-rolename']) > 40 || preg_match('/[^a-z]/', $_POST['createrole-rolename']) || AccessRules::roleNameExists($_POST['createrole-rolename'])) {
        header("Location: ./access.php?create&invalid");
        die();
      } else {
        $tmp_new_roleid = AccessRules::createRole($_POST['createrole-rolename'], $_POST['createrole-roledesc']);
        UsersLog::record($_COOKIE['sid'], UsersLog::CREATE_ROLE, 'Створено роль id'.$tmp_new_roleid.' з назвою '.$_POST['createrole-rolename']);
        header("Location: ./access.php?create&success=".$tmp_new_roleid);
        die();
      }
    }

    $content = '<h2 class="mdl-card__title-text">Створити нову роль</h2>';
    $content .= '<div class="mdl-card__supporting-text">';

    $content .= '<form action="access.php?create" method="post">
    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label'.(isset($_GET['invalid']) ? ' is-invalid' : '').'">
      <input class="mdl-textfield__input" type="text" id="createrole-rolename" name="createrole-rolename" minlength="1" maxlength="40" pattern="-?[a-z]*(\.[a-z]+)?">
      <label class="mdl-textfield__label" for="createrole-rolename">Назва ролі</label>
    </div>
    <br />
    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label'.(isset($_GET['invalid']) ? ' is-invalid' : '').'">
      <textarea class="mdl-textfield__input" type="text" rows="1" id="createrole-roledesc" name="createrole-roledesc" maxlength="256"></textarea>
      <label class="mdl-textfield__label" for="createrole-roledesc">Опис ролі</label>
    </div>
    <br />
    <button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" type="submit">Створити</button>
  </form>';

    $content .= '</div>';
    $page->setContent($content);
  }
} else {
  $roles_list = AccessRules::getRolesList();

  $content = '<div class="carditem-border-bottom"><button onclick="location.href=\'access.php?create\'" class="mdl-button mdl-js-button mdl-button--primary mdl-js-ripple-effect btn-align-right">Створити</button></div>';
  $content .= ' <div class="page-list mdl-list">';
  foreach($roles_list as $r) {
    $content .= '<div class="mdl-list__item mdl-list__item--two-line">
      <span class="mdl-list__item-primary-content">
        <i class="material-icons mdl-list__item-avatar">groups</i>
        <span>'.$r['name'].'</span>
        <span class="mdl-list__item-sub-title">'.$r['description'].'</span>
      </span>
      <button id="roleslist-id-'.$r['id'].'" class="mdl-button mdl-js-button mdl-button--icon mdl-button--accent" onclick="location.href=\'access.php?role='.$r['id'].'\'"><i class="material-icons">settings</i></button>
    </div>';
  }
  $content .= '
  </div>';
  $page->setContent($content);
}

$page->create();
?>
