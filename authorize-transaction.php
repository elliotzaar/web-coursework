<?php
include_once('classes/page-builder.php');
include_once('classes/access-rules.php');
include_once('classes/transaction.php');
include_once('classes/account.php');
include_once('classes/user.php');

$usr_perms = AccessRules::getUsersPermissions($_COOKIE['uid']);

if(!AccessRules::hasPermission('AUTH_TRANSACTIONS', $usr_perms) && !AccessRules::hasPermission('AUTH_SELFTRANSACTIONS', $usr_perms)) {
  NoPermissionPage::display('Транзакції');
}

$page = new Page('Транзакції');

if(isset($_GET['id'])) {
  $tr = Transactions::getTransaction($_GET['id']);

  if($tr == -1) {
    $page->setContent('Неправильно вказано ідентифікатор транзакції.');
  } else {
    if($tr['status'] == 'HOLD') {
      if((Session::getSession($tr['creator_session_id'])['users_id'] != $_COOKIE['uid'] && AccessRules::hasPermission('AUTH_TRANSACTIONS', $usr_perms)) || AccessRules::hasPermission('AUTH_SELFTRANSACTIONS', $usr_perms)) {
        if(isset($_GET['success'])) {
          Transactions::setAuthorizationStatus($tr['id'], $_COOKIE['sid'], true);
          $page->setContent('Транзакцію авторизовано.');
        } else if(isset($_GET['decline'])) {
          Transactions::setAuthorizationStatus($tr['id'], $_COOKIE['sid'], false);
          $page->setContent('Відхилено авторизацію транзакції.');
        } else {
          $content = '<h2 class="mdl-card__title-text">Авторизація транзакції</h2>
          <div class="mdl-card__supporting-text">';

          $acc_r = Accounts::getAccountRow($tr['account_id']);
          $acc_tr = Accounts::getAccountRow($tr['target_account_id']);

          $content .= 'Дебет: '.$acc_r['number'].' - '.$acc_r['name'].'<br />';
          $content .= 'Кредит: '.$acc_tr['number'].' - '.$acc_tr['name'].'<br />';
          $content .= 'Тип транзакції: '.Transactions::getTransactionTypeInfo($tr['transaction_type_id'])['name'].' ('.Transactions::getTransactionTypeInfo($tr['transaction_type_id'])['description'].')'.'<br />';
          $content .= 'Сума транзакції: '.$tr['amount'].' '.Currency::getCurrencyRow($acc_r['currency_id'])['code'].'<br />';
          $content .= 'Оператор: '.UserAccount::getUsername(Session::getSession($tr['creator_session_id'])['users_id']).'<br />';

          $content .= '<div class="carditem-border-top"><button onclick="location.href=\'authorize-transaction.php?id='.$tr['id'].'&success\'" class="mdl-button mdl-js-button mdl-button--raised mdl-button--accent">Авторизувати</button>';
          $content .= '<button onclick="location.href=\'authorize-transaction.php?id='.$tr['id'].'&decline\'" class="mdl-button mdl-js-button mdl-button--raised">Відхилити</button></div>';

          $content .= '</div>';
          $page->setContent($content);
        }
      } else {
        $page->setContent('Ви не можете авторизовувати власні транзакції.');
      }
    } else {
      $page->setContent('Транзакція вже була проведена або видалена.');
    }
  }
} else {
  $page->setContent('Неправильно вказано ідентифікатор транзакції.');
}

$page->create();
?>
