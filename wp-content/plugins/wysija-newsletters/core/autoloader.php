<?php
defined('WYSIJA') or die('Restricted access');
/**
 * Classes Autoloader.
 * It loads automatically the right class on class instantation.
 * Since we still can't use namespaces, we use 'WJ_' as a prefix for our classes.
 * @param  Class $class Class name
 * @return
 */
function wysija_classes_autoloader($class) {
    // Check if the class name has our prefix.
    if (strpos($class, 'WJ_') !== false) {
      // Class file path.
      $class_path = WYSIJA_CLASSES . $class . '.php';
      // If the class file exists, let's load it.
      if (file_exists($class_path)) {
        require_once $class_path;
      }
    }
}

// This is the global PHP autoload register, where we register our autoloaders.
spl_autoload_register('wysija_classes_autoloader');