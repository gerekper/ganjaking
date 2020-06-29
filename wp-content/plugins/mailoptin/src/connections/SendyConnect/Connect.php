<?php

namespace MailOptin\SendyConnect;

use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\Connections\ConnectionInterface;
use MailOptin\Core\PluginSettings\Connections;

class Connect extends AbstractConnect implements ConnectionInterface
{
    /**
     * @var string key of connection service. its important all connection name ends with "Connect"
     */
    public static $connectionName = 'SendyConnect';

    public function __construct()
    {
        ConnectSettingsPage::get_instance();

        add_filter('mailoptin_registered_connections', array($this, 'register_connection'));

        parent::__construct();
    }

    public static function features_support()
    {
        return [  self::OPTIN_CAMPAIGN_SUPPORT, self::EMAIL_CAMPAIGN_SUPPORT ];
    }

    /**
     * Is Sendy successfully connected to?
     *
     * @return bool
     */
    public static function is_connected()
    {
        $db_options = get_option(MAILOPTIN_CONNECTIONS_DB_OPTION_NAME);

        return !empty($db_options['sendy_api_key']);
    }

    /**
     * Register Sendy Connection.
     *
     * @param array $connections
     *
     * @return array
     */
    public function register_connection($connections)
    {
        $connections[self::$connectionName] = __('Sendy', 'mailoptin');

        return $connections;
    }

    /**
     * Replace placeholder tags with actual Sendy tags.
     *
     * {@inheritdoc}
     */
    public function replace_placeholder_tags($content, $type = 'html')
    {
        $search = ['{{webversion}}', '{{unsubscribe}}'];
        $replace = ['[webversion]', '[unsubscribe]'];

        $content = str_replace($search, $replace, $content);

        return $this->replace_footer_placeholder_tags($content);
    }

    /**
     * {@inherit_doc}
     *
     * Return array of email list for use by optin and email newsletter services.
     *
     * @return array
     */
    public function get_email_list()
    {
        $instance = Connections::instance();

        // "sendy_email_list" method call handled by magic __call in MailOptin\Core\PluginSettings\Connections
        $db_email_lists = (array)$instance->sendy_email_list();

        $returnValue = [];
        foreach ($db_email_lists as $db_email_list) {
            $list_id = $db_email_list['list_id'];
            $list_name = $db_email_list['list_name'];

            $returnValue[$list_id] = $list_name;
        }

        return $returnValue;
    }

    public function get_optin_fields($list_id = '')
    {
        return [];
    }

    /**
     * @param int $email_campaign_id
     * @param int $campaign_log_id
     * @param string $subject
     * @param string $content_html
     * @param string $content_text
     *
     * @throws \Exception
     *
     * @return array
     */
    public function send_newsletter($email_campaign_id, $campaign_log_id, $subject, $content_html, $content_text)
    {
        return (new SendCampaign($email_campaign_id, $campaign_log_id, $subject, $content_html, $content_text))->send();
    }

    /**
     * @param string $email
     * @param string $name
     * @param string $list_id ID of email list to add subscriber to
     * @param mixed|null $extras
     *
     * @return mixed
     */
    public function subscribe($email, $name, $list_id, $extras = null)
    {
        return (new Subscription($email, $name, $list_id, $extras))->subscribe();
    }

    /**
     * Singleton poop.
     *
     * @return Connect|null
     */
    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}