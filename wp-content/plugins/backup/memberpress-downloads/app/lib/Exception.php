<?php
namespace memberpress\downloads\lib;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\downloads as base;

class Exception extends \Exception { }

class LogException extends Exception {
  public function __construct($message, $code = 0, Exception $previous = null) {
    $classname = get_class($this);
    Utils::error_log("{$classname}: {$message}");
    parent::__construct($message, $code, $previous);
  }
}

class DbMigrationException extends LogException {
  public function __construct($message, $code = 0, Exception $previous = null) {
    delete_transient(base\SLUG_KEY.'_migrating');
    delete_transient(base\SLUG_KEY.'_current_migration');
    set_transient(base\SLUG_KEY.'_migration_error',$message,Utils::hours(4));
    parent::__construct($message, $code, $previous);
  }
}

class DbMigrationRollbackException extends DbMigrationException {
  public function __construct($message, $code = 0, Exception $previous = null) {
    global $wpdb;
    $wpdb->query('ROLLBACK'); // Attempt a rollback
    parent::__construct($message, $code, $previous);
  }
}

class ValidationException extends Exception { }
class CreateException extends Exception { }
class UpdateException extends Exception { }
class DeleteException extends Exception { }

class InvalidEmailException extends Exception { }
class InvalidMethodException extends Exception { }
class InvalidVariableException extends Exception { }
