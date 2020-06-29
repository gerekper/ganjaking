<?php

namespace MailOptin\AweberConnect;

use Authifly\Provider\Aweber;
use Authifly\Storage\OAuthCredentialStorage;
use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\PluginSettings\Connections;
use MailOptin\Core\PluginSettings\Settings;

class AbstractAweberConnect extends AbstractConnect
{
    /** @var \MailOptin\Core\PluginSettings\Settings */
    protected $plugin_settings;

    /** @var \MailOptin\Core\PluginSettings\Connections */
    protected $connections_settings;

    /** @var string Aweber account ID */
    protected $account_id;

    public function __construct()
    {
        $this->plugin_settings = Settings::instance();
        $this->connections_settings = Connections::instance();

        $this->account_id = $this->connections_settings->aweber_account_id();

        parent::__construct();
    }

    /**
     * Is Aweber successfully connected to?
     *
     * @return bool
     */
    public static function is_connected()
    {
        $db_options = get_option(MAILOPTIN_CONNECTIONS_DB_OPTION_NAME);

        return !empty($db_options['aweber_access_token']) &&
            !empty($db_options['aweber_access_token_secret']) &&
            !empty($db_options['aweber_account_id']);
    }

    /**
     * Return instance of MailChimp list class.
     *
     * @throws \Exception
     *
     * @return Aweber
     */
    public function aweber_instance()
    {
        $access_token = $this->connections_settings->aweber_access_token();
        $access_token_secret = $this->connections_settings->aweber_access_token_secret();

        if (empty($access_token) || empty($access_token_secret)) {
            throw new \Exception(__('AWeber access_token and/or access token secret not found.', 'mailoptin'));
        }

        $config = [
            'callback' => MAILOPTIN_OAUTH_URL,
            'keys' => ['key' => 'AkQax4L1pChqGxMlmZ1gBVLw', 'secret' => 'nFfjQHCh1zTAGehgWlmI0Xb97jy43zeNUhxDSHSA']
        ];

        $instance = new Aweber($config, null, new OAuthCredentialStorage([
            'aweber.access_token' => $access_token,
            'aweber.access_token_secret' => $access_token_secret,
        ]));

        return $instance;
    }
}