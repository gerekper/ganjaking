<?php
namespace memberpress\downloads\lib;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\downloads as base;

/** Ctrls in MemberPress Downloads are all singletons, so we can
  * use this factory to churn out objects for us.
  */
class CtrlFactory {
  public static function fetch($class, $args=array()) {
    static $objs;

    if(0 !== strpos($class, base\CTRLS_NAMESPACE) && $args['path'] === base\CTRLS_PATH) {
      $class = base\CTRLS_NAMESPACE . '\\' . Inflector::classify($class);
    }

    if(0 !== strpos($class, base\ADMIN_CTRLS_NAMESPACE) && $args['path'] === base\ADMIN_CTRLS_PATH) {
      $class = base\ADMIN_CTRLS_NAMESPACE . '\\' . Inflector::classify($class);
    }

    if(isset($objs[$class]) && ($objs[$class] instanceof BaseCtrl)) {
      return $objs[$class];
    }

    if(!class_exists($class)) {
      throw new \Exception(sprintf(__('Ctrl: %s wasn\'t found', 'memberpress-downloads'), $class));
    }

    // We'll let the autoloader in main.php
    // handle including files containing these classes
    $r = new \ReflectionClass($class);
    $obj = $r->newInstanceArgs();

    $objs[$class] = $obj;

    return $obj;
  }

  public static function all($args=array()) {
    $objs = array();

    foreach(self::paths() as $path) {
      $args['path'] = $path;
      $ctrls = @glob($path . '/*.php', GLOB_NOSORT);
      foreach($ctrls as $ctrl) {
        if(basename($ctrl) == 'index.php') {
          continue; // don't load index.php
        }
        $class = preg_replace('#\.php#', '', basename($ctrl));
        $objs[$class] = self::fetch($class, $args);
      }
    }

    return $objs;
  }

  public static function paths() {
    return apply_filters(base\SLUG_KEY.'_ctrls_paths', array(base\CTRLS_PATH, base\ADMIN_CTRLS_PATH));
  }
}
