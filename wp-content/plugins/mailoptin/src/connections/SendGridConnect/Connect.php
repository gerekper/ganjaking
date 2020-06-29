<?php

namespace MailOptin\SendGridConnect;

use MailOptin\Core\Connections\ConnectionInterface;

class Connect extends AbstractSendGridConnect implements ConnectionInterface
{
    /**
     * @var string key of connection service. its important all connection name ends with "Connect"
     */
    public static $connectionName = 'SendGridConnect';

    public function __construct()
    {
        ConnectSettingsPage::get_instance();

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

    /**
     * Register Constant Contact Connection.
     *
     * @param array $connections
     *
     * @return array
     */
    public function register_connection($connections)
    {
        $connections[self::$connectionName] = __('SendGrid Email Marketing', 'mailoptin');

        return $connections;
    }

    /**
     * Replace placeholder tags with actual SendGrid tags.
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
            // not working at the time of testing. [Weblink] seem to be the older tag which automatically gets replaced with {{weblink}}
            '{{weblink}}',
            '{{{unsubscribe}}}'
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

            $response = $this->sendgrid_instance()->make_request('marketing/lists', ['page_size' => 1000]);

            // an array with list id as key and name as value.
            $lists_array = ['none' => esc_html__('All Contacts', 'mailoptin')];

            if (is_array($response['body']['result'])) {

                foreach ($response['body']['result'] as $list) {
                    $lists_array[$list['id']] = $list['name'];
                }
            }

            return $lists_array;

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'sendgrid');
        }
    }

    public function get_optin_fields($list_id = '')
    {
        try {

            $response = $this->sendgrid_instance()->make_request('marketing/field_definitions');

            $custom_fields_array = [];

            if (isset($response['body']['reserved_fields']) && ! empty($response['body']['reserved_fields'])) {

                foreach ($response['body']['reserved_fields'] as $customField) {

                    $fieldName = $customField['name'];

                    if (in_array($fieldName, ['first_name', 'last_name', 'email'])) continue;

                    $custom_fields_array[$fieldName] = ucwords(str_replace('_', ' ', $fieldName));
                }
            }

            if (isset($response['body']['custom_fields']) && ! empty($response['body']['custom_fields'])) {
                foreach ($response['body']['custom_fields'] as $customField) {
                    // sgcf_ to identify custom fields from reserved fields
                    // https://github.com/sendgrid/sendgrid-nodejs/issues/953
                    $custom_fields_array['sgcf_' . $customField['id']] = ucwords(str_replace('_', ' ', $customField['name']));;
                }
            }

            return $custom_fields_array;

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'sendgrid');
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