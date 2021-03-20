<?php
include_once('classes/account.php');

class Transactions {

  public static function generateUUID() {
    $data = random_bytes(16);
    assert(strlen($data) == 16);

    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
  }

  public static function getTransactionsTypesList() {
    return Database::query('SELECT * FROM `transaction_types`');
  }

  public static function getTransactionType($id) {
    return Database::query('SELECT `name` FROM `transaction_types` WHERE `id` = :id', array('id' => $id))[0][0];
  }

  public static function getTransactionTypeInfo($id) {
    return Database::query('SELECT * FROM `transaction_types` WHERE `id` = :id', array('id' => $id))[0];
  }

  public static function transactionTypeExists($id) {
    $res = Database::query('SELECT COUNT(*) FROM `transaction_types` WHERE `id` = :id', array('id' => $id));
    return $res[0][0] == '1';
  }

  public static function createTransaction($account_number, $account_target_number, $amount, $transaction_type_id, $description, $creator_session_id) {
    $account_id = Accounts::getAccountRowByNum($account_number)['id'];
    $target_account_id = Accounts::getAccountRowByNum($account_target_number)['id'];
    $uuid = Transactions::generateUUID();

    return Database::insertQuery('INSERT INTO `transactions` (`uuid`, `account_id`, `target_account_id`, `amount`, `transaction_type_id`, `description`, `creator_session_id`) VALUES (:uuid, :accid, :taccid, :amount, :ttid, :descr, :csid)', array('uuid' => $uuid, 'accid' => $account_id, 'taccid' => $target_account_id, 'amount' => $amount, 'ttid' => $transaction_type_id, 'descr' => $description, 'csid' => $creator_session_id));
  }

  public static function getTransaction($id) {
    $res = Database::query('SELECT * FROM `transactions` WHERE `id` = :id', array('id' => $id));
    if(count($res) > 0) {
      return $res[0];
    } else {
      return -1;
    }
  }

  public static function searchTransactions($uuid, $account_number, $amount, $currency_id, $transaction_type_id, $status, $operator_id, $description, $date) {
    $query = 'SELECT * FROM `transactions` WHERE `description` LIKE :description';
    $query_params = array('description' => '%'.$description.'%');

    if($uuid != '') {
      $query .= ' AND `uuid` = :uuid';
      $query_params['uuid'] = $uuid;
    }
    if($account_number != '') {
      $acc_id = -1;

      if(Accounts::accountNumberExists($account_number)) {
        $acc_id = Accounts::getAccountRowByNum($account_number)['id'];
      }

      $query .= ' AND (`account_id` = '.$acc_id.' OR `target_account_id` = '.$acc_id.')';
    }
    if($amount != '') {
      $query .= ' AND `amount` = :amount';
      $query_params['amount'] = $amount;
    }
    if($currency_id != '' && $currency_id != '0') {
      $query .= ' AND `account_id` IN (SELECT `accounts`.`id` FROM `accounts` WHERE `accounts`.`currency_id` = :currid)';
      $query_params['currid'] = $currency_id;
    }
    if($transaction_type_id != '' && $transaction_type_id != '0') {
      $query .= ' AND `transaction_type_id` = :ttid';
      $query_params['ttid'] = $transaction_type_id;
    }
    if($status != '' && $status != '0') {
      $query .= ' AND `status` = :status';
      $query_params['status'] = $status;
    }
    if($operator_id != '') {
      $query .= ' AND `creator_session_id` IN (SELECT `users_sessions`.`id` FROM `users_sessions` WHERE `users_sessions`.`users_id` = :opid)';
      $query_params['opid'] = $operator_id;
    }
    if($date != '') {
      $query .= ' AND `create_time` LIKE :dt';
      $query_params['dt'] = $date.'%';
    }

    $query .= ' ORDER BY `create_time` DESC';

    $qr = Database::query($query, $query_params);
    $r = array();

    if(count($qr) > 0) {
      $r = $qr;
    }

    return $r;
  }

  public static function setAuthorizationStatus($transaction_id, $user_session_id, $auth_status) {
    $tr = Transactions::getTransaction($transaction_id);

    if($auth_status) {
      Database::query('UPDATE `accounts` SET `balance` = :bal WHERE `accounts`.`id` = :accid', array('bal' => Accounts::getAccountBalance($tr['account_id']) - floatval($tr['amount']), 'accid' => $tr['account_id']));
      Database::query('UPDATE `accounts` SET `balance` = :bal WHERE `accounts`.`id` = :accid', array('bal' => Accounts::getAccountBalance($tr['target_account_id']) + floatval($tr['amount']), 'accid' => $tr['target_account_id']));
      Database::query('UPDATE `transactions` SET `status` = "AUTHORIZED", `controller_session_id` = :csid, `controller_time` = NOW() WHERE `transactions`.`id` = :trid', array('trid' => $transaction_id, 'csid' => $user_session_id));
    } else {
      Database::query('UPDATE `transactions` SET `status` = "CANCELLED", `controller_session_id` = :csid, `controller_time` = NOW() WHERE `transactions`.`id` = :trid', array('trid' => $transaction_id, 'csid' => $user_session_id));
    }
  }
}

?>
