<?php

namespace MailOptin\WPFormsConnect;


class Connect
{
    /**
     * @var string key of connection service. its important all connection name ends with "Connect"
     */
    public static $connectionName = 'WPFormsConnect';

    public function __construct()
    {
        ConnectSettingsPage::get_instance();
        add_filter('mailoptin_registered_connections', array($this, 'register_connection'));

        add_action('wpforms_loaded', function () {
            MailOptin::get_instance();
        });
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
        return class_exists('WPForms\WPForms');
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
        $connections[self::$connectionName] = esc_html__('WPForms', 'mailoptin');

        return $connections;
    }

    /**
     * Singleton poop.
     *
     * @return Connect|null
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