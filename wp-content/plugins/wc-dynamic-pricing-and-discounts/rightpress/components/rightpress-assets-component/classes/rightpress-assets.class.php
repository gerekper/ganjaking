<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * RightPress Assets Class
 *
 * @class RightPress_Assets
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Assets
{

    // Properties to be overriden by plugins
    protected $plugin_prefix = null;

    // Hold assets
    protected $assets = array();

    // Define included assets
    protected $included_assets = array(
        'datetimepicker' => 'RightPress_Datetimepicker',
    );

    /**
     * Singleton control
     *
     * Child classes must define instance property:
     * protected static $instance = false;
     *
     * @access public
     * @return object
     */
    public static function get_instance()
    {

        // Get called child class
        $class = get_called_class();

        // Instantiate child class if it does not exist yet
        if (!$class::$instance) {
            $class::$instance = new $class;
        }

        // Return instance of child
        return $class::$instance;
    }

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Load asset
     *
     * @access public
     * @param string $key
     * @param array $asset_args
     * @return object|bool
     */
    public function load_asset($key, $asset_args = array())
    {

        // Asset already loaded, return asset object
        if (isset($this->assets[$key])) {
            return $this->assets[$key];
        }

        // Check if asset is included
        $is_included = isset($this->included_assets[$key]);

        // Invalid asset
        if (!$is_included && empty($asset_args['class'])) {
            RightPress_Help::doing_it_wrong((get_called_class() . '::' . __FUNCTION__), 'Invalid asset: ' . $key . '.', '1.0');
            return false;
        }

        // Get asset class name
        $class_name = $is_included ? $this->included_assets[$key] : $asset_args['class'];

        // Require included asset
        if ($is_included && !class_exists($class_name)) {

            // Format class path
            $class_path = $this->get_included_assets_path($key . '/rightpress-' . $key . '.class.php');

            // Check if path exists
            if (!file_exists($class_path)) {
                RightPress_Help::doing_it_wrong((get_called_class() . '::' . __FUNCTION__), 'Unable to load asset class for asset: ' . $key . '.', '1.0');
                return false;
            }

            // Require class
            require_once $class_path;
        }

        // Get asset constructor arguments
        $arguments = $this->get_asset_constructor_arguments($key, $asset_args);

        // Load asset
        $this->assets[$key] = new $class_name($arguments);

        // Return loaded asset object
        return $this->assets[$key];
    }

    /**
     * Load asset scripts on page
     *
     * This method accepts one asset key as string and multiple asset keys as array
     *
     * @access public
     * @param string|array $asset_keys
     * @return bool
     */
    public function load_asset_scripts($asset_keys)
    {

        return $this->load_asset_assets('scripts', $asset_keys);
    }

    /**
     * Load asset styles on page
     *
     * This method accepts one asset key as string and multiple asset keys as array
     *
     * @access public
     * @param string|array $asset_keys
     * @return bool
     */
    public function load_asset_styles($asset_keys)
    {

        return $this->load_asset_assets('styles', $asset_keys);
    }

    /**
     * Load asset scripts or styles on page
     *
     * @access protected
     * @param string $context
     * @param string|array $asset_keys
     * @return bool
     */
    public function load_asset_assets($context, $asset_keys)
    {

        $result = false;

        // Cast keys to array in case it's single key
        $asset_keys = (array) $asset_keys;

        // Iterate over assets to load
        foreach ($asset_keys as $key) {

            // Load asset
            if ($asset = $this->load_asset($key)) {

                // Format method name
                $method = 'load_' . $context;

                // Load assets
                $asset->$method();

                // Set result
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Get included assets path
     *
     * @access public
     * @param string $relative_path
     * @return string
     */
    public function get_included_assets_path($relative_path = '')
    {
        return dirname(__FILE__) . '/../assets/' . $relative_path;
    }

    /**
     * Get plugin prefix
     *
     * @access public
     * @return string
     */
    public function get_plugin_prefix()
    {

        return $this->plugin_prefix;
    }

    /**
     * Get asset constructor arguments
     *
     * @access public
     * @param string $key
     * @param array $asset_args
     * @return array
     */
    public function get_asset_constructor_arguments($key, $asset_args = array())
    {

        return array_merge($asset_args, array(
            'controller' => $this,
        ));
    }





}
