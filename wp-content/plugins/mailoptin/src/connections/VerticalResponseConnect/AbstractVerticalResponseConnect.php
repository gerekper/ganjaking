<?php

namespace MailOptin\VerticalResponseConnect;

use Authifly\Provider\VerticalResponse;
use Authifly\Storage\OAuthCredentialStorage;
use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\PluginSettings\Connections;
use MailOptin\Core\PluginSettings\Settings;

class AbstractVerticalResponseConnect extends AbstractConnect
{
    /** @var \MailOptin\Core\PluginSettings\Settings */
    protected $plugin_settings;

    /** @var \MailOptin\Core\PluginSettings\Connections */
    protected $connections_settings;

    protected $client_id;

    protected $access_token;

    public function __construct()
    {
        $this->plugin_settings      = Settings::instance();
        $this->connections_settings = Connections::instance();
        $this->access_token         = $this->connections_settings->verticalresponse_access_token();

        parent::__construct();
    }

    /**
     * Is Vertical Response successfully connected to?
     *
     * @return bool
     */
    public static function is_connected()
    {
        $db_options = get_option(MAILOPTIN_CONNECTIONS_DB_OPTION_NAME);

        return ! empty($db_options['verticalresponse_access_token']);
    }

    /**
     * Return instance of VerticalResponse class.
     *
     * @throws \Exception
     *
     * @return VerticalResponse
     */
    public function verticalresponseInstance()
    {
        $access_token = $this->access_token;

        if (empty($access_token)) {
            throw new \Exception(__('VerticalResponse access token not found.', 'mailoptin'));
        }

        $config = [
            'callback'     => MAILOPTIN_OAUTH_URL,
            'keys'         => ['id' => 'cqsqf292u848pe9ershyp7sj', 'secret' => '__'],
            'access_token' => $access_token
        ];

        return new VerticalResponse($config, null, new OAuthCredentialStorage());
    }

}