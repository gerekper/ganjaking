<?php

namespace MailOptin\ZohoCampaignsConnect;

use Authifly\Provider\Zoho;
use Authifly\Storage\OAuthCredentialStorage;
use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\PluginSettings\Connections;

class AbstractZohoCampaignsConnect extends AbstractConnect
{
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
        switch ($location) {
            case 'us':
                $location = 'com';
                break;
            case 'eu':
                $location = 'eu';
                break;
            case 'au':
                $location = 'com.au';
                break;
            case 'cn':
                $location = 'com.cn';
                break;
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
        $connections_settings = Connections::instance(true);
        $access_token         = $connections_settings->zohocampaigns_access_token();
        $refresh_token        = $connections_settings->zohocampaigns_refresh_token();
        $expires_at           = $connections_settings->zohocampaigns_expires_at();
        $location             = $connections_settings->zohocampaigns_location();
        $accounts_server      = $connections_settings->zohocampaigns_accounts_server();
        $api_domain           = $connections_settings->zohocampaigns_api_domain();

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
                'zoho.access_token'    => $access_token,
                'zoho.refresh_token'   => $refresh_token,
                'zoho.expires_at'      => $expires_at,
                'zoho.location'        => $location,
                'zoho.api_domain'      => $api_domain,
                'zoho.accounts_server' => $accounts_server,
            ]));

        $instance->apiBaseUrl = sprintf('https://campaigns.zoho.%s/api/v1.1/', $this->parse_location($location));

        if ($instance->hasAccessTokenExpired()) {

            try {

                $result = $this->oauth_token_refresh('zohocampaigns', $refresh_token, ['location' => $location]);

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
                        'zoho.refresh_token'   => $refresh_token,
                        'zoho.api_domain'      => $api_domain,
                        'zoho.accounts_server' => $accounts_server,
                    ]));

                $instance->apiBaseUrl = sprintf('https://campaigns.zoho.%s/api/v1.1/', $this->parse_location($result['data']['location']));

            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }

        return $instance;
    }
}