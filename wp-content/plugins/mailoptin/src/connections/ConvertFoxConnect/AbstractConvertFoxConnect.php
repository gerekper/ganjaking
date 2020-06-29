<?php

namespace MailOptin\ConvertFoxConnect;

use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\PluginSettings\Connections;
use MailOptin\Core\PluginSettings\Settings;

class AbstractConvertFoxConnect extends AbstractConnect
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
     * Is ConvertFox successfully connected to?
     *
     * @return bool
     */
    public static function is_connected($return_error = false)
    {
        $db_options = isset($_POST['mailoptin_connections']) ? $_POST['mailoptin_connections'] : get_option(MAILOPTIN_CONNECTIONS_DB_OPTION_NAME);
        $api_key    = isset($db_options['convertfox_api_key']) ? $db_options['convertfox_api_key'] : '';

        //If the user has not setup convertfox, abort early
        if (empty($api_key)) {
            delete_transient('_mo_convertfox_is_connected');

            return false;
        }

        if (isset($_POST['wp_csa_nonce'])) {
            delete_transient('_mo_convertfox_is_connected');
        }

        // Check for connection status from cache only if we are not saving the form.
        if ('true' == get_transient('_mo_convertfox_is_connected')) {
            return true;
        }

        try {

            $api    = new APIClass($api_key);
            $result = $api->make_request('users', array('per_page' => 1));

            if (self::is_http_code_success($result['status_code'])) {
                set_transient('_mo_convertfox_is_connected', 'true', WEEK_IN_SECONDS);

                return true;
            }

            if ($return_error) {
                $error = array_pop($result['body']->errors);

                return $error->message;
            }

            return false;

        } catch (\Exception $e) {
            if ($return_error) {
                return $e->getMessage();
            }

            return false;
        }
    }

    /**
     * Returns instance of API class.
     *
     * @throws \Exception
     *
     * @return APIClass
     */
    public function convertfox_instance()
    {
        $api_key = $this->connections_settings->convertfox_api_key();

        if (empty($api_key)) {
            throw new \Exception(__('Gist API Key not found.', 'mailoptin'));
        }

        return new APIClass($api_key);
    }
}