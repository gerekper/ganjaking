<?php

namespace MailOptin\OntraportConnect;

use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\PluginSettings\Connections;
use MailOptin\Core\PluginSettings\Settings;

class AbstractOntraportConnect extends AbstractConnect
{
    /** @var \MailOptin\Core\PluginSettings\Settings */
    protected $plugin_settings;

    /** @var \MailOptin\Core\PluginSettings\Connections */
    protected $connections_settings;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->plugin_settings      = Settings::instance();
        $this->connections_settings = Connections::instance();

        parent::__construct();
    }

    /**
     * Is Ontraport successfully connected to?
     *
     * @return bool
     */
    public static function is_connected($return_error = false)
    {
        $db_options = isset($_POST['mailoptin_connections']) ? $_POST['mailoptin_connections'] : get_option(MAILOPTIN_CONNECTIONS_DB_OPTION_NAME);
        $api_key    = isset($db_options['ontraport_api_key']) ? $db_options['ontraport_api_key'] : '';
        $app_id     = isset($db_options['ontraport_app_id']) ? $db_options['ontraport_app_id'] : '';

        //If the user has not setup ontraport, abort early
        if (empty($api_key) && empty($app_id)) {
            delete_transient('_mo_ontraport_is_connected');

            return false;
        }

        //Verify the api key
        if (empty($api_key)) {
            if ($return_error) {
                return esc_html__('Provide a valid api key', 'mailoptin');
            }

            return false;
        }

        //Verify the app id
        if (empty($app_id)) {
            if ($return_error) {
                return esc_html__('Provide a valid app id', 'mailoptin');
            }

            return false;
        }

        //In case the user is saving options, clear cache
        if (isset($_POST['wp_csa_nonce'])) {
            delete_transient('_mo_ontraport_is_connected');
        }

        //Check for connection status from cache
        if ('true' == get_transient('_mo_ontraport_is_connected')) {
            return true;
        }

        try {

            $api = new APIClass($api_key, $app_id);
            $api->get_fields();

            set_transient('_mo_ontraport_is_connected', 'true', WEEK_IN_SECONDS);

            return true;

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
    public function ontraportInstance()
    {
        $api_key = $this->connections_settings->ontraport_api_key();
        $app_id  = $this->connections_settings->ontraport_app_id();

        if (empty($api_key)) {
            throw new \Exception(__('Ontraport API Key not found.', 'mailoptin'));
        }

        if (empty($app_id)) {
            throw new \Exception(__('Ontraport APP ID not found.', 'mailoptin'));
        }

        return new APIClass($api_key, $app_id);
    }
}