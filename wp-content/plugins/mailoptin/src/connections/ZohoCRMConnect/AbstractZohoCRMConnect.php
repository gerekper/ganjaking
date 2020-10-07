<?php

namespace MailOptin\ZohoCRMConnect;

use Authifly\Provider\Zoho;
use Authifly\Storage\OAuthCredentialStorage;
use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\PluginSettings\Connections;

class AbstractZohoCRMConnect extends AbstractConnect
{
    /** @var \MailOptin\Core\PluginSettings\Connections */
    protected $connections_settings;

    protected $access_token;

    protected $refresh_token;

    protected $expires_at;

    protected $location;

    protected $accounts_server;

    protected $api_domain;

    public function __construct()
    {
        $this->connections_settings = Connections::instance();
        $this->access_token         = $this->connections_settings->zohocrm_access_token();
        $this->refresh_token        = $this->connections_settings->zohocrm_refresh_token();
        $this->expires_at           = $this->connections_settings->zohocrm_expires_at();
        $this->location             = $this->connections_settings->zohocrm_location();
        $this->api_domain           = $this->connections_settings->zohocrm_api_domain();
        $this->accounts_server      = $this->connections_settings->zohocrm_accounts_server();

        parent::__construct();
    }

    /**
     * Is Constant Contact successfully connected to?
     *
     * @return bool
     */
    public static function is_connected()
    {
        $db_options = get_option(MAILOPTIN_CONNECTIONS_DB_OPTION_NAME);

        return ! empty($db_options['zohocrm_access_token']);
    }

    /**
     * Return instance of ConstantContact class.
     *
     * @return Zoho
     * @throws \Exception
     *
     */
    public function zcrmInstance()
    {
        $access_token = $this->access_token;

        if (empty($access_token)) {
            throw new \Exception(__('Zoho CRM access token not found.', 'mailoptin'));
        }

        $config = [
            // secret key and callback not needed but authifly requires they have a value hence the MAILOPTIN_OAUTH_URL constant and "__"
            'callback' => MAILOPTIN_OAUTH_URL,
            'keys'     => ['id' => '1000.6KNUXSFNEUE487359IEXNH6DSCPAFH', 'secret' => '__']
        ];

        $instance = new Zoho($config, null,
            new OAuthCredentialStorage([
                'zoho.access_token'    => $access_token,
                'zoho.refresh_token'   => $this->refresh_token,
                'zoho.expires_at'      => $this->expires_at,
                'zoho.api_domain'      => $this->api_domain,
                'zoho.location'        => $this->location,
                'zoho.accounts_server' => $this->accounts_server,
            ]));

        $instance->apiBaseUrl = $this->api_domain . '/crm/v2/';

        if ($instance->hasAccessTokenExpired()) {

            try {

                $result = $this->oauth_token_refresh('zohocrm', $this->refresh_token, ['location' => $this->location]);

                $option_name = MAILOPTIN_CONNECTIONS_DB_OPTION_NAME;
                $old_data    = get_option($option_name, []);

                $expires_at = $this->oauth_expires_at_transform($result['data']['expires_at']);

                $new_data = [
                    'zohocrm_access_token' => $result['data']['access_token'],
                    // when a token is refreshed, zoho doesn't include a new refresh token as it never expires unless it was revoked.
                    // And in that case, the user will re-authorize mailoptin to generate a new token
                    'zohocrm_expires_at'   => $expires_at,
                    'zohocrm_api_domain'   => $result['data']['api_domain'],
                ];

                update_option($option_name, array_merge($old_data, $new_data));

                $instance = new Zoho($config, null,
                    new OAuthCredentialStorage([
                        'zoho.access_token'    => $result['data']['access_token'],
                        'zoho.expires_at'      => $expires_at,
                        'zoho.api_domain'      => $result['data']['api_domain'],
                        'zoho.location'        => $this->location,
                        'zoho.refresh_token'   => $this->refresh_token,
                        'zoho.accounts_server' => $this->accounts_server,
                    ]));

                $instance->apiBaseUrl = $result['data']['api_domain'] . '/crm/v2/';

            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }

        return $instance;
    }
}