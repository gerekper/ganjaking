<?php

namespace MailOptin\ContactForm7Connect;

class Connect
{
    /**
     * @var string key of connection service. its important all connection name ends with "Connect"
     */
    public static $connectionName = 'ContactForm7Connect';

    public function __construct()
    {
        ConnectSettingsPage::get_instance();
        add_filter('mailoptin_registered_connections', array($this, 'register_connection'));
        add_action('wpcf7_init', function () {
            new CF7();
            \WPCF7_Integration::get_instance()->add_service('mailoptin', IntegrationsListing::get_instance());
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
        return class_exists('WPCF7');
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
        $connections[self::$connectionName] = esc_html__('Contact Form 7', 'mailoptin');

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