<?php

namespace MailOptin\ZohoCRMConnect;

use MailOptin\Core\Connections\ConnectionInterface;
use MailOptin\Core\Logging\CampaignLogRepository;

class Connect extends AbstractZohoCRMConnect implements ConnectionInterface
{
    /**
     * @var string key of connection service. its important all connection name ends with "Connect"
     */
    public static $connectionName = 'ZohoCRMConnect';

    public function __construct()
    {
        ConnectSettingsPage::get_instance();

        add_filter('mailoptin_registered_connections', array($this, 'register_connection'));
        add_filter('mo_optin_form_integrations_default', array($this, 'integration_customizer_settings'));
        add_filter('mo_optin_integrations_controls_after', array($this, 'integration_customizer_controls'));

        add_action('mo_optin_customizer_footer_scripts', [$this, 'contextual_module_setting_display']);

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
        $connections[self::$connectionName] = __('Zoho CRM', 'mailoptin');

        return $connections;
    }

    /**
     * Fulfill interface contract.
     *
     * {@inheritdoc}
     */
    public function replace_placeholder_tags($content, $type = 'html')
    {
        return $this->replace_footer_placeholder_tags($content);
    }

    /**
     * @param array $settings
     *
     * @return mixed
     */
    public function integration_customizer_settings($settings)
    {
        $settings['ZohoCRMConnect_tags'] = '';

        $settings['ZohoCRMConnect_lead_owner']    = '';
        $settings['ZohoCRMConnect_contact_owner'] = '';

        $settings['ZohoCRMConnect_lead_source'] = '';

        $settings['ZohoCRMConnect_description'] = '';

        $settings['ZohoCRMConnect_triggers'] = ['approval', 'workflow', 'blueprint'];

        return $settings;
    }

