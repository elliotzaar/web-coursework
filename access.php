<?php
include_once('classes/page-builder.php');
include_once('classes/access-rules.php');

$usr_perms = AccessRules::getUsersPermissions($_COOKIE['uid']);

if(!AccessRules::hasPermission('MODIFY_ROLES', $usr_perms)) {
  NoPermissionPage::display('Ролі');
}

$page = new Page('Ролі');

if(isset($_GET['role'])) {
  $role_id = $_GET['role'];

  if(!AccessRules::roleExists($role_id)) {
    $page->setContent('Помилка: роль з ідентифікатором '.$role_id.' не знайдено.');
  } else {
    $content = '<h2 class="mdl-card__title-text">Налаштування ролі '.AccessRules::getRole($role_id)['name'].'</h2>';
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
} else {
  $roles_list = AccessRules::getRolesList();

  $content = ' <div class="page-list mdl-list">';
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
