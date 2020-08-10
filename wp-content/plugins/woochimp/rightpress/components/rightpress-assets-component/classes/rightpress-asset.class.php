<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * RightPress Asset Class
 *
 * @class RightPress_Asset
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Asset
{

    // Properties to be overriden by child classes
    protected $key          = null;
    protected $path         = null;
    protected $url          = null;
    protected $scripts      = array();
    protected $styles       = array();
    protected $is_included  = false;

    // Properties set in constructor
    protected $args;
    protected $controller;
    protected $plugin_prefix;

    // Properties set during execution
    protected $scripts_loaded   = false;
    protected $styles_loaded    = false;

    /**
     * Constructor
     *
     * @access public
     * @param array $args
     * @return void
     */
    public function __construct($args)
    {

        // Set all arguments
        $this->args = $args;

        // Set controller
        $this->controller = $args['controller'];

        // Set plugin prefix
        $this->plugin_prefix = $this->controller->get_plugin_prefix();

        // Define scripts and styles
        $this->define_scripts();
        $this->define_styles();
    }

    /**
     * Load scripts
     *
     * @access public
     * @return void
     */
    public function load_scripts()
    {

        // Scripts already loaded
        if ($this->scripts_loaded) {
            return;
        }

        // Iterate over scripts
        foreach ($this->scripts as $script_key => $properties) {

            // Sanitize asset properties
            $properties = $this->sanitize_asset_properties($properties);

            // Enqueue script
            wp_enqueue_script($script_key, $properties['url'], $properties['dependencies'], $properties['version']);

            // Pass variables
            foreach ($properties['variables'] as $variable_key => $value) {

                // Get value using provided callback
                if (is_callable($value)) {
                    $value = call_user_func($value);
                }

                // Allow developers to override
                if ($this->is_included) {
                    $value = apply_filters(('rightpress_' . $this->key . '_' . $variable_key), $value);
                }

                // Allow developers to override
                $value = apply_filters(($this->plugin_prefix . $this->key . '_' . $variable_key), $value);

                // Set value
                // Note: We nest value within wrapper array to prevent WordPress from casting boolean values to '1'/'' strings
                wp_localize_script($script_key, $this->prefix_variable_key($variable_key), array('x' => $value));
            }
        }

        // Set flag
        $this->scripts_loaded = true;
    }

    /**
     * Load styles
     *
     * @access public
     * @return void
     */
    public function load_styles()
    {

        // Styles already loaded
        if ($this->styles_loaded) {
            return;
        }

        // Iterate over styles
        foreach ($this->styles as $style_key => $properties) {

            // Sanitize asset properties
            $properties = $this->sanitize_asset_properties($properties);

            // Enqueue or inject style
            RightPress_Help::enqueue_or_inject_stylesheet($style_key, $properties['url'], $properties['version']);
        }

        // Set flag
        $this->styles_loaded = true;
    }

    /**
     * Sanitize asset properties
     *
     * @access public
     * @param array $properties
     * @return array
     */
    public function sanitize_asset_properties($properties)
    {

        return array(
            'url'           => ($this->url . $properties['relative_url']),
            'dependencies'  => !empty($properties['dependencies']) ? $properties['dependencies'] : array(),
            'version'       => !empty($properties['version']) ? $properties['version'] : $this->get_version(),
            'variables'     => !empty($properties['variables']) ? $properties['variables'] : array(),
        );
    }

    /**
     * Prefix frontend variable key
     *
     * @access public
     * @param string $variable_key
     * @return string
     */
    public function prefix_variable_key($variable_key)
    {

        return $this->plugin_prefix . $this->key . '_' . $variable_key;
    }

    /**
     * Get asset version
     *
     * @access public
     * @return string
     */
    public function get_version()
    {

        global $rightpress_version;
        return $rightpress_version;
    }

    /**
     * Get controller
     *
     * @access public
     * @return object
     */
    public function get_controller()
    {

        return $this->controller;
    }

    /**
     * Define scripts
     *
     * @access public
     * @return void
     */
    public function define_scripts() {}

    /**
     * Define styles
     *
     * @access public
     * @return void
     */
    public function define_styles() {}





}
