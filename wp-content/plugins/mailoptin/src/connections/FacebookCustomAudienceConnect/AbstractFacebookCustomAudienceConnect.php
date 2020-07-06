<?php

namespace MailOptin\FacebookCustomAudienceConnect;

use Authifly\Provider\Facebook;
use Authifly\Storage\OAuthCredentialStorage;
use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\PluginSettings\Connections;
use MailOptin\Core\PluginSettings\Settings;

class AbstractFacebookCustomAudienceConnect extends AbstractConnect
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
     * Is Facebook Custom Audience successfully connected to?
     *
     * @return bool
     */
    public static function is_connected($return_error = false)
    {
        $db_options        = $db_options = isset($_POST['mailoptin_connections']) ? $_POST['mailoptin_connections'] : get_option(MAILOPTIN_CONNECTIONS_DB_OPTION_NAME);
        $fbca_app_id       = isset($db_options['fbca_app_id']) ? $db_options['fbca_app_id'] : '';
        $fbca_app_secret   = isset($db_options['fbca_app_secret']) ? $db_options['fbca_app_secret'] : '';
        $fbca_adaccount_id = isset($db_options['fbca_adaccount_id']) ? $db_options['fbca_adaccount_id'] : '';

        if (empty($fbca_app_id) || empty($fbca_app_secret) || empty($fbca_adaccount_id)) {
            delete_transient('_mo_facebookcustomaudience_is_connected');

            return false;
        }

        if (isset($_POST['wp_csa_nonce'])) {
            delete_transient('_mo_facebookcustomaudience_is_connected');
        }

        // Check for connection details from cache
        if ('true' == get_transient('_mo_facebookcustomaudience_is_connected')) return true;

        try {

            $response = (new self())->fbca_instance()->getCustomAudiences($fbca_adaccount_id);

            if ($response) {
                set_transient('_mo_facebookcustomaudience_is_connected', 'true', WEEK_IN_SECONDS);

                return true;
            }

            return $return_error === true ? $response->result_message : false;

        } catch (\Exception $e) {

            return $return_error === true ? $e->getMessage() : false;
        }
    }

    public function is_access_token_expired()
    {
        $request = wp_remote_get(
            sprintf(
                'https://graph.facebook.com/debug_token?input_token=%s&access_token=%s|%s',
                $this->connections_settings->fbca_app_access_token(),
                $this->connections_settings->fbca_app_id(),
                $this->connections_settings->fbca_app_secret()
            )
        );

        if (is_wp_error($request)) {
            update_option('mo_fbca_access_token_expired_status', 'true');
            throw new \Exception($request->get_error_message());
        }

        $response = json_decode(wp_remote_retrieve_body($request), true);

        if (isset($response['data']['is_valid'])) {
            update_option('mo_fbca_access_token_expired_status', $response['data']['is_valid'] === true ? 'false' : 'true');

            return $response['data']['is_valid'] === true;
        }

        return false;
    }

    /**
     * Returns instance of API class.
     *
     * @return Facebook
     * @throws \Exception
     *
     */
    public function fbca_instance()
    {
        $fbca_app_id           = $this->connections_settings->fbca_app_id();
        $fbca_app_secret       = $this->connections_settings->fbca_app_secret();
        $fbca_app_access_token = $this->connections_settings->fbca_app_access_token();
        $fbca_adaccount_id     = $this->connections_settings->fbca_adaccount_id();

        if (empty($fbca_app_id)) {
            throw new \Exception(__('Facebook App ID not found.', 'mailoptin'));
        }

        if (empty($fbca_app_secret)) {
            throw new \Exception(__('Facebook App secret not found.', 'mailoptin'));
        }

        if (empty($fbca_app_access_token)) {
            throw new \Exception(__('Facebook App access token not found.', 'mailoptin'));
        }

        if (empty($fbca_adaccount_id)) {
            throw new \Exception(__('Facebook Ad account ID not found.', 'mailoptin'));
        }

        if ( ! $this->is_access_token_expired()) {
            throw new \Exception(__('Facebook access token is expired.', 'mailoptin'));
        }

        $config = [
            // callback not needed but authifly requires it has a value
            'callback'     => MAILOPTIN_OAUTH_URL,
            'keys'         => ['id' => $fbca_app_id, 'secret' => $fbca_app_secret],
            'scope'        => 'ads_management',
            'access_token' => $fbca_app_access_token,
            'apiVersion'   => apply_filters('mo_facebook_custom_audience_api_version', '7.0')
        ];

        return new Facebook($config, null, new OAuthCredentialStorage());
    }
}