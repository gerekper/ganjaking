<?php

namespace MailOptin\WeMailConnect;

use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\PluginSettings\Connections;
use MailOptin\Core\PluginSettings\Settings;

class AbstractWeMailConnect extends AbstractConnect
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
        $this->api_key              = $this->connections_settings->wemail_api_key();
        parent::__construct();
    }

    /**
     * Is WeMail successfully connected to?
     *
     * @return bool
     */
    public static function is_connected($return_error = false)
    {
        $db_options = isset($_POST['mailoptin_connections']) ? $_POST['mailoptin_connections'] : get_option(MAILOPTIN_CONNECTIONS_DB_OPTION_NAME);
        $api_key    = isset($db_options['wemail_api_key']) ? $db_options['wemail_api_key'] : '';

        if (empty($api_key)) {
            delete_transient('_mo_wemail_is_connected');

            return false;
        }

        if (isset($_POST['wp_csa_nonce'])) {
            delete_transient('_mo_wemail_is_connected');
        }

        //Check for connection status from cache
        if ('true' == get_transient('_mo_wemail_is_connected')) {
            return true;
        }

        try {

            $wemail = new APIClass($api_key);
            $result   = $wemail->make_request('lists');

            if (self::is_http_code_success($result['status_code']) && isset($result['body']['lists'])) {
                set_transient('_mo_wemail_is_connected', 'true', WEEK_IN_SECONDS);

                return true;
            }

            return $return_error === true ? $result['body']['message'] : false;

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
    public function wemail_instance()
    {
        $api_key = $this->connections_settings->wemail_api_key();

        if (empty($api_key)) {
            throw new \Exception(__('weMail API Key not found.', 'mailoptin'));
        }

        return new APIClass($api_key);
    }
}