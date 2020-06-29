<?php

namespace MailOptin\GEMConnect;

use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\PluginSettings\Connections;
use MailOptin\Core\PluginSettings\Settings;

class AbstractGEMConnect extends AbstractConnect
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
     * Is EmailOctopus successfully connected to?
     *
     * @return bool
     */
    public static function is_connected($return_error = false)
    {
        $db_options = $db_options = isset($_POST['mailoptin_connections']) ? $_POST['mailoptin_connections'] : get_option(MAILOPTIN_CONNECTIONS_DB_OPTION_NAME);
        $api_key    = isset($db_options['gem_api_key']) ? $db_options['gem_api_key'] : '';
        $gem_email  = isset($db_options['gem_email']) ? $db_options['gem_email'] : '';

        if (empty($api_key) || empty($gem_email)) {
            delete_transient('_mo_gem_is_connected');

            return false;
        }

        if (isset($_POST['wp_csa_nonce'])) {
            delete_transient('_mo_gem_is_connected');
        }

        // Check for connection status from cache
        if ('true' == get_transient('_mo_gem_is_connected')) return true;

        try {

            $api      = new APIClass($api_key, $gem_email);
            $response = $api->get_lists();

            if (isset($response['status']) && self::is_http_code_success($response['status'])) {
                set_transient('_mo_gem_is_connected', 'true', WEEK_IN_SECONDS);

                return true;
            }

            return $return_error === true ? $response['body'] : false;

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
    public function gem_instance()
    {
        $gem_email = $this->connections_settings->gem_email();

        if (empty($gem_email)) {
            throw new \Exception(__('GoDaddy Email Marketing email address not found.', 'mailoptin'));
        }

        $api_key = $this->connections_settings->gem_api_key();

        if (empty($api_key)) {
            throw new \Exception(__('GoDaddy Email Marketing API key not found.', 'mailoptin'));
        }

        return new APIClass($api_key, $gem_email);
    }
}