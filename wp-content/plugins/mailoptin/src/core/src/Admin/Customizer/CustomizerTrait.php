<?php

namespace MailOptin\Core\Admin\Customizer;

use MailOptin\Core\PluginSettings\Settings;

trait CustomizerTrait
{
    public function modify_customizer_publish_button()
    {
        add_filter('gettext', function ($translations, $text, $domain) {
            if ($domain == 'default' && $text == 'Publish') {
                $translations = __('Save Changes', 'mailoptin');
            }
            if ($domain == 'default' && $text == 'Published') {
                $translations = __('Saved', 'mailoptin');
            }

            return $translations;
        }, 10, 3);
    }

    /**
     * @param \WP_Customize_Manager $wp_customize
     */
    public function register_custom_section($wp_customize)
    {
        $wp_customize->register_section_type('MailOptin\Core\Admin\Customizer\UpsellCustomizerSection');
    }

    /**
     * Registered customize control as eligible to be rendered via JS and created dynamically.
     *
     * @param \WP_Customize_Manager $wp_customize
     */
    public function register_control_type($wp_customize)
    {
        $controls = apply_filters('mo_optin_registered_control_types', [
            'MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Button_Set_Control'
        ]);

        foreach ($controls as $control) {
            $wp_customize->register_control_type($control);
        }

        do_action('mo_customizer_register_control_type', $wp_customize);
    }

    public function is_ninja_form_shortcode()
    {
        if (class_exists('\Ninja_Forms') && class_exists('\NF_Display_Render') && isset($_GET['mailoptin_optin_campaign_id'])) {
            $optin_campaign_id = absint($_GET['mailoptin_optin_campaign_id']);

            return \MailOptin\Core\is_ninja_form_shortcode($optin_campaign_id);
        }

        return false;
    }

