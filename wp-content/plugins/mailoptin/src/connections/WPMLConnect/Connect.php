<?php

namespace MailOptin\WPMLConnect;

class Connect
{
    /**
     * @var string key of connection service. its important all connection name ends with "Connect"
     */
    public static $connectionName = 'WPMLConnect';

    public function __construct()
    {
        ConnectSettingsPage::get_instance();
        add_filter('mailoptin_registered_connections', array($this, 'register_connection'));
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
        return defined('ICL_LANGUAGE_CODE');
    }

    /**
     *
     * @param array $connections
     *
     * @return array
     */
    public function register_connection($connections)
    {
        $connections[self::$connectionName] = __('WPML', 'mailoptin');

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