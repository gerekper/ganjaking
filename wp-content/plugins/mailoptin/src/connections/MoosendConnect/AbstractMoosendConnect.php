<?php

namespace MailOptin\MoosendConnect;

use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\PluginSettings\Connections;
use MailOptin\Core\PluginSettings\Settings;

class AbstractMoosendConnect extends AbstractConnect
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
     * Is Moosend successfully connected to?
     *
     * @return bool
     */
    public static function is_connected($return_error = false)
    {
        $db_options = isset($_POST['mailoptin_connections']) ? $_POST['mailoptin_connections'] : get_option(MAILOPTIN_CONNECTIONS_DB_OPTION_NAME);
        $api_key    = isset($db_options['moosend_api_key']) ? $db_options['moosend_api_key'] : '';

        if (empty($api_key)) {
            delete_transient('_mo_moosend_is_connected');

            return false;
        }

        if (isset($_POST['wp_csa_nonce'])) {
            delete_transient('_mo_moosend_is_connected');
        }

        //Check for connection status from cache
        if ('true' == get_transient('_mo_moosend_is_connected')) {
            return true;
        }

        try {

            //Instantiate the class
            $api    = new APIClass($api_key);

            //Try fetching forms
            $api->get_lists();

            //If we are here, the connection is successful
            set_transient('_mo_moosend_is_connected', 'true', WEEK_IN_SECONDS);

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
    public function moosend_instance()
    {
        $api_key = $this->connections_settings->moosend_api_key();

        if (empty($api_key)) {
            throw new \Exception(__('Moosend API Key not found.', 'mailoptin'));
        }

        return new APIClass($api_key);
    }
}