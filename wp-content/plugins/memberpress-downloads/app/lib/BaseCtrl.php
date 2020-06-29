<?php
namespace memberpress\downloads\lib;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\downloads as base;

abstract class BaseCtrl {
  public function __construct() {
    // This is to ensure that the load_hooks method is
    // only ever loaded once across all instansiations
    static $loaded;

    if( !isset($loaded) ) { $loaded = array(); }

    $class_name = get_class($this);

    if(!isset($loaded[$class_name])) {
      $this->load_hooks();
      $loaded[$class_name] = true;
    }
  }

  abstract public function load_hooks();

  public static function fetch($force=false) {
    static $obj;

    $class = get_called_class();
    if(!isset($obj) || $force) {
      $obj = new $class;
    }

    return apply_filters(base\SLUG_KEY.'_fetch_ctrl_'.$class, $obj);
  }
}

