<?php

namespace MailOptin\ElementorConnect;

class Connect
{
    /**
     * @var string key of connection service. its important all connection name ends with "Connect"
     */
    public static $connectionName = 'ElementorConnect';

    public function __construct()
    {
        ConnectSettingsPage::get_instance();
        add_filter('mailoptin_registered_connections', array($this, 'register_connection'));
        Init::get_instance();
    }

    public static function features_support()
    {
        return [];
    }

    /**
     * Is Elementor successfully connected to?
     *
     * @return bool
     */
    public static function is_connected()
    {
        return class_exists('ElementorPro\Plugin');
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
        $connections[self::$connectionName] = esc_html__('Elementor', 'mailoptin');

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