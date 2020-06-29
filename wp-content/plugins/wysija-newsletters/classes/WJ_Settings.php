<?php
defined('WYSIJA') or die('Restricted access');
/**
* Settings.
* Global Wysija Premium settings singleton.
*/
class WJ_Settings {

  static private $db_prefix;
  static private $name = 'wysija';

  private function __construct(){}

  // db_prefix();
  // # => wp_wysija_
  // db_prefix('custom_field');
  // # => wp_wysija_custom_field
  static function db_prefix($table_name = false) {
    global $wpdb;
    self::$db_prefix = $wpdb->prefix . self::$name . '_';
    if ($table_name) {
      $prefixed = self::$db_prefix . $table_name;
    } else {
      $prefixed = self::$db_prefix;
    }
    return $prefixed;
  }

}
