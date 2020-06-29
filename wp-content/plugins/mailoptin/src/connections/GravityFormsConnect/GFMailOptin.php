<?php

namespace MailOptin\GravityFormsConnect;

use MailOptin\Connections\Init;
use MailOptin\Core\AjaxHandler;
use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\Connections\ConnectionFactory;
use MailOptin\Core\OptinForms\ConversionDataBuilder;
use MailOptin\Core\Repositories\ConnectionsRepository;
use function MailOptin\Core\moVar;

// Include the Gravity Forms Feed Add-On Framework.
\GFForms::include_feed_addon_framework();

class GFMailOptin extends \GFFeedAddOn
{
    /**
     * Contains an instance of this class, if available.
     */
    private static $_instance = null;

    /**
     * Defines the version of this addon.
     */
    protected $_version = MAILOPTIN_VERSION_NUMBER;

    /**
     * Defines the minimum Gravity Forms version required.
     */
    protected $_min_gravityforms_version = '2.4.13';

    /**
     * Defines the plugin slug.
     */
    protected $_slug = 'gfmailoptin';

    /**
     * Defines the full path to this class file.
     */
    protected $_full_path = __FILE__;

    /**
     * Defines the URL where this add-on can be found.
     */
    protected $_url = 'https://mailoptin.io';

    /**
     * Defines the title of this add-on.
     */
    protected $_title = 'MailOptin for Gravity Forms Add-On';

    /**
     * Defines the short title of the add-on.
     */
    protected $_short_title = 'MailOptin';

    /**
     * Defines the capability needed to access the Add-On settings page.
     */
    protected $_capabilities_settings_page = 'manage_options';

    /**
     * Defines the capability needed to access the Add-On form settings page.
     */
    protected $_capabilities_form_settings = 'manage_options';

    /**
     * Defines the capability needed to uninstall the Add-On.
     */
    protected $_capabilities_uninstall = 'manage_options';

    public static function get_instance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Feed starting point.
     */
    public function init()
    {
        parent::init();

        add_action('admin_footer', [$this, 'select2_init']);

        $this->add_delayed_payment_support(
            array(
                'option_label' => esc_html__('Subscribe contact to your email marketing software or CRM via MailOptin only when payment is received.', 'mailoptin'),
            )
        );
    }

    public function select2_init()
    {
        ?>
        <script type="text/javascript">
            var run = function () {
                var cache = jQuery('.mofg_select2');
                if (typeof cache.select2 !== 'undefined') {
                    cache.select2()
                }
            };
            run(); // immediately
            // jQuery(run); // on ready
            jQuery(window).on('load', run);
        </script>';
        <?php
    }

    public function scripts()
    {
        $scripts = parent::scripts();

        $scripts[] = [
            'handle'    => 'mailoptin-select2',
            'src'       => MAILOPTIN_ASSETS_URL . 'js/customizer-controls/select2/select2.min.js',
            'deps'      => ['jquery'],
            'in_footer' => true,
            // Determines where the script will be enqueued. The script will be enqueued if any of the conditions match.
            'enqueue'   => [
                // admin_page - Specified one or more pages (known pages) where the script is supposed to be enqueued.
                ['admin_page' => ['form_settings']]
            ]
        ];

        return $scripts;
    }

    public function styles()
    {
        $styles = parent::styles();

        $styles[] = [
            'handle'  => 'mailoptin-select2',
            'src'     => MAILOPTIN_ASSETS_URL . 'js/customizer-controls/select2/select2.min.css',
            'enqueue' => [
                ['admin_page' => ['form_settings']],
            ]
        ];

        return $styles;
    }

