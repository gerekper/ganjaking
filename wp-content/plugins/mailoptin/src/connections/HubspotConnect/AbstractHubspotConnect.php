<?php

namespace MailOptin\HubspotConnect;

use Authifly\Provider\Hubspot;
use Authifly\Storage\OAuthCredentialStorage;
use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\PluginSettings\Connections;

class AbstractHubspotConnect extends AbstractConnect
{
    /** @var \MailOptin\Core\PluginSettings\Connections */
    protected $connections_settings;

    protected $access_token;

    protected $refresh_token;

    protected $expires_at;

    public function __construct()
    {
        $this->connections_settings = Connections::instance();
        $this->access_token         = $this->connections_settings->hubspot_access_token();
        $this->refresh_token        = $this->connections_settings->hubspot_refresh_token();
        $this->expires_at           = $this->connections_settings->hubspot_expires_at();
        parent::__construct();
    }

    /**
     * Is hubspot successfully connected to?
     *
     * @return bool
     */
    public static function is_connected()
    {
        $db_options = get_option(MAILOPTIN_CONNECTIONS_DB_OPTION_NAME);

        return ! empty($db_options['hubspot_access_token']);
    }

    /**
     * Return instance of hubspot class.
     *
     * @return Hubspot|mixed
     * @throws \Exception
     *
     */
    public function hubspotInstance()
    {
        $access_token = $this->access_token;

        if (empty($access_token)) {
            throw new \Exception(__('Hubspot access token not found.', 'mailoptin'));
        }

        $config = [
            // secret key and callback not needed but authifly requires they have a value hence the MAILOPTIN_OAUTH_URL constant and "__"
            'callback' => MAILOPTIN_OAUTH_URL,
            'keys'     => ['id' => 'ce27c12c-e8bb-466d-b37a-48ae50964138', 'secret' => '__'],
            'scope'    => 'contacts',
        ];

        $instance = new Hubspot($config, null,
            new OAuthCredentialStorage([
                'hubspot.access_token'  => $access_token,
                'hubspot.refresh_token' => $this->refresh_token,
                'hubspot.expires_at'    => $this->expires_at,
            ]));

        if ($instance->hasAccessTokenExpired()) {

            try {
                $response = wp_remote_get(MAILOPTIN_OAUTH_URL . '/hubspot/?refresh_token=' . $this->refresh_token);
                $result   = json_decode(wp_remote_retrieve_body($response), true);

                if ( ! isset($result['success']) || $result['success'] !== true) {
                    self::save_optin_error_log('Error failed to refresh ' . json_encode($result), 'hubspot');
                    throw new \Exception(__('Error failed to refresh', 'mailoptin'));
                }

                $option_name = MAILOPTIN_CONNECTIONS_DB_OPTION_NAME;
                $old_data    = get_option($option_name, []);
                $new_data    = [
                    'hubspot_access_token'  => $result['data']['access_token'],
                    'hubspot_refresh_token' => $result['data']['refresh_token'],
                    'hubspot_expires_at'    => $result['data']['expires_at']
                ];

                update_option($option_name, array_merge($old_data, $new_data));

                $instance = new Hubspot($config, null,
                    new OAuthCredentialStorage([
                        'hubspot.access_token'  => $result['data']['access_token'],
                        'hubspot.refresh_token' => $result['data']['refresh_token'],
                        'hubspot.expires_at'    => $result['data']['expires_at'],
                    ]));

            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }

        return $instance;
    }

}