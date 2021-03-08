<?php

namespace MailOptin\InfusionsoftConnect;

use Authifly\Provider\Infusionsoft;
use Authifly\Storage\OAuthCredentialStorage;
use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\PluginSettings\Connections;

class AbstractInfusionsoftConnect extends AbstractConnect
{
    /**
     * Is infusionsoft successfully connected to?
     *
     * @return bool
     */
    public static function is_connected()
    {
        $db_options = get_option(MAILOPTIN_CONNECTIONS_DB_OPTION_NAME);

        return ! empty($db_options['infusionsoft_access_token']);
    }

    /**
     * Return instance of infusionsoft class.
     *
     * @return Infusionsoft|mixed
     * @throws \Exception
     *
     */
    public function infusionsoftInstance()
    {
        $connections_settings = Connections::instance(true);
        $access_token         = $connections_settings->infusionsoft_access_token();
        $refresh_token        = $connections_settings->infusionsoft_refresh_token();
        $expires_at           = $connections_settings->infusionsoft_expires_at();

        if (empty($access_token)) {
            throw new \Exception(__('Infusionsoft access token not found.', 'mailoptin'));
        }

        $config = [
            'callback' => MAILOPTIN_OAUTH_URL,
            'keys'     => ['id' => '0c6hPxAP0UVFhJtsTXXrVylZ8DKNaGjE', 'secret' => '__']
        ];

        $instance = new Infusionsoft($config, null,
            new OAuthCredentialStorage([
                'infusionsoft.access_token'  => $access_token,
                'infusionsoft.refresh_token' => $refresh_token,
                'infusionsoft.expires_at'    => $expires_at,
            ]));

        if ($instance->hasAccessTokenExpired()) {

            try {

                $result   = $this->oauth_token_refresh('infusionsoft', $refresh_token);

                $option_name = MAILOPTIN_CONNECTIONS_DB_OPTION_NAME;
                $old_data    = get_option($option_name, []);
                $expires_at  = $this->oauth_expires_at_transform($result['data']['expires_at']);
                $new_data    = [
                    'infusionsoft_access_token'  => $result['data']['access_token'],
                    'infusionsoft_refresh_token' => $result['data']['refresh_token'],
                    'infusionsoft_expires_at'    => $expires_at
                ];

                update_option($option_name, array_merge($old_data, $new_data));

                $instance = new Infusionsoft($config, null,
                    new OAuthCredentialStorage([
                        'infusionsoft.access_token'  => $result['data']['access_token'],
                        'infusionsoft.refresh_token' => $result['data']['refresh_token'],
                        'infusionsoft.expires_at'    => $expires_at,
                    ]));

            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }

        return $instance;
    }

}