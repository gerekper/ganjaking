<?php

namespace MailOptin\MailChimpConnect;

use Mailchimp\http\MailchimpCurlHttpClient;
use Mailchimp\MailchimpCampaigns;
use Mailchimp\MailchimpLists;
use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\PluginSettings\Connections;
use MailOptin\Core\PluginSettings\Settings;
use MailOptin\Core\Repositories\OptinCampaignsRepository;

class AbstractMailChimpConnect extends AbstractConnect
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
     * Is MailChimp successfully connected to?
     *
     * @return bool
     */
    public static function is_connected($return_error = false)
    {
        $db_options = isset($_POST['mailoptin_connections']) ? $_POST['mailoptin_connections'] : get_option(MAILOPTIN_CONNECTIONS_DB_OPTION_NAME);
        $api_key    = isset($db_options['mailchimp_api_key']) ? $db_options['mailchimp_api_key'] : '';

        //If the user has not setup MC, abort early
        if (empty($api_key)) {
            delete_transient('_mo_mailchimp_is_connected');

            return false;
        }

        if (isset($_POST['wp_csa_nonce'])) {
            delete_transient('_mo_mailchimp_is_connected');
        }

        //Check for connection status from cache
        if ('true' == get_transient('_mo_mailchimp_is_connected')) {
            return true;
        }

        try {

            $client = new MailchimpCurlHttpClient(['timeout' => 10]);
            $api    = new MailchimpCampaigns($api_key, 'apikey', ['timeout' => 10], $client);
            $result = $api->getAccount();

            if (isset($result->account_id) && $result->account_id) {
                set_transient('_mo_mailchimp_is_connected', 'true', WEEK_IN_SECONDS);

                return true;
            }

            return false;

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

    public function get_first_name_merge_tag()
    {
        $firstname_key    = 'FNAME';
        $db_firstname_key = $this->get_integration_data('MailChimpConnect_first_name_field_key');

        if ( ! empty($db_firstname_key) && $db_firstname_key !== $firstname_key) {
            $firstname_key = $db_firstname_key;
        }

        return apply_filters('mo_connections_mailchimp_firstname_key', $firstname_key);
    }

    public function get_last_name_merge_tag()
    {
        $lastname_key    = 'LNAME';
        $db_lastname_key = $this->get_integration_data('MailChimpConnect_last_name_field_key');

        if ( ! empty($db_lastname_key) && $db_lastname_key !== $lastname_key) {
            $lastname_key = $db_lastname_key;
        }

        return apply_filters('mo_connections_mailchimp_lastname_key', $lastname_key);
    }

    /**
     * Return instance of MailChimp list class.
     *
     * @throws \Exception
     *
     * @return MailchimpLists
     */
    public function mc_list_instance()
    {
        $api_key = $this->connections_settings->mailchimp_api_key();

        if (empty($api_key)) {
            throw new \Exception(__('MailChimp API key not found.', 'mailoptin'));
        }

        $client = new MailchimpCurlHttpClient(['timeout' => 10]);

        return new MailchimpLists($api_key, 'apikey', ['timeout' => 10], $client);
    }

    /**
     * Return instance of MailChimp campaign class.
     *
     * @throws \Exception
     *
     * @return MailchimpCampaigns
     */
    public function mc_campaign_instance()
    {
        $api_key = $this->connections_settings->mailchimp_api_key();

        if (empty($api_key)) {
            throw new \Exception(__('MailChimp API key not found.', 'mailoptin'));
        }

        $client = new MailchimpCurlHttpClient(['timeout' => 10]);

        return new MailchimpCampaigns($api_key, 'apikey', ['timeout' => 10], $client);
    }
}