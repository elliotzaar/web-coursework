<?php
class Database {

  private static function connect() {
    try {
      $pdo = new PDO('mysql:host=localhost;dbname=webcw', 'root', '');

      $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      header("Location: ./init.php");
      die();
    }

    return $pdo;
  }

  public static function query($query, $params = array()) {
    try {
      $statement = self::connect()->prepare($query);
    } catch (PDOException $e) {
      header("Location: ./init.php");
      die();
    }
    $statement->execute($params);

    $data = $statement->fetchAll();

    return $data;
  }

  public static function insertQuery($query, $params = array()) {
    $db = self::connect();
    $statement = $db->prepare($query);
    $statement->execute($params);

    return $db->lastInsertId();
  }
}
?>
