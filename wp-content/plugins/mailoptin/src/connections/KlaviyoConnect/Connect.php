<?php

namespace MailOptin\KlaviyoConnect;

use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Custom_Content;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Toggle_Control;
use MailOptin\Core\Admin\Customizer\OptinForm\Customizer;
use MailOptin\Core\Connections\ConnectionInterface;

class Connect extends AbstractKlaviyoConnect implements ConnectionInterface
{
    /**
     * @var string key of connection service. its important all connection name ends with "Connect"
     */
    public static $connectionName = 'KlaviyoConnect';

    public function __construct()
    {
        ConnectSettingsPage::get_instance();

        add_filter('mailoptin_registered_connections', array($this, 'register_connection'));

        add_filter('mo_optin_customizer_field_map_description', [$this, 'custom_field_map_ui_description'], 10, 2);

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
     * Register Klaviyo Connection.
     *
     * @param array $connections
     *
     * @return array
     */
    public function register_connection($connections)
    {
        $connections[self::$connectionName] = __('Klaviyo', 'mailoptin');

        return $connections;
    }

    /**
     * Replace placeholder tags with actual Klaviyo merge tags.
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
            '{% web_view_link %}',
            '{% unsubscribe_link %}'
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
            $response = $this->klaviyo_instance()->get_lists();

            // an array with list id as key and name as value.
            $lists_array = array();

            if (self::is_http_code_success($response['status_code'])) {

                $lists = $response['body'];

                if ( ! empty($lists)) {
                    foreach ($lists as $list) {
                        $lists_array[$list->list_id] = $list->list_name;
                    }
                }

                return $lists_array;
            }

            self::save_optin_error_log($response['body']->status . ': ' . $response['body']->message, 'klaviyo');

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'klaviyo');
        }
    }

    public function get_optin_fields($list_id = '')
    {
        try {

            $merge_fields_array = [
                '$phone_number' => __('Phone Number', 'mailoptin'),
                '$title'        => __('Job Title', 'mailoptin'),
                '$organization' => __('Organization Name', 'mailoptin'),
                '$city'         => __('City', 'mailoptin'),
                '$region'       => __('State', 'mailoptin'),
                '$country'      => __('Country', 'mailoptin'),
                '$zip'          => __('ZIP or Postal Code', 'mailoptin'),
                '$image'        => __('Photo URL', 'mailoptin'),
            ];

            return apply_filters('mo_connections_klaviyo_custom_fields', $merge_fields_array);

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'klaviyo');
        }
    }

    public function custom_field_map_ui_description($val, $connection)
    {
        if ($connection == self::$connectionName) {
            $val = '<p>' . sprintf(__('You can only map to Klaviyo special identify properties. Custom fields not mapped will be added as custom properties. <a target="_blank" href="%s">Learn more</a>', 'mailoptin'), 'https://mailoptin.io/article/add-custom-fields-wordpress-form/#klaviyo') . '</p>';
        }

        return $val;
    }

    /**
     *
     * {@inheritdoc}
     *
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