    public function get_users()
    {
        if ( ! self::is_connected()) return [];

        try {

            $cache_key = 'mo_zohocrm_users';

            $users_array = get_transient($cache_key);

            if (empty($users_array) || false === $users_array) {

                $response = parent::zcrmInstance()->apiRequest('users');

                if ( ! isset($response->users) || ! is_array($response->users)) return [];

                $users_array = ['' => esc_html__('Select...', 'mailoptin')];

                foreach ($response->users as $user) {
                    $users_array[$user->id] = $user->full_name;
                }

                set_transient($cache_key, $users_array, 10 * MINUTE_IN_SECONDS);
            }

            return $users_array;

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'zohocrm');

            return [];
        }
    }

    public function get_lead_sources($module = 'Contacts')
    {
        if ( ! self::is_connected()) return [];

        try {

            $cache_key = 'mo_zohocrm_lead_sources_' . $module;

            $sources = get_transient($cache_key);

            if (empty($sources) || false === $sources) {
                $response = $this->zcrmInstance()->apiRequest('settings/fields?module=' . $module);

                if ( ! isset($response->fields) || ! is_array($response->fields)) return [];

                $sources = [];

                foreach ($response->fields as $field) {
                    if ($field->api_name == 'Lead_Source') {
                        foreach ($field->pick_list_values as $value) {
                            $sources[$value->actual_value] = $value->display_value;
                        }
                        break;
                    }
                }

                set_transient($cache_key, $sources, 6 * HOUR_IN_SECONDS);
            }

            return $sources;

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'zohocrm');

            return [];
        }
    }

    /**
     * @param array $controls
     *
     * @return mixed
     */
    public function integration_customizer_controls($controls)
    {
        if (defined('MAILOPTIN_DETACH_LIBSODIUM') === true) {
            // always prefix with the name of the connect/connection service.
            $controls[] = [
                'field'       => 'text',
                'name'        => 'ZohoCRMConnect_tags',
                'label'       => esc_html__('Tags', 'mailoptin'),
                'placeholder' => 'tag1, tag2',
                'description' => esc_html__('Comma-separated list of tags to assign to subscribers.', 'mailoptin'),
            ];

            $controls[] = [
                'field'   => 'select',
                'name'    => 'ZohoCRMConnect_contact_owner',
                'choices' => $this->get_users(),
                'label'   => esc_html__('Contact Owner', 'mailoptin')
            ];

            $controls[] = [
                'field'   => 'select',
                'name'    => 'ZohoCRMConnect_lead_owner',
                'choices' => $this->get_users(),
                'label'   => esc_html__('Lead Owner', 'mailoptin')
            ];

            $controls[] = [
                'field'   => 'select',
                'name'    => 'ZohoCRMConnect_contact_lead_source',
                'choices' => $this->get_lead_sources(),
                'label'   => esc_html__('Lead Source', 'mailoptin')
            ];

            $controls[] = [
                'field'   => 'select',
                'name'    => 'ZohoCRMConnect_lead_lead_source',
                'choices' => $this->get_lead_sources('Leads'),
                'label'   => esc_html__('Lead Source', 'mailoptin')
            ];

            $controls[] = [
                'field' => 'textarea',
                'name'  => 'ZohoCRMConnect_description',
                'label' => esc_html__('Description', 'mailoptin')
            ];

            $controls[] = [
                'field'       => 'chosen_select',
                'name'        => 'ZohoCRMConnect_triggers',
                'choices'     => [
                    'approval'  => esc_html__('Approval', 'mailoptin'),
                    'workflow'  => esc_html__('Workflow', 'mailoptin'),
                    'blueprint' => esc_html__('Blueprint', 'mailoptin'),
                ],
                'label'       => esc_html__('Trigger', 'mailoptin'),
                'description' => esc_html__('Select triggers that will be executed when a user subscribes.', 'mailoptin')
            ];

        } else {

            $content = sprintf(
                __("%sMailOptin Premium%s allows you to apply tags to subscribers and access other conversion features.", 'mailoptin'),
                '<a target="_blank" href="https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=zohocrm_connection">',
                '</a>',
                '<strong>',
                '</strong>'
            );

            // always prefix with the name of the connect/connection service.
            $controls[] = [
                'name'    => 'ZohoCRMConnect_upgrade_notice',
                'field'   => 'custom_content',
                'content' => $content
            ];
        }

        return $controls;
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
        return [
            'Contacts' => esc_html__('Contacts', 'mailoptin'),
            'Leads'    => esc_html__('Leads', 'mailoptin')
        ];
    }

    public function contextual_module_setting_display()
    {
        if ( ! defined('MAILOPTIN_DETACH_LIBSODIUM') === true) return;
        ?>
        <script type="text/javascript">
            (function ($) {
                $(document).on('toggle_connect_service_connected_fields', function (e, parent, selected_connection_service) {
                    if (selected_connection_service !== 'ZohoCRMConnect') return;
                    $('.ZohoCRMConnect_contact_owner, .ZohoCRMConnect_lead_owner, .ZohoCRMConnect_contact_lead_source, .ZohoCRMConnect_lead_lead_source', parent).hide();
                });

                $(document).on('toggle_connect_service_email_list_field', function (e, parent, selected_email_list, selected_connection_service) {
                    if (selected_connection_service !== 'ZohoCRMConnect') return;
                    var logic = function (selected_email_list) {
                        $('.ZohoCRMConnect_contact_owner, .ZohoCRMConnect_lead_owner, .ZohoCRMConnect_contact_lead_source, .ZohoCRMConnect_lead_lead_source', parent).hide();
                        if (selected_email_list === 'Contacts') $('.ZohoCRMConnect_contact_owner, .ZohoCRMConnect_contact_lead_source', parent).show();
                        if (selected_email_list === 'Leads') $('.ZohoCRMConnect_lead_owner, .ZohoCRMConnect_lead_lead_source', parent).show();
                    };

                    logic(selected_email_list);

                    $("select[name='connection_email_list']", parent).change(function () {
                        logic($(this).val());
                    });
                });
            })(jQuery);
        </script>
        <?php
    }

    public function get_optin_fields($module = '')
    {
        try {

            $response = $this->zcrmInstance()->apiRequest('settings/fields?module=' . $module);

            $fields = [];
            if (isset($response->fields) && is_array($response->fields)) {
                foreach ($response->fields as $field) {
                    // skip unsupported field types
                    if (in_array($field->data_type, ['ownerlookup', 'lookup', 'boolean', 'currency', 'profileimage'])) continue;

                    if (in_array($field->api_name, ['Description', 'Owner', 'Lead_Source', 'Email', 'First_Name', 'Last_Name', 'Created_Time', 'Modified_Time', 'Last_Activity_Time'])) continue;
                    $fields[$field->api_name] = $field->field_label;
                }

                return $fields;
            }

            return self::save_optin_error_log(json_encode($response), 'zohocrm');

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'zohocrm');

            return [];
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