<?php
include_once('classes/page-builder.php');
include_once('classes/access-rules.php');
include_once('classes/log.php');
include_once('classes/account.php');

$usr_perms = AccessRules::getUsersPermissions($_COOKIE['uid']);

if(!AccessRules::hasPermission('VIEW_ACCOUNTS', $usr_perms)) {
  NoPermissionPage::display('Рахунки');
  die();
}

$page = new Page('Рахунки');

$content = '';

if(AccessRules::hasPermission('CREATE_ACCOUNTS', $usr_perms)) {
  $content .= '<div class="carditem-border-bottom"><button onclick="location.href=\'accounts.php?create\'" class="mdl-button mdl-js-button mdl-button--primary mdl-js-ripple-effect btn-align-right">Створити</button></div>';
}

$content .= '<h2'.(AccessRules::hasPermission('CREATE_ACCOUNTS', $usr_perms) ? ' style="margin-top: 8px"' : '').' class="mdl-card__title-text">Пошук рахунків</h2>';
$content .= '<div class="mdl-card__supporting-text">';

$content .= '<form action="accounts.php" method="get">
  <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
    <input class="mdl-textfield__input" type="text" name="accname" id="accnt-search-name" value="'.(isset($_GET['accname']) ? $_GET['accname'] : '').'">
    <label class="mdl-textfield__label" for="accnt-search-name">Найменування</label>
  </div>
  <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
    <input class="mdl-textfield__input" type="text" name="accnum" id="accnt-search-number" pattern="-?[A-Z0-9]*(\.[A-Z0-9]+)?" value="'.(isset($_GET['accnum']) ? $_GET['accnum'] : '').'">
    <label class="mdl-textfield__label" for="accnt-search-number">Номер</label>
  </div>
  <select id="accnt-search-currency-selector" class="mdl-textfield__input" name="acccurr">
  <option value="0">Валюта</option>';
$currency_list = Currency::getCurrenciesList();
foreach($currency_list as $r) {
  $content .= '<option value='.$r['id'].''.((isset($_GET['acccurr']) && $r['id'] == $_GET['acccurr']) ? ' selected' : '').'>'.$r['code'].' - '.$r['name'].'</option>';
}
$content .= '</select>';
$content .= '<br /><button id="accnt-search-btn" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" type="submit">Виконати пошук</button>';
$content .= '</form></div>';

if(isset($_GET['accname']) && isset($_GET['accnum']) && isset($_GET['acccurr']) && ($_GET['accname'] != '' || $_GET['accnum'] != '' || $_GET['acccurr'] != 0)) {
  $content .= '<table class="mdl-data-table page-table">
  <thead>
  <tr>
  <th class="mdl-data-table__cell--non-numeric">Найменування</th>
  <th class="mdl-data-table__cell">Номер</th>
  <th class="mdl-data-table__cell">Баланс</th>
  <th class="mdl-data-table__cell--non-numeric">Валюта</th>
  <th class="mdl-data-table__cell">Час створення</th>
  </tr>
  </thead>
  <tbody>';

  $accs_search_results = Accounts::findAccounts($_GET['accname'], $_GET['accnum'], $_GET['acccurr']);

  foreach($accs_search_results as $r) {
    $content .= '<td class="mdl-data-table__cell--non-numeric">'.$r['name'].'</td>
    <td class="mdl-data-table__cell">'.$r['number'].'</td>
    <td class="mdl-data-table__cell">'.$r['balance'].'</td>
    <td class="mdl-data-table__cell--non-numeric">'.Currency::getCurrencyRow($r['currency_id'])['code'].'</td>
    <td class="mdl-data-table__cell">'.$r['create_time'].'</td>';
  }

  $content .= '</tbody></table>';
}

$page->setContent($content);
$page->create();
 ?>
