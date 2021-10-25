<?php

namespace MailOptin\ForminatorFormConnect;

use Forminator_Addon_Quiz_Hooks_Abstract;
use Forminator_Addon_Abstract;
use Forminator_Addon_Exception;
use MailOptin\Connections\Init;
use MailOptin\Core\AjaxHandler;
use MailOptin\Core\OptinForms\ConversionDataBuilder;
use function MailOptin\Core\moVar;

class QuizHook extends Forminator_Addon_Quiz_Hooks_Abstract {
    /**
     * Addon instance
     *
     * @var FFMailOptin
     */
    protected $addon;

    /**
     * Quiz settings instance
     *
     * @var ConnectionQuizSettingsPage | null
     *
     */
    protected $quiz_settings_instance;

    /**
     *
     * @param Forminator_Addon_Abstract $addon
     * @param                           $quiz_id
     *
     * @throws Forminator_Addon_Exception
     */
    public function __construct( Forminator_Addon_Abstract $addon, $quiz_id ) {
        parent::__construct( $addon, $quiz_id );
        $this->_submit_quiz_error_message = __( 'MailOptin failed to process submitted data. Please check your form and try again', 'mailoptin');
    }

    /**
     * Check submitted_data met requirement to sent to mailchimp
     * Send if possible, add result to entry fields
     *
     * MailOptin AddOn
     * @since 1.7 Add $form_entry_fields arg
     *
     * @param array $submitted_data
     * @param array $form_entry_fields
     *
     * @return array
     */
    public function add_entry_fields($submitted_data, $form_entry_fields = []) {
        $quiz_id                = $this->quiz_id;
        $quiz_settings_instance = $this->quiz_settings_instance;

        $submitted_data = apply_filters(
            'forminator_addon_mailoptin_quiz_submitted_data',
            $submitted_data,
            $quiz_id,
            $quiz_settings_instance
        );

        forminator_addon_maybe_log( __METHOD__, $submitted_data );

        $quiz_submitted_data  = get_quiz_submitted_data( $this->quiz, $submitted_data, $form_entry_fields );

        //addon settings
        $addon_setting_values = $this->quiz_settings_instance->get_quiz_settings_values();
        $quiz_settings        = $this->quiz_settings_instance->get_quiz_settings();
        $addons_fields        = $this->quiz_settings_instance->get_form_fields();


        $form_entry_fields   = forminator_lead_form_data( $submitted_data );
        $submitted_data      = get_addons_lead_form_entry_data( $quiz_settings, $submitted_data, $addons_fields );
        $submitted_data      = array_merge( $submitted_data, $quiz_submitted_data );


        $postdata = $this->get_field_values_for_mailoptin($submitted_data, $addon_setting_values, []);

        $name = moVar($postdata, 'moName');
        $first_name = moVar($postdata, 'moFirstName');
        $last_name = moVar($postdata, 'moLastName');

        $connected_service = $addon_setting_values['connected_email_providers'];

        $optin_data = new ConversionDataBuilder();

        // since it's non mailoptin form, set it to zero.
        $optin_data->optin_campaign_id          = 0;
        $optin_data->payload                    = $postdata;
        $optin_data->name                       = Init::return_name($name, $first_name, $last_name);
        $optin_data->email                      = $postdata['moEmail'];
        $optin_data->optin_campaign_type        = esc_html__('Forminator Quiz', 'mailoptin');
        $optin_data->connection_service         = $connected_service;
        $optin_data->connection_email_list      = $addon_setting_values[$connected_service]['lists'];
        $optin_data->user_agent                 = esc_html($_SERVER['HTTP_USER_AGENT']);
        $optin_data->is_timestamp_check_active  = false;

        if (isset($submitted_data['referer_url'])) {
            $optin_data->conversion_page = $submitted_data['referer_url'];
        }

        //map tags
        if(!empty($addon_setting_values[$connected_service]['tags'])) {
            $optin_data->form_tags = $addon_setting_values[$connected_service]['tags'];
        }

        //custom field mapping
        foreach($postdata as $key => $value) {
            if (in_array($key, ['moName', 'moEmail'])) continue;

            $field_value = moVar($postdata, $key);

            if (!empty($field_value)) {
                $optin_data->form_custom_field_mappings[$key] = $key;
            }
        }

        AjaxHandler::do_optin_conversion($optin_data);
    }

    public function get_field_values_for_mailoptin($entry, $settings, $vars)
    {
        foreach($settings['fields_map'] as $field_tag => $field_id) {
            if(!empty($field_id)) {
                $vars[$field_tag] = $this->get_entry_or_post_value($entry, $field_id);
            }
        }

        return $vars;
    }

    public function get_entry_or_post_value($entry, $field_id)
    {
        foreach($entry as $key => $value) {
            if(!empty($value) && $key == $field_id) {
                return $value;
            }
        }
    }

}