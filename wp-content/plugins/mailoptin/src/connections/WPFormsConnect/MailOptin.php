<?php

namespace MailOptin\WPFormsConnect;

use MailOptin\Connections\Init;
use MailOptin\Core\AjaxHandler;
use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\Connections\ConnectionFactory;
use MailOptin\Core\Connections\ConnectionInterface;
use MailOptin\Core\OptinForms\ConversionDataBuilder;
use MailOptin\Core\Repositories\ConnectionsRepository;
use function MailOptin\Core\moVar;

class MailOptin extends \WPForms_Provider
{
    public $account = false;

    /** @var ConnectionInterface */
    public $api = false;

    public function init()
    {
        $this->version  = MAILOPTIN_VERSION_NUMBER;
        $this->name     = 'MailOptin';
        $this->slug     = 'mailoptin';
        $this->priority = 20;
        $this->icon     = MAILOPTIN_ASSETS_URL . 'images/wpforms-mo.png';

        add_action('admin_enqueue_scripts', [$this, 'select2_enqueue']);
    }

    public function process_entry($fields, $entry, $form_data, $entry_id = 0)
    {
        // Only run if this form has a connections for this provider.
        if (empty($form_data['providers'][$this->slug])) return;

        // Fire for each connection. --------------------------------------//

        foreach ($form_data['providers'][$this->slug] as $connection) :

            // Before proceeding make sure required fields are configured.
            if (empty($connection['fields']['moEmail'])) continue;

            // Check for conditionals.
            $pass = $this->process_conditionals($fields, $entry, $form_data, $connection);
            if ( ! $pass) {
                wpforms_log(
                    'MailOptin subscription stopped by conditional logic',
                    $fields,
                    array(
                        'type'    => array('provider', 'conditional_logic'),
                        'parent'  => $entry_id,
                        'form_id' => $form_data['id'],
                    )
                );
                continue;
            }

            $email_data = explode('.', $connection['fields']['moEmail']);
            $email_id   = $email_data[0];
            $email      = $fields[$email_id]['value'];

            $name_data = explode('.', $connection['fields']['moName']);
            $name_id   = $name_data[0];
            $name      = $fields[$name_id]['value'];

            // Email is required.
            if (empty($email)) {
                continue;
            }

            $optin_data = new ConversionDataBuilder();
            // since it's non mailoptin form, set it to zero.
            $optin_data->optin_campaign_id = 0;
            $optin_data->payload           = [];
            $optin_data->email             = $email;

            if ( ! empty($name)) {
                $optin_data->name = $name;
            }

            $optin_data->optin_campaign_type       = 'WPForms';
            $optin_data->connection_service        = $connection['account_id'];
            $optin_data->connection_email_list     = isset($connection['list_id']) ? $connection['list_id'] : '';
            $optin_data->is_timestamp_check_active = false;
            $optin_data->user_agent                = esc_html($_SERVER['HTTP_USER_AGENT']);

            if (isset($_REQUEST['referrer'])) {
                $optin_data->conversion_page = esc_url_raw($_REQUEST['referrer']);
            }

            $tags = isset($connection['options']['tags']) ? $connection['options']['tags'] : '';
            if (defined('MAILOPTIN_DETACH_LIBSODIUM') && ! empty($tags)) {
                $optin_data->form_tags = $tags;
                if (in_array($connection['account_id'], Init::select2_tag_connections())) {
                    $optin_data->form_tags = array_map('trim', explode(',', $tags));
                }
            }

            // Setup the custom fields --------------------------------------//

            foreach ($connection['fields'] as $fieldKey => $merge_var) {

                // Don't include Email or Full name fields.
                if (in_array($fieldKey, ['moEmail', 'moName'])) continue;

                // Check if merge var is mapped.
                if (empty($merge_var)) continue;

                $merge_var = explode('.', $merge_var);
                $id        = $merge_var[0];
                $key       = ! empty($merge_var[1]) ? $merge_var[1] : 'value';

                // Check if mapped form field has a value.
                if (empty($fields[$id][$key])) continue;

                // we are populating the payload var because when it is used for lookup to get the value.
                $optin_data->payload[$fieldKey] = $fields[$id][$key];

                $optin_data->form_custom_field_mappings[$fieldKey] = $fieldKey;
            }

            $response = AjaxHandler::do_optin_conversion($optin_data);

            if ( ! AbstractConnect::is_ajax_success($response)) {
                wpforms_log(
                    'MailOptin Subscription error',
                    $response['message'],
                    array(
                        'type'    => array('provider', 'error'),
                        'parent'  => $entry_id,
                        'form_id' => $form_data['id'],
                    )
                );
            }

        endforeach;
    }

