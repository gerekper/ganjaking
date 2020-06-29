<?php

namespace MailOptin\MailerliteConnect;

use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\PluginSettings\Connections;
use MailOptin\Core\PluginSettings\Settings;
use MailOptin\MailerliteConnect\APIClass\MailerLite;

class AbstractMailerliteConnect extends AbstractConnect
{
    /** @var \MailOptin\Core\PluginSettings\Settings */
    protected $plugin_settings;

    /** @var \MailOptin\Core\PluginSettings\Connections */
    protected $connections_settings;

    public function __construct()
    {
        $this->plugin_settings      = Settings::instance();
        $this->connections_settings = Connections::instance();

        parent::__construct();
    }

    /**
     * Is MailerLite successfully connected to?
     *
     * @return bool
     */
    public static function is_connected($return_error = false)
    {
        $db_options = isset($_POST['mailoptin_connections']) ? $_POST['mailoptin_connections'] : get_option(MAILOPTIN_CONNECTIONS_DB_OPTION_NAME);

        $api_key = isset($db_options['mailerlite_api_key']) ? $db_options['mailerlite_api_key'] : '';

        if (empty($api_key)) {
            delete_transient('_mo_mailerlite_is_connected');

            return false;
        }

        if (isset($_POST['wp_csa_nonce'])) {
            delete_transient('_mo_mailerlite_is_connected');
        }

        //Check for connection status from cache
        if ('true' == get_transient('_mo_mailerlite_is_connected')) {
            return true;
        }

        try {

            $api    = new MailerLite($api_key);
            $result = $api->settings()->getDoubleOptin();

            if (empty($result->error)) {
                set_transient('_mo_mailerlite_is_connected', 'true', WEEK_IN_SECONDS);

                return true;
            }

            return $return_error === true ? $result->error->message : false;

        } catch (\Exception $e) {

            return $return_error === true ? $e->getMessage() : false;
        }

    }

    /**
     * Returns instance of API class.
     *
     * @throws \Exception
     *
     * @return MailerLite
     */
    public function mailerlite_instance()
    {
        $api_key = $this->connections_settings->mailerlite_api_key();

        if (empty($api_key)) {
            throw new \Exception(__('MailerLite API key not found.', 'mailoptin'));
        }

        return new MailerLite($api_key);
    }
}