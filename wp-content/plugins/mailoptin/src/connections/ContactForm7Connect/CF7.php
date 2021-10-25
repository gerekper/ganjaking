<?php

namespace MailOptin\ContactForm7Connect;

use MailOptin\Connections\Init;
use MailOptin\Core\AjaxHandler;
use MailOptin\Core\Connections\ConnectionFactory;
use MailOptin\Core\OptinForms\ConversionDataBuilder;
use MailOptin\Core\Repositories\ConnectionsRepository;
use function MailOptin\Core\moVar;

class CF7
{
    public function __construct()
    {
        add_filter('wpcf7_editor_panels', [$this, 'add_panel']);
        add_action('wpcf7_after_save', [$this, 'save_settings']);
        add_action('wpcf7_submit', [$this, 'process_form'], 1, 2);

        add_action('admin_enqueue_scripts', [$this, 'select2_enqueue']);
        add_action('admin_footer', [$this, 'js_script']);
    }

    public function select2_enqueue()
    {
        wp_enqueue_script('mailoptin-select2', MAILOPTIN_ASSETS_URL . 'js/customizer-controls/select2/select2.min.js', array('jquery'), false, true);
        wp_enqueue_style('mailoptin-select2', MAILOPTIN_ASSETS_URL . 'js/customizer-controls/select2/select2.min.css', null);
    }

    /**
     * @param \WPCF7_ContactForm $contact_form
     * @param mixed $result
     */
    public function process_form($contact_form, $result)
    {
        if (empty($result['status']) || ! in_array($result['status'], ['mail_sent', 'mail_failed'])) {
            return;
        }

        $contact_form_id = $contact_form->id();
        $obj             = \WPCF7_Submission::get_instance();
        $posted_data     = $obj->get_posted_data();

        $mocf7_settings = get_post_meta($contact_form_id, 'mocf7_settings', true);

        $required_acceptance = moVar($mocf7_settings, 'require_acceptance');

        if ( ! empty($required_acceptance) && moVar($posted_data, $required_acceptance) != '1') return;

        $field_mapping = moVar($mocf7_settings, 'custom_fields');

        $name = $posted_data[moVar($field_mapping, 'moName')];
        $first_name = $posted_data[moVar($field_mapping, 'moFirstName')];
        $last_name = $posted_data[moVar($field_mapping, 'moLastName')];
        $connection_service = moVar($mocf7_settings, 'integration');

        $double_optin = false;
        if(in_array($connection_service, Init::double_optin_support_connections(true))) {
            $double_optin = moVar($mocf7_settings, 'is_double_optin') === "true";
        }

        $optin_data = new ConversionDataBuilder();
        // since it's non mailoptin form, set it to zero.
        $optin_data->optin_campaign_id   = 0;
        $optin_data->payload             = $posted_data;

        //check if the full name moName is empty, else join both the first name and last name
        $optin_data->name                = Init::return_name($name, $first_name, $last_name);
        $optin_data->email               = $posted_data[moVar($field_mapping, 'moEmail')];
        $optin_data->optin_campaign_type = esc_html__('Contact Form 7', 'mailoptin');

        $optin_data->connection_service    = $connection_service;
        $optin_data->connection_email_list = moVar($mocf7_settings, 'list');

        $optin_data->user_agent                = esc_html($_SERVER['HTTP_USER_AGENT']);
        $optin_data->is_timestamp_check_active = false;
        $optin_data->is_double_optin      = $double_optin;

        if (isset($_REQUEST['referrer'])) {
            $optin_data->conversion_page = esc_url_raw($_REQUEST['referrer']);
        }

        $optin_data->form_tags = moVar($mocf7_settings, 'tags');

        foreach ($field_mapping as $key => $cf7_form_tag) {
            if (in_array($key, ['moEmail', 'moName', 'moFirstName', 'moLastName'])) continue;
            $field_value = moVar($posted_data, $cf7_form_tag);

            if ( ! empty($field_value)) {
                $optin_data->form_custom_field_mappings[$key] = $cf7_form_tag;
            }
        }

        AjaxHandler::do_optin_conversion($optin_data);
    }

    public function add_panel($panels)
    {
        $panels['mailoptin'] = array(
            'title'    => 'MailOptin',
            'callback' => array($this, 'panel_content')
        );

        return $panels;
    }

    public static function email_service_providers()
    {
        $connections = ConnectionsRepository::get_connections();

        if (defined('MAILOPTIN_DETACH_LIBSODIUM')) {
            $connections['leadbank'] = __('MailOptin Leads', 'mailoptin');
        }

        return $connections;
    }

