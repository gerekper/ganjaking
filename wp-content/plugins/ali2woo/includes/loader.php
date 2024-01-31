<?php

/**
 * Description of Loader
 *
 * @author Ali2Woo Team
 */

namespace Ali2Woo;
use DI\Container;

class Loader {

    const DEFAULT_INCLUDE_POSITION = 1000;
    const DEFAULT_INCLIDE_ACTION = 'global';

    static protected $_instance;

    private function __construct() {
        
    }

    static public function getInstance() {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    static public function classes($classpath, $default_load_action, Container $DI) {
        $this_class = Loader::getInstance();

        $result = $this_class->load_classpath($classpath, $default_load_action);

        foreach ($result['delay_include'] as $action => $files) {

            if ('global' === $action) {
                $include_array = array();
                foreach ($files as $file) {
                    $tmp = explode("###", $file);
                    $include_array[$tmp[0]] = $tmp[1];
                }
                asort($include_array);
                foreach ($include_array as $file => $p) {
                    include_once($file);
                }
            } else {
                $this_class->add_method($action . "_inclide", function () use (&$this_class, $action, $files) {
                    $include_array = array();
                    foreach ($files as $file) {
                        $tmp = explode("###", $file);
                        $include_array[$tmp[0]] = $tmp[1];
                    }
                    asort($include_array);
                    foreach ($include_array as $file => $p) {
                        include_once($file);
                    }
                });
                add_action($action, array($this_class, $action . "_inclide"), 10);
            }
        }

        foreach ($result['autoload'] as $action => $class_array) {
            if ('global' === $action) {
                foreach ($class_array as $className) {
                    if(
                        (defined('DOING_AJAX') && in_array($className, $result['skip_ajax'])) || 
                        (defined('DOING_CRON') && in_array($className, $result['skip_cron'])) 
                    ) {
                        continue;
                    }

                    $this_class->createClassInstance($className, $DI);
                }
            } else {
                $this_class->add_method($action, function () use (&$this_class, $action, $class_array, $result, $DI) {
                    foreach ($class_array as $className) {
                        if(
                            (defined('DOING_AJAX') && in_array($className, $result['skip_ajax'])) || 
                            (defined('DOING_CRON') && in_array($className, $result['skip_cron'])) 
                        ) {
                            continue;
                        }

                        $this_class->createClassInstance($className, $DI);
                    }
                });
                add_action($action, array($this_class, $action), 20);
            }
        }
    }

    static public function addons($classpath) {
        if (substr($classpath, -1) !== "/") {
            $classpath.='/';
        }
        $dirs = glob($classpath . '*', GLOB_ONLYDIR);
        if ($dirs && is_array($dirs)) {
            foreach (glob($classpath . '*', GLOB_ONLYDIR) as $dir) {
                $file_list = scandir($dir . '/');
                foreach ($file_list as $f) {
                    if (is_file($dir . '/' . $f)) {
                        $file_info = pathinfo($f);
                        if ($file_info["extension"] == "php") {
                            include_once($dir . '/' . $f);
                        }
                    }
                }
            }
        }
    }

    private function createClassInstance(string $className, Container $DI): void {
        $className = 'Ali2Woo\\' . $className;

        if ($DI->has($className)) {
            //load class using DI id it has DI definition in config
            $DI->get($className);
        } else {
            new $className();
        }
    }

    private function load_classpath($classpath, $default_load_action) {
        $result = array('delay_include' => array(), 'autoload' => array(), 'skip_ajax' => array(), 'skip_cron' => array());
        
        if ($classpath) {
            $classpath .= substr($classpath, -1) === "/" ? "" : "/";

            $include_array = $subdir_array = array();

            foreach (glob($classpath . "*") as $f) {
                if (is_file($f)) {
                    $file_info = pathinfo($f);
                    if ($file_info["extension"] == "php") {
                        $file_data = get_file_data($f, array('position' => '@position', 'autoload' => '@autoload', 'include_action' => '@include_action', 'ajax' => '@ajax', 'cron' => '@cron'));
                        if (isset($file_data['autoload']) && $file_data['autoload']) {
                            $action = (!is_null(filter_var($file_data['autoload'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE))) ? $default_load_action : $file_data['autoload'];
                            if (!isset($result['autoload'][$action])) {
                                $result['autoload'][$action] = array();
                            }
                            $result['autoload'][$action][] = $file_info['filename'];

                            if(isset($file_data['ajax'])){
                                if(!filter_var($file_data['ajax'], FILTER_VALIDATE_BOOLEAN)){
                                    $result['skip_ajax'][] = $file_info['filename'];
                                }
                            }

                            if(isset($file_data['cron'])){
                                if(!filter_var($file_data['cron'], FILTER_VALIDATE_BOOLEAN)){
                                    $result['skip_cron'][] = $file_info['filename'];
                                }
                            }
                        }

                        if (isset($file_data['include_action']) && $file_data['include_action']) {
                            $include_action = (!is_null(filter_var($file_data['include_action'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE))) ? (filter_var($file_data['include_action'], FILTER_VALIDATE_BOOLEAN)?self::DEFAULT_INCLIDE_ACTION:"a2w_fake_action") : $file_data['include_action'];
                            if (!isset($result['delay_include'][$include_action])) {
                                $result['delay_include'][$include_action] = array();
                            }
                            $result['delay_include'][$include_action][] = $f . "###" . (IntVal($file_data['position']) ? IntVal($file_data['position']) : self::DEFAULT_INCLUDE_POSITION);
                        } else {
                            $include_array[$f] = IntVal($file_data['position']) ? IntVal($file_data['position']) : self::DEFAULT_INCLUDE_POSITION;
                        }
                    }
                } else if (is_dir($f)) {
                    $subdir_array[] = $f;
                }
            }
            asort($include_array);
            foreach ($include_array as $file => $p) {
                include_once($file);
            }

            foreach ($subdir_array as $subdir) {
                $result = array_merge_recursive($result, $this->load_classpath($subdir, $default_load_action));
            }
        }

        return $result;
    }

    private function add_method($name, $method) {
        $this->{$name} = $method;
    }

    public function __call($name, $arguments) {
        return call_user_func($this->{$name}, $arguments);
    }

}
