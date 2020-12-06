<?php
namespace memberpress\downloads\lib;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\downloads as base;

/** Class used for getting config data **/
class Config {
  // Attempts to retrieve data from a config file
  public static function get($name) {
    $filename = base\CONFIG_PATH . "/{$name}.php";

    if(!file_exists($filename)) {
      return new WP_Error(sprintf(__("A config file for %s wasn\'t found", 'memberpress-downloads'), $name));
    }

    return require($filename);
  }
}