    public function connected_integrations()
    {
        return ConnectionsRepository::get_connections();
    }

    /**
     * @param string $account_id this is the selected connected integration
     *
     * @return \MailOptin\Core\Connections\ConnectionInterface|mixed|\WP_Error
     */
    public function api_connect($account_id)
    {
        if ( ! empty($this->api) && $account_id === $this->account) {
            return $this->api;
        }

        $providers = get_option('wpforms_providers');

        if ( ! empty($providers[$this->slug][$account_id])) {
            $this->api     = ConnectionFactory::make($account_id);
            $this->account = $account_id;

            return $this->api;
        }

        return $this->error(esc_html__('Error: select an integration', 'mailoptin'));
    }

    protected function js_script()
    {
        ob_start();
        ?>
        <script type="text/javascript">
            (function ($) {
                $(document).on('change', '#moIntegrations', function () {
                    $('#moSelectedIntegration').val(this.value);
                });
            })(jQuery);
        </script>
        <?php
        return ob_get_clean();
    }

    protected function js_script2($connection_id)
    {
        ob_start();
        ?>
        <script type="text/javascript">
            (function ($) {
                var run = function () {
                    var cache = $('#<?=$connection_id?>_select2TagsSelectField');

                    if (typeof cache.select2 !== "undefined" && typeof cache.on !== "undefined") {
                        cache.on('select2:select', function () {
                            var values = $(this).val();
                            if (!$.isArray(values) || values.length < 1) values = [];
                            $('#<?=$connection_id?>_select2TagsHiddenField').val(values.join(","));
                        }).select2();
                    }
                };

                // sometimes running immediately can cause issue hence the double initialization
                run();

                $(window).on('load', run);
            })(jQuery);
        </script>
        <?php
        return ob_get_clean();
    }

    public function output_auth()
    {
        $providers = get_option('wpforms_providers');

        $class = ! empty($providers[$this->slug]) ? 'hidden' : '';

        $output = '<div class="wpforms-provider-account-add ' . $class . ' wpforms-connection-block">';

        $output .= '<h4>' . esc_html__('Add New Integration', 'mailoptin') . '</h4>';

        $output .= '<select id="moIntegrations" data-name="label" class="wpforms-required" style="max-width: 350px;width: 100%;margin-bottom: 5px;">';

        foreach ($this->connected_integrations() as $key => $value) {
            $output .= sprintf('<option value="%s">%s</option>', $key, $value);
        }

        $output .= '</select>';

        $output .= '<input type="hidden" id="moSelectedIntegration" data-name="label">';

        $output .= '<button data-provider="' . esc_attr($this->slug) . '">' . esc_html__('Connect', 'mailoptin') . '</button>';

        $output .= '</div>';

        $output .= $this->js_script();

        return $output;
    }

    public function api_auth($data = array(), $form_id = '')
    {
        $providers = get_option('wpforms_providers', array());

        $integration = sanitize_text_field($data['label']);

        $providers[$this->slug][$integration] = array(
            'label' => moVar($this->connected_integrations(), $integration),
            'date'  => time(),
        );

        update_option('wpforms_providers', $providers);

        return $integration;
    }

    public function api_lists($connection_id = '', $account_id = '')
    {
        $this->api_connect($account_id);

        try {

            $lists = $this->api->get_email_list();

            if ( ! empty($lists)) {

                $l = [];

                foreach ($lists as $id => $name) {
                    $l[$id] = [
                        'id'   => $id,
                        'name' => $name,
                    ];
                }

                return $l;
            }

            return $this->error(esc_html__('API list error: No lists', 'mailoptin'));

        } catch (\Exception $e) {
            return $this->error(sprintf(esc_html__('MailOptin error: %s', 'mailoptin'), $e->getMessage()));
        }
    }


