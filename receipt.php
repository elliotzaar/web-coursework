<?php
include_once('classes/database.php');
include_once('classes/user.php');
include_once('classes/transaction.php');

function getFormattedDate($date_str) {
  return date_format(date_create_from_format('Y-m-d H:i:s', $date_str), 'd.m.Y H:i:s');
}

if(isset($_GET['uuid'])) {
  $tr = Transactions::getTransactionByUUID($_GET['uuid']);

  if($tr == -1) {
    http_response_code(404);
    die('Transaction with this UUID was not found.');
  } else {
    echo('<tt>');
    echo('Квитанція <strong>'.mb_strtoupper($tr['uuid']).'</strong><br />');
    echo('від <strong>'.getFormattedDate($tr['create_time']).'</strong><br />');

    if($tr['status'] != 'AUTHORIZED') {
      echo('<strong>УВАГА! ЦЯ ТРАНЗАКЦІЯ НЕ ПРОВЕДЕНА, А КВИТАНЦІЯ НЕ Є РОЗРАХУНКОВИМ ДОКУМЕНТОМ!</strong>');
    } else {
      echo('<strong>Проведено: </strong>'.getFormattedDate($tr['controller_time']));
    }

    echo('<br /><br />');

    $acc_r = Accounts::getAccountRow($tr['account_id']);
    $acc_tr = Accounts::getAccountRow($tr['target_account_id']);

    echo('<strong>Дебет</strong> (рахунок списання): <br />'.$acc_r['number'].' - '.$acc_r['name']);
    echo('<br /><br />');
    echo('<strong>Кредит</strong> (рахунок зарахування): <br />'.$acc_tr['number'].' - '.$acc_tr['name']);
    echo('<br /><br />');
    echo('<strong>Сума:</strong> '.$tr['amount'].'<br />');
    echo('<strong>Валюта:</strong> '.Currency::getCurrencyRow($acc_r['currency_id'])['code'].' - '.Currency::getCurrencyRow($acc_r['currency_id'])['name'].'<br />');
    echo('<strong>Призначення:</strong> '.$tr['description']);

    echo('<br /><br /><br />');

    echo('Оператор '.UserAccount::getUsername(Session::getSession($tr['creator_session_id'])['users_id']).'&nbsp;&nbsp;&nbsp;&nbsp;____________');
    echo('<br /><br /><br />');
    if($tr['status'] == 'AUTHORIZED') {
      echo('Контролер '.UserAccount::getUsername(Session::getSession($tr['controller_session_id'])['users_id']).'&nbsp;&nbsp;&nbsp;&nbsp;____________');
    } else {
      echo('Контролер відсутній');
    }

    die('</tt>');
  }
} else {
  http_response_code(404);
  die('Transaction with this UUID was not found.');
}
?>
