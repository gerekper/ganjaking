<?php

namespace MailOptin\ZohoCampaignsConnect;

use Authifly\Provider\Zoho;
use Authifly\Storage\OAuthCredentialStorage;
use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\PluginSettings\Connections;

class AbstractZohoCampaignsConnect extends AbstractConnect
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
        $this->access_token         = $this->connections_settings->zohocampaigns_access_token();
        $this->refresh_token        = $this->connections_settings->zohocampaigns_refresh_token();
        $this->expires_at           = $this->connections_settings->zohocampaigns_expires_at();
        $this->location             = $this->connections_settings->zohocampaigns_location();
        $this->accounts_server      = $this->connections_settings->zohocampaigns_accounts_server();
        $this->api_domain           = $this->connections_settings->zohocampaigns_api_domain();

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

        return ! empty($db_options['zohocampaigns_access_token']);
    }

    public function parse_location($location)
    {
        if ($location == 'us') {
            $location = 'com';
        }

        if ($location == 'eu') {
            $location = 'eu';
        }

        if ($location == 'au') {
            $location = 'com.au';
        }

        if ($location == 'cn') {
            $location = 'com.cn';
        }

        return $location;
    }

    /**
     * Return instance of ConstantContact class.
     *
     * @return Zoho
     * @throws \Exception
     *
     */
    public function zcInstance()
    {
        $access_token = $this->access_token;

        if (empty($access_token)) {
            throw new \Exception(__('Zoho Campaigns access token not found.', 'mailoptin'));
        }

        $config = [
            // secret key and callback not needed but authifly requires they have a value hence the MAILOPTIN_OAUTH_URL constant and "__"
            'callback' => MAILOPTIN_OAUTH_URL,
            'keys'     => ['id' => '1000.6KNUXSFNEUE487359IEXNH6DSCPAFH', 'secret' => '__']
        ];

        $instance = new Zoho($config, null,
            new OAuthCredentialStorage([
                'zoho.access_token'    => $this->access_token,
                'zoho.refresh_token'   => $this->refresh_token,
                'zoho.expires_at'      => $this->expires_at,
                'zoho.location'        => $this->location,
                'zoho.api_domain'      => $this->api_domain,
                'zoho.accounts_server' => $this->accounts_server,
            ]));

        $instance->apiBaseUrl = sprintf('https://campaigns.zoho.%s/api/v1.1/', $this->parse_location($this->location));

        if ($instance->hasAccessTokenExpired()) {

            try {

                $result = $this->oauth_token_refresh('zohocampaigns', $this->refresh_token, ['location' => $this->location]);

                $option_name = MAILOPTIN_CONNECTIONS_DB_OPTION_NAME;
                $old_data    = get_option($option_name, []);

                $expires_at = $this->oauth_expires_at_transform($result['data']['expires_at']);

                $new_data = [
                    'zohocampaigns_access_token' => $result['data']['access_token'],
                    // when a token is refreshed, zoho doesn't include a new refresh token as it never expires unless it was revoked.
                    // And in that case, the user will re-authorize mailoptin to generate a new token
                    'zohocampaigns_expires_at'   => $expires_at,
                    'zohocampaigns_location'     => $result['data']['location']
                ];

                update_option($option_name, array_merge($old_data, $new_data));

                $instance = new Zoho($config, null,
                    new OAuthCredentialStorage([
                        'zoho.access_token'    => $result['data']['access_token'],
                        'zoho.expires_at'      => $expires_at,
                        'zoho.location'        => $result['data']['location'],
                        'zoho.refresh_token'   => $this->refresh_token,
                        'zoho.api_domain'      => $this->api_domain,
                        'zoho.accounts_server' => $this->accounts_server,
                    ]));

                $instance->apiBaseUrl = sprintf('https://campaigns.zoho.%s/api/v1.1/', $this->parse_location($result['data']['location']));

            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }

        return $instance;
    }
}