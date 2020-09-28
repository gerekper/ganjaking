<?php

namespace MailOptin\ActiveCampaignConnect;

use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\PluginSettings\Connections;
use MailOptin\Core\PluginSettings\Settings;

class AbstractActiveCampaignConnect extends AbstractConnect
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
     * Is ActiveCampaign successfully connected to?
     *
     * @return bool
     */
    public static function is_connected($return_error = false)
    {
        $db_options = $db_options = isset($_POST['mailoptin_connections']) ? $_POST['mailoptin_connections'] : get_option(MAILOPTIN_CONNECTIONS_DB_OPTION_NAME);
        $api_url    = isset($db_options['activecampaign_api_url']) ? $db_options['activecampaign_api_url'] : '';
        $api_key    = isset($db_options['activecampaign_api_key']) ? $db_options['activecampaign_api_key'] : '';

        if (empty($api_key)) {
            delete_transient('_mo_activecampaign_is_connected');

            return false;
        }

        if (isset($_POST['wp_csa_nonce'])) {
            delete_transient('_mo_activecampaign_is_connected');
        }

        //Check for connection details from cache
        if ('true' == get_transient('_mo_activecampaign_is_connected')) {
            return true;
        }

        try {

            $api = new \ActiveCampaign($api_url, $api_key);

            $response = $api->api('list/list?ids=all');

            if ( ! is_object($response)) return false;

            if (self::is_http_code_success($response->http_code) && 1 === $response->result_code) {
                set_transient('_mo_activecampaign_is_connected', 'true', WEEK_IN_SECONDS);

                return true;
            }

            return $return_error === true ? $response->result_message : false;

        } catch (\Exception $e) {

            return $return_error === true ? $e->getMessage() : false;
        }
    }

    /**
     * Returns instance of API class.
     *
     * @return \ActiveCampaign
     * @throws \Exception
     *
     */
    public function activecampaign_instance()
    {
        $api_url = $this->connections_settings->activecampaign_api_url();
        $api_key = $this->connections_settings->activecampaign_api_key();

        if (empty($api_url)) {
            throw new \Exception(__('ActiveCampaign API URL not found.', 'mailoptin'));
        }

        if (empty($api_key)) {
            throw new \Exception(__('ActiveCampaign API key not found.', 'mailoptin'));
        }

        return new \ActiveCampaign($api_url, $api_key);
    }
}