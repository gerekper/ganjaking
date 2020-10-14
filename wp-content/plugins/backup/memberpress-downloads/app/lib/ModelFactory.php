<?php
namespace memberpress\downloads\lib;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\downloads as base;

/** Get a specific model from a name string. */
class ModelFactory {
  public static function fetch($model, $id) {
    $class = base\MODELS_NAMESPACE . '\\' . Inflector::classify($model);

    if(!class_exists($class)) {
      return new WP_Error(sprintf(__('A model for %s wasn\'t found', 'memberpress-downloads'), $model));
    }

    // We'll let the autoloader handle including files containing these classes
    $r = new ReflectionClass($class);
    $obj = $r->newInstanceArgs(array($id));

    if(isset($obj->ID) && $obj->ID <= 0) {
      return new WP_Error(sprintf(__('There was no %s with an id of %d found', 'memberpress-downloads'), $model, $obj->ID));
    }
    else if(isset($obj->id) && $obj->id <= 0) {
      return new WP_Error(sprintf(__('There was no %s with an id of %d found', 'memberpress-downloads'), $model, $obj->id));
    }
    else if(isset($obj->term_id) && $obj->term_id <= 0) {
      return new WP_Error(sprintf(__('There was no %s with an id of %d found', 'memberpress-downloads'), $model, $obj->term_id));
    }

    $objs[$class] = $obj;

    return $obj;
  }
}

