<?php

namespace MailOptin\KlaviyoConnect;

use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\PluginSettings\Connections;
use MailOptin\Core\PluginSettings\Settings;

class AbstractKlaviyoConnect extends AbstractConnect
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
     * Is Klaviyo successfully connected to?
     *
     * @return bool
     */
    public static function is_connected($return_error = false)
    {
        $db_options = isset($_POST['mailoptin_connections']) ? $_POST['mailoptin_connections'] : get_option(MAILOPTIN_CONNECTIONS_DB_OPTION_NAME);
        $api_key    = isset($db_options['klaviyo_api_key']) ? $db_options['klaviyo_api_key'] : '';

        //If the user has not setup klaviyo, abort early
        if (empty($api_key)) {
            delete_transient('_mo_klaviyo_is_connected');

            return false;
        }

        if (isset($_POST['wp_csa_nonce'])) {
            delete_transient('_mo_klaviyo_is_connected');
        }

        //Check for connection status from cache
        if ('true' == get_transient('_mo_klaviyo_is_connected')) {
            return true;
        }

        try {

            $api    = new APIClass($api_key);
            $result = $api->get_lists();

            if (self::is_http_code_success($result['status_code'])) {
                set_transient('_mo_klaviyo_is_connected', 'true', WEEK_IN_SECONDS);

                return true;
            }

            return $return_error === true ? $result['body']->message : false;

        } catch (\Exception $e) {

            return $return_error === true ? $e->getMessage() : false;
        }
    }

    /**
     * Returns instance of API class.
     *
     * @throws \Exception
     *
     * @return APIClass
     */
    public function klaviyo_instance()
    {
        $api_key = $this->connections_settings->klaviyo_api_key();

        if (empty($api_key)) {
            throw new \Exception(__('Klaviyo API Key not found.', 'mailoptin'));
        }

        return new APIClass($api_key);
    }
}