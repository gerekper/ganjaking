<?php

namespace MailOptin\ForminatorFormConnect;

use Forminator_Addon_Form_Hooks_Abstract;
use Forminator_Addon_Abstract;
use Forminator_Addon_Exception;
use MailOptin\Connections\Init;
use MailOptin\Core\OptinForms\ConversionDataBuilder;
use MailOptin\Core\AjaxHandler;
use function MailOptin\Core\moVar;

class FormHook extends Forminator_Addon_Form_Hooks_Abstract
{

    /**
     * Addon instance
     *
     * @var FFMailOptin
     */
    protected $addon;

    /**
     * Form settings instance
     *
     * @var ConnectionFormSettingsPage | null
     *
     */
    protected $form_settings_instance;

    /**
     *
     * @param Forminator_Addon_Abstract $addon
     * @param                           $form_id
     *
     * @throws Forminator_Addon_Exception
     */
    public function __construct(Forminator_Addon_Abstract $addon, $form_id)
    {
        parent::__construct($addon, $form_id);
        $this->_submit_form_error_message = __('MailOptin failed to process submitted data. Please check your form and try again', 'mailoptin');
    }

    public function add_entry_fields($submitted_data, $form_entry_fields = [])
    {
        $form_id                = $this->form_id;
        $form_settings_instance = $this->form_settings_instance;

        $submitted_data = apply_filters(
            'forminator_addon_mailoptin_form_submitted_data',
            $submitted_data,
            $form_id,
            $form_settings_instance
        );

        //addon settings
        $addon_setting_values = $this->form_settings_instance->get_form_settings_values();

        $postdata = $this->get_field_values_for_mailoptin($form_entry_fields, $addon_setting_values, []);

        $name       = moVar($postdata, 'moName');
        $first_name = moVar($postdata, 'moFirstName');
        $last_name  = moVar($postdata, 'moLastName');

        $connected_service = $addon_setting_values['connected_email_providers'];

        $optin_data = new ConversionDataBuilder();

        // since it's non mailoptin form, set it to zero.
        $optin_data->optin_campaign_id         = 0;
        $optin_data->payload                   = $postdata;
        $optin_data->name                      = Init::return_name($name, $first_name, $last_name);
        $optin_data->email                     = $postdata['moEmail'];
        $optin_data->optin_campaign_type       = esc_html__('Forminator Form', 'mailoptin');
        $optin_data->connection_service        = $connected_service;
        $optin_data->connection_email_list     = $addon_setting_values[$connected_service]['lists'];
        $optin_data->user_agent                = esc_html($_SERVER['HTTP_USER_AGENT']);
        $optin_data->is_timestamp_check_active = false;

        if (isset($_REQUEST['referrer'])) {
            $optin_data->conversion_page = esc_url_raw($_REQUEST['referrer']);
        }

        //map tags
        if ( ! empty($addon_setting_values[$connected_service]['tags'])) {
            $optin_data->form_tags = $addon_setting_values[$connected_service]['tags'];
        }

        //custom field mapping
        foreach ($postdata as $key => $value) {
            if (in_array($key, ['moName', 'moEmail', 'moFirstName', 'moLastName'])) continue;

            $field_value = moVar($postdata, $key);

            if ( ! empty($field_value)) {
                $optin_data->form_custom_field_mappings[$key] = $key;
            }
        }

        AjaxHandler::do_optin_conversion($optin_data);
    }

    public function get_field_values_for_mailoptin($entry, $settings, $vars)
    {
        foreach ($settings['fields_map'] as $field_tag => $field_id) {
            if ( ! empty($field_id)) {
                $vars[$field_tag] = $this->get_entry_or_post_value($entry, $field_id);
            }
        }

        return $vars;
    }

    public function get_entry_or_post_value($entry, $field_id)
    {
        foreach ($entry as $key => $value) {
            if (isset($value['name']) && $value['name'] == $field_id) {
                return $value['value'];
            }
        }
    }
}