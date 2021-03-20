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

if(isset($_POST['from-account']) && isset($_POST['target-account']) && isset($_POST['transaction-type']) && isset($_POST['amount']) && isset($_POST['description'])) {
  if(!preg_match('/^\d{1,18}(,\d{3})*(\.\d\d)?$/', $_POST['amount'])) {
    header("Location: ./new-transaction.php?invalida");
    die();
  } else {
    if(!Transactions::transactionTypeExists($_POST['transaction-type'])) {
      header("Location: ./new-transaction.php?invalidtt");
      die();
    } else {
      if(Accounts::accountNumberExists($_POST['from-account']) && Accounts::accountNumberExists($_POST['target-account']) && Accounts::getAccountRowByNum($_POST['from-account'])['currency_id'] == Accounts::getAccountRowByNum($_POST['target-account'])['currency_id'] && $_POST['target-account'] != $_POST['from-account']) {
        $tr = Transactions::createTransaction($_POST['from-account'], $_POST['target-account'], $_POST['amount'], $_POST['transaction-type'], $_POST['description'], $_COOKIE['sid']);

        $content = 'Транзакцію створено. Потребує підтвердження другим оператором.';

        if(($_POST['transaction-type'] == 1 || $_POST['transaction-type'] == 3) && Accounts::getAvailableAccountBalance(Accounts::getAccountRowByNum($_POST['from-account'])['id']) < 0) {
          $content .= '<br /><strong><font color="#7f0000"><span class="material-icons">warning_amber</span><br />ЗВЕРНІТЬ УВАГУ! ЗАЛИШОК НА РАХУНКУ КОРИСТУВАЧА ПІСЛЯ ПРОВЕДЕННЯ ОПЕРАЦІЇ БУДЕ ВІД\'ЄМНИМ!</font></strong>';
        }

        $page->setContent($content);
        $page->create();
        die();
      } else {
        header("Location: ./new-transaction.php?invalidacc");
        die();
      }
    }
  }

  $page->setContent($content);
  $page->create();
  die();
}

$content .= '<h2 class="mdl-card__title-text">Нова транзакція</h2>';
$content .= '<div class="mdl-card__supporting-text">';
$content .= '<form action="new-transaction.php" method="post">';
$content .= '
  <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label'.(isset($_GET['invalidacc']) ? ' is-invalid' : '').'" id="transaction-new-fromaccount-div">
    <input class="mdl-textfield__input" type="text" name="from-account" id="transaction-new-account" pattern="-?[A-Za-z0-9]*([A-Za-z0-9]+)?" onchange="checkNewTransactionsInput()">
    <label class="mdl-textfield__label" for="transaction-new-account">Номер рахунку платника</label>
    <label id="transaction-new-fromaccount-lbl"></label>
  </div>
  <br />
  <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label'.(isset($_GET['invalidacc']) ? ' is-invalid' : '').'" id="transaction-new-toaccount-div">
    <input class="mdl-textfield__input" type="text" name="target-account" id="transaction-new-targetacc" pattern="-?[A-Za-z0-9]*([A-Za-z0-9]+)?" onchange="checkNewTransactionsInput()">
    <label class="mdl-textfield__label" for="transaction-new-targetacc">Номер рахунку одержувача</label>
    <label id="transaction-new-toaccount-lbl"></label>
  </div>';

$content .= '<select onchange="checkNewTransactionsInput()" id="transaction-search-type-selector" class="mdl-textfield__input" name="transaction-type">
  <option value="0">Тип транзакції</option>';
$currency_list = Transactions::getTransactionsTypesList();
foreach($currency_list as $r) {
  $content .= '<option value='.$r['id'].'>'.$r['name'].' - '.$r['description'].'</option>';
}
$content .= '</select>';

$content .= '<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label'.(isset($_GET['invalida']) ? ' is-invalid' : '').'">
    <input class="mdl-textfield__input" onchange="checkNewTransactionsInput()" type="text" name="amount" id="transaction-new-amount" pattern="^\d{1,18}(,\d{3})*(\.\d\d)?$">
    <label class="mdl-textfield__label" for="transaction-new-amount">Сума транзакції</label>
  </div>
  <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
      <input class="mdl-textfield__input" onchange="checkNewTransactionsInput()" type="text" name="description" id="transaction-new-description">
      <label class="mdl-textfield__label" for="transaction-new-description">Призначення платежу</label>
    </div>';
$content .= '<br /><button disabled id="transaction-new-btn" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" type="submit">Створити</button></form></div>';
$content .= '</form></div>';

$page->setContent($content);
$page->create();
?>