    /**
     * Return an array of list fields which can be mapped to the Form fields/entry meta.
     *
     * @return array Field map or empty array on failure.
     *
     */
    public function merge_vars_field_map()
    {
        $field_map = [
            'moEmail' => [
                'name'       => 'moEmail',
                'label'      => esc_html__('Email Address', 'mailoptin'),
                'required'   => true,
                'field_type' => ['email', 'hidden'],
            ],
            'moName'  => [
                'name'  => 'moName',
                'label' => esc_html__('Full Name', 'mailoptin')
            ],
        ];

        $saved_integration = $this->get_setting('mailoptinSelectIntegration');

        $saved_list = $this->get_setting('mailoptinSelectList');


        if ( ! empty($saved_integration) && $saved_integration != 'leadbank') {

            if (defined('MAILOPTIN_DETACH_LIBSODIUM')) {

                $instance = ConnectionFactory::make($saved_integration);

                if (in_array($instance::OPTIN_CUSTOM_FIELD_SUPPORT, $instance::features_support())) {

                    $cfields = $instance->get_optin_fields($saved_list);

                    if (is_array($cfields) && ! empty($cfields)) {

                        foreach ($cfields as $key => $value) {

                            $field_map[$key] = [
                                'name'  => $key,
                                'label' => $value
                            ];
                        }
                    }
                }
            }
        }

        return $field_map;
    }

    /**
     * Form settings page title
     *
     * @return string Form Settings Title
     */
    public function feed_settings_title()
    {
        return esc_html__('Feed Settings', 'mailoptin');
    }

    /**
     * Enable feed duplication.
     *
     * @param int $id Feed ID requesting duplication.
     *
     * @return bool
     */
    public function can_duplicate_feed($id)
    {
        return true;
    }

    public static function email_service_providers()
    {
        $connections = ConnectionsRepository::get_connections();

        if (defined('MAILOPTIN_DETACH_LIBSODIUM')) {
            $connections['leadbank'] = __('MailOptin Leads', 'mailoptin');
        }

        return $connections;
    }

    public function gf_select_integration_options()
    {
        $integrations = self::email_service_providers();

        if ( ! empty($integrations)) {

            $options = [
                [
                    'label' => esc_html__('Select...', 'mailoptin'),
                    'value' => '',
                ],
            ];

            foreach ($integrations as $value => $label) {

                if (empty($value)) continue;

                // Add list to select options.
                $options[] = [
                    'label' => $label,
                    'value' => $value,
                ];
            }

            return $options;
        }

        return [];
    }

    public function gf_select_list_options()
    {
        $saved_integration = $this->get_setting('mailoptinSelectIntegration');

        $lists = [];
        if ( ! empty($saved_integration) && $saved_integration != 'leadbank') {
            $lists = ConnectionFactory::make($saved_integration)->get_email_list();
        }

        if ( ! empty($lists)) {

            $options = [
                [
                    'label' => esc_html__('Select...', 'mailoptin'),
                    'value' => '',
                ]
            ];

            foreach ($lists as $value => $label) {

                if (empty($value)) continue;

                // Add list to select options.
                $options[] = [
                    'label' => $label,
                    'value' => $value,
                ];
            }

            return $options;
        }

        return [];
    }

    public function gf_lead_tag_settings()
    {
        $saved_integration = $this->get_setting('mailoptinSelectIntegration');

        if (empty($saved_integration)) return false;

        if (in_array($saved_integration, Init::select2_tag_connections())) {

            $tags     = [];
            $instance = ConnectionFactory::make($saved_integration);
            if (method_exists($instance, 'get_tags')) {
                $tags = $instance->get_tags();
            }


            $options = [];

            foreach ($tags as $value => $label) {

                if (empty($value)) continue;

                $options[] = [
                    'label' => $label,
                    'value' => $value,
                ];
            }

            return [
                'name'     => 'moTag[]',
                'label'    => esc_html__('Tags', 'mailoptin'),
                'type'     => 'select',
                'choices'  => $options,
                'class'    => 'mofg_select2',
                'multiple' => 'multiple',
                'tooltip'  => esc_html__('Select tags to assign to subscribers or leads.', 'mailoptin'),
            ];
        }

        if (in_array($saved_integration, Init::text_tag_connections())) {

            return [
                'name'    => 'moTag',
                'class'   => 'medium',
                'label'   => esc_html__('Tags', 'mailoptin'),
                'type'    => 'text',
                'tooltip' => esc_html__('Enter a comma-separated list of tags to assign to subscribers.', 'mailoptin'),
            ];
        }
    }

