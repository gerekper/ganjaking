<?php

namespace MailOptin\MailjetConnect;

use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\PluginSettings\Connections;
use MailOptin\Core\PluginSettings\Settings;

class AbstractMailjetConnect extends AbstractConnect
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
     * Is Mailjet successfully connected to?
     *
     * @return bool
     */
    public static function is_connected($return_error = false)
    {
        $db_options = isset($_POST['mailoptin_connections']) ? $_POST['mailoptin_connections'] : get_option(MAILOPTIN_CONNECTIONS_DB_OPTION_NAME);

        //Set up details
        $api_key    = isset($db_options['mailjet_api_key']) ? $db_options['mailjet_api_key'] : '';
        $secret_key = isset($db_options['mailjet_secret_key']) ? $db_options['mailjet_secret_key'] : '';

        //If the user has not setup mailjet, abort early
        if (empty($api_key) && empty($secret_key)) {
            delete_transient('_mo_mailjet_is_connected');
            return false;
        }

        //Verify the api key
        if (empty($api_key)) {
            if ($return_error) {
                return esc_html__('Provide a valid API key', 'mailoptin');
            }

            return false;
        }

        //Verify the secret key
        if (empty($secret_key)) {
            if ($return_error) {
                return esc_html__('Provide a valid secret key', 'mailoptin');
            }

            return false;
        }

        //If the user has edited the settings, clear cache
        if (isset($_POST['wp_csa_nonce'])) {
            delete_transient('_mo_mailjet_is_connected');
        }

        //Check for connection status from cache
        if ('true' == get_transient('_mo_mailjet_is_connected')) {
            return true;
        }

        try {

            $api    = new APIClass($api_key, $secret_key);
            $lists  = $api->get_lists(1);

            //Sucessfully fetched lists
            if ( is_array($lists) ) {
                set_transient('_mo_mailjet_is_connected', 'true', WEEK_IN_SECONDS);
                delete_transient('_mo_mailjet_get_sender_id');
                return true;
            }

            //If we are here, then an unknown error occured
            return false;

        } catch (\Exception $e) {

            //Maybe return any error returned by the api class
            return $return_error === true ? $e->getMessage() : false;
        }

    }

    public function get_first_name_property()
    {
        $firstname_key    = 'firstname';
        $db_firstname_key = $this->get_integration_data('MailjetConnect_first_name_field_key');

        if ( ! empty($db_firstname_key) && $db_firstname_key !== $firstname_key) {
            $firstname_key = $db_firstname_key;
        }

        return apply_filters('mo_connections_mailjet_firstname_key', $firstname_key);
    }

    public function get_last_name_property()
    {
        $lastname_key    = 'name';
        $db_lastname_key = $this->get_integration_data('MailjetConnect_last_name_field_key');

        if ( ! empty($db_lastname_key) && $db_lastname_key !== $lastname_key) {
            $lastname_key = $db_lastname_key;
        }

        return apply_filters('mo_connections_mailjet_lastname_key', $lastname_key);
    }

    /**
     * Returns instance of API class.
     *
     * @throws \Exception
     *
     * @return APIClass
     */
    public function mailjet_instance()
    {
        $api_key    = $this->connections_settings->mailjet_api_key();
        $secret_key = $this->connections_settings->mailjet_secret_key();

        if (empty($api_key)) {
            throw new \Exception(__('Mailjet API Key not found.', 'mailoptin'));
        }

        if (empty($secret_key)) {
            throw new \Exception(__('Mailjet secret key not found.', 'mailoptin'));
        }

        return new APIClass($api_key, $secret_key);
    }
}