    public function clean_up_customizer()
    {
        // this should never change from init to say admin_init in future because it will
        // cause wp_enqueue_scripts filter from taking effect cos its used in frontend.
        add_action('init', function () {

            remove_all_actions('admin_print_footer_scripts');

            // remove all custom media button added by plugins and core.
            remove_all_actions('media_buttons');
            remove_all_filters('media_buttons_context');
            remove_all_filters('mce_buttons', 10);
            remove_all_filters('mce_external_plugins', 10);
            remove_all_actions('after_wp_tiny_mce');

            remove_all_actions('wp_head');
            remove_all_actions('wp_print_styles');
            remove_all_actions('wp_print_head_scripts');
            remove_all_actions('wp_footer');

            // Handle `wp_head`
            add_action('wp_head', 'wp_enqueue_scripts', 1);
            add_action('wp_head', 'wp_print_styles', 8);
            add_action('wp_head', 'wp_print_head_scripts', 9);
            add_action('wp_head', 'wp_site_icon');

            // Handle `wp_footer`
            add_action('wp_footer', 'wp_print_footer_scripts', 20);

            // add core media button back.
            add_action('media_buttons', 'media_buttons');

            $is_switch_loader_method = Settings::instance()->switch_customizer_loader();

            if ($is_switch_loader_method == 'false' && ! $this->is_ninja_form_shortcode()) {

                $wp_get_theme = wp_get_theme();

                $active_plugins = array_reduce(get_option('active_plugins'), function ($carry, $item) {
                    $name = dirname($item);
                    if ($name != 'mailoptin' && $name != '.') {
                        $carry[] = $name;
                    }

                    return $carry;
                });

                $active_plugins = ! is_array($active_plugins) ? [] : $active_plugins;

                add_action('customize_controls_enqueue_scripts', function () use ($wp_get_theme, $active_plugins) {
                    global $wp_styles;
                    global $wp_scripts;

                    $child_theme  = $wp_get_theme->get_stylesheet();
                    $parent_theme = $wp_get_theme->get_template();

                    foreach ($wp_scripts->registered as $key => $value) {
                        $src = $value->src;
                        if (strpos($src, "themes/$child_theme/") !== false || strpos($src, "themes/$parent_theme/") !== false) {
                            unset($wp_scripts->registered[$key]);
                        }

                        if (strpos($src, "/uploads/$child_theme/") !== false || strpos($src, "/uploads/$parent_theme/") !== false) {
                            unset($wp_scripts->registered[$key]);
                        }

                        foreach ($active_plugins as $active_plugin) {
                            if (strpos($src, "plugins/$active_plugin/") !== false) {
                                unset($wp_scripts->registered[$key]);
                            }
                        }
                    }

                    foreach ($wp_styles->registered as $key => $value) {
                        $src = $value->src;
                        if (strpos($src, "themes/$child_theme/") !== false || strpos($src, "themes/$parent_theme/") !== false) {
                            unset($wp_styles->registered[$key]);
                        }

                        if (strpos($src, "/uploads/$child_theme/") !== false || strpos($src, "/uploads/$parent_theme/") !== false) {
                            unset($wp_styles->registered[$key]);
                        }

                        foreach ($active_plugins as $active_plugin) {
                            if (strpos($src, "plugins/$active_plugin/") !== false) {
                                unset($wp_styles->registered[$key]);
                            }
                        }
                    }

                }, 9999999999999);

                // was surprised a theme called Awaken used this action to enqueue styles.
                // do not change the priority from 20. that's where it seem to work.
                add_action('customize_controls_print_styles', function () use ($wp_get_theme, $active_plugins) {
                    global $wp_styles;

                    $child_theme  = $wp_get_theme->get_stylesheet();
                    $parent_theme = $wp_get_theme->get_template();

                    foreach ($wp_styles->registered as $key => $value) {
                        $src = $value->src;

                        if (strpos($src, "themes/$child_theme/") !== false || strpos($src, "themes/$parent_theme/") !== false) {
                            unset($wp_styles->registered[$key]);
                        }
                    }

                }, 20);

                remove_all_actions('wp_enqueue_scripts');
            }

            if (class_exists('Astra_Customizer') && method_exists('Astra_Customizer', 'print_footer_scripts')) {
                remove_action('customize_controls_print_footer_scripts', [\Astra_Customizer::get_instance(), 'print_footer_scripts']);
            }

            // flatbase theme compat
            add_filter('nice_scripts', '__return_false');

            if (function_exists('td_customize_js')) {
                remove_action('customize_controls_print_footer_scripts', 'td_customize_js');
            }

            // compatibility with easy google font plugin
            if (class_exists('EGF_Customize_Manager')) {
                remove_action('customize_controls_enqueue_scripts', [\EGF_Customize_Manager::get_instance(), 'easy-google-fonts-customize-controls-js']);
                remove_action('customize_register', [\EGF_Customize_Manager::get_instance(), 'register_font_control_type']);
            }

            add_action('customize_controls_enqueue_scripts', array($this, 'mo_customizer_js'));

        }, 9999999999999);

        add_action('customize_controls_print_footer_scripts', [$this, 'js_script']);
        add_action('customize_controls_print_footer_scripts', [$this, 'js_wp_editor']);
    }

    public function mo_customizer_js()
    {
        wp_enqueue_script(
            'mailoptin-rename-customizer-title',
            MAILOPTIN_ASSETS_URL . 'js/customizer-controls/rename-customizer-title.js',
            array('customize-controls'),
            MAILOPTIN_VERSION_NUMBER
        );

        wp_enqueue_script(
            'mailoptin-wp-editor',
            MAILOPTIN_ASSETS_URL . 'js/customizer-controls/mo-wp-editor.js',
            array('customize-controls'),
            MAILOPTIN_VERSION_NUMBER
        );

        wp_localize_script('mailoptin-wp-editor', 'moWPEditor_globals', array(
            'url'                      => get_home_url(),
            'includes_url'             => includes_url(),
            'wpeditor_texttab_label'   => __('Text', 'mailoptin'),
            'wpeditor_visualtab_label' => __('Visual', 'mailoptin'),
            'wpeditor_addmedia_label'  => __('Add Media', 'mailoptin')
        ));
    }

