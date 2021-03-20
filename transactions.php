<?php
include_once('classes/page-builder.php');
include_once('classes/log.php');
include_once('classes/access-rules.php');
include_once('classes/account.php');
include_once('classes/transaction.php');
include_once('classes/user.php');

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
    <input class="mdl-textfield__input" type="text" name="uuid" id="transaction-search-uuid" value="'.(isset($_GET['uuid']) ? $_GET['uuid'] : '').'">
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
    <option value="HOLD"'.((isset($_GET['status']) && $_GET['status'] == 'HOLD') ? ' selected' : '').'>HOLD</option>
    <option value="AUTHORIZED"'.((isset($_GET['status']) && $_GET['status'] == 'AUTHORIZED') ? ' selected' : '').'>AUTHORIZED</option>
    <option value="CANCELLED"'.((isset($_GET['status']) && $_GET['status'] == 'CANCELLED') ? ' selected' : '').'>CANCELLED</option>
    <option value="DELETED"'.((isset($_GET['status']) && $_GET['status'] == 'DELETED') ? ' selected' : '').'>DELETED</option>
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

if(isset($_GET['uuid']) && isset($_GET['account']) && isset($_GET['amount']) && isset($_GET['currency']) && isset($_GET['transaction-type']) && isset($_GET['status']) && isset($_GET['opid']) && isset($_GET['description']) && isset($_GET['date'])) {
  $transactions_search_results = Transactions::searchTransactions($_GET['uuid'], $_GET['account'], $_GET['amount'], $_GET['currency'], $_GET['transaction-type'], $_GET['status'], $_GET['opid'], $_GET['description'], $_GET['date']);
  $content .= '<br />Знайдено транзакцій: '.count($transactions_search_results).'<br /><br />';
  $content .= '<table class="mdl-data-table page-table">
  <thead>
  <tr>
  <th class="mdl-data-table__cell">Дебет</th>
  <th class="mdl-data-table__cell">Кредит</th>
  <th class="mdl-data-table__cell">Сума</th>
  <th class="mdl-data-table__cell--non-numeric">Тип транзакції</th>
  <th class="mdl-data-table__cell--non-numeric">Опис</th>
  <th class="mdl-data-table__cell--non-numeric">Статус</th>
  <th class="mdl-data-table__cell">Час створення</th>
  <th class="mdl-data-table__cell">Дії</th>
  </tr>
  </thead>
  <tbody>';

  foreach($transactions_search_results as $sr) {
    $content .= '<tr>';
    $content .= '<td class="mdl-data-table__cell">'.Accounts::getAccountRow($sr['account_id'])['number'].'</td>
      <td class="mdl-data-table__cell">'.Accounts::getAccountRow($sr['target_account_id'])['number'].'</td>
      <td class="mdl-data-table__cell">'.(($sr['transaction_type_id'] == 1 || $sr['transaction_type_id'] == 3) && ($_GET['account'] == '' || Accounts::getAccountRow($sr['account_id'])['number'] == $_GET['account']) ? '-' : '').$sr['amount'].'</td>
      <td class="mdl-data-table__cell--non-numeric">'.Transactions::getTransactionType($sr['transaction_type_id']).'</td>
      <td class="mdl-data-table__cell--non-numeric">'.(strlen($sr['description']) > 15 ? substr($sr['description'],0,12)."..." : $sr['description']).'</td>
      <td class="mdl-data-table__cell--non-numeric">'.$sr['status'].'</td>
      <td class="mdl-data-table__cell">'.$sr['create_time'].'</td>
      <td class="mdl-data-table__cell">';
        if($sr['status'] == 'HOLD' && ((Session::getSession($sr['creator_session_id'])['users_id'] != $_COOKIE['uid'] && AccessRules::hasPermission('AUTH_TRANSACTIONS', $usr_perms)) || AccessRules::hasPermission('AUTH_SELFTRANSACTIONS', $usr_perms))) {
          $content .= '<button onclick="location.href=\'authorize-transaction.php?id='.$sr['id'].'\'" class="mdl-button mdl-js-button mdl-button--icon mdl-button--colored" id="tr-search-auth-item-'.$sr['id'].'"><i class="material-icons">flaky</i></button><div class="mdl-tooltip" data-mdl-for="tr-search-auth-item-'.$sr['id'].'">Авторизація / відхилення</div>';
        }
        if(AccessRules::hasPermission('ROLLBACK_TRANSACTIONS', $usr_perms) || Session::getSession($sr['creator_session_id'])['users_id'] == $_COOKIE['uid']) {
          $content .= '<button class="mdl-button mdl-js-button mdl-button--icon mdl-button--colored" id="tr-search-del-item-'.$sr['id'].'"><i class="material-icons">restore</i></button><div class="mdl-tooltip" data-mdl-for="tr-search-del-item-'.$sr['id'].'">Видалити</div>';
        }

    $content .= '<button class="mdl-button mdl-js-button mdl-button--icon mdl-button--colored"><i class="material-icons" id="tr-search-receipt-item-'.$sr['id'].'">receipt_long</i></button><div class="mdl-tooltip" data-mdl-for="tr-search-receipt-item-'.$sr['id'].'">Переглянути квитанцію</div></td></tr>';
  }

  $content .= '</tbody></table>';

  if(count($transactions_search_results) > 0) {
    $content .= '<br /><button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored">Сформувати виписку</button>';
  }
}

$page->setContent($content);
$page->create();
 ?>
