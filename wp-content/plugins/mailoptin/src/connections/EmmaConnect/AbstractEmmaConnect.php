<?php

namespace MailOptin\EmmaConnect;

use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\PluginSettings\Connections;
use MailOptin\Core\PluginSettings\Settings;

class AbstractEmmaConnect extends AbstractConnect
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

        $public_api_key  = isset($db_options['emma_public_api_key']) ? $db_options['emma_public_api_key'] : '';
        $private_api_key = isset($db_options['emma_private_api_key']) ? $db_options['emma_private_api_key'] : '';
        $account_id      = isset($db_options['emma_account_id']) ? $db_options['emma_account_id'] : '';

        //If the user has not setup emma, abort early
        if (empty($public_api_key) && empty($private_api_key) && empty($account_id)) {
            delete_transient('_mo_emma_is_connected');

            return false;
        }

        if (empty($public_api_key)) {
            if ($return_error) {
                return esc_html__('Provide a valid Public Key', 'mailoptin');
            }

            return false;
        }

        if (empty($private_api_key)) {
            if ($return_error) {
                return esc_html__('Provide a valid Public Key', 'mailoptin');
            }

            return false;
        }

        if (empty($account_id)) {
            if ($return_error) {
                return esc_html__('Provide a valid Account ID', 'mailoptin');
            }

            return false;
        }

        if (isset($_POST['wp_csa_nonce'])) {
            delete_transient('_mo_emma_is_connected');
        }

        //Check for connection status from cache
        if ('true' == get_transient('_mo_emma_is_connected')) {
            return true;
        }

        try {

            $emma   = new APIClass($public_api_key, $private_api_key, $account_id);
            $result = $emma->make_request('groups');

            if (self::is_http_code_success($result['status_code'])) {
                set_transient('_mo_emma_is_connected', 'true', WEEK_IN_SECONDS);

                return true;
            }

            return false;

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
    public function emma_instance()
    {
        $emma_public_api_key  = $this->connections_settings->emma_public_api_key();
        $emma_private_api_key = $this->connections_settings->emma_private_api_key();
        $emma_account_id      = $this->connections_settings->emma_account_id();

        if (empty($emma_public_api_key)) {
            throw new \Exception(__('Emma public key not found.', 'mailoptin'));
        }

        if (empty($emma_private_api_key)) {
            throw new \Exception(__('Emma private key not found.', 'mailoptin'));
        }

        if (empty($emma_account_id)) {
            throw new \Exception(__('Emma account ID not found.', 'mailoptin'));
        }

        return new APIClass($emma_public_api_key, $emma_private_api_key, $emma_account_id);
    }
}