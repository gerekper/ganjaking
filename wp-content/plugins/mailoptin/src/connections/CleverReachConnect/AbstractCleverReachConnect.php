<?php

namespace MailOptin\CleverReachConnect;

use Authifly\Provider\CleverReach;
use Authifly\Storage\OAuthCredentialStorage;
use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\PluginSettings\Connections;

class AbstractCleverReachConnect extends AbstractConnect
{
    /**
     * Is cleverreach successfully connected to?
     *
     * @return bool
     */
    public static function is_connected()
    {
        $db_options = get_option(MAILOPTIN_CONNECTIONS_DB_OPTION_NAME);

        return ! empty($db_options['cleverreach_access_token']);
    }

    public function get_first_name_field()
    {
        $db_firstname_key = $this->get_integration_data('CleverReachConnect_first_name_field_key');

        return apply_filters('mo_connections_cleverreach_firstname', $db_firstname_key);
    }

    public function get_last_name_field()
    {
        $db_lastname_key = $this->get_integration_data('CleverReachConnect_last_name_field_key');

        return apply_filters('mo_connections_cleverreach_lastname', $db_lastname_key);
    }

    /**
     * Return instance of cleverreach class.
     *
     * @return CleverReach|mixed
     * @throws \Exception
     *
     */
    public function cleverreachInstance()
    {
        $connections_settings = Connections::instance(true);
        $access_token         = $connections_settings->cleverreach_access_token();
        $refresh_token        = $connections_settings->cleverreach_refresh_token();
        $expires_at           = $connections_settings->cleverreach_expires_at();

        if (empty($access_token)) {
            throw new \Exception(__('CleverReach access token not found.', 'mailoptin'));
        }

        $config = [
            'callback' => MAILOPTIN_OAUTH_URL,
            'keys'     => ['id' => '0c6hPxAP0UVFhJtsTXXrVylZ8DKNaGjE', 'secret' => '__']
        ];

        $instance = new CleverReach($config, null,
            new OAuthCredentialStorage([
                'cleverreach.access_token'  => $access_token,
                'cleverreach.refresh_token' => $refresh_token,
                'cleverreach.expires_at'    => $expires_at,
            ]));

        if ($instance->hasAccessTokenExpired()) {

            try {

                $result = $this->oauth_token_refresh('cleverreach', $refresh_token);

                $option_name = MAILOPTIN_CONNECTIONS_DB_OPTION_NAME;
                $old_data    = get_option($option_name, []);
                $expires_at  = $this->oauth_expires_at_transform($result['data']['expires_at']);
                $new_data    = [
                    'cleverreach_access_token'  => $result['data']['access_token'],
                    'cleverreach_refresh_token' => $result['data']['refresh_token'],
                    'cleverreach_expires_at'    => $expires_at
                ];

                update_option($option_name, array_merge($old_data, $new_data));

                $instance = new CleverReach($config, null,
                    new OAuthCredentialStorage([
                        'cleverreach.access_token'  => $result['data']['access_token'],
                        'cleverreach.refresh_token' => $result['data']['refresh_token'],
                        'cleverreach.expires_at'    => $expires_at,
                    ]));

            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }

        return $instance;
    }
}