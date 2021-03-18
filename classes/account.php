<?php
class Currency {

  public static function getCurrenciesList() {
    return Database::query('SELECT * FROM `currencies`');
  }

  public static function getCurrencyRow($c_id) {
    $res = Database::query('SELECT * FROM `currencies` WHERE `id` = :id', array('id' => $c_id));

    if(count($res) > 0) {
      return $res[0];
    } else {
      return array();
    }
  }
}

class Accounts {

  public static function findAccounts($name, $number, $currency) {
    if(intval($currency) <= 0) {
      return Database::query('SELECT * FROM `accounts` WHERE `number` LIKE :num AND `name` LIKE :name', array('num' => '%'.$number.'%', 'name' => '%'.$name.'%'));
    } else {
      return Database::query('SELECT * FROM `accounts` WHERE `number` LIKE :num AND `name` LIKE :name AND `currency_id` = :currency_id', array('num' => '%'.$number.'%', 'name' => '%'.$name.'%', 'currency_id' => intval($currency)));
    }
  }
}
?>
