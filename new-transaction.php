<?php
include_once('classes/page-builder.php');
include_once('classes/log.php');
include_once('classes/access-rules.php');
include_once('classes/account.php');
include_once('classes/transaction.php');

$usr_perms = AccessRules::getUsersPermissions($_COOKIE['uid']);

if(!AccessRules::hasPermission('CREATE_TRANSACTIONS', $usr_perms)) {
  NoPermissionPage::display('Транзакції');
}

$page = new Page('Транзакції');

$content = '';

$content .= '<h2 class="mdl-card__title-text">Нова транзакція</h2>';
$content .= '<div class="mdl-card__supporting-text">';
$content .= '<form action="new-transaction.php" method="post">';
$content .= '
  <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" id="transaction-new-fromaccount-div">
    <input class="mdl-textfield__input" type="text" name="from-account" id="transaction-new-account" pattern="-?[A-Za-z0-9]*([A-Za-z0-9]+)?">
    <label class="mdl-textfield__label" for="transaction-new-account">Номер рахунку платника</label>
    <label id="transaction-new-fromaccount-lbl"></label>
  </div>
  <br />
  <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" id="transaction-new-toaccount-div">
    <input class="mdl-textfield__input" type="text" name="target-account" id="transaction-new-targetacc" pattern="-?[A-Za-z0-9]*([A-Za-z0-9]+)?">
    <label class="mdl-textfield__label" for="transaction-new-targetacc">Номер рахунку одержувача</label>
    <label id="transaction-new-toaccount-lbl"></label>
  </div>';

$content .= '<select id="transaction-search-type-selector" class="mdl-textfield__input" name="transaction-type">
  <option value="0">Тип транзакції</option>';
$currency_list = Transactions::getTransactionsTypesList();
foreach($currency_list as $r) {
  $content .= '<option value='.$r['id'].'>'.$r['name'].' - '.$r['description'].'</option>';
}
$content .= '</select>';

$content .= '<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
    <input class="mdl-textfield__input" type="text" name="amount" id="transaction-new-amount" pattern="^\d{1,18}(,\d{3})*(\.\d\d)?$">
    <label class="mdl-textfield__label" for="transaction-new-amount">Сума транзакції</label>
  </div>';
$content .= '<br /><button id="transaction-new-btn" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" type="submit">Створити</button></form></div>';
$content .= '</form></div>';

$page->setContent($content);
$page->create();
?>
