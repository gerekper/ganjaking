<?php

namespace MailOptin\SendFoxConnect;

use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\PluginSettings\Connections;
use MailOptin\Core\PluginSettings\Settings;

class AbstractSendFoxConnect extends AbstractConnect
{
    /** @var \MailOptin\Core\PluginSettings\Settings */
    protected $plugin_settings;

    /** @var \MailOptin\Core\PluginSettings\Connections */
    protected $connections_settings;

    protected $api_key;

    public function __construct()
    {
        $this->plugin_settings      = Settings::instance();
        $this->connections_settings = Connections::instance();
        $this->api_key              = $this->connections_settings->sendfox_api_key();
        parent::__construct();
    }

    /**
     * Is Constant Contact successfully connected to?
     *
     * @return bool
     */
    public static function is_connected($return_error = false)
    {
        $db_options = isset($_POST['mailoptin_connections']) ? $_POST['mailoptin_connections'] : get_option(MAILOPTIN_CONNECTIONS_DB_OPTION_NAME);
        $api_key    = isset($db_options['sendfox_api_key']) ? $db_options['sendfox_api_key'] : '';

        if (empty($api_key)) {
            delete_transient('_mo_sendfox_is_connected');

            return false;
        }

        if (isset($_POST['wp_csa_nonce'])) {
            delete_transient('_mo_sendfox_is_connected');
        }

        //Check for connection status from cache
        if ('true' == get_transient('_mo_sendfox_is_connected')) {
            return true;
        }

        try {

            $result = (new APIClass($api_key))->make_request('me');

            if (self::is_http_code_success($result['status_code']) && isset($result['body']['id'])) {
                set_transient('_mo_sendfox_is_connected', 'true', WEEK_IN_SECONDS);

                return true;
            }

        } catch (\Exception $e) {

            return $return_error === true ? $e->getMessage() : false;
        }
    }

    /**
     * Returns instance of API class.
     *
     * @return APIClass
     * @throws \Exception
     *
     */
    public function sendfox_instance()
    {
        $api_key = $this->connections_settings->sendfox_api_key();

        if (empty($api_key)) {
            throw new \Exception(__('SendFox API Key not found.', 'mailoptin'));
        }

        return new APIClass($api_key);
    }
}