    public function form_tags(\WPCF7_ContactForm $contact_form, $field_type = null)
    {
        return array_reduce($contact_form->scan_form_tags(), function ($carry, $item) use ($field_type) {
            if ( ! empty($item->name)) {
                if ( ! empty($field_type) && $item->basetype != $field_type) return $carry;

                $carry[$item->name] = $item->name;
            }

            return $carry;
        }, ['' => esc_html__('Select...', 'mailoptin')]);
    }

    /**
     * @param \WPCF7_ContactForm $contact_form
     */
    public function panel_content($contact_form)
    {
        $connections = self::email_service_providers();

        $post_id = $contact_form->id();

        $mocf7_settings = get_post_meta($post_id, 'mocf7_settings', true);

        $saved_require_acceptance = moVar($mocf7_settings, 'require_acceptance');
        $saved_integration        = moVar($mocf7_settings, 'integration');
        $saved_list               = moVar($mocf7_settings, 'list');
        $saved_tags               = moVar($mocf7_settings, 'tags');
        $saved_double_optin       = moVar($mocf7_settings, 'is_double_optin');
        $mapped_custom_fields     = moVar($mocf7_settings, 'custom_fields');

        $tags = [];
        if ( ! empty($saved_integration) && in_array($saved_integration, Init::select2_tag_connections())) {
            $instance = ConnectionFactory::make($saved_integration);
            if (method_exists($instance, 'get_tags')) {
                $tags = $instance->get_tags();
            }
        }

        $lists = [];
        if ( ! empty($saved_integration) && $saved_integration != 'leadbank') {
            $lists = ConnectionFactory::make($saved_integration)->get_email_list();
        }

        $custom_fields = [
            'moEmail' => esc_html__('Email Address', 'mailoptin'),
            'moName'  => esc_html__('Full Name', 'mailoptin'),
            'moFirstName'  => esc_html__('First Name', 'mailoptin'),
            'moLastName'  => esc_html__('Last Name', 'mailoptin'),
        ];

        if (in_array($saved_integration, Init::no_name_mapping_connections())) {
            unset($custom_fields['moName']);
            unset($custom_fields['moFirstName']);
            unset($custom_fields['moLastName']);
        }

        if ( ! empty($saved_integration) && $saved_integration != 'leadbank') {

            if (defined('MAILOPTIN_DETACH_LIBSODIUM')) {
                $instance = ConnectionFactory::make($saved_integration);

                if (in_array($instance::OPTIN_CUSTOM_FIELD_SUPPORT, $instance::features_support())) {
                    $cfields = $instance->get_optin_fields($saved_list);
                    if (is_array($cfields) && ! empty($cfields)) {
                        $custom_fields += $cfields;
                    }
                }
            }
        }

        $default_double_optin = false;
        if(! empty($saved_integration) && defined('MAILOPTIN_DETACH_LIBSODIUM')) {
            $double_optin_connections = Init::double_optin_support_connections();
            foreach($double_optin_connections as $key => $value) {
                if($saved_integration === $key) {
                    $default_double_optin = $value;
                }
            }
        }

        require dirname(__FILE__) . '/panel-settings-view.php';
    }

    public function sanitize_settings($data)
    {
        $sanitized_data = [];
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized_data[$key] = sanitize_text_field($value);
            }

            if (is_array($value)) {
                $sanitized_data[$key] = self::sanitize_settings($value);
            }
        }

        return $sanitized_data;
    }

    /**
     * @param \WPCF7_ContactForm $contact_form
     */
    public function save_settings($contact_form)
    {
        if (empty($_POST)) return;

        $post_id = $contact_form->id();

        update_post_meta($post_id, 'mocf7_settings', self::sanitize_settings($_POST['mocf7_settings']));
    }

    public function js_script()
    {
        ?>
        <script>
            (function ($) {
                var run = function () {
                    var cache = $('select.mocf7Tags');
                    if (typeof cache.select2 !== 'undefined') {
                        cache.select2()
                    }
                };
                run();
                $(window).on('load', run);

                $('#mocf7SelectIntegration, #mocf7SelectList').on('change', function () {
                    var btnSaveList = document.getElementsByName('wpcf7-save');
                    if (btnSaveList.length > 0) btnSaveList[0].click();
                });
            })(jQuery)
        </script>
        <?php
    }
}