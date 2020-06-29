<?php

namespace MailOptin\SendyConnect;

use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\PluginSettings\Connections;
use MailOptin\Core\PluginSettings\Settings;

class AbstractSendyConnect extends AbstractConnect
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
     * Is successfully connected?
     *
     * @return bool
     */
    public static function is_connected($return_error = false)
    {
        $db_options = isset($_POST['mailoptin_connections']) ? $_POST['mailoptin_connections'] : get_option(MAILOPTIN_CONNECTIONS_DB_OPTION_NAME);

        //Set up details
        $api_key          = isset($db_options['sendy_api_key']) ? $db_options['sendy_api_key'] : '';
        $installation_url = isset($db_options['sendy_installation_url']) ? $db_options['sendy_installation_url'] : '';
        $lists            = isset($db_options['sendy_email_list']) ? array_pop($db_options['sendy_email_list']) : [];
        $provided_data    = ! (empty($api_key) && empty($installation_url));

        //If no data has been provided, return early
        if ( ! $provided_data) {
            delete_transient('_mo_sendy_is_connected');

            return false;
        }

        //Verify the api key
        if (empty($api_key)) {
            if ($return_error) {
                return esc_html__('Provide a valid API Key', 'mailoptin');
            }

            return false;
        }

        //Verify the installation url
        if (empty($installation_url)) {
            if ($return_error) {
                return esc_html__('Provide a valid installation url', 'mailoptin');
            }

            return false;
        }

        //Verify the list id
        if (empty($lists['list_id'])) {
            if ($return_error) {
                return __('Provide a valid list id', 'mailoptin');
            }

            return false;
        }

        if (isset($_POST['wp_csa_nonce'])) {
            delete_transient('_mo_sendy_is_connected');
        }

        //Check for connection status from cache
        if ('true' == get_transient('_mo_sendy_is_connected')) {
            return true;
        }

        $config = array(
            'api_key'          => $api_key,
            'installation_url' => $installation_url,
            'list_id'          => $lists['list_id']
        );

        try {

            $sendy = new SendyPHP($config);
            $subs  = $sendy->subcount();


            if (true == $subs['status']) {
                set_transient('_mo_sendy_is_connected', 'true', WEEK_IN_SECONDS);

                return true;
            }

            return $return_error === true ? $subs['message'] : false;

        } catch (\Exception $e) {

            return $return_error === true ? $e->getMessage() : false;
        }
    }

    /**
     * Return basic sendy API config settings
     */
    public function api_config()
    {
        $reply_to = $this->plugin_settings->reply_to();

        $api_key          = $this->connections_settings->sendy_api_key();
        $installation_url = $this->connections_settings->sendy_installation_url();

        return array(
            'api_key'          => $api_key,
            'installation_url' => $installation_url,
            'reply_to'         => $reply_to
        );
    }
}