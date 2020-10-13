<?php
namespace memberpress\courses\helpers;
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class Options {
  public static function val($options,$option_name,$default='') {
    if(isset($_POST['mpcs-options'][$option_name])) {
      return $_POST['mpcs-options'][$option_name];
    }

    return isset($options[$option_name]) ? $options[$option_name] : $default;
  }

  /**
   * Generates RGB from hex color
   *
   * @param  mixed $options
   * @param  mixed $option_key
   * @return void
   */
  public static function get_rgb($options, $option_key){
    $color = self::val($options, $option_key);
    $color = ltrim($color, '#');

    if(empty($color)){
      return array();
    }

    list($r, $g, $b) = array_map('hexdec', str_split($color, 2));
    return array($r, $g, $b);
  }
}