    /**
     * Renders upsell block
     */
    public function settings_moupsell()
    {
        ?>
        <style>
            .mo-gf-upsell-block {
                background-color: #d9edf7;
                border: 1px solid #bce8f1;
                box-sizing: border-box;
                color: #31708f;
                outline: 0;
                padding: 15px 10px;
            }
        </style>
        <?php
        $upgrade_url   = 'https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=gravity_forms_builder_settings';
        $learnmore_url = 'https://mailoptin.io/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=gravity_forms_builder_settings';
        $output        = '<p>' . sprintf(esc_html__('Upgrade to %s to remove the 500 subscribers per month limit, add support for custom field mapping and assign tags to subscribers.', 'mailoptin'), '<strong>MailOptin premium</strong>') . '</p>';
        $output        .= '<p><a href="' . $upgrade_url . '" style="margin-right: 10px;" class="button-primary" target="_blank">' . esc_html__('Upgrade to MailOptin Premium', 'mailoptin') . '</a>';
        $output        .= sprintf(esc_html__('%sLearn more about us%s', 'mailoptin'), '<a href="' . $learnmore_url . '" target="_blank">', '</a>') . '</p>';

        echo '<div class="mo-gf-upsell-block">' . $output . '</div>';
    }

    /**
     * Feed settings
     *
     * @return array
     */
    public function feed_settings_fields()
    {
        $fields = [];

        if ($this->get_setting('mailoptinSelectIntegration') != 'leadbank') {
            $fields[] = [
                'name'       => 'mailoptinSelectList',
                'label'      => esc_html__('Select List', 'mailoptin'),
                'type'       => 'select',
                'choices'    => $this->gf_select_list_options(),
                'required'   => true,
                'no_choices' => esc_html__('Please ensure you have an email list created to continue.', 'mailoptin'),
                'tooltip'    => sprintf(
                    '%s',
                    esc_html__('Select the email list or segment you would like to add your contacts to.', 'mailoptin')
                ),
                'onchange'   => 'jQuery(this).parents("form").submit();',
            ];
        }

        $fields[] = [
            'name'      => 'mappedFields',
            'label'     => esc_html__('Map Fields', 'mailoptin'),
            'type'      => 'field_map',
            'field_map' => $this->merge_vars_field_map()
        ];

        if (defined('MAILOPTIN_DETACH_LIBSODIUM')) {
            $tags_settings = $this->gf_lead_tag_settings();
        }

        if ( ! empty($tags_settings)) $fields[] = $tags_settings;

        $fields[] = [
            'name'    => 'optinCondition',
            'label'   => esc_html__('Conditional Logic', 'mailoptin'),
            'type'    => 'feed_condition',
            'tooltip' => sprintf(
                '<h6>%s</h6>%s',
                esc_html__('Conditional Logic', 'mailoptin'),
                esc_html__('When conditional logic is enabled, this integration will only be processed when the conditions are met. When disabled all form submissions will be exported.', 'mailoptin')
            ),
        ];

        if ( ! defined('MAILOPTIN_DETACH_LIBSODIUM')) {
            $fields[] = [
                'name'  => 'moUpsell',
                'label' => '',
                'type'  => 'moupsell'
            ];
        }

        $fields[] = ['type' => 'save'];

        return array(
            array(
                'fields' => [
                    [
                        'name'     => 'feedName',
                        'label'    => esc_html__('Name', 'mailoptin'),
                        'type'     => 'text',
                        'required' => true,
                        'class'    => 'medium',
                        'tooltip'  => sprintf(
                            '<h6>%s</h6>%s',
                            esc_html__('Name', 'mailoptin'),
                            esc_html__('Enter a feed name to uniquely identify this setup.', 'mailoptin')
                        ),
                    ],
                    [
                        'name'       => 'mailoptinSelectIntegration',
                        'label'      => esc_html__('Select Integration', 'mailoptin'),
                        'type'       => 'select',
                        'choices'    => $this->gf_select_integration_options(),
                        'required'   => true,
                        'no_choices' => sprintf(
                            esc_html__('%sConnect MailOptin first%s to your email marketing marketing software or CRM to continue setup.', 'mailoptin'),
                            '<a href="' . MAILOPTIN_CONNECTIONS_SETTINGS_PAGE . '">', '</a>'),
                        'onchange'   => 'jQuery(this).parents("form").submit();',
                    ],
                ],
            ),
            [
                'dependency' => 'mailoptinSelectIntegration',
                'fields'     => $fields,
            ],
        );
    }

