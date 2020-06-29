<?php

namespace MailOptin\PolylangConnect;

class Connect
{
    /**
     * @var string key of connection service. its important all connection name ends with "Connect"
     */
    public static $connectionName = 'PolylangConnect';

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
        return function_exists('pll_current_language');
    }

    /**
     *
     * @param array $connections
     *
     * @return array
     */
    public function register_connection($connections)
    {
        $connections[self::$connectionName] = __('Polylang', 'mailoptin');

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