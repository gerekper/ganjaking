<?php

namespace MailOptin\Ctctv3Connect;

use MailOptin\Core\Connections\ConnectionInterface;

class Connect extends AbstractCtctv3Connect implements ConnectionInterface
{
    /**
     * @var string key of connection service. its important all connection name ends with "Connect"
     */
    public static $connectionName = 'Ctctv3Connect';

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
     * Register Constant Contact v3 Connection.
     *
     * @param array $connections
     *
     * @return array
     */
    public function register_connection($connections)
    {
        $connections[self::$connectionName] = __('Constant Contact', 'mailoptin');

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
            '{{webversion}}'
        ];

        $replace = [
            '[[viewAsWebpage]]'
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
            return $this->ctctv3Instance()->getContactList();

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'constantcontactv3');
        }
    }

    public function get_optin_fields($list_id = '')
    {
        $custom_fields = [
            'job_title'         => __('Job Title', 'mailoptin'),
            'company_name'      => __('Company Name', 'mailoptin'),
            'birthday_month'    => __('Birthday Month', 'mailoptin'),
            'birthday_day'      => __('Birthday Day', 'mailoptin'),
            'anniversary'       => __('Anniversary', 'mailoptin'),
            'phone_number'      => __('Phone Number', 'mailoptin'),

            // Home address
            'mohma_street'      => __('Home Address Street', 'mailoptin'),
            'mohma_city'        => __('Home Address City', 'mailoptin'),
            'mohma_state'       => __('Home Address State', 'mailoptin'),
            'mohma_postal_code' => __('Home Address Postal Code', 'mailoptin'),
            'mohma_country'     => __('Home Address Country', 'mailoptin'),

            // Work address
            'mowka_street'      => __('Work Address Street', 'mailoptin'),
            'mowka_city'        => __('Work Address City', 'mailoptin'),
            'mowka_state'       => __('Work Address State', 'mailoptin'),
            'mowka_postal_code' => __('Work Address Postal Code', 'mailoptin'),
            'mowka_country'     => __('Work Address Country', 'mailoptin'),

            // Other address
            'moota_street'      => __('Other Address Street', 'mailoptin'),
            'moota_city'        => __('Other Address City', 'mailoptin'),
            'moota_state'       => __('Other Address State', 'mailoptin'),
            'moota_postal_code' => __('Other Address Postal Code', 'mailoptin'),
            'moota_country'     => __('Other Address Country', 'mailoptin'),
        ];

        try {

            $fields = $this->ctctv3Instance()->getContactsCustomFields();

            if (is_array($fields) && ! empty($fields)) {

                // custom fields (cufd)
                foreach ($fields as $field) {
                    $custom_fields['cufd_' . $field->custom_field_id] = $field->label;
                }
            }
        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'constantcontactv3');
        }

        return $custom_fields;
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