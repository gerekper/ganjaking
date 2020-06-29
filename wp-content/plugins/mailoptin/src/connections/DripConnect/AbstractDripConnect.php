<?php

namespace MailOptin\DripConnect;

use DrewM\Drip\Drip;
use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\PluginSettings\Connections;
use MailOptin\Core\PluginSettings\Settings;
use MailOptin\Core\Repositories\OptinCampaignsRepository;

class AbstractDripConnect extends AbstractConnect
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
     * Is Drip successfully connected to?
     *
     * @return bool
     */
    public static function is_connected($return_error = false)
    {
        $db_options = isset($_POST['mailoptin_connections']) ? $_POST['mailoptin_connections'] : get_option(MAILOPTIN_CONNECTIONS_DB_OPTION_NAME);
        $api_token  = isset($db_options['drip_api_token']) ? $db_options['drip_api_token'] : '';
        $account_id = isset($db_options['drip_account_id']) ? $db_options['drip_account_id'] : '';

        if (empty($api_token)) {
            delete_transient('_mo_drip_is_connected');

            return false;
        }

        if (isset($_POST['wp_csa_nonce'])) {
            delete_transient('_mo_drip_is_connected');
        }

        //Check for connection status from cache
        if ('true' == get_transient('_mo_drip_is_connected')) {
            return true;
        }

        try {

            $api    = new Drip($api_token, $account_id);
            $result = $api->get('forms');

            if (self::is_http_code_success($result->status)) {
                set_transient('_mo_drip_is_connected', 'true', WEEK_IN_SECONDS);

                return true;
            }

            return $return_error === true ? $result->message : false;

        } catch (\Exception $e) {

            return $return_error === true ? $e->getMessage() : false;
        }

    }

    public function get_integration_data($data_key, $integration_data = [], $default = '')
    {
        if (empty($integration_data) && ! empty($_POST['optin_campaign_id'])) {
            $optin_campaign_id = absint($_POST['optin_campaign_id']);
            $index             = absint($_POST['integration_index']);
            $val               = json_decode(OptinCampaignsRepository::get_customizer_value($optin_campaign_id, 'integrations'), true);

            if (isset($val[$index])) {
                $integration_data = $val[$index];
            }
        }

        return parent::get_integration_data($data_key, $integration_data, $default);
    }

    public function get_first_name_custom_field()
    {
        $firstname_key    = 'first_name';
        $db_firstname_key = $this->get_integration_data('DripConnect_first_name_field_key');

        if ( ! empty($db_firstname_key) && $db_firstname_key !== $firstname_key) {
            $firstname_key = $db_firstname_key;
        }

        return apply_filters('mo_connections_drip_firstname_key', $firstname_key);
    }

    public function get_last_name_custom_field()
    {
        $lastname_key    = 'last_name';
        $db_lastname_key = $this->get_integration_data('DripConnect_last_name_field_key');

        if ( ! empty($db_lastname_key) && $db_lastname_key !== $lastname_key) {
            $lastname_key = $db_lastname_key;
        }

        return apply_filters('mo_connections_drip_lastname_key', $lastname_key);
    }

    /**
     * Returns instance of API class.
     *
     * @throws \Exception
     *
     * @return Drip
     */
    public function drip_instance()
    {
        $api_token  = $this->connections_settings->drip_api_token();
        $account_id = $this->connections_settings->drip_account_id();

        if (empty($api_token)) {
            throw new \Exception(__('Drip API Token not found.', 'mailoptin'));
        }

        if (empty($account_id)) {
            throw new \Exception(__('Drip Account ID not found.', 'mailoptin'));
        }

        return new Drip($api_token, $account_id);
    }
}