<?php

namespace MailOptin\FluentCRMConnect;

use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\Connections\ConnectionInterface;

class Connect extends AbstractConnect implements ConnectionInterface
{
    /**
     * @var string key of connection service. its important all connection name ends with "Connect"
     */
    public static $connectionName = 'FluentCRMConnect';

    public function __construct()
    {
        add_filter('mailoptin_registered_connections', array($this, 'register_connection'));

        add_filter('mo_optin_form_integrations_default', array($this, 'integration_customizer_settings'));
        add_filter('mo_optin_integrations_controls_after', array($this, 'integration_customizer_controls'));

        parent::__construct();
    }

    public static function features_support()
    {
        return [
            self::OPTIN_CAMPAIGN_SUPPORT,
            self::OPTIN_CUSTOM_FIELD_SUPPORT
        ];
    }

    public static function is_connected()
    {
        return function_exists('FluentCrmApi');
    }

    /**
     * Register FluentCRM Connection.
     *
     * @param array $connections
     *
     * @return array
     */
    public function register_connection($connections)
    {
        if (self::is_connected()) {
            $connections[self::$connectionName] = __('FluentCRM', 'mailoptin');
        }

        return $connections;
    }

    /**
     * {@inheritdoc}
     */
    public function replace_placeholder_tags($content, $type = 'html')
    {
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
            // an array with list id as key and name as value.
            $lists_array = array();

            if (self::is_connected()) {
                $response = FluentCrmApi('lists')->all();

                foreach ($response as $list) {
                    $lists_array[$list->id] = $list->title;
                }
            }

            return $lists_array;

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'fluentcrm');
        }
    }

    /**
     * {@inherit_doc}
     *
     * Return array of email list
     *
     * @return mixed
     */
    public function get_optin_fields($list_id = '')
    {
        $default = [
            'prefix'         => __('Prefix', 'mailoptin'),
            'phone'          => __('Phone', 'mailoptin'),
            'address_line_1' => __('Address Line 1', 'mailoptin'),
            'address_line_2' => __('Address Line 2', 'mailoptin'),
            'city'           => __('City', 'mailoptin'),
            'state'          => __('State', 'mailoptin'),
            'country'        => __('Country', 'mailoptin'),
            'postal_code'    => __('Postal Code', 'mailoptin'),
            'date_of_birth'  => __('Date of Birth', 'mailoptin')
        ];

        if (self::is_connected()) {
            $custom_fields = fluentcrm_get_option('contact_custom_fields', []);

            if (is_array($custom_fields) && ! empty($custom_fields)) {
                foreach ($custom_fields as $custom_field) {
                    $default['cf_' . $custom_field['slug']] = $custom_field['label'];
                }
            }
        }

        return $default;
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
        return [];
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
     * @param array $settings
     *
     * @return mixed
     */
    public function integration_customizer_settings($settings)
    {
        $settings['FluentCRMConnect_disable_double_optin'] = apply_filters('mailoptin_customizer_optin_campaign_FluentCRMConnect_disable_double_optin', false);

        return $settings;
    }

    /**
     * @param array $controls
     *
     * @return mixed
     */
    public function integration_customizer_controls($controls)
    {
        // always prefix with the name of the connect/connection service.
        $controls[] = [
            'field'       => 'chosen_select',
            'name'        => 'FluentCRMConnect_lead_tags',
            'choices'     => $this->get_tags(),
            'label'       => __('Tags', 'mailoptin'),
            'description' => __('Select tags to assign to leads.', 'mailoptin')
        ];

        $controls[] = [
            'field'       => 'toggle',
            'name'        => 'FluentCRMConnect_disable_double_optin',
            'label'       => __('Disable Double Optin', 'mailoptin'),
            'description' => __("Double optin requires users to confirm their email address before they are added or subscribed.", 'mailoptin'),
        ];

        return $controls;
    }

    /**
     * @return array|mixed
     */
    public function get_tags()
    {
        try {

            $tags_array = [];


            if (self::is_connected()) {

                $all_Tags = FluentCrmApi('tags')->all();

                if (is_object($all_Tags) && ! is_wp_error($all_Tags)) {
                    foreach ($all_Tags as $tag) {
                        if (isset($tag->id)) {
                            $tags_array[$tag->id] = $tag->title;
                        }
                    }
                }
            }

            return $tags_array;

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'fluentcrm');

            return [];
        }
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