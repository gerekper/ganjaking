<?php
namespace memberpress\downloads\lib;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

/** Specific base class for Builtin Style models */
abstract class BaseBuiltinModel extends BaseModel {
  public $meta_attrs;
  /** Get all the meta attributes and default values */
  public function get_meta_attrs() {
    return (array)$this->meta_attrs;
  }

  /** Get only the data that is specified as meta attributes */
  public function get_meta_values() {
    $all_values = (array)$this->get_values();
    $meta_attrs = array_keys((array)$this->get_meta_attrs());
    return Utils::filter_array_keys($all_values, $meta_attrs);
  }
}

