<?php

namespace MailOptin\CampaignMonitorConnect;

use Authifly\Provider\CampaignMonitor;
use Authifly\Storage\OAuthCredentialStorage;
use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\PluginSettings\Connections;
use MailOptin\Core\PluginSettings\Settings;

class AbstractCampaignMonitorConnect extends AbstractConnect
{
    /** @var \MailOptin\Core\PluginSettings\Settings */
    protected $plugin_settings;

    /** @var \MailOptin\Core\PluginSettings\Connections */
    protected $connections_settings;

    protected $client_id;

    protected $access_token;

    protected $refresh_token;

    protected $expires_at;

    public function __construct()
    {
        $this->plugin_settings      = Settings::instance();
        $this->connections_settings = Connections::instance();

        $this->client_id     = $this->connections_settings->campaignmonitor_client_id();
        $this->access_token  = $this->connections_settings->campaignmonitor_access_token();
        $this->refresh_token = $this->connections_settings->campaignmonitor_refresh_token();
        $this->expires_at    = $this->connections_settings->campaignmonitor_expires_at();

        parent::__construct();
    }

    /**
     * Is Campaign Monitor successfully connected to?
     *
     * @return bool
     */
    public static function is_connected()
    {
        $db_options = get_option(MAILOPTIN_CONNECTIONS_DB_OPTION_NAME);

        return ! empty($db_options['campaignmonitor_access_token']);
    }

    /**
     * Return instance of CampaignMonitor class.
     *
     * @throws \Exception
     *
     * @return CampaignMonitor
     */
    public function campaignmonitorInstance()
    {
        $access_token = $this->access_token;

        if (empty($access_token)) {
            throw new \Exception(__('CampaignMonitor access token not found.', 'mailoptin'));
        }

        $config = [
            // secret key and callback not needed but authifly requires they have a value hence the MAILOPTIN_OAUTH_URL constant and "__"
            'callback' => MAILOPTIN_OAUTH_URL,
            'keys'     => ['id' => '108102', 'secret' => '__'],
            'scope'    => 'ManageLists,ImportSubscribers,CreateCampaigns,SendCampaigns,ViewReports',
        ];

        $instance = new CampaignMonitor($config, null,
            new OAuthCredentialStorage([
                'campaignmonitor.access_token'  => $this->access_token,
                'campaignmonitor.refresh_token' => $this->refresh_token,
                'campaignmonitor.expires_at'    => $this->expires_at,
            ]));

        if ($instance->hasAccessTokenExpired()) {

            // only requires grant_type and refresh_token parameters unlike hubspot that
            // in addition require client secret (and client ID) so no need for remote refresh.
            $instance->refreshAccessToken();

            $option_name = MAILOPTIN_CONNECTIONS_DB_OPTION_NAME;
            $old_data    = get_option($option_name, []);
            $expires_at = $this->oauth_expires_at_transform($instance->getStorage()->get('campaignmonitor.expires_at'));
            $new_data    = [
                'campaignmonitor_access_token'  => $instance->getStorage()->get('campaignmonitor.access_token'),
                'campaignmonitor_refresh_token' => $instance->getStorage()->get('campaignmonitor.refresh_token'),
                'campaignmonitor_expires_at'    => $expires_at
            ];

            update_option($option_name, array_merge($old_data, $new_data));

            $instance = new CampaignMonitor($config, null,
                new OAuthCredentialStorage([
                    'campaignmonitor.access_token'  => $instance->getStorage()->get('campaignmonitor.access_token'),
                    'campaignmonitor.refresh_token' => $instance->getStorage()->get('campaignmonitor.refresh_token'),
                    'campaignmonitor.expires_at'    => $expires_at,
                ]));
        }

        return $instance;
    }
}