    /**
     * Process the feed
     *
     * @param array $feed The feed object to be processed.
     * @param array $entry The entry object currently being processed.
     * @param array $form The form object currently being processed.
     *
     */
    public function process_feed($feed, $entry, $form)
    {
        $field_map = $this->get_field_map_fields($feed, 'mappedFields');

        $email = $this->get_field_value($form, $entry, $field_map['moEmail']);

        // If email address is invalid, log error and return.
        if (\GFCommon::is_invalid_or_empty_email($email)) {
            $this->add_feed_error(esc_html__('A valid Email address must be provided.', 'mailoptin'), $feed, $entry, $form);

            return;
        }

        $payload = [];

        foreach ($field_map as $name => $field_id) {
            $payload[$name] = $this->get_field_value($form, $entry, $field_id);
        }

        $optin_data = new ConversionDataBuilder();

        $optin_data->optin_campaign_id   = 0; // since it's non mailoptin form, set it to zero.
        $optin_data->payload             = $payload;
        $optin_data->name                = $this->get_field_value($form, $entry, $field_map['moName']);
        $optin_data->email               = $email;
        $optin_data->optin_campaign_type = esc_html__('Gravity Forms', 'mailoptin');

        $optin_data->connection_service    = rgars($feed, 'meta/mailoptinSelectIntegration');
        $optin_data->connection_email_list = rgars($feed, 'meta/mailoptinSelectList');

        $optin_data->user_agent                = esc_html($_SERVER['HTTP_USER_AGENT']);
        $optin_data->is_timestamp_check_active = false;

        if (isset($_REQUEST['referrer'])) {
            $optin_data->conversion_page = esc_url_raw($_REQUEST['referrer']);
        }

        $optin_data->form_tags = rgars($feed, 'meta/moTag');

        // Loop through field map.
        foreach ($field_map as $name => $field_id) {

            // If no field is mapped, skip it.
            if (rgblank($field_id)) {
                continue;
            }

            if (in_array($name, ['moEmail', 'moName'])) continue;

            $field_value = $this->get_field_value($form, $entry, $field_id);

            if (empty($field_value)) continue;

            $optin_data->form_custom_field_mappings[$name] = $name;
        }

        $response = AjaxHandler::do_optin_conversion($optin_data);

        if ( ! AbstractConnect::is_ajax_success($response)) {
            $this->add_feed_error(esc_html__('Unable to add subscriber via MailOptin: ', 'mailoptin') . $response['message'], $feed, $entry, $form);
        }
    }

    /**
     * Returns the value to be displayed in the MailOptin Integration List column.
     *
     * @param array $feed The feed being included in the feed list.
     *
     * @return string
     */
    public function get_column_value_mailoptin_integration($feed)
    {
        $integration = isset($feed['meta']['mailoptinSelectIntegration']) ? $feed['meta']['mailoptinSelectIntegration'] : '';

        if (empty($integration)) {
            $this->log_debug(__METHOD__ . '(): MailOptin integration list is not set in the feed settings.');

            return;
        }

        $integrationName = moVar(self::email_service_providers(), $integration);

        if ( ! empty($integrationName)) {
            return $integrationName;
        };

        return '';
    }

    /**
     * Configures which columns should be displayed on the feed list page.
     *
     * @return array
     */
    public function feed_list_columns()
    {
        return array(
            'feedName'              => esc_html__('Name', 'mailoptin'),
            'mailoptin_integration' => esc_html__('MailOptin Integration', 'mailoptin'),
        );
    }
}
