<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

abstract class MpdtCtrlFactory {

  public static function load($class) {
    $obj = self::fetch($class);
    $obj->load_hooks();
  }

  public static function fetch($class) {
    static $obj;

    if(!isset($obj)) {
      $obj = array();
    }

    if(!isset($obj[$class])) {
      $class = MpdtInflector::camelize($class);
      $classname = 'Mpdt'.ucwords($class).'Ctrl';
      $obj[$class] = new $classname;
      //$obj->load_hooks();
    }

    return $obj[$class];
  }

}

