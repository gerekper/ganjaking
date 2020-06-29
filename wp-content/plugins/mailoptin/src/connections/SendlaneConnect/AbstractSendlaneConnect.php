<?php

namespace MailOptin\SendlaneConnect;

use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\PluginSettings\Connections;
use MailOptin\Core\PluginSettings\Settings;

class AbstractSendlaneConnect extends AbstractConnect
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

        //Set up details
        $api_key  = isset($db_options['sendlane_api_key']) ? $db_options['sendlane_api_key'] : '';
        $hash_key = isset($db_options['sendlane_hash_key']) ? $db_options['sendlane_hash_key'] : '';
        $domain   = isset($db_options['sendlane_domain']) ? $db_options['sendlane_domain'] : '';

        //If the user has not setup sendlane, abort early
        if (empty($api_key) && empty($hash_key)) {
            delete_transient('_mo_sendlane_is_connected');

            return false;
        }

        //Verify the api key
        if (empty($api_key)) {
            if ($return_error) {
                return esc_html__('Provide a valid API Key', 'mailoptin');
            }

            return false;
        }

        //Verify the hash key
        if (empty($hash_key)) {
            if ($return_error) {
                return esc_html__('Provide a valid Hash Key', 'mailoptin');
            }

            return false;
        }

        //Verify the domain
        if (empty($domain)) {
            if ($return_error) {
                return esc_html__('Provide a valid Sendlane domain', 'mailoptin');
            }

            return false;
        }

        if (isset($_POST['wp_csa_nonce'])) {
            delete_transient('_mo_sendlane_is_connected');
        }

        //Check for connection status from cache
        if ('true' == get_transient('_mo_sendlane_is_connected')) {
            return true;
        }

        try {

            $sendlane = new APIClass($api_key, $hash_key, $domain);
            $result   = $sendlane->make_request('lists');

            if ( ! isset($result['body']->error)) {
                set_transient('_mo_sendlane_is_connected', 'true', WEEK_IN_SECONDS);

                return true;
            }

            if ($return_error) {
                $error = (array)$result['body']->error;
                $error = array_values($error);

                return array_pop($error);
            }

            return false;

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
    public function sendlane_instance()
    {
        $sendlane_api_key  = $this->connections_settings->sendlane_api_key();
        $sendlane_hash_key = $this->connections_settings->sendlane_hash_key();
        $sendlane_domain   = $this->connections_settings->sendlane_domain();

        if (empty($sendlane_api_key)) {
            throw new \Exception(__('Sendlane API Key not found.', 'mailoptin'));
        }

        if (empty($sendlane_hash_key)) {
            throw new \Exception(__('Sendlane Hash Key not found.', 'mailoptin'));
        }

        if (empty($sendlane_domain)) {
            throw new \Exception(__('Sendlane Domain not found.', 'mailoptin'));
        }

        return new APIClass($sendlane_api_key, $sendlane_hash_key, $sendlane_domain);
    }
}