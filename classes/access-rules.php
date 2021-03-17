<?php
require_once('./classes/database.php');

class AccessRules {

  public static function getRole($uid) {
    $res = Database::query('SELECT `role_id` FROM `users` WHERE `uid` = :uid', array('uid' => $uid));

    if(count($res) > 0) {
        $role_id = $res[0]['role_id'];
        $res = Database::query('SELECT `name`, `description` FROM `roles` WHERE `id` = :id', array('id' => $role_id));

        if(count($res) > 0) {
          return ['id' => $role_id, 'name' => $res[0]['name'], 'desc' => $res[0]['description']];
        }
    }

    return -1;
  }

  public static function getRoleRow($role_id) {
    $res = Database::query('SELECT * FROM `roles` WHERE `id` = :id', array('id' => $role_id));

    if(count($res) > 0) {
      return $res[0];
    } else {
      return array();
    }
  }

  public static function getRolePermissionsList($role_id) {
    $res = Database::query('SELECT `permissions_id` FROM `roles_permissions` WHERE `roles_id` = :rid', array('rid' => $role_id));
    $tmp_arr = [];
    if(count($res) > 0) {
      foreach ($res as $v) {
        array_push($tmp_arr, $v['permissions_id']);
      }
    }

    return $tmp_arr;
  }

  public static function getPermission($permission_id) {
    $res = Database::query('SELECT `name`, `description` FROM `permissions` WHERE `id` = :pid', array('pid' => $permission_id));

    if(count($res) > 0) {
      return ['name' => $res[0]['name'], 'description' => $res[0]['description']];
    }

    return '';
  }

  public static function getPermissionId($permission_name) {
    $res = Database::query('SELECT `id` FROM `permissions` WHERE `name` = :name', array('name' => $permission_name));

    if(count($res) > 0) {
      return $res[0]['id'];
    }

    return -1;
  }

  public static function getUsersPermissions($uid) {
    $perms_list = AccessRules::getRolePermissionsList(AccessRules::getRole($uid)['id']);
    $tmp_arr = [];
    if(count($perms_list) > 0) {
      foreach ($perms_list as $v) {
        $tmp_perm = AccessRules::getPermission($v);
        $tmp_arr[$tmp_perm['name']] = $tmp_perm['description'];
      }
    }

    return $tmp_arr;
  }

  public static function hasPermission($permission_name, $user_perm_list) {
    if(count($user_perm_list) == 0) {
      return false;
    }

    return array_key_exists($permission_name, $user_perm_list);
  }

  public static function getRolesList() {
    $res = Database::query('SELECT `id`, `name`, `description` FROM `roles`');
    $tmp_arr = [];
    if(count($res) > 0) {
      foreach ($res as $v) {
        array_push($tmp_arr, $v);
      }
    }

    return $tmp_arr;
  }

  public static function roleExists($id) {
    $res = Database::query('SELECT COUNT(*) FROM `roles` WHERE `id` = :id', array('id' => $id));
    return $res[0][0] == '1';
  }

  public static function roleNameExists($role_name) {
    $res = Database::query('SELECT COUNT(*) FROM `roles` WHERE `name` = :role_name', array('role_name' => $role_name));
    return $res[0][0] == '1';
  }

  public static function permissionExists($id) {
    $res = Database::query('SELECT COUNT(*) FROM `permissions` WHERE `id` = :id', array('id' => $id));
    return $res[0][0] == '1';
  }

  public static function getRoleUsersAmount($role_id) {
    $res = Database::query('SELECT COUNT(*) FROM `users` WHERE `role_id` = :role_id AND `suspended` = 0', array('role_id' => $role_id));
    return $res[0][0];
  }

  public static function getRolePermissionsListAsArray($role_id) {
    return Database::query('SELECT * FROM `permissions` WHERE `permissions`.`id` IN (SELECT `roles_permissions`.`permissions_id` FROM `roles_permissions` WHERE `roles_permissions`.`roles_id` = :role_id)', array('role_id' => $role_id));
  }

  public static function getAllPermissionsList($role_id) {
     $res = Database::query('SELECT * FROM `permissions`');

     $perms_req = AccessRules::getRolePermissionsListAsArray($role_id);
     $perms = array();
     foreach ($perms_req as $pi) {
       array_push($perms, $pi['name']);
     }

     for ($i = 0; $i < count($res); $i++) {
       array_push($res[$i], in_array($res[$i]['name'], $perms));
     }

     return $res;
  }

  public static function addPermission($role_id, $permission_id) {
    Database::query('INSERT INTO `roles_permissions` (`roles_id`, `permissions_id`) VALUES (:role_id, :perm_id)', array('role_id' => $role_id, 'perm_id' => $permission_id));
  }

  public static function removePermission($role_id, $permission_id) {
    Database::query('DELETE FROM `roles_permissions` WHERE `roles_permissions`.`roles_id` = :role_id AND `roles_permissions`.`permissions_id` = :perm_id', array('role_id' => $role_id, 'perm_id' => $permission_id));
  }

  public static function getRoleUsersList($role_id) {
    $res =  Database::query('SELECT * FROM `users` WHERE `role_id` = :role_id AND `suspended` = 0', array('role_id' => $role_id));

    $tmp_arr = [];
    if(count($res) > 0) {
      foreach ($res as $v) {
        array_push($tmp_arr, $v);
      }
    }

    return $tmp_arr;
  }

  public static function createRole($role_name, $role_description) {
    return Database::insertQuery('INSERT INTO `roles` (`name`, `description`) VALUES (:name, :description)', array('name' => $role_name, 'description' => $role_description));
  }

  public static function setUserRole($user_id, $role_id) {
    Database::query('UPDATE `users` SET `role_id` = :role_id WHERE `users`.`uid` = :user_id', array('role_id' => $role_id, 'user_id' => $user_id));
  }
}
?>
