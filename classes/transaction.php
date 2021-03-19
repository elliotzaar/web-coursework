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

  public static function transactionTypeExists($id) {
    $res = Database::query('SELECT COUNT(*) FROM `transaction_types` WHERE `id` = :id', array('id' => $id));
    return $res[0][0] == '1';
  }

  public static function createTransaction($account_number, $account_target_number, $amount, $transaction_type_id, $description, $creator_session_id) {
    $account_id = Accounts::getAccountRowByNum($account_number)['id'];
    $target_account_id = Accounts::getAccountRowByNum($account_target_number)['id'];
    $uuid = Transactions::generateUUID();

    Database::insertQuery('INSERT INTO `transaction_status` (`transaction_uuid`) VALUES (:uuid)', array('uuid' => $uuid));
    return Database::insertQuery('INSERT INTO `transactions` (`uuid`, `account_id`, `target_account_id`, `amount`, `transaction_type_id`, `description`, `creator_session_id`) VALUES (:uuid, :accid, :taccid, :amount, :ttid, :descr, :csid)', array('uuid' => $uuid, 'accid' => $account_id, 'taccid' => $target_account_id, 'amount' => $amount, 'ttid' => $transaction_type_id, 'descr' => $description, 'csid' => $creator_session_id));
  }
}

?>
