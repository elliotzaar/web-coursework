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

  public static function currencyExists($curr_id) {
    $res = Database::query('SELECT COUNT(*) FROM `currencies` WHERE `id` = :id', array('id' => $curr_id));
    return $res[0][0] == '1';
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

  public static function accountExists($acc_id) {
    $res = Database::query('SELECT COUNT(*) FROM `accounts` WHERE `id` = :acc_id', array('acc_id' => $acc_id));
    return $res[0][0] == '1';
  }

  public static function accountNumberExists($num) {
    $res = Database::query('SELECT COUNT(*) FROM `accounts` WHERE UPPER( `number` ) = :num', array('num' => strtoupper($num)));
    return $res[0][0] == '1';
  }

  public static function accountNumberExistsExcluding($num, $exclude_id) {
    $res = Database::query('SELECT COUNT(*) FROM `accounts` WHERE UPPER( `number` ) = :num AND `id` <> :eid', array('num' => strtoupper($num), 'eid' => $exclude_id));
    return $res[0][0] == '1';
  }

  public static function createAccount($name, $number, $currency, $balance) {
    return Database::insertQuery('INSERT INTO `accounts` (`number`, `name`, `balance`, `currency_id`) VALUES (:num, :name, :curr, :bal)', array('num' => $number, 'name' => $name, 'curr' => $currency, 'bal' => $balance));
  }

  public static function getAccountRow($acc_id) {
    $res = Database::query('SELECT * FROM `accounts` WHERE `id` = :id', array('id' => $acc_id));

    if(count($res) > 0) {
      return $res[0];
    } else {
      return array();
    }
  }

  public static function getAccountRowByNum($acc_num) {
    $res = Database::query('SELECT * FROM `accounts` WHERE `number` = :num', array('num' => $acc_num));

    if(count($res) > 0) {
      return $res[0];
    } else {
      return array();
    }
  }

  public static function editAccount($id, $name, $number, $currency) {
    Database::query('UPDATE `accounts` SET `number` = :num, `name` = :name, `currency_id` = :curr WHERE `id` = :id', array('num' => $number, 'name' => $name, 'curr' => $currency, 'id' => $id));
  }
}
?>
