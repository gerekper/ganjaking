<?php

namespace MailOptin\MoosendConnect;

use MailOptin\Core\Connections\ConnectionInterface;
use MailOptin\Core\Logging\CampaignLogRepository;

class Connect extends AbstractMoosendConnect implements ConnectionInterface
{
    /**
     * @var string key of connection service. its important all connection name ends with "Connect"
     */
    public static $connectionName = 'MoosendConnect';

    public function __construct()
    {
        ConnectSettingsPage::get_instance();

        add_action('init', [$this, 'campaign_log_public_preview']);

        add_filter('mailoptin_registered_connections', array($this, 'register_connection'));
        parent::__construct();
    }

    public static function features_support()
    {
        return [
            self::OPTIN_CAMPAIGN_SUPPORT,
            self::EMAIL_CAMPAIGN_SUPPORT,
            self::OPTIN_CUSTOM_FIELD_SUPPORT
        ];
    }

    public function campaign_log_public_preview()
    {
        if (isset($_GET['moosend_preview_type'], $_GET['uuid'])) {
            $preview_type = sanitize_text_field($_GET['moosend_preview_type']);

            if ( ! in_array($preview_type, ['text', 'html'])) {
                return;
            }

            $campaign_uuid = sanitize_text_field($_GET['uuid']);

            $campaign_log_id = absint($this->uuid_to_campaignlog_id($campaign_uuid, 'moosend_email_fetcher'));

            $type_method = 'retrieveContent' . ucfirst($preview_type);

            echo $this->replace_placeholder_tags(CampaignLogRepository::instance()->$type_method($campaign_log_id), $preview_type);
            exit;
        }
    }

    /**
     * Register Moosend Connection.
     *
     * @param array $connections
     *
     * @return array
     */
    public function register_connection($connections)
    {
        $connections[self::$connectionName] = __('Moosend', 'mailoptin');

        return $connections;
    }

    /**
     * Fulfill interface contract.
     *
     * {@inheritdoc}
     */
    public function replace_placeholder_tags($content, $type = 'html')
    {
        $search = [
            '{{webversion}}',
            '{{unsubscribe}}'
        ];

        $replace = [
            '{VR_HOSTED_LINK}',
            '{UNSUBSCRIBE_LINK}',
        ];

        $content = str_replace($search, $replace, $content);

        return $this->replace_footer_placeholder_tags($content);
    }

    /**
     * {@inherit_doc}
     *
     * Return array of email list
     *
     * @return mixed
     */
    public function get_email_list()
    {
        try {

            //Fetch the lists
            $response = $this->moosend_instance()->get_lists();

            //Convert it to array of id=>name
            return wp_list_pluck($response, 'Name', 'ID');

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'moosend');
        }
    }

    public function get_optin_fields($list_id = '')
    {
        try {

            $response = $this->moosend_instance()->get_custom_fields( $list_id );

            //Convert it to array of name=>name
            return wp_list_pluck($response, 'Name', 'ID');

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'moosend');
        }
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