    /**
     * Chhanging instances of "Account" to "Integration"
     *
     * @param string $connection_id Unique connection ID.
     * @param array $connection Array of connection data.
     *
     * @return string
     */
    public function output_accounts($connection_id = '', $connection = array())
    {
        if (empty($connection_id) || empty($connection)) {
            return '';
        }

        $providers = wpforms_get_providers_options();

        if (empty($providers[$this->slug])) {
            return '';
        }

        $output = '<div class="wpforms-provider-accounts wpforms-connection-block">';

        $output .= sprintf('<h4>%s</h4>', esc_html__('Select Integration', 'mailoptin'));

        $output .= sprintf('<select name="providers[%s][%s][account_id]">', $this->slug, $connection_id);
        foreach ($providers[$this->slug] as $key => $provider_details) {
            $selected = ! empty($connection['account_id']) ? $connection['account_id'] : '';
            $output   .= sprintf(
                '<option value="%s" %s>%s</option>',
                $key,
                selected($selected, $key, false),
                esc_html($provider_details['label'])
            );
        }
        $output .= sprintf('<option value="">%s</a>', esc_html__('Add New Integration', 'mailoptin'));
        $output .= '</select>';

        $output .= '</div>';

        return $output;
    }

    public function output_groups($connection_id = '', $connection = array())
    {
        return '';
    }

    public function output_fields($connection_id = '', $connection = array(), $form = '')
    {
        // for the sake of the likes of covertfox with no list and because wpforms bails if list id is empty
        if (empty($connection['list_id'])) {
            $connection['list_id'] = 'empty';
        }

        return parent::output_fields($connection_id, $connection, $form);
    }

    public function api_fields($connection_id = '', $account_id = '', $list_id = '')
    {
        $provider_fields = [
            [
                'name'       => esc_html__('Email', 'mailoptin'),
                'field_type' => 'email',
                'req'        => '1',
                'tag'        => 'moEmail',
            ],
            [
                'name'       => esc_html__('Full Name', 'mailoptin'),
                'field_type' => 'text',
                'tag'        => 'moName',
            ]
        ];

        if (defined('MAILOPTIN_DETACH_LIBSODIUM')) {

            $this->api_connect($account_id);

            $instance = $this->api;

            if (in_array($instance::OPTIN_CUSTOM_FIELD_SUPPORT, $instance::features_support())) {

                $fields = $this->api->get_optin_fields($list_id);

                if ( ! empty($fields)) {
                    foreach ($fields as $key => $value) {
                        $provider_fields[$key] = [
                            'name'       => $value,
                            'field_type' => 'text',
                            'tag'        => $key,
                        ];
                    }
                }
            }
        }

        return $provider_fields;
    }

    public function fetch_tags($account_id)
    {
        $this->api_connect($account_id);

        if (method_exists($this->api, 'get_tags')) {

            $tags = $this->api->get_tags();

            if ( ! empty($tags)) return $tags;
        }

        return [];
    }

