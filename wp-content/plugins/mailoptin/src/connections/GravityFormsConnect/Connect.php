<?php

namespace MailOptin\GravityFormsConnect;

class Connect
{
    /**
     * @var string key of connection service. its important all connection name ends with "Connect"
     */
    public static $connectionName = 'GravityFormsConnect';

    public function __construct()
    {
        ConnectSettingsPage::get_instance();
        add_filter('mailoptin_registered_connections', array($this, 'register_connection'));

        add_action('gform_loaded', function () {
            // Registers the class name with GFAddOn.
            \GFAddOn::register(__NAMESPACE__ . '\GFMailOptin');
        }, 5);
    }

    public static function features_support()
    {
        return [];
    }

    /**
     * @return bool
     */
    public static function is_connected()
    {
        return class_exists('GFForms');
    }

    /**
     * Register EmailOctopus Connection.
     *
     * @param array $connections
     *
     * @return array
     */
    public function register_connection($connections)
    {
        $connections[self::$connectionName] = esc_html__('Gravity Forms', 'mailoptin');

        return $connections;
    }

    /**
     * @return self
     */
    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}