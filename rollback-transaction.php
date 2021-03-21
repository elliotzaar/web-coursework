<?php
include_once('classes/page-builder.php');
include_once('classes/access-rules.php');
include_once('classes/transaction.php');
include_once('classes/account.php');
include_once('classes/user.php');
include_once('classes/log.php');

$usr_perms = AccessRules::getUsersPermissions($_COOKIE['uid']);

if(!AccessRules::hasPermission('ROLLBACK_TRANSACTIONS', $usr_perms) && Session::getSession($sr['creator_session_id'])['users_id'] != $_COOKIE['uid']) {
  NoPermissionPage::display('Транзакції');
}

$page = new Page('Транзакції');

if(isset($_GET['id'])) {
  $tr = Transactions::getTransaction($_GET['id']);

  if($tr == -1) {
    $page->setContent('Неправильно вказано ідентифікатор транзакції.');
  } else {
    if($tr['status'] == 'DELETED' || $tr['status'] == 'CANCELLED') {
      $page->setContent('Цю транзакцію вже було видалено чи відхилено.');
    } else {
      if(isset($_GET['success'])) {
        Transactions::rollbackTransaction($tr['id']);
        UsersLog::record($_COOKIE['sid'], UsersLog::ROLLBACK_TRANSACTION, 'Видалено транзакцію UUID '.$tr['uuid'].', статус '.$tr['status']);
        $page->setContent('Транзакцію видалено.');
      } else {
        $content = '<h2 class="mdl-card__title-text">Видалення транзакції</h2>
        <div class="mdl-card__supporting-text">';

        $acc_r = Accounts::getAccountRow($tr['account_id']);
        $acc_tr = Accounts::getAccountRow($tr['target_account_id']);

        $content .= 'Дебет: '.$acc_r['number'].' - '.$acc_r['name'].'<br />';
        $content .= 'Кредит: '.$acc_tr['number'].' - '.$acc_tr['name'].'<br />';
        $content .= 'Тип транзакції: '.Transactions::getTransactionTypeInfo($tr['transaction_type_id'])['name'].' ('.Transactions::getTransactionTypeInfo($tr['transaction_type_id'])['description'].')'.'<br />';
        $content .= 'Сума транзакції: '.$tr['amount'].' '.Currency::getCurrencyRow($acc_r['currency_id'])['code'].'<br />';
        $content .= 'Призначення: '.$tr['description'].'<br />';
        $content .= 'Оператор: '.UserAccount::getUsername(Session::getSession($tr['creator_session_id'])['users_id']).'<br />';

        $content .= '<div class="carditem-border-top"><button onclick="location.href=\'rollback-transaction.php?id='.$tr['id'].'&success\'" class="mdl-button mdl-js-button mdl-button--raised mdl-button--accent">Видалити</button>';

        $content .= '</div>';
        $page->setContent($content);
      }
    }
  }
} else {
  $page->setContent('Неправильно вказано ідентифікатор транзакції.');
}

$page->create();
?>
