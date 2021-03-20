<?php
include_once('classes/page-builder.php');
include_once('classes/log.php');
include_once('classes/access-rules.php');
include_once('classes/account.php');
include_once('classes/transaction.php');

$usr_perms = AccessRules::getUsersPermissions($_COOKIE['uid']);

if(!AccessRules::hasPermission('VIEW_TRANSACTIONS', $usr_perms)) {
  NoPermissionPage::display('Транзакції');
}

$page = new Page('Транзакції');

$content = '';

if(AccessRules::hasPermission('CREATE_TRANSACTIONS', $usr_perms)) {
  $content .= '<div class="carditem-border-bottom" style="border-bottom: 0;"><button onclick="location.href=\'new-transaction.php\'" class="mdl-button mdl-js-button mdl-button--raised mdl-button--accent btn-align-right">Нова</button></div>';
}
$content .= '<h2'.(AccessRules::hasPermission('CREATE_TRANSACTIONS', $usr_perms) ? ' style="margin-top: 8px"' : '').' class="mdl-card__title-text">Пошук транзакцій</h2>';
$content .= '<div class="mdl-card__supporting-text">';

$content .= '<form action="transactions.php" method="get">';
$content .= '
  <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
    <input class="mdl-textfield__input" type="text" name="uuid" id="transaction-search-uuid" pattern="-?[A-Za-z0-9]*([A-Za-z0-9]+)?" value="'.(isset($_GET['uuid']) ? $_GET['uuid'] : '').'">
    <label class="mdl-textfield__label" for="transaction-search-uuid">Унікальний ідентифікатор транзакції</label>
  </div>
  <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
    <input class="mdl-textfield__input" type="text" name="account" id="transaction-search-account" value="'.(isset($_GET['account']) ? $_GET['account'] : '').'">
    <label class="mdl-textfield__label" for="transaction-search-account">Номер рахунку</label>
  </div>
  <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
    <input class="mdl-textfield__input" type="text" name="amount" id="transaction-search-amount" pattern="-?[0-9]*(\.[0-9]+)?" value="'.(isset($_GET['amount']) ? $_GET['amount'] : '').'">
    <label class="mdl-textfield__label" for="transaction-search-amount">Сума транзакції</label>
  </div>';

$content .= '<div style="display: flex;"><select id="transaction-search-currency-selector" class="mdl-textfield__input" name="currency">
  <option value="0">Валюта</option>';
$currency_list = Currency::getCurrenciesList();
foreach($currency_list as $r) {
  $content .= '<option value='.$r['id'].''.((isset($_GET['currency']) && $r['id'] == $_GET['currency']) ? ' selected' : '').'>'.$r['code'].' - '.$r['name'].'</option>';
}
$content .= '</select>';

$content .= '<select id="transaction-search-type-selector" class="mdl-textfield__input" name="transaction-type">
  <option value="0">Тип транзакції</option>';
$currency_list = Transactions::getTransactionsTypesList();
foreach($currency_list as $r) {
  $content .= '<option value='.$r['id'].''.((isset($_GET['transaction-type']) && $r['id'] == $_GET['transaction-type']) ? ' selected' : '').'>'.$r['name'].'</option>';
}
$content .= '</select>';

$content .= '<select id="transaction-search-type-selector" class="mdl-textfield__input" name="status">
    <option value="0"'.((!isset($_GET['status']) || $_GET['status'] == '' || $_GET['status'] == '0') ? ' selected' : '').'>Статус операції</option>
    <option value="1"'.((isset($_GET['status']) && $_GET['status'] == '1') ? ' selected' : '').'>HOLD</option>
    <option value="2"'.((isset($_GET['status']) && $_GET['status'] == '2') ? ' selected' : '').'>AUTHORIZED</option>
    <option value="3"'.((isset($_GET['status']) && $_GET['status'] == '3') ? ' selected' : '').'>CANCELLED</option>
    <option value="4"'.((isset($_GET['status']) && $_GET['status'] == '4') ? ' selected' : '').'>DELETED</option>
  </select></div>';

$content .= '  <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
    <input class="mdl-textfield__input" type="text" name="opid" id="transaction-search-opid" pattern="-?[0-9]*([0-9]+)?" value="'.(isset($_GET['opid']) ? $_GET['opid'] : '').'">
    <label class="mdl-textfield__label" for="transaction-search-opid">Ідентифікатор оператора</label>
  </div>
  <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
    <input class="mdl-textfield__input" type="text" name="description" id="transaction-search-description" value="'.(isset($_GET['description']) ? $_GET['description'] : '').'">
    <label class="mdl-textfield__label" for="transaction-search-description">Призначення</label>
  </div>
  <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
    <label class="mdl-textfield__label" for="transaction-search-date">Дата транзакції</label>
    <input class="mdl-textfield__input" type="date" placeholder="" name="date" id="transaction-search-date" value="'.(isset($_GET['date']) ? $_GET['date'] : '').'">
  </div>';

$content .= '<br /><button id="transaction-search-btn" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" type="submit">Виконати пошук</button></form></div>';

if(isset($_GET['uuid']) || isset($_GET['account']) || isset($_GET['amount']) || isset($_GET['currency']) || isset($_GET['transaction-type']) || isset($_GET['status']) || isset($_GET['opid']) || isset($_GET['description']) || isset($_GET['date'])) {
  $content .= '<table class="mdl-data-table page-table">
  <thead>
  <tr>
  <th class="mdl-data-table__cell--non-numeric">Номер рахунку</th>
  <th class="mdl-data-table__cell">Сума</th>
  <th class="mdl-data-table__cell--non-numeric">Тип транзакції</th>
  <th class="mdl-data-table__cell--non-numeric">Опис</th>
  <th class="mdl-data-table__cell--non-numeric">Статус</th>
  <th class="mdl-data-table__cell">Час створення</th>
  <th class="mdl-data-table__cell">Дії</th>
  </tr>
  </thead>
  <tbody>';
}

$page->setContent($content);
$page->create();
 ?>
