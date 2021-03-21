<?php
require_once('./classes/database.php');

class UsersLog {
  public const STOP_SESSION = 'STOP_SESSION';
  public const SUSPEND_USER = 'SUSPEND_USER';
  public const UNSUSPEND_USER = 'UNSUSPEND_USER';
  public const CREATE_USER = 'CREATE_USER';
  public const GRANT_ROLE_PERMISSION = 'GRANT_ROLE_PERMISSION';
  public const REMOVE_ROLE_PERMISSION = 'REMOVE_ROLE_PERMISSION';
  public const CREATE_ROLE = 'CREATE_ROLE';
  public const ASSIGN_ROLE = 'ASSIGN_ROLE';
  public const CREATE_ACCOUNT = 'CREATE_ACCOUNT';
  public const EDIT_ACCOUNT = 'EDIT_ACCOUNT';
  public const ROLLBACK_TRANSACTION = 'ROLLBACK_TRANSACTION';

  public static function record($operator_sid, $action, $description) {
    Database::query('INSERT INTO `users_log` (`action`, `action_description`, `operator_session_id`) VALUES (:action, :action_description, :operator_session_id)', array('action' => $action, 'action_description' => $description, 'operator_session_id' => $operator_sid));
  }

  public static function getSessionActions($session_id) {
    $res = Database::query('SELECT * FROM `users_log` WHERE `operator_session_id` = :sid', array('sid' => $session_id));
    if(count($res) > 0) {
      return $res;
    } else {
      return array();
    }
  }
}
?>
