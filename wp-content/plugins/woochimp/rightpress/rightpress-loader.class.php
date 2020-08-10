<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * Version Control
 */
$version = '1021';

global $rightpress_version;

if (!$rightpress_version || $rightpress_version < $version) {
    $rightpress_version = $version;
}

// Check if class has already been loaded
if (!class_exists('RightPress_Loader_1021')) {

    /**
     * Main Loader Class
     *
     * @class RightPress_Loader_1021
     * @package RightPress
     * @author RightPress
     */
    final class RightPress_Loader_1021
    {

        /**
         * Get version number
         *
         * @access public
         * @return string
         */
        public static function get_version()
        {

            global $rightpress_version;
            return $rightpress_version;
        }

        /**
         * Load classes used in all RightPress plugins
         *
         * Note: Plugins must call this during plugins_loaded action
         *
         * @access public
         * @return void
         */
        public static function load()
        {

            // Initialize
            self::init();

            // Load helper classes
            require_once self::get_path('classes/helper/rightpress-conditions.class.php');
            require_once self::get_path('classes/helper/rightpress-forms.class.php');
            require_once self::get_path('classes/helper/rightpress-help.class.php');
            require_once self::get_path('classes/helper/rightpress-wc.class.php');

            // Load utility classes
            require_once self::get_path('classes/utility/rightpress-datetime.class.php');
            require_once self::get_path('classes/utility/rightpress-exception.class.php');
            require_once self::get_path('classes/utility/rightpress-main-site-controller.class.php');
            require_once self::get_path('classes/utility/rightpress-scheduler.class.php');
            require_once self::get_path('classes/utility/rightpress-legacy.class.php');
            require_once self::get_path('classes/utility/rightpress-time.class.php');

            // Load library legacy class
            require_once self::get_path('classes/rightpress-library-legacy.class.php');

            // Indicate that system is ready for use after init position 9
            add_action('init', function() {
                do_action('rightpress_init');
            }, 9);
        }

        /**
         * Load class collection(s)
         *
         * @access public
         * @param string|array $names
         * @return void
         */
        public static function load_class_collection($names)
        {

            // Initialize
            self::init();

            // Iterate over collection names
            foreach ((array) $names as $name) {

                // Load class collection loader file
                require_once self::get_path('class-collections/' . $name . '/loader.php');
            }
        }

        /**
         * Load component(s)
         *
         * @access public
         * @param string|array $names
         * @return void
         */
        public static function load_component($names)
        {

            // Initialize
            self::init();

            // Iterate over component names
            foreach ((array) $names as $name) {

                // Get main component file path
                $file_path = self::get_component_path($name, ($name . '.class.php'));

                // Component does not exist
                if (!file_exists($file_path)) {
                    RightPress_Help::doing_it_wrong('RightPress_Loader::load_component', "Component $name does not exist.", RightPress_Loader::get_version());
                    exit;
                }

                // Load main component file
                require_once $file_path;
            }
        }

        /**
         * Load jQuery plugin(s)
         *
         * @access public
         * @param string|array $names
         * @return void
         */
        public static function load_jquery_plugin($names)
        {

            global $rightpress_version;

            // Initialize
            self::init();

            // Iterate over plugin names
            foreach ((array) $names as $name) {

                // Get relative file path
                $file_path = 'jquery-plugins/' . $name . '/' . $name;

                // jQuery plugin does not exist
                if (!file_exists(plugin_dir_path(__FILE__) . $file_path . '.js')) {
                    RightPress_Help::doing_it_wrong('RightPress_Loader::load_jquery_plugin', "jQuery plugin $name does not exist.", RightPress_Loader::get_version());
                    exit;
                }

                // Enqueue script file
                wp_enqueue_script($name, RIGHTPRESS_LIBRARY_URL . '/' . $file_path . '.js', array('jquery'), $rightpress_version);

                // Enqueue optional styles file
                if (file_exists(plugin_dir_path(__FILE__) . $file_path . '.css')) {
                    wp_enqueue_style($name, RIGHTPRESS_LIBRARY_URL . '/' . $file_path . '.css', array(), $rightpress_version);
                }
            }
        }

        /**
         * Get path with trailing slash (if $suffix is omitted or includes trailing slash)
         *
         * @access public
         * @param string $suffix
         * @return string
         */
        public static function get_path($suffix = '')
        {

            // Initialize
            self::init();

            // Get path
            return dirname(__FILE__) . '/' . ltrim($suffix, '/');
        }

        /**
         * Get component path with trailing slash (if $suffix is omitted or includes trailing slash)
         *
         * @access public
         * @param string $name
         * @param string $suffix
         * @return string
         */
        public static function get_component_path($name, $suffix = '')
        {

            // Initialize
            self::init();

            // Get component path
            return self::get_path('components/' . $name . '/') . ltrim($suffix, '/');
        }

        /**
         * Get component url with trailing slash (if $suffix is omitted or includes trailing slash)
         *
         * @access public
         * @param string $name
         * @param string $suffix
         * @return string
         */
        public static function get_component_url($name, $suffix = '')
        {

            // Initialize
            self::init();

            // Get component url
            $url = RIGHTPRESS_LIBRARY_URL . '/components/' . $name . '/' . ltrim($suffix, '/');

            // Set correct scheme
            return set_url_scheme($url);
        }

        /**
         * Initialize
         *
         * Note: This method must be called at the beginning of each method of this class
         *
         * @access private
         * @return void
         */
        private static function init()
        {

            // System not ready
            if (!did_action('plugins_loaded')) {
                error_log('Error: RightPress library must not be used before WordPress action plugins_loaded is executed.');
                exit;
            }

            // Define library url
            if (!defined('RIGHTPRESS_LIBRARY_URL')) {
                $library_url = defined('RIGHTPRESS_DEVELOPMENT_ENVIRONMENT') ? 'http://localhost/rightpress' : plugins_url('', __FILE__);
                define('RIGHTPRESS_LIBRARY_URL', $library_url);
            }
        }
    }
}

// Check if class has already been loaded
if (!class_exists('RightPress_Loader')) {

    /**
     * Convenience Loader Class
     *
     * Warning! No changes can be made to this class since this one can be of any version, not the latest one
     * if more than one version of RightPress library is on the same installation
     *
     * @class RightPress_Loader
     * @package RightPress
     * @author RightPress
     */
    final class RightPress_Loader
    {

        /**
         * Method overload
         *
         * @access public
         * @param string $method_name
         * @param array $arguments
         * @return mixed
         */
        public static function __callStatic($method_name, $arguments)
        {

            global $rightpress_version;

            // Call method of main class
            return call_user_func_array(array(('RightPress_Loader_' . $rightpress_version), $method_name), $arguments);
        }
    }
}
