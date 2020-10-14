<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

abstract class MpdtUtilsFactory {

  public static function load($class) {
    self::fetch($class);
  }

  public static function fetch($class) {
    static $obj;

    if(!isset($obj)) {
      $obj = array();
    }

    if(!isset($obj[$class])) {
      $class = MpdtInflector::camelize($class);
      $classname = 'Mpdt'.ucwords($class).'Utils';
      $obj[$class] = new $classname;
    }

    return $obj[$class];
  }

  public static function fetch_for_api($api_class) {
    if(preg_match('/^Mpdt(.*)Api$/', $api_class, $m)) {
      return self::fetch(MpdtInflector::singularize($m[1]));
    }
    else {
      return false;
    }
  }

}

