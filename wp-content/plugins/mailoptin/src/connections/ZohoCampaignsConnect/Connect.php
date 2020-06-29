<?php

namespace MailOptin\ZohoCampaignsConnect;

use MailOptin\Core\Connections\ConnectionInterface;
use MailOptin\Core\Logging\CampaignLogRepository;

class Connect extends AbstractZohoCampaignsConnect implements ConnectionInterface
{
    /**
     * @var string key of connection service. its important all connection name ends with "Connect"
     */
    public static $connectionName = 'ZohoCampaignsConnect';

    public function __construct()
    {
        ConnectSettingsPage::get_instance();

        add_filter('mailoptin_registered_connections', array($this, 'register_connection'));

        add_action('init', [$this, 'campaign_log_public_preview']);

        parent::__construct();
    }

    public static function features_support()
    {
        return [
            self::OPTIN_CAMPAIGN_SUPPORT,
            self::OPTIN_CUSTOM_FIELD_SUPPORT
        ];
    }

    /**
     * Register Constant Contact Connection.
     *
     * @param array $connections
     *
     * @return array
     */
    public function register_connection($connections)
    {
        $connections[self::$connectionName] = __('Zoho Campaigns', 'mailoptin');

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
            '$[LI:VIEWINBROWSER]$',
            '$[LI:UNSUBSCRIBE]$',
        ];

        $content = str_replace($search, $replace, $content);

        return $this->replace_footer_placeholder_tags($content);
    }

    public function campaign_log_public_preview()
    {
        if (isset($_GET['zohocampaigns_preview_type'], $_GET['uuid'])) {
            $preview_type = sanitize_text_field($_GET['zohocampaigns_preview_type']);

            if ( ! in_array($preview_type, ['text', 'html'])) return;

            $campaign_uuid = sanitize_text_field($_GET['uuid']);

            $campaign_log_id = absint($this->uuid_to_campaignlog_id($campaign_uuid, 'zohocampaigns_email_fetcher'));

            $type_method = 'retrieveContent' . ucfirst($preview_type);

            echo $this->replace_placeholder_tags(CampaignLogRepository::instance()->$type_method($campaign_log_id), $preview_type);
            exit;
        }
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

            $response = $this->zcInstance()->apiRequest('getmailinglists?resfmt=JSON');

            $lists_array = array();

            if (isset($response->list_of_details) && is_array($response->list_of_details) && ! empty($response->list_of_details)) {
                foreach ($response->list_of_details as $list) {
                    $lists_array[$list->listkey] = $list->listname;
                }

                return $lists_array;
            }

            self::save_optin_error_log(json_encode($response), 'zohocampaigns');

            return $lists_array;

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'zohocampaigns');

            return [];
        }
    }

    public function get_optin_fields($list_id = '')
    {
        try {

            $response = $this->zcInstance()->apiRequest('contact/allfields?type=json');

            $fields = [];
            if (isset($response->response->fieldnames->fieldname) && is_array($response->response->fieldnames->fieldname)) {
                foreach ($response->response->fieldnames->fieldname as $field) {
                    if (in_array($field->FIELD_DISPLAY_NAME, ['CONTACT_EMAIL', 'FIRSTNAME', 'LASTNAME'])) continue;
                    $fields[$field->DISPLAY_NAME] = $field->DISPLAY_NAME;
                }

                return $fields;
            }

            return self::save_optin_error_log(json_encode($response), 'zohocampaigns');

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'zohocampaigns');
        }
    }

    /**
     * @param int $email_campaign_id
     * @param int $campaign_log_id
     * @param string $subject
     * @param string $content_html
     * @param string $content_text
     *
     * @return array
     * @throws \Exception
     *
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