<?php

namespace MailOptin\GetResponseConnect;

use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\PluginSettings\Connections;
use MailOptin\Core\PluginSettings\Settings;

class AbstractGetResponseConnect extends AbstractConnect
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
     * Is GetResponse successfully connected to?
     *
     * @return bool
     */
    public static function is_connected($return_error = false)
    {
        $db_options                       = isset($_POST['mailoptin_connections']) ? $_POST['mailoptin_connections'] : get_option(MAILOPTIN_CONNECTIONS_DB_OPTION_NAME);
        $api_key                          = isset($db_options['getresponse_api_key']) ? $db_options['getresponse_api_key'] : '';
        $getresponse_is_360               = isset($db_options['getresponse_is_360']) ? $db_options['getresponse_is_360'] : '';
        $getresponse360_registered_domain = isset($db_options['getresponse360_registered_domain']) ? $db_options['getresponse360_registered_domain'] : '';
        $getresponse360_country           = isset($db_options['getresponse360_country']) ? $db_options['getresponse360_country'] : '';

        //If the user has not setup getresponse, abort early
        if (empty($api_key)) {
            delete_transient('_mo_getresponse_is_connected');

            return false;
        }

        if (isset($_POST['wp_csa_nonce'])) {
            delete_transient('_mo_getresponse_is_connected');
        }

        //Check for connection status from cache
        if ('true' == get_transient('_mo_getresponse_is_connected')) {
            return true;
        }

        try {

            $api = new GetResponseAPI3($api_key);

            if ($getresponse_is_360 == 'true' && isset($getresponse360_country) && $getresponse360_country != 'none') {
                $api->enterprise_domain = $getresponse360_registered_domain;
                $api->api_url           = 'https://api3.getresponse360.com/v3'; //default

                if ($getresponse360_country == 'poland') {
                    $api->api_url = 'https://api3.getresponse360.pl/v3'; //for PL domains
                }
            }

            $response = (array) $api->accounts();

            if ( ! isset($response['message'], $response['moreInfo'])) {
                set_transient('_mo_getresponse_is_connected', 'true', WEEK_IN_SECONDS);
                return true;
            }

            return $return_error === true ? $response['message'] : false;

        } catch (\Exception $e) {
            return $return_error === true ? $e->getMessage() : false;
        }
    }

    /**
     * Returns instance of API class.
     *
     * @throws \Exception
     *
     * @return GetResponseAPI3
     */
    public function getresponse_instance()
    {
        $api_key                          = $this->connections_settings->getresponse_api_key();
        $getresponse_is_360               = $this->connections_settings->getresponse_is_360();
        $getresponse360_registered_domain = $this->connections_settings->getresponse360_registered_domain();
        $getresponse360_country           = $this->connections_settings->getresponse360_country();

        if (empty($api_key)) {
            throw new \Exception(__('GetResponse API key not found.', 'mailoptin'));
        }

        $getresponse = new GetResponseAPI3($api_key);

        if ($getresponse_is_360 == 'true' && isset($getresponse360_country) && $getresponse360_country != 'none') {
            $getresponse->enterprise_domain = $getresponse360_registered_domain;

            $getresponse->api_url = 'https://api3.getresponse360.com/v3'; //default

            if ($getresponse360_country == 'poland') {
                $getresponse->api_url = 'https://api3.getresponse360.pl/v3'; //for PL domains
            }
        }

        return $getresponse;
    }
}