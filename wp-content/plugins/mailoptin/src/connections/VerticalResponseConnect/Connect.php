<?php

namespace MailOptin\VerticalResponseConnect;

use MailOptin\Core\Connections\ConnectionInterface;

class Connect extends AbstractVerticalResponseConnect implements ConnectionInterface
{
    /**
     * @var string key of connection service. its important all connection name ends with "Connect"
     */
    public static $connectionName = 'VerticalResponseConnect';

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
     * Register Vertical Response Connection.
     *
     * @param array $connections
     *
     * @return array
     */
    public function register_connection($connections)
    {
        $connections[self::$connectionName] = __('Vertical Response', 'mailoptin');

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
            $response = $this->verticalresponseInstance()->getEmailList();

            //Convert it to array of id=>name
            if (is_array($response)) {
                $response = wp_list_pluck($response, 'attributes');

                return wp_list_pluck($response, 'name', 'id');
            }

            //In case there was an error, return an empty list
            return array();


        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'verticalresponse');
        }
    }

    public static function predefined_custom_fields()
    {
        return [
            'birthdate'        => __('Birthday', 'mailoptin'),
            'gender'           => __('Gender', 'mailoptin'),
            'marital_status'   => __('Marital Status', 'mailoptin'),
            'company'          => __('Company', 'mailoptin'),
            'title'            => __('Title in Company', 'mailoptin'),
            'website'          => __('Website', 'mailoptin'),
            'street_address'   => __('Street Address', 'mailoptin'),
            'extended_address' => __('Street Address 2', 'mailoptin'),
            'city'             => __('City', 'mailoptin'),
            'region'           => __('State', 'mailoptin'),
            'postal_code'      => __('Postal Code', 'mailoptin'),
            'country'          => __('Country', 'mailoptin'),
            'mobile_phone'     => __('Mobile Phone Number', 'mailoptin'),
            'home_phone'       => __('Home Phone Number', 'mailoptin'),
            'work_phone'       => __('Work Phone Number', 'mailoptin'),
            'fax'              => __('Fax Number', 'mailoptin')
        ];
    }

    public function get_optin_fields($list_id = '')
    {
        $custom_fields = self::predefined_custom_fields();

        try {

            $response = $this->verticalresponseInstance()->getListCustomFields();

            //Convert it to array of name=>name
            if (is_array($response)) {
                $response      = wp_list_pluck($response, 'attributes');
                $result        = wp_list_pluck($response, 'name', 'name');
                $custom_fields = $custom_fields + $result;
            }

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'verticalresponse');
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
        return (new Subscription($email, $name, $list_id, $extras, $this))->subscribe();
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