<?php
class Currency {

  public static function getCurrenciesList() {
    return Database::query('SELECT * FROM `currencies`');
  }
}

class Accounts {

  public static function findAccounts($name, $number, $currency) {
    return Database::query('SELECT * FROM `accounts` WHERE `number` LIKE :num AND `name` LIKE :name'.(strval($currency) != '0' ? ' AND `currency_id` = :currency_id' : ''), array('num' => '%'.$number.'%', 'name' => '%'.$name.'%', 'currency_id' => intval($currency)));
  }
}
?>
