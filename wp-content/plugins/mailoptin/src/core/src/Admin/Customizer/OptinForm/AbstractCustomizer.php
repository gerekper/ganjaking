<?php

namespace MailOptin\Core\Admin\Customizer\OptinForm;

use MailOptin\Core\Repositories\OptinCampaignsRepository;

/**
 * Main aim is to serve as a unify store for all optin form customizer default settings.
 */
class AbstractCustomizer
{
    /** @var array store arrays of optin form customizer default values. */
    public $customizer_defaults;

    /** @var int Optin campaign ID */
    protected $optin_campaign_id;

    /**
     * AbstractCustomizer constructor.
     *
     * @param null|int $optin_campaign_id
     */
    public function __construct($optin_campaign_id = null)
    {
        $this->optin_campaign_id = $optin_campaign_id;

        $this->optin_campaign_uuid  = OptinCampaignsRepository::get_optin_campaign_uuid($optin_campaign_id);
        $this->optin_campaign_type  = OptinCampaignsRepository::get_optin_campaign_type($optin_campaign_id);
        $this->optin_campaign_class = OptinCampaignsRepository::get_optin_campaign_class($optin_campaign_id);

        $this->customizer_defaults = $this->register_customizer_defaults();
    }

    /**
     * Return array of optin customizer default values.
     *
     * @return array
     */
    public function register_customizer_defaults()
    {
        $form_width = 700;
        if (in_array($this->optin_campaign_type, ['slidein', 'sidebar'])) {
            $form_width = 400;
        }
        if (in_array($this->optin_campaign_type, ['inpost'])) {
            $form_width = 100;
        }

        $defaults                    = [];
        $defaults['remove_branding'] = apply_filters('mo_optin_form_remove_branding_default', true, $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);

        $defaults['form_width']            = apply_filters('mo_optin_form_width_default', $form_width, $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['form_background_image'] = apply_filters('mo_optin_form_background_image_default', '', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['form_image']            = apply_filters('mo_optin_form_image_default', '', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['hide_form_image']       = apply_filters('mo_optin_hide_form_image_default', false, $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['form_background_color'] = apply_filters('mo_optin_form_background_color_default', '', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['form_border_color']     = apply_filters('mo_optin_form_border_color_default', '', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['form_custom_css']       = apply_filters('mo_optin_form_custom_css_default', '', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);

        $defaults['headline']                   = apply_filters('mo_optin_form_headline_default', __("Don't miss our update", 'mailoptin'), $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['headline_font_color']        = apply_filters('mo_optin_form_headline_font_color_default', '', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['headline_font']              = apply_filters('mo_optin_form_headline_font_default', 'Helvetica Neue', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['headline_font_size_tablet']  = apply_filters('mo_optin_form_headline_font_size_tablet_default', 30, $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['headline_font_size_mobile']  = apply_filters('mo_optin_form_headline_font_size_mobile_default', 25, $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['headline_font_size_desktop'] = apply_filters('mo_optin_form_headline_font_size_desktop_default', 32, $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);

        $defaults['description']                   = apply_filters('mo_optin_form_description_default', __('Be the first to get latest updates and exclusive content straight to your email inbox.', 'mailoptin'), $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['description_font_color']        = apply_filters('mo_optin_form_description_font_color_default', '', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['description_font']              = apply_filters('mo_optin_form_description_font_default', 'Tahoma', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['description_font_size_tablet']  = apply_filters('mo_optin_form_description_font_size_tablet_default', 18, $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['description_font_size_mobile']  = apply_filters('mo_optin_form_description_font_size_mobile_default', 16, $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['description_font_size_desktop'] = apply_filters('mo_optin_form_description_font_size_desktop_default', 18, $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);


        $defaults['note']                     = apply_filters('mo_optin_form_note_default', __('We promise not to spam you. You can unsubscribe at any time.', 'mailoptin'), $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['note_font_color']          = apply_filters('mo_optin_form_note_font_color_default', '', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['note_font']                = apply_filters('mo_optin_form_note_font_default', '', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['note_close_optin_onclick'] = apply_filters('mo_optin_form_note_close_optin_onclick_default', false, $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['note_acceptance_checkbox'] = apply_filters('mo_optin_form_note_acceptance_checkbox_default', false, $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['note_acceptance_error']    = apply_filters('mo_optin_form_note_acceptance_error_default', __('Please accept our terms.', 'mailoptin'), $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['note_font_size_tablet']    = apply_filters('mo_optin_form_note_font_size_tablet_default', 16, $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['note_font_size_mobile']    = apply_filters('mo_optin_form_note_font_size_mobile_default', 12, $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['note_font_size_desktop']   = apply_filters('mo_optin_form_note_font_size_desktop_default', 16, $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);

        $defaults['display_only_button']      = apply_filters('mo_optin_form_display_only_button_default', false, $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['use_custom_html']          = apply_filters('mo_optin_form_use_custom_html_default', false, $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['custom_html_content']      = apply_filters('mo_optin_form_custom_html_content_default', __('', 'mailoptin'), $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['hide_name_field']          = apply_filters('mo_optin_form_hide_name_field_default', false, $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['name_field_placeholder']   = apply_filters('mo_optin_form_name_field_placeholder_default', __('Enter your name here...', 'mailoptin'), $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['name_field_color']         = apply_filters('mo_optin_form_name_field_color_default', '', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['name_field_background']    = apply_filters('mo_optin_form_name_field_background_default', '', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['name_field_font']          = apply_filters('mo_optin_form_name_field_font_default', 'Consolas, Lucida Console, monospace', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['name_field_required']      = apply_filters('mo_optin_form_name_field_required_default', true, $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['email_field_placeholder']  = apply_filters('mo_optin_form_email_field_placeholder_default', __('Enter your email address here...', 'mailoptin'), $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['email_field_color']        = apply_filters('mo_optin_form_email_field_color_default', '', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['email_field_background']   = apply_filters('mo_optin_form_email_field_background_default', '', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['email_field_font']         = apply_filters('mo_optin_form_email_field_font_default', 'Consolas, Lucida Console, monospace', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['submit_button']            = apply_filters('mo_optin_form_submit_button_default', __('Subscribe Now', 'mailoptin'), $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['submit_button_color']      = apply_filters('mo_optin_form_submit_button_color_default', '', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['submit_button_background'] = apply_filters('mo_optin_form_submit_button_background_default', '', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['submit_button_font']       = apply_filters('mo_optin_form_submit_button_font_default', '', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);

        $cta_button_action_default_val         = defined('MAILOPTIN_DETACH_LIBSODIUM') ? 'reveal_optin_form' : 'navigate_to_url';
        $defaults['cta_button_action']         = apply_filters('mo_optin_form_cta_button_action_default', $cta_button_action_default_val, $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['cta_button_navigation_url'] = apply_filters('mo_optin_form_cta_button_navigation_url_default', '', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['cta_button']                = apply_filters('mo_optin_form_cta_button_default', __('Take Action Now!', 'mailoptin'), $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['cta_button_color']          = apply_filters('mo_optin_form_cta_button_color_default', $defaults['submit_button_color'], $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['cta_button_background']     = apply_filters('mo_optin_form_cta_button_background_default', $defaults['submit_button_background'], $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['cta_button_font']           = apply_filters('mo_optin_form_cta_button_font_default', $defaults['submit_button_font'], $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);


        $defaults['campaign_title']  = apply_filters('mo_optin_form_campaign_title_default', OptinCampaignsRepository::get_optin_campaign_name($this->optin_campaign_id), $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['split_test_note'] = apply_filters('mo_optin_form_split_test_note_default', '', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);

        $defaults['bar_position']         = apply_filters('mo_optin_form_bar_position_default', 'top', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['slidein_position']     = apply_filters('mo_optin_form_slidein_position_default', 'bottom_right', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['bar_sticky']           = apply_filters('mo_optin_form_hide_headline_default', true, $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['hide_close_button']    = apply_filters('mo_optin_form_hide_close_button_default', false, $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['close_backdrop_click'] = apply_filters('mo_optin_form_close_backdrop_click_default', true, $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['hide_headline']        = apply_filters('mo_optin_form_hide_headline_default', false, $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['hide_description']     = apply_filters('mo_optin_form_hide_description_default', false, $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['hide_note']            = apply_filters('mo_optin_form_hide_note_default', false, $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['success_message']      = apply_filters('mo_optin_form_success_message_default', __('Thanks for subscribing!', 'mailoptin'), $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['cookie']               = apply_filters('mo_optin_form_cookie_default', 30);

        $defaults['load_optin_globally'] = apply_filters('mo_optin_form_load_optin_globally_default', true, $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);

        $defaults['inpost_form_optin_position'] = apply_filters('mo_optin_form_inpost_form_optin_position_default', 'after_content', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);

        $defaults['schedule_status']   = apply_filters('mo_optin_form_schedule_status_default', false, $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['schedule_start']    = apply_filters('mo_optin_form_schedule_start_default', '', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['schedule_end']      = apply_filters('mo_optin_form_schedule_end_default', '', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['schedule_timezone'] = apply_filters('mo_optin_form_schedule_timezone_default', 'visitors_local_time', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);

        $defaults['adblock_status']   = apply_filters('mo_optin_form_adblock_status_default', false, $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['adblock_settings'] = apply_filters('mo_optin_form_adblock_settings_default', 'adblock_enabled', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);


        $defaults['newvsreturn_status']   = apply_filters('mo_optin_form_newvsreturn_status_default', false, $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['newvsreturn_settings'] = apply_filters('mo_optin_form_newvsreturn_settings_default', 'is_new', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);

        $defaults['referrer_detection_status']   = apply_filters('mo_optin_form_referrer_detection_status_default', false, $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['referrer_detection_settings'] = apply_filters('mo_optin_form_referrer_detection_settings_default', 'show_to', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['referrer_detection_values']   = apply_filters('mo_optin_form_referrer_detection_values_default', '', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);

        $defaults['modal_effects'] = apply_filters('mo_optin_form_modal_effects_default', '', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);

        $defaults['success_action']              = apply_filters('mo_optin_form_success_action_default', 'success_message', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['pass_lead_data_redirect_url'] = apply_filters('mo_optin_form_pass_lead_data_redirect_url_default', false, $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['state_after_conversion']      = apply_filters('mo_optin_form_state_after_conversion_default', 'success_message_shown', $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);

        $defaults['mo_optin_branding_outside_form'] = apply_filters('mo_optin_branding_outside_form', false, $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);

        $defaults['integrations'] = apply_filters('mo_optin_form_integrations_default', [], $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);
        $defaults['fields']       = apply_filters('mo_optin_form_fields_default', [], $this->customizer_defaults, $this->optin_campaign_type, $this->optin_campaign_class);

        return apply_filters('mo_optin_form_customizer_defaults', $defaults, $this->optin_campaign_type, $this->optin_campaign_class);
    }
}