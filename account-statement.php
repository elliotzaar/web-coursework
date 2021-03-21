<?php
include_once('classes/page-builder.php');
include_once('classes/log.php');
include_once('classes/access-rules.php');
include_once('classes/account.php');
include_once('classes/transaction.php');
include_once('classes/user.php');

function getFormattedDate($date_str) {
  return date_format(date_create_from_format('Y-m-d H:i:s', $date_str), 'd.m.Y H:i:s');
}

$usr_perms = AccessRules::getUsersPermissions($_COOKIE['uid']);

if(!AccessRules::hasPermission('VIEW_TRANSACTIONS', $usr_perms)) {
  die('Not enough permissions.');
}

if(isset($_GET['uuid']) && isset($_GET['account']) && isset($_GET['amount']) && isset($_GET['currency']) && isset($_GET['transaction-type']) && isset($_GET['status']) && isset($_GET['opid']) && isset($_GET['description']) && isset($_GET['from-date']) && isset($_GET['to-date'])) {
  $transactions_search_results = Transactions::searchTransactions($_GET['uuid'], $_GET['account'], $_GET['amount'], $_GET['currency'], $_GET['transaction-type'], $_GET['status'], $_GET['opid'], $_GET['description'], $_GET['from-date'], $_GET['to-date']);

  if(count($transactions_search_results) > 0) {
    echo('<tt>');
    echo('<center><strong>ВИПИСКА</strong></center>');
    echo('<p style="text-align: right"><em>Сформовано '.date('d.m.Y H:i:s', time()).'</em></p>');

    foreach($transactions_search_results as $sr) {
      echo('Дата та час транзакції: '.getFormattedDate($sr['create_time']).'<br />');
      echo('Дебет:&nbsp;&nbsp;'.Accounts::getAccountRow($sr['account_id'])['number'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
      echo('Кредит: '.Accounts::getAccountRow($sr['target_account_id'])['number'].'<br />');
      echo('Сума:&nbsp;&nbsp;&nbsp;'.$sr['amount'].' '.Currency::getCurrencyRow(Accounts::getAccountRow($sr['account_id'])['currency_id'])['code'].'<br />');
      echo('Статус: '.$sr['status'].'<br />');
      echo('<hr />');
    }

    die('</tt>');
  } else {
    die('Invalid request.');
  }
} else {
  die('Invalid request.');
}
?>