    public function output_options($connection_id = '', $connection = array())
    {
        if (empty($connection_id) || empty($connection['account_id'])) {
            return '';
        }

        $account_id = $connection['account_id'];

        $output = '<div class="wpforms-provider-options wpforms-connection-block">';

        if (defined('MAILOPTIN_DETACH_LIBSODIUM')) {

            if (in_array($account_id, Init::text_tag_connections())) {

                $output .= '<h4>' . esc_html__('Options', 'mailoptin') . '</h4>';

                $output .= sprintf(
                    '<p>
				<label for="%s_options_tags" class="block">%s <i class="fa fa-question-circle wpforms-help-tooltip" title="%s"></i></label>
				<input id="%s_options_tags" type="text" name="providers[%s][%s][options][tags]" value="%s">
			</p>',
                    esc_attr($connection_id),
                    esc_html__('Tags', 'mailoptin'),
                    esc_html__('Enter comma-separated list of tags to assign to subscribers.', 'mailoptin'),
                    esc_attr($connection_id),
                    esc_attr($this->slug),
                    esc_attr($connection_id),
                    ! empty($connection['options']['tags']) ? esc_attr($connection['options']['tags']) : ''
                );
            }

            if (in_array($account_id, Init::select2_tag_connections())) {

                $output .= '<h4>' . esc_html__('Options', 'mailoptin') . '</h4>';

                $tags = $this->fetch_tags($account_id);

                if (empty($tags)) {
                    $output .= '<p>' . esc_html__('Lead tagging disabled because no tag was found. Consider creating one in your CRM or email service provider and try again.', 'mailoptin') . '</p>';
                } else {
                    $saved_values = [];

                    if ( ! empty($connection['options']['tags'])) {
                        $saved_values = array_map('trim', explode(',', $connection['options']['tags']));
                    }

                    $output .= sprintf(
                        '<p>
				<label for="%1$s" class="block">%2$s <i class="fa fa-question-circle wpforms-help-tooltip" title="%3$s"></i></label>
				<select id="%1$s" class="select2TagsSelectField" multiple>
			    </p>',
                        sprintf('%s_select2TagsSelectField', $connection_id),
                        esc_html__('Tags', 'mailoptin'),
                        esc_html__('Select tags to assign to subscribers.', 'mailoptin')
                    );

                    foreach ($tags as $key => $value) {
                        $selected = in_array($key, $saved_values) ? 'selected' : '';
                        $output   .= sprintf('<option value="%s" %s>%s</option>', $key, $selected, $value);
                    }

                    $output .= '</select>';
                    $output .= sprintf('<input id="%3$s" type="hidden" name="providers[%1$s][%2$s][options][tags]">', $this->slug, $connection_id, $connection_id . '_select2TagsHiddenField');
                    $output .= $this->js_script2($connection_id);
                }
            }
        } else {
            $upgrade_url   = 'https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=wpforms_builder_settings';
            $learnmore_url = 'https://mailoptin.io/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=wpforms_builder_settings';
            $output        .= '<div class="wpforms-alert wpforms-alert-info">';
            $output        .= '<p>' . sprintf(esc_html__('Upgrade to %s to remove the 500 subscribers per month limit, add support for custom field mapping and assign tags to subscribers.', 'mailoptin'), '<strong>MailOptin premium</strong>') . '</p>';
            $output        .= '<p><a href="' . $upgrade_url . '" style="margin-right: 10px;" class="button-primary" target="_blank">' . esc_html__('Upgrade to MailOptin Premium', 'mailoptin') . '</a>';
            $output        .= sprintf(esc_html__('Learn more about our %slead generation and email automation features%s', 'mailoptin'), '<a href="' . $learnmore_url . '" target="_blank">', '</a>') . '</p>';
            $output        .= '</div>';
        }

        $output .= '</div>';

        return $output;
    }

    public function select2_enqueue()
    {
        wp_enqueue_script('mailoptin-select2', MAILOPTIN_ASSETS_URL . 'js/customizer-controls/select2/select2.min.js', array('jquery'), false, true);
        wp_enqueue_style('mailoptin-select2', MAILOPTIN_ASSETS_URL . 'js/customizer-controls/select2/select2.min.css', null);

        wp_add_inline_style('mailoptin-select2', 'html .select2-container .select2-dropdown {z-index: 900000 !important;width: 300px !important}
html .select2-results__option.select2-results__message {margin: 0 !important;}
#wpforms-builder  .select2-search.select2-search--inline {margin-bottom:0 !important;}

#wpforms-builder .select2-container--default .select2-selection--multiple {
    border: 1px solid #aaa!important;
    width: 300px
}

#wpforms-builder .select2-container--default .select2-search--inline .select2-search__field {
    background: transparent;
    border: none;
    outline: 0;
    box-shadow: none;
    -webkit-appearance: textfield;
}

#wpforms-builder .select2-container--default .select2-search--inline .select2-search__field:focus {
    border: 0 !important;
    box-shadow: none !important;
    outline: none !important;
}');
    }

    /**
     * Singleton poop.
     *
     * @return self
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