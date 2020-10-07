<?php

namespace MailOptin\InfusionsoftConnect;

use Authifly\Provider\Infusionsoft;
use Authifly\Storage\OAuthCredentialStorage;
use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\PluginSettings\Connections;

class AbstractInfusionsoftConnect extends AbstractConnect
{
    /** @var \MailOptin\Core\PluginSettings\Connections */
    protected $connections_settings;

    protected $access_token;

    protected $refresh_token;

    protected $expires_at;

    public function __construct()
    {
        $this->connections_settings = Connections::instance();
        $this->access_token         = $this->connections_settings->infusionsoft_access_token();
        $this->refresh_token        = $this->connections_settings->infusionsoft_refresh_token();
        $this->expires_at           = $this->connections_settings->infusionsoft_expires_at();
        parent::__construct();
    }

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
        $access_token = $this->access_token;

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
                'infusionsoft.refresh_token' => $this->refresh_token,
                'infusionsoft.expires_at'    => $this->expires_at,
            ]));

        if ($instance->hasAccessTokenExpired()) {

            try {

                $result   = $this->oauth_token_refresh('infusionsoft', $this->refresh_token);

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