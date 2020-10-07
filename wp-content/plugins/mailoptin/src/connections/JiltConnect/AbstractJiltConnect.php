<?php

namespace MailOptin\JiltConnect;

use Authifly\Provider\Jilt;
use Authifly\Storage\OAuthCredentialStorage;
use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\PluginSettings\Connections;

class AbstractJiltConnect extends AbstractConnect
{
    /** @var \MailOptin\Core\PluginSettings\Connections */
    protected $connections_settings;

    protected $access_token;

    protected $refresh_token;

    protected $expires_at;

    public function __construct()
    {
        $this->connections_settings = Connections::instance();
        $this->access_token         = $this->connections_settings->jilt_access_token();
        $this->refresh_token        = $this->connections_settings->jilt_refresh_token();
        $this->expires_at           = $this->connections_settings->jilt_expires_at();
        parent::__construct();
    }

    /**
     * Is jilt successfully connected to?
     *
     * @return bool
     */
    public static function is_connected()
    {
        $db_options = get_option(MAILOPTIN_CONNECTIONS_DB_OPTION_NAME);

        return ! empty($db_options['jilt_access_token']);
    }

    /**
     * @return Jilt|mixed
     * @throws \Exception
     *
     */
    public function jiltInstance()
    {
        $access_token = $this->access_token;

        if (empty($access_token)) {
            throw new \Exception(__('Jilt access token not found.', 'mailoptin'));
        }

        $config = [
            'callback' => MAILOPTIN_OAUTH_URL,
            'keys'     => ['id' => 'e02fc3d0aa19176ea6d289660785d8d5cc13710f55a44e01b7a09923c19f5778', 'secret' => '__']
        ];

        $instance = new Jilt($config, null,
            new OAuthCredentialStorage([
                'jilt.access_token'  => $access_token,
                'jilt.refresh_token' => $this->refresh_token,
                'jilt.expires_at'    => $this->expires_at,
            ]));

        if ($instance->hasAccessTokenExpired()) {

            $instance->refreshAccessToken();

            $option_name = MAILOPTIN_CONNECTIONS_DB_OPTION_NAME;
            $old_data    = get_option($option_name, []);
            $expires_at  = $this->oauth_expires_at_transform($instance->getStorage()->get('jilt.expires_at'));
            $new_data    = [
                'jilt_access_token'  => $instance->getStorage()->get('jilt.access_token'),
                'jilt_refresh_token' => $instance->getStorage()->get('jilt.refresh_token'),
                'jilt_expires_at'    => $expires_at
            ];

            update_option($option_name, array_merge($old_data, $new_data));

            $instance = new Jilt($config, null,
                new OAuthCredentialStorage([
                    'jilt.access_token'  => $instance->getStorage()->get('jilt.access_token'),
                    'jilt.refresh_token' => $instance->getStorage()->get('jilt.refresh_token'),
                    'jilt.expires_at'    => $expires_at,
                ]));
        }

        return $instance;
    }
}