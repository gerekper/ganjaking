<?php

namespace MailOptin\Core\Admin\Customizer\OptinForm;

class CustomizerSettings extends AbstractCustomizer
{
    /** @var \WP_Customize_Manager */
    public $wp_customize;

    /** @var Customizer */
    public $customizerClassInstance;

    /** @var string DB option name prefix */
    public $option_prefix;

    public $optin_campaign_id;

    /**
     *
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param Customizer $customizerClassInstance
     */
    public function __construct($wp_customize, $option_prefix, $customizerClassInstance)
    {
        $this->wp_customize            = $wp_customize;
        $this->customizerClassInstance = $customizerClassInstance;
        $this->option_prefix           = $option_prefix;

        $this->optin_campaign_id = $customizerClassInstance->optin_campaign_id;

        parent::__construct($this->optin_campaign_id);
    }

    /**
     * Customize setting for all optin form design controls.
     */
    public function design_settings()
    {
        $design_settings_args = apply_filters("mo_optin_form_customizer_design_settings",
            array(
                'form_width'            => array(
                    'default'           => $this->customizer_defaults['form_width'],
                    'type'              => 'option',
                    'sanitize_callback' => 'absint',
                    'transport'         => 'refresh',
                ),
                'form_background_image' => array(
                    'default'           => $this->customizer_defaults['form_background_image'],
                    'type'              => 'option',
                    'sanitize_callback' => 'esc_url',
                    'transport'         => 'postMessage',
                ),
                'form_image'            => array(
                    'default'           => $this->customizer_defaults['form_image'],
                    'type'              => 'option',
                    'sanitize_callback' => 'absint',
                    'transport'         => 'postMessage',
                ),
                'hide_form_image'       => array(
                    'default'   => $this->customizer_defaults['hide_form_image'],
                    'type'      => 'option',
                    'transport' => 'refresh',
                ),
                'form_background_color' => array(
                    'default'           => $this->customizer_defaults['form_background_color'],
                    'type'              => 'option',
                    'sanitize_callback' => 'sanitize_hex_color',
                    'transport'         => 'postMessage',
                ),
                'form_border_color'     => array(
                    'default'           => $this->customizer_defaults['form_border_color'],
                    'type'              => 'option',
                    'sanitize_callback' => 'sanitize_hex_color',
                    'transport'         => 'postMessage',
                ),
                'form_custom_css'       => array(
                    'default'   => $this->customizer_defaults['form_custom_css'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'custom_css_notice'     => array(
                    'default'   => apply_filters('mo_optin_form_custom_css_notice', false),
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
            ),
            $this
        );

        foreach ($design_settings_args as $id => $args) {
            $args['autoload'] = false;
            $this->wp_customize->add_setting($this->option_prefix . '[' . $id . ']', $args);
        }

        do_action('mo_optin_after_design_customizer_settings', $this->wp_customize, $design_settings_args);
    }


    /**
     * Remove paragraph from being added to $val.
     *
     * @param string $val
     *
     * @return string
     */
    public function _remove_paragraph_from_headline($val)
    {
        return str_replace(['<p>', '</p>'], ['', ''], $val);
    }

    /**
     * Customize setting for all optin form headline controls.
     */
    public function headline_settings()
    {
        $headline_settings_args = apply_filters("mo_optin_form_customizer_headline_settings",
            array(
                'hide_headline'              => array(
                    'default'   => $this->customizer_defaults['hide_headline'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'headline'                   => array(
                    'default'           => $this->customizer_defaults['headline'],
                    'type'              => 'option',
                    'transport'         => 'postMessage',
                    'sanitize_callback' => array($this, '_remove_paragraph_from_headline'),
                ),
                'headline_font_color'        => array(
                    'default'   => $this->customizer_defaults['headline_font_color'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'headline_font'              => array(
                    'default'   => $this->customizer_defaults['headline_font'],
                    'type'      => 'option',
                    'transport' => 'refresh',
                ),
                'headline_font_size_mobile'  => array(
                    'default'           => $this->customizer_defaults['headline_font_size_mobile'],
                    'type'              => 'option',
                    'transport'         => 'refresh',
                    'sanitize_callback' => 'absint'
                ),
                'headline_font_size_tablet'  => array(
                    'default'           => $this->customizer_defaults['headline_font_size_tablet'],
                    'type'              => 'option',
                    'transport'         => 'refresh',
                    'sanitize_callback' => 'absint'
                ),
                'headline_font_size_desktop' => array(
                    'default'           => $this->customizer_defaults['headline_font_size_desktop'],
                    'type'              => 'option',
                    'transport'         => 'refresh',
                    'sanitize_callback' => 'absint'
                )
            ),
            $this
        );

        foreach ($headline_settings_args as $id => $args) {
            $args['autoload'] = false;
            $this->wp_customize->add_setting($this->option_prefix . '[' . $id . ']', $args);
        }

        do_action('mo_optin_after_headline_customizer_settings', $this->wp_customize, $headline_settings_args, $this);
    }

    /**
     * Customize setting for all optin form description controls.
     */
    public function description_settings()
    {
        $description_settings_args = apply_filters("mo_optin_form_customizer_description_settings",
            array(
                'hide_description'              => array(
                    'default'   => $this->customizer_defaults['hide_description'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'description'                   => array(
                    'default'   => $this->customizer_defaults['description'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'description_font_color'        => array(
                    'default'   => $this->customizer_defaults['description_font_color'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'description_font'              => array(
                    'default'   => $this->customizer_defaults['description_font'],
                    'type'      => 'option',
                    'transport' => 'refresh',
                ),
                'description_font_size_mobile'  => array(
                    'default'           => $this->customizer_defaults['description_font_size_mobile'],
                    'type'              => 'option',
                    'transport'         => 'refresh',
                    'sanitize_callback' => 'absint'
                ),
                'description_font_size_tablet'  => array(
                    'default'           => $this->customizer_defaults['description_font_size_tablet'],
                    'type'              => 'option',
                    'transport'         => 'refresh',
                    'sanitize_callback' => 'absint'
                ),
                'description_font_size_desktop' => array(
                    'default'           => $this->customizer_defaults['description_font_size_desktop'],
                    'type'              => 'option',
                    'transport'         => 'refresh',
                    'sanitize_callback' => 'absint'
                )
            ),
            $this
        );

        foreach ($description_settings_args as $id => $args) {
            $args['autoload'] = false;
            $this->wp_customize->add_setting($this->option_prefix . '[' . $id . ']', $args);
        }

        do_action('mo_optin_after_description_customizer_settings', $this->wp_customize, $description_settings_args);
    }

    /**
     * Customize setting for all optin form note controls.
     */
    public function note_settings()
    {
        $note_settings_args = apply_filters("mo_optin_form_customizer_note_settings",
            array(
                'hide_note'                => array(
                    'default'   => $this->customizer_defaults['hide_note'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'note'                     => array(
                    'default'   => $this->customizer_defaults['note'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'note_font_color'          => array(
                    'default'   => $this->customizer_defaults['note_font_color'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'note_font'                => array(
                    'default'   => $this->customizer_defaults['note_font'],
                    'type'      => 'option',
                    'transport' => 'refresh',
                ),
                'note_font_size_mobile'    => array(
                    'default'           => $this->customizer_defaults['note_font_size_mobile'],
                    'type'              => 'option',
                    'transport'         => 'refresh',
                    'sanitize_callback' => 'absint'
                ),
                'note_font_size_tablet'    => array(
                    'default'           => $this->customizer_defaults['note_font_size_tablet'],
                    'type'              => 'option',
                    'transport'         => 'refresh',
                    'sanitize_callback' => 'absint'
                ),
                'note_font_size_desktop'   => array(
                    'default'           => $this->customizer_defaults['note_font_size_desktop'],
                    'type'              => 'option',
                    'transport'         => 'refresh',
                    'sanitize_callback' => 'absint'
                ),
                'note_acceptance_checkbox' => array(
                    'default'   => $this->customizer_defaults['note_acceptance_checkbox'],
                    'type'      => 'option',
                    'transport' => 'refresh',
                ),
                'note_acceptance_error'    => array(
                    'default'   => $this->customizer_defaults['note_acceptance_error'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'note_close_optin_onclick' => array(
                    'default'   => $this->customizer_defaults['note_close_optin_onclick'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                )
            ),
            $this
        );

        foreach ($note_settings_args as $id => $args) {
            $args['autoload'] = false;
            $this->wp_customize->add_setting($this->option_prefix . '[' . $id . ']', $args);
        }

        do_action('mo_optin_after_note_customizer_settings', $this->wp_customize, $note_settings_args);
    }


    /**
     * Customize setting for all optin form fields controls.
     */
    public function fields_settings()
    {
        $fields_settings_args = apply_filters("mo_optin_form_customizer_fields_settings",
            array(
                'fields'                 => array(
                    'default'   => '',
                    'type'      => 'option',
                    'transport' => 'refresh',
                ),
                'display_only_button'    => array(
                    'default'   => $this->customizer_defaults['display_only_button'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'hide_name_field'        => array(
                    'default'   => $this->customizer_defaults['hide_name_field'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'name_field_header'      => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'email_field_header'     => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'submit_button_header'   => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'cta_button_header'      => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'name_field_placeholder' => array(
                    'default'   => $this->customizer_defaults['name_field_placeholder'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'name_field_color'       => array(
                    'default'   => $this->customizer_defaults['name_field_color'],
                    'type'      => 'option',
                    'transport' => 'refresh',
                ),
                'name_field_background'  => array(
                    'default'   => $this->customizer_defaults['name_field_background'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'name_field_font'        => array(
                    'default'   => $this->customizer_defaults['name_field_font'],
                    'type'      => 'option',
                    'transport' => 'refresh',
                ),
                'name_field_required'    => array(
                    'default'   => $this->customizer_defaults['name_field_required'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),

                // ---- Email fields -------- //

                'email_field_placeholder' => array(
                    'default'   => $this->customizer_defaults['email_field_placeholder'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'email_field_color'       => array(
                    'default'   => $this->customizer_defaults['email_field_color'],
                    'type'      => 'option',
                    'transport' => 'refresh',
                ),
                'email_field_background'  => array(
                    'default'   => $this->customizer_defaults['email_field_background'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'email_field_font'        => array(
                    'default'   => $this->customizer_defaults['email_field_font'],
                    'type'      => 'option',
                    'transport' => 'refresh',
                ),

                // ---- submit button -------- //

                'submit_button'            => array(
                    'default'   => $this->customizer_defaults['submit_button'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'submit_button_color'      => array(
                    'default'   => $this->customizer_defaults['submit_button_color'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'submit_button_background' => array(
                    'default'   => $this->customizer_defaults['submit_button_background'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'submit_button_font'       => array(
                    'default'   => $this->customizer_defaults['submit_button_font'],
                    'type'      => 'option',
                    'transport' => 'refresh',
                ),

                // ---- call-to-action button -------- //

                'cta_button_action'         => array(
                    'default'   => $this->customizer_defaults['cta_button_action'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'cta_button_navigation_url' => array(
                    'default'   => $this->customizer_defaults['cta_button_navigation_url'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'cta_button'                => array(
                    'default'   => $this->customizer_defaults['cta_button'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'cta_button_color'          => array(
                    'default'   => $this->customizer_defaults['cta_button_color'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'cta_button_background'     => array(
                    'default'   => $this->customizer_defaults['cta_button_background'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'cta_button_font'           => array(
                    'default'   => $this->customizer_defaults['cta_button_font'],
                    'type'      => 'option',
                    'transport' => 'refresh',
                ),

                'use_custom_html'     => array(
                    'default'   => $this->customizer_defaults['use_custom_html'],
                    'type'      => 'option',
                    'transport' => 'refresh',
                ),
                'custom_html_content' => array(
                    'default'   => $this->customizer_defaults['custom_html_content'],
                    'type'      => 'option',
                    'transport' => 'refresh',
                ),
            ),
            $this
        );

        foreach ($fields_settings_args as $id => $args) {
            $args['autoload'] = false;
            $this->wp_customize->add_setting($this->option_prefix . '[' . $id . ']', $args);
        }

        do_action('mo_optin_after_fields_customizer_settings', $this->wp_customize, $fields_settings_args);
    }


    /**
     * Customize setting for all optin form configuration controls.
     */
    public function configuration_settings()
    {
        $configuration_settings_args = apply_filters("mo_optin_form_customizer_configuration_settings",
            array(
                'split_test_note'            => array(
                    'default'   => $this->customizer_defaults['split_test_note'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'inpost_form_optin_position' => array(
                    'default'   => $this->customizer_defaults['inpost_form_optin_position'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'bar_position'               => array(
                    'default'   => $this->customizer_defaults['bar_position'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'slidein_position'           => array(
                    'default'   => $this->customizer_defaults['slidein_position'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'bar_sticky'                 => array(
                    'default'   => $this->customizer_defaults['bar_sticky'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'hide_close_button'          => array(
                    'default'   => $this->customizer_defaults['hide_close_button'],
                    'type'      => 'option',
                    'transport' => 'refresh',
                ),
                'close_backdrop_click'       => array(
                    'default'   => $this->customizer_defaults['close_backdrop_click'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'success_message'            => array(
                    'default'   => $this->customizer_defaults['success_message'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'cookie'                     => array(
                    'default'   => $this->customizer_defaults['cookie'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'success_cookie'             => array(
                    // default to the default value of exit cookie.
                    'default'   => $this->customizer_defaults['cookie'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'remove_branding'            => array(
                    'default'   => $this->customizer_defaults['remove_branding'],
                    'type'      => 'option',
                    'transport' => 'refresh',
                )
            ),
            $this
        );

        foreach ($configuration_settings_args as $id => $args) {
            $args['autoload'] = false;
            $this->wp_customize->add_setting($this->option_prefix . '[' . $id . ']', $args);
        }

        do_action('mo_optin_after_configuration_customizer_settings', $this->wp_customize, $configuration_settings_args);
    }

    /**
     * Customize setting for all optin form integration controls.
     */
    public function integration_settings()
    {
        $integration_settings_args = apply_filters("mo_optin_form_customizer_integration_settings",
            array(
                'lead_bank_only'        => array(
                    'default'   => '',
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'integrations'          => array(
                    'default'   => '',
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'custom_field_mappings' => array(
                    'default'   => '',
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'no_integration_notice' => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'leadbank_notice'       => array(
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'ajax_nonce'            => array(
                    'default'   => wp_create_nonce('customizer-fetch-email-list'),
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
            ),
            $this
        );

        foreach ($integration_settings_args as $id => $args) {
            $args['autoload'] = false;
            $this->wp_customize->add_setting($this->option_prefix . '[' . $id . ']', $args);
        }

        do_action('mo_optin_after_integration_customizer_settings', $this->wp_customize, $integration_settings_args);
    }

    /**
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param Customizer $customizerClassInstance
     */
    public function after_conversion_settings()
    {
        $success_settings_args = apply_filters("mo_optin_form_customizer_success_settings",
            array(
                'success_action'              => array(
                    'default'           => $this->customizer_defaults['success_action'],
                    'type'              => 'option',
                    'sanitize_callback' => 'sanitize_text_field',
                    'transport'         => 'postMessage',
                ),
                'redirect_url_value'          => array(
                    'default'           => '',
                    'type'              => 'option',
                    'sanitize_callback' => 'esc_url',
                    'transport'         => 'postMessage',
                ),
                'pass_lead_data_redirect_url' => array(
                    'default'   => $this->customizer_defaults['pass_lead_data_redirect_url'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'success_message_config_link' => array(
                    'default'   => '',
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'success_js_script'           => array(
                    'default'   => '',
                    'type'      => 'option',
                    'transport' => 'postMessage',
                )
            ),
            $this
        );

        foreach ($success_settings_args as $id => $args) {
            $args['autoload'] = false;
            $this->wp_customize->add_setting($this->option_prefix . '[' . $id . ']', $args);
        }

        do_action('mo_optin_after_success_customizer_settings', $this->wp_customize, $success_settings_args);
    }

    /**
     * Customize setting for all display rules controls.
     */
    public function display_rules_settings()
    {
        $output_settings_args = apply_filters("mo_optin_form_customizer_output_settings",
            array(
                'activate_optin'                  => array(
                    'default'   => apply_filters('mo_optin_form_activate_optin_default', ''),
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'load_optin_globally'             => array(
                    'default'   => $this->customizer_defaults['load_optin_globally'],
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'filter_query_string'             => array(
                    'default'   => '',
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'filter_query_value'              => array(
                    'default'   => '',
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'filter_query_action'             => array(
                    'default'   => '0',
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'who_see_optin'                   => array(
                    'default'   => apply_filters('mo_optin_form_who_see_optin', 'show_all'),
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'show_to_roles'                   => array(
                    'default'   => apply_filters('mo_optin_form_show_to_roles', ''),
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'prefill_logged_user_data'        => array(
                    'default'   => apply_filters('mo_optin_form_prefill_logged_user_data', false),
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'after_conversion_notice'         => array(
                    'default'   => apply_filters('mo_optin_form_after_conversion_notice', false),
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'optin_trigger_notice'            => array(
                    'default'   => apply_filters('mo_optin_form_optin_trigger_notice', false),
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'exit_intent_status'              => array(
                    'default'   => apply_filters('mo_optin_form_exit_intent_status', false),
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'x_seconds_status'                => array(
                    'default'   => apply_filters('mo_optin_form_x_seconds_status', false),
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'x_seconds_value'                 => array(
                    'default'   => apply_filters('mo_optin_form_x_seconds_value', 1),
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'x_scroll_status'                 => array(
                    'default'   => apply_filters('mo_optin_form_x_scroll_status', false),
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'x_scroll_value'                  => array(
                    'default'   => apply_filters('mo_optin_form_x_scroll_value', 1),
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'x_page_views_status'             => array(
                    'default'   => apply_filters('mo_optin_form_x_page_views_status', false),
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'x_page_views_condition'          => array(
                    'default'   => apply_filters('mo_optin_form_x_page_views_condition', '...'),
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'x_page_views_value'              => array(
                    'default'   => apply_filters('mo_optin_form_x_page_views_value', 1),
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'load_optin_index'                => array(
                    'default'   => apply_filters('mo_optin_form_load_optin_index', ''),
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'posts_never_load'                => array(
                    'default'   => apply_filters('mo_optin_form_posts_never_load', ''),
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'pages_never_load'                => array(
                    'default'   => apply_filters('mo_optin_form_pages_never_load', ''),
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'cpt_never_load'                  => array(
                    'default'   => apply_filters('mo_optin_form_cpt_never_load', ''),
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'post_categories_load'            => array(
                    'default'   => apply_filters('mo_optin_form_post_categories_load', ''),
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'post_categories_hide'            => array(
                    'default'   => apply_filters('mo_optin_form_post_categories_hide', ''),
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'post_tags_load'                  => array(
                    'default'   => apply_filters('mo_optin_form_post_tags_load', ''),
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'exclusive_post_types_posts_load' => array(
                    'default'   => apply_filters('mo_optin_form_exclusive_post_types_posts_load', ''),
                    'type'      => 'option',
                    'transport' => 'postMessage',
                ),
                'exclusive_post_types_load'       => array(
                    'default'   => apply_filters('mo_optin_form_exclusive_post_types_load', ''),
                    'type'      => 'option',
                    'transport' => 'postMessage',
                )
            ),
            $this
        );

        foreach ($output_settings_args as $id => $args) {
            $args['autoload'] = false;
            $this->wp_customize->add_setting($this->option_prefix . '[' . $id . ']', $args);
        }

        do_action('mo_optin_after_output_customizer_settings', $this->wp_customize, $output_settings_args);
    }

    /**
     * Customize setting for all embed controls.
     */
    public function embed_settings()
    {
        $shortcode_embed    = '[mo-optin-form id="' . $this->optin_campaign_uuid . '"]';
        $template_tag_embed = "do_action('mo_optin_form', '$this->optin_campaign_uuid');";

        $settings = [];

        $settings['embed_notice'] = array(
            'type'      => 'option',
            'transport' => 'postMessage',
        );

        $settings['widget_embed'] = array(
            'type'      => 'option',
            'transport' => 'postMessage',
        );

        $settings['block_embed'] = array(
            'type'      => 'option',
            'transport' => 'postMessage',
        );

        $settings['shortcode_embed'] = array(
            'default'   => apply_filters('mo_optin_form_shortcode_embed', $shortcode_embed),
            'type'      => 'option',
            'transport' => 'postMessage',
        );

        $settings['template_tag_embed'] = array(
            'default'   => apply_filters('mo_optin_form_template_tag_embed', $template_tag_embed),
            'type'      => 'option',
            'transport' => 'postMessage',
        );


        $settings_args = apply_filters("mo_optin_form_customizer_embed_settings", $settings, $this);

        foreach ($settings_args as $id => $args) {
            $args['autoload'] = false;
            $this->wp_customize->add_setting($this->option_prefix . '[' . $id . ']', $args);
        }

        do_action('mo_optin_after_embed_customizer_settings', $this->wp_customize, $settings_args);
    }
}