    /**
     * Change the label from "Email Provider List" to "ConvertKit Form" on convertkit selected as email provider.
     */
    public function js_script()
    {
        $ck_label      = __('ConvertKit Forms', 'mailoptin');
        $drip_label    = __('Drip Campaigns', 'mailoptin');
        $gr_label      = __('GetResponse Campaigns', 'mailoptin');
        $zohocrm_label = __('ZohoCRM Modules', 'mailoptin');
        $fbca_label    = __('Custom Audience', 'mailoptin');
        $default_label = __('Select Email List', 'mailoptin');
        ?>
        <script type="text/javascript">
            (function ($) {
                    function logic(connection_service) {
                        if (connection_service === undefined) {
                            connection_service = $("select[data-customize-setting-link*='connection_service']").val();
                        }

                        var title_obj = $('li[id*="connection_email_list"] .customize-control-title');

                        if (connection_service === 'GetResponseConnect') {
                            title_obj.text('<?php echo $gr_label; ?>');
                        }

                        if (connection_service === 'ConvertKitConnect') {
                            title_obj.text('<?php echo $ck_label; ?>');
                        }

                        if (connection_service === 'DripConnect') {
                            title_obj.text('<?php echo $drip_label; ?>');
                        }

                        if (connection_service === 'ZohoCRMConnect') {
                            title_obj.text('<?php echo $zohocrm_label; ?>');
                        }

                        if (connection_service === 'FacebookCustomAudienceConnect') {
                            title_obj.text('<?php echo $fbca_label; ?>');
                        }
                    }

                    function logic_new(connection_service, parent) {
                        if (connection_service === undefined) {
                            $('.mo-integration-widget').each(function () {
                                var parent = $(this);
                                connection_service = $("select[name='connection_service']", parent).val();

                                var title_obj = $(".connection_email_list label.customize-control-title", parent);

                                if (connection_service === 'GetResponseConnect') {
                                    title_obj.text('<?php echo $gr_label; ?>');
                                }

                                if (connection_service === 'ConvertKitConnect') {
                                    title_obj.text('<?php echo $ck_label; ?>');
                                }

                                if (connection_service === 'DripConnect') {
                                    title_obj.text('<?php echo $drip_label; ?>');
                                }

                                if (connection_service === 'ZohoCRMConnect') {
                                    title_obj.text('<?php echo $zohocrm_label; ?>');
                                }

                                if (connection_service === 'FacebookCustomAudienceConnect') {
                                    title_obj.text('<?php echo $fbca_label; ?>');
                                }
                            });
                        } else {

                            connection_service = $("select[name='connection_service']", parent).val();

                            var title_obj = $(".connection_email_list label.customize-control-title", parent);

                            if (connection_service === 'GetResponseConnect') {
                                title_obj.text('<?php echo $gr_label; ?>');
                            }

                            if (connection_service === 'ConvertKitConnect') {
                                title_obj.text('<?php echo $ck_label; ?>');
                            }

                            if (connection_service === 'DripConnect') {
                                title_obj.text('<?php echo $drip_label; ?>');
                            }

                            if (connection_service === 'ZohoCRMConnect') {
                                title_obj.text('<?php echo $zohocrm_label; ?>');
                            }

                            if (connection_service === 'FacebookCustomAudienceConnect') {
                                title_obj.text('<?php echo $fbca_label; ?>');
                            }
                        }
                    }

                    // on ready event
                    $(window).load(function () {
                        logic();
                        logic_new();
                        $(document.body).on('mo_email_list_data_found', function (e, connection_service) {
                            // restore default label before change
                            $('li[id*="connection_email_list"] .customize-control-title').text('<?php echo $default_label; ?>');
                            logic(connection_service);
                        });

                        $(document.body).on('mo_new_email_list_data_found', function (e, connection_service, parent) {
                            $(".connection_email_list label.customize-control-title", parent).text('<?php echo $default_label; ?>');
                            logic_new(connection_service, parent);
                        });
                    })
                }

            )(jQuery);
        </script>
        <?php
    }

    public function js_wp_editor()
    {
        // Enable rich editing for this view (Overrides 'Disable the visual editor when writing' option for current user)
        add_filter('user_can_richedit', '__return_true');
        wp_enqueue_editor();
        wp_enqueue_editor();

        if ( ! empty($GLOBALS['post'])) {
            wp_enqueue_media(array('post' => $GLOBALS['post']->ID));
        } else {
            wp_enqueue_media();
        }
    }
}