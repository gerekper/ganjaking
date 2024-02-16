<?php

namespace Essential_Addons_Elementor\Pro\Classes;

use Essential_Addons_Elementor\Traits\Login_Registration;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

class Bootstrap
{
    use \Essential_Addons_Elementor\Pro\Traits\Library;
    use \Essential_Addons_Elementor\Pro\Traits\Core;
    use \Essential_Addons_Elementor\Pro\Traits\Extender;
    use \Essential_Addons_Elementor\Pro\Traits\Enqueue;
    use \Essential_Addons_Elementor\Pro\Traits\Helper;
    use \Essential_Addons_Elementor\Pro\Traits\Instagram_Feed;
    use Login_Registration;
    // instance container
    private static $instance = null;

    /**
     * Singleton instance
     *
     * @since 3.0.0
     */
    public static function instance()
    {
        if (self::$instance == null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Constructor of plugin class
     *
     * @since 3.0.0
     */
    private function __construct()
    {
        // mark pro version is enabled
        add_filter('eael/pro_enabled', '__return_true');

        // injecting pro elements
        add_filter('eael/registered_elements', array($this, 'inject_new_elements'));
        add_filter('eael/registered_extensions', array($this, 'inject_new_extensions'));
        add_filter('eael/post_args', [$this, 'eael_post_args']);

        // register hooks
        $this->register_hooks();

        // license
        $this->plugin_licensing();
    }

    public function register_hooks()
    {
        // Extender filters
        add_filter('add_eael_progressbar_layout', [$this, 'add_progressbar_pro_layouts']);
        add_filter('fancy_text_style_types', [$this, 'fancy_text_style_types']);
        add_filter('eael_ticker_options', [$this, 'ticker_options']);
        add_filter('eael_progressbar_rainbow_wrap_class', [$this, 'progress_bar_rainbow_class'], 10, 2);
        add_filter('eael_progressbar_circle_fill_wrap_class', [$this, 'progress_bar_circle_fill_class'], 10, 2);
        add_filter('eael_progressbar_half_circle_wrap_class', [$this, 'progressbar_half_circle_wrap_class'], 10, 2);
        add_filter('eael_progressbar_general_style_condition', [$this, 'progressbar_general_style_condition']);
        add_filter('eael_progressbar_line_fill_stripe_condition', [$this, 'progressbar_line_fill_stripe_condition']);
        add_filter('eael_circle_style_general_condition', [$this, 'circle_style_general_condition']);
        add_filter('eael_pricing_table_styles', [$this, 'add_pricing_table_styles']);
        add_filter('pricing_table_subtitle_field_for', [$this, 'pricing_table_subtitle_field']);
        add_filter('eael_pricing_table_icon_supported_style', [$this, 'pricing_table_icon_support']);
        add_filter('eael_pricing_table_header_radius_supported_style', [$this, 'pricing_table_header_radius_support']);
        add_filter('eael_pricing_table_header_bg_supported_style', [$this, 'pricing_table_header_background_support']);
        add_filter('eael/advanced-data-table/table_html/integration/database', [$this, 'advanced_data_table_database_integration'], 10, 1);
        add_filter('eael/advanced-data-table/table_html/integration/remote', [$this, 'advanced_data_table_remote_database_integration'], 10, 1);
        add_filter('eael/advanced-data-table/table_html/integration/google', [$this, 'advanced_data_table_google_sheets_integration'], 10, 1);
        add_filter('eael/advanced-data-table/table_html/integration/tablepress', [$this, 'advanced_data_table_tablepress_integration'], 10, 1);
        add_filter('eael/event-calendar/integration', [$this, 'event_calendar_eventon_integration'], 10, 2);
        add_filter('eael_team_member_style_presets_condition', [$this, 'team_member_presets_condition']);

        //Extended actions
        add_action('eael_section_data_table_enabled', [$this, 'data_table_sorting']);
        add_action('eael_ticker_custom_content_controls', [$this, 'ticker_custom_contents']);
        add_action('add_progress_bar_control', [$this, 'progress_bar_box_control'], 10, 3);
        add_action('add_eael_progressbar_block', [$this, 'add_box_progress_bar_block'], 10, 3);
        add_action('add_pricing_table_settings_control', [$this, 'pricing_table_header_image_control']);
        add_action('pricing_table_currency_position', [$this, 'pricing_table_style_2_currency_position']);
        add_action('add_pricing_table_style_block', [$this, 'add_pricing_table_pro_styles'], 10, 6);
        add_action('eael_pricing_table_after_pricing_style', [$this, 'pricing_table_style_five_settings_control']);
        add_action('eael_pricing_table_control_header_extra_layout', [$this, 'pricing_table_style_header_layout_two']);
        add_action('add_admin_license_markup', [$this, 'add_admin_licnes_markup_html'], 10, 5);
        add_action('eael_premium_support_link', [$this, 'add_eael_premium_support_link'], 10, 5);
        add_action('eael_additional_support_links', [$this, 'add_eael_additional_support_links'], 10, 5);
        add_action('eael_manage_license_action_link', [$this, 'add_manage_linces_action_link'], 10, 5);
        add_action('eael_creative_button_pro_controls', [$this, 'add_creative_button_controls'], 10, 1);
        add_action('eael_creative_button_style_pro_controls', [$this, 'add_creative_button_style_pro_controls'], 10, 5);
        add_action('wp_ajax_eael_ajax_post_search', [$this, 'ajax_post_search']);
        add_action('wp_ajax_nopriv_eael_ajax_post_search', [$this, 'ajax_post_search']);
        add_action('eael/team_member_circle_controls', [$this, 'add_team_member_circle_presets']);
        add_action('eael/team_member_social_botton_markup', [$this, 'add_team_member_social_bottom_markup'], 10, 2);
        add_action('eael/team_member_social_right_markup', [$this, 'add_team_member_social_right_markup'], 10, 2);
        add_action('eael/controls/advanced-data-table/source', [$this, 'advanced_data_table_source_control'], 10, 1);
        add_action('eael/event-calendar/source/control', [$this, 'event_calendar_source_control'], 10, 1);
        add_action('eael/event-calendar/activation-notice', [$this, 'event_calendar_activation_notice'], 10, 1);

        add_filter('eael/woo-checkout/layout', [$this, 'eael_woo_checkout_layout']);
        add_action('eael_add_woo_checkout_pro_layout', [$this, 'add_woo_checkout_pro_layout'], 10, 2);
        add_action('eael_woo_checkout_pro_enabled_general_settings', [$this, 'add_woo_checkout_tabs_data']);
        add_action('eael_woo_checkout_pro_enabled_tabs_styles', [$this, 'add_woo_checkout_tabs_styles']);
        add_action('eael_woo_checkout_pro_enabled_tabs_styles', [$this, 'add_woo_checkout_section_styles']);
        add_action('eael_woo_checkout_pro_enabled_steps_btn_styles', [$this, 'add_woo_checkout_steps_btn_styles']);

        add_action('eael/login-register/after-general-controls', [$this, 'lr_init_content_ajax_controls']);
        add_action('eael/login-register/after-init-login-button-style', [$this, 'lr_init_content_login_spinner_controls']);
        add_action('eael/login-register/after-init-register-button-style', [$this, 'lr_init_content_register_spinner_controls']);
        add_action('eael/login-register/after-login-controls-section', [$this, 'lr_init_content_social_login_controls']);
        add_action('eael/login-register/after-login-footer', [$this, 'lr_print_social_login']);
        add_action('eael/login-register/after-register-footer', [$this, 'lr_print_social_login_on_register']);
        add_action('eael/login-register/after-style-controls', [$this, 'lr_init_style_social_controls']);
        add_action('eael/login-register/after-style-controls', [$this, 'lr_init_style_pass_strength_controls']);
        add_action('eael/login-register/mailchimp-integration', [$this, 'lr_init_mailchimp_integration_controls']);
        add_action('eael/login-register/after-register-options-controls', [$this, 'lr_init_content_pass_strength_controls']);
        add_action('eael/login-register/after-pass-visibility-controls', [$this, 'lr_init_content_icon_controls']);
        add_filter('eael/login-register/scripts', [$this, 'lr_load_pro_scripts']);
        add_filter('eael/login-register/styles', [$this, 'lr_load_pro_styles']);
        add_action('eael/login-register/register-repeater', [$this, 'lr_add_register_fields_icons']);
        add_action('eael/login-register/register-rf-default', [$this, 'lr_add_register_fields_default_icons']);
        add_action('eael/login-register/after-password-field', [$this, 'lr_show_password_strength_meter']);
        add_action('eael/login-register/mailchimp-integration-action', [$this, 'login_register_mailchimp_integration_subscribe'], 10, 3);
        add_filter('eael/login-register/register-user-password-validation', [$this, 'lr_register_user_password_validation'], 10, 3);

        // ajax
        add_action('wp_ajax_woo_checkout_post_code_validate', [$this, 'eael_woo_checkout_post_code_validate']);
        add_action('wp_ajax_nopriv_woo_checkout_post_code_validate', [$this, 'eael_woo_checkout_post_code_validate']);
        add_action('wp_ajax_mailchimp_subscribe', [$this, 'mailchimp_subscribe_with_ajax']);
        add_action('wp_ajax_nopriv_mailchimp_subscribe', [$this, 'mailchimp_subscribe_with_ajax']);
        add_action('wp_ajax_instafeed_load_more', [$this, 'instafeed_render_items']);
        add_action('wp_ajax_nopriv_instafeed_load_more', [$this, 'instafeed_render_items']);
        add_action('wp_ajax_connect_remote_db', [$this, 'connect_remote_db']);
        add_action('wp_ajax_eael-login-register-form', [$this, 'login_or_register_user']);
        add_action('wp_ajax_nopriv_eael-login-register-form', [$this, 'login_or_register_user']);
        add_action('eael/login-register/before-processing-login-register', [$this, 'lr_handle_social_login']);
		//adv search
	    add_action('wp_ajax_fetch_search_result', array($this, 'fetch_search_result'));
	    add_action('wp_ajax_nopriv_fetch_search_result', array($this, 'fetch_search_result'));

        // localize script
        add_filter('eael/localize_objects', [$this, 'script_localizer']);

        // pro scripts
        add_action('eael/before_enqueue_scripts', [$this, 'before_enqueue_scripts']);

        // admin script
        add_action('admin_enqueue_scripts', [$this, 'admin_scripts']);

	    if ( is_admin() ) {
		    // Core
		    add_filter( 'plugin_action_links_' . EAEL_PRO_PLUGIN_BASENAME, array( $this, 'insert_plugin_links' ) );
	    }
    }

    // push pro widgets in lite
    public function inject_new_elements($elements)
    {
        return array_merge_recursive($elements, $GLOBALS['eael_pro_config']['elements']);
    }

    // push pro extensions in lite
    public function inject_new_extensions($extensions)
    {
        return array_merge_recursive($extensions, $GLOBALS['eael_pro_config']['extensions']);
    }
}
