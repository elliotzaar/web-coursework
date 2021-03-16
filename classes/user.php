<?php
require_once('./classes/database.php');

class UserAccount {

  public static function deriveSecurePassword($pass_text) {
    return hash('sha512', crypt($pass_text, '$5$rounds=5000$salt'.hash('md5', $pass_text).'endsalt$'));
  }

  public static function getUserId($username) {
    $username = strtolower(str_replace(' ', '', $username));

    $res = Database::query('SELECT `uid` FROM `users` WHERE `username` = :username', array('username' => $username));

    if(count($res) > 0) {
        return $res[0]['uid'];
    }

    return -1;
  }

  public static function getUsername($uid) {
    $res = Database::query('SELECT `username` FROM `users` WHERE `uid` = :uid', array('uid' => $uid));

    if(count($res) > 0) {
        return $res[0]['username'];
    }

    return '';
  }

  public static function userExists($id) {
    $res = Database::query('SELECT COUNT(*) FROM `users` WHERE `uid` = :uid', array('uid' => $id));
    return $res[0][0] == '1';
  }

  public static function usernameExists($name) {
    $res = Database::query('SELECT COUNT(*) FROM `users` WHERE `username` = :name', array('name' => $name));
    return $res[0][0] == '1';
  }

  public static function isSuspended($id) {
    $res = Database::query('SELECT `suspended` FROM `users` WHERE `uid` = :uid', array('uid' => $id));

    if(count($res) > 0) {
        return $res[0]['suspended'];
    }

    return -1;
  }

  public static function setSuspended($id, $status) {
    if(UserAccount::userExists($id)) {
      Database::query('UPDATE `users` SET `suspended`=:status WHERE `uid` = :uid', array('uid' => $id, 'status' => $status));
    } else {
      return -1;
    }
  }

  public static function checkCredentials($username, $password) {
    $username = strtolower(str_replace(' ', '', $username));

    $res = Database::query('SELECT `uid`, `suspended` FROM `users` WHERE `username` = :username AND `password` = :password', array('username' => $username, 'password' => UserAccount::deriveSecurePassword($password)));

    if(count($res) > 0) {
        return ['uid' => $res[0]['uid'], 'suspended' => $res[0]['suspended']];
    }

    return false;
  }

  public static function getUsersList() {
    $res = Database::query('SELECT `uid`, `username`, `role_id`, `suspended` FROM `users`');
    $tmp_arr = [];
    if(count($res) > 0) {
      foreach ($res as $v) {
        array_push($tmp_arr, $v);
      }
    }

    return $tmp_arr;
  }

  public static function createUser($username, $password) {
    return Database::insertQuery('INSERT INTO `users` (`username`, `password`) VALUES (:username, :password)', array('username' => $username, 'password' => UserAccount::deriveSecurePassword($password)));
  }
}

class Session {
  public static function createSession($username, $password, $ip_address) {
    $username = strtolower(str_replace(' ', '', $username));

    $creds = UserAccount::checkCredentials($username, $password);
    if($creds == false) {
      return -1;
    }
    if($creds['suspended'] == 1) {
      return -1;
    }

    $uid = $creds['uid'];
    $token = $uid.strtoupper($username).time().'TOKEN'.bin2hex(openssl_random_pseudo_bytes(256));

    Database::query('INSERT INTO `users_sessions` (`users_id`, `token`, `address`) VALUES (:users_id, :token, :address)', array('users_id' => $uid, 'token' => hash('sha512', $token), 'address' => $ip_address));
    $sid = Database::query('SELECT `id` FROM `users_sessions` WHERE `users_id` = :users_id AND `token` = :token AND `address` = :address', array('users_id' => $uid, 'token' => hash('sha512', $token), 'address' => $ip_address))[0]['id'];

    return ['token' => $token, 'username' => $username, 'uid' => $uid, 'sid' => $sid];
  }

  public static function checkSession($uid, $session_token) {
    if(UserAccount::isSuspended($uid)) {
      return false;
    }

    $res = Database::query('SELECT `id` FROM `users_sessions` WHERE `users_id` = :uid AND `token` = :token AND `stopped` = 0 AND `create_time` > :ed', array('uid' => $uid, 'token' => hash('sha512', $session_token), 'ed' => date('Y-m-d H:i:s', time() - (60 * 60 * 24))));

    if(count($res) > 0) {
      return ['uid' => $uid, 'username' => UserAccount::getUsername($uid), 'session_id' => $res[0]['id']];
    } else {
      return false;
    }
  }

  public static function destroySession($uid, $session_token) {
    if(Session::checkSession($uid, $session_token) == false) {
      return false;
    }

    Database::query('UPDATE `users_sessions` SET `stopped` = 1 WHERE `users_id` = :uid AND `token` = :token', array('uid' => $uid, 'token' => hash('sha512', $session_token)));
  }

  public static function countSessions($uid) {
    $res = Database::query('SELECT COUNT(*) FROM `users_sessions` WHERE `users_id` = :uid AND `stopped` = 0 AND `create_time` > :ed', array('uid' => $uid, 'ed' => date('Y-m-d H:i:s', time() - (60 * 60 * 24))));
    return $res[0][0];
  }

  public static function countAllSessions($uid) {
    $res = Database::query('SELECT COUNT(*) FROM `users_sessions` WHERE `users_id` = :uid', array('uid' => $uid));
    return $res[0][0];
  }

  public static function getLastSession($uid) {
    $res = Database::query('SELECT * FROM `users_sessions` WHERE `users_id` = :uid AND `stopped` = 0 AND `create_time` > :ed ORDER BY `create_time` DESC LIMIT 1', array('uid' => $uid, 'ed' => date('Y-m-d H:i:s', time() - (60 * 60 * 24))));
    return $res[0];
  }

  public static function getAllSessions($uid) {
    $res = Database::query('SELECT * FROM `users_sessions` WHERE `users_id` = :uid ORDER BY `stopped` ASC, `id` DESC', array('uid' => $uid));
    return $res;
  }

  public static function isActive($session_id) {
    $res = Database::query('SELECT COUNT(*) FROM `users_sessions` WHERE `id` = :token_id AND `stopped` = 0 AND `create_time` > :ed', array('token_id' => $session_id, 'ed' => date('Y-m-d H:i:s', time() - (60 * 60 * 24))));
    return $res[0][0] == 1;
  }

  public static function destroySessionById($session_id) {
    if(!Session::isActive($session_id)) {
      return false;
    }

    Database::query('UPDATE `users_sessions` SET `stopped` = 1 WHERE `id` = :sid', array('sid' => $session_id));
  }

  public static function sessionExists($session_id) {
    $res = Database::query('SELECT COUNT(*) FROM `users_sessions` WHERE `id` = :token_id', array('token_id' => $session_id));
    return $res[0][0] == 1;
  }

  public static function getSession($session_id) {
    $res = Database::query('SELECT * FROM `users_sessions` WHERE `id` = :token_id', array('token_id' => $session_id));
    if(count($res) > 0) {
      return $res[0];
    } else {
      return -1;
    }
  }
}
?>
