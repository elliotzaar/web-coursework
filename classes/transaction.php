<?php
class Transactions {

  public static function getTransactionsTypesList() {
    return Database::query('SELECT * FROM `transaction_types`');
  }
}

?>
