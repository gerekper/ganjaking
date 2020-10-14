<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

// Autoload all the requisite classes
function mpdt_autoloader($class_name) {
  // Only load MemberPress DT classes here
  if(preg_match('/^Mpdt.+$/', $class_name)) {
    if(preg_match('/^MpdtUtils$/', $class_name)) {
      $filepath = MPDT_LIB_PATH."/{$class_name}.php";
    }
    elseif(preg_match('/^.+Base.+$/', $class_name)) {
      $filepath = MPDT_LIB_PATH."/{$class_name}.php";
    }
    elseif(preg_match('/^.+Ctrl$/', $class_name)) {
      $filepath = MPDT_CTRLS_PATH."/{$class_name}.php";
    }
    elseif(preg_match('/^.+Utils$/', $class_name)) {
      $filepath = MPDT_UTILS_PATH."/{$class_name}.php";
    }
    elseif(preg_match('/^.+Api$/', $class_name)) {
      $filepath = MPDT_API_PATH."/{$class_name}.php";
    }
    else {
      $filepath = MPDT_LIB_PATH."/{$class_name}.php";
    }

    if(file_exists($filepath)) { require_once($filepath); }
  }
}

// Add the autoloader
spl_autoload_register('mpdt_autoloader');
