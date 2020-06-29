<?php

namespace MailOptin\DripConnect;

use MailOptin\Core\Connections\ConnectionInterface;

class Connect extends AbstractDripConnect implements ConnectionInterface
{
    /**
     * @var string key of connection service. its important all connection name ends with "Connect"
     */
    public static $connectionName = 'DripConnect';

    public function __construct()
    {
        ConnectSettingsPage::get_instance();

        add_filter('mailoptin_registered_connections', array($this, 'register_connection'));

        add_filter('mo_optin_form_integrations_default', array($this, 'integration_customizer_settings'));
        add_filter('mo_optin_integrations_controls_after', array($this, 'integration_customizer_controls'));


        add_filter('mo_optin_integrations_advance_controls', array($this, 'customizer_advance_controls'));
        add_filter('mo_optin_form_integrations_default', [$this, 'customizer_advance_controls_defaults']);

        add_filter('mo_connections_with_advance_settings_support', function ($val) {
            $val[] = self::$connectionName;

            return $val;
        });

        parent::__construct();
    }

    public static function features_support()
    {
        return [
            self::OPTIN_CAMPAIGN_SUPPORT,
            self::OPTIN_CUSTOM_FIELD_SUPPORT
        ];
    }

    public function customizer_advance_controls_defaults($defaults)
    {
        $defaults['DripConnect_first_name_field_key'] = 'first_name';
        $defaults['DripConnect_last_name_field_key']  = 'last_name';

        return $defaults;
    }

    /**
     * @param $controls
     *
     * @return array
     */
    public function customizer_advance_controls($controls)
    {
        // always prefix with the name of the connect/connection service.
        $controls[] = [
            'field'       => 'text',
            'name'        => 'DripConnect_first_name_field_key',
            'label'       => __('First Name Field ID', 'mailoptin'),
            'description' => sprintf(
                __('If subscribers first names are missing, change this to the correct custom field ID. %sLearn more%s', 'mailoptin'),
                '<a href="https://mailoptin.io/?p=21482" target="_blank">', '</a>'
            )
        ];
        $controls[] = [
            'field'       => 'text',
            'name'        => 'DripConnect_last_name_field_key',
            'label'       => __('Last Name Field ID', 'mailoptin'),
            'description' => sprintf(
                __('If subscribers last names are missing, change this to the correct custom field ID. %sLearn more%s', 'mailoptin'),
                '<a href="https://mailoptin.io/?p=21482" target="_blank">', '</a>'
            )
        ];

        return $controls;
    }

    /**
     * @param array $settings
     *
     * @return mixed
     */
    public function integration_customizer_settings($settings)
    {
        $settings['DripConnect_lead_tags'] = apply_filters('mailoptin_customizer_optin_campaign_DripConnect_lead_tags', '');

        $settings['DripConnect_enable_double_optin'] = apply_filters('mailoptin_customizer_optin_campaign_DripConnect_enable_double_optin', false);

        return $settings;
    }

    /**
     * @param array $controls
     *
     * @return mixed
     */
    public function integration_customizer_controls($controls)
    {
        //DripConnect_upgrade_notice
        if (defined('MAILOPTIN_DETACH_LIBSODIUM') === true) {
            // always prefix with the name of the connect/connection service.
            $controls[] = [
                'field'       => 'text',
                'name'        => 'DripConnect_lead_tags',
                'placeholder' => 'tag1, tag2',
                'label'       => __('Lead Tags', 'mailoptin'),
                'description' => __('Enter comma-separated list of tags to assign to subscribers who opt-in via this campaign.', 'mailoptin')
            ];

            $controls[] = [
                'field'       => 'toggle',
                'name'        => 'DripConnect_enable_double_optin',
                'label'       => __('Enable Double Optin', 'mailoptin'),
                'description' => __("Double optin requires users to confirm their email address before being added to your campaign (recommended).", 'mailoptin'),
            ];

        } else {

            $content = sprintf(
                __("Upgrade to %sMailOptin Premium%s to enable Drip %sdouble optin%s, apply tags to subscribers and get loads of other conversion features.", 'mailoptin'),
                '<a target="_blank" href="https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=drip_connection">',
                '</a>',
                '<strong>',
                '</strong>'
            );

            $controls[] = [
                'name'    => 'DripConnect_upgrade_notice',
                'field'   => 'custom_content',
                'content' => $content
            ];
        }

        return $controls;
    }

    /**
     * Register Drip Connection.
     *
     * @param array $connections
     *
     * @return array
     */
    public function register_connection($connections)
    {
        $connections[self::$connectionName] = __('Drip', 'mailoptin');

        return $connections;
    }

    /**
     *
     * {@inheritdoc}
     */
    public function replace_placeholder_tags($content, $type = 'html')
    {

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

            $response = $this->drip_instance()->get('campaigns', ['status' => 'all']);

            if (isset($response->error, $response->message)) {
                return self::save_optin_error_log($response->error . ': ' . $response->message, 'drip');
            }

            if (self::is_http_code_success($response->status)) {

                $campaigns = $response->campaigns;

                // an array with list id as key and name as value.
                $lists_array = array();

                if ( ! empty($campaigns)) {
                    foreach ($campaigns as $campaign) {
                        $lists_array[$campaign['id']] = $campaign['name'];
                    }
                }

                return $lists_array;
            }


        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'drip');
        }
    }

    public function get_optin_fields($list_id = '')
    {
        try {

            $response = $this->drip_instance()->get('custom_field_identifiers');

            if (isset($response->error, $response->message)) {
                return self::save_optin_error_log($response->error . ': ' . $response->message, 'drip');
            }

            if (self::is_http_code_success($response->status)) {

                $firstname_key = $this->get_first_name_custom_field();
                $lastname_key  = $this->get_last_name_custom_field();
                $name_key      = apply_filters('mo_connections_drip_name_key', 'name');

                $custom_fields = $response->custom_field_identifiers;

                $custom_fields_array = array();

                if (is_array($custom_fields) && ! empty($custom_fields)) {
                    foreach ($custom_fields as $custom_field) {
                        if (in_array($custom_field, [$name_key, $firstname_key, $lastname_key])) continue;

                        $custom_fields_array[$custom_field] = $custom_field;
                    }
                }

                return $custom_fields_array;
            }

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'drip');
        }
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
     * @throws \Exception
     *
     * @return array
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