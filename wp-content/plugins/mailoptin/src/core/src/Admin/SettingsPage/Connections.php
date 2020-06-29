<?php

namespace MailOptin\Core\Admin\SettingsPage;

// Exit if accessed directly
use MailOptin\Core\Connections\AbstractConnect;
use W3Guy\Custom_Settings_Page_Api;

if ( ! defined('ABSPATH')) {
    exit;
}


class Connections extends AbstractSettingsPage
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'register_settings_page'));

        add_action('mailoptin_admin_notices', function () {
            add_action('admin_notices', array($this, 'admin_notices'));
        });

        add_filter('removable_query_args', array($this, 'removable_query_args'));

        add_action('wp_cspa_after_persist_settings', [$this, 'bust_all_connection_cache'], 10, 2);
    }

    /**
     * Delete or burst all connection cache when connection settings is (re-) saved.
     *
     * @param array $sanitized_data
     * @param string $option_name
     */
    public function bust_all_connection_cache($sanitized_data, $option_name)
    {
        if ($option_name === MAILOPTIN_CONNECTIONS_DB_OPTION_NAME) {
            global $wpdb;
            $table = $wpdb->prefix . 'options';

            $wpdb->query("DELETE FROM $table where option_name LIKE '%_mo_connection_cache_%'");
        }
    }

    public function register_settings_page()
    {

        add_submenu_page(
            MAILOPTIN_SETTINGS_SETTINGS_SLUG,
            __('Integrations - MailOptin', 'mailoptin'),
            __('Integrations', 'mailoptin'),
            \MailOptin\Core\get_capability(),
            MAILOPTIN_CONNECTIONS_SETTINGS_SLUG,
            array($this, 'settings_admin_page_callback')
        );
    }

    public function filter_sub_menu()
    {
        $emailmarketing_url = add_query_arg('connect-type', AbstractConnect::EMAIL_MARKETING_TYPE, MAILOPTIN_CONNECTIONS_SETTINGS_PAGE);
        $social_url         = add_query_arg('connect-type', AbstractConnect::SOCIAL_TYPE, MAILOPTIN_CONNECTIONS_SETTINGS_PAGE);
        $analytics_url      = add_query_arg('connect-type', AbstractConnect::ANALYTICS_TYPE, MAILOPTIN_CONNECTIONS_SETTINGS_PAGE);
        $crm_url            = add_query_arg('connect-type', AbstractConnect::CRM_TYPE, MAILOPTIN_CONNECTIONS_SETTINGS_PAGE);
        $other_url          = add_query_arg('connect-type', AbstractConnect::OTHER_TYPE, MAILOPTIN_CONNECTIONS_SETTINGS_PAGE);

        $all_menu_active            = isset($_GET['page']) && ! isset($_GET['connect-type']) ? 'mailoptin-type-active' : null;
        $emailmarketing_menu_active = isset($_GET['connect-type']) && $_GET['page'] == MAILOPTIN_CONNECTIONS_SETTINGS_SLUG && $_GET['connect-type'] == 'emailmarketing' ? 'mailoptin-type-active' : null;
        $social_menu_active         = isset($_GET['connect-type']) && $_GET['page'] == MAILOPTIN_CONNECTIONS_SETTINGS_SLUG && $_GET['connect-type'] == 'social' ? 'mailoptin-type-active' : null;
        $crm_menu_active            = isset($_GET['connect-type']) && $_GET['page'] == MAILOPTIN_CONNECTIONS_SETTINGS_SLUG && $_GET['connect-type'] == 'crm' ? 'mailoptin-type-active' : null;
        $other_menu_active          = isset($_GET['connect-type']) && $_GET['page'] == MAILOPTIN_CONNECTIONS_SETTINGS_SLUG && $_GET['connect-type'] == 'other' ? 'mailoptin-type-active' : null;
        $analytics_menu_active      = isset($_GET['connect-type']) && $_GET['page'] == MAILOPTIN_CONNECTIONS_SETTINGS_SLUG && $_GET['connect-type'] == 'analytics' ? 'mailoptin-type-active' : null;
        ?>
        <div id="mailoptin-sub-bar">
            <div class="mailoptin-new-toolbar mailoptin-clear" style="border-top: 0;margin-bottom:0">
                <h4><?php _e('Filter By:', 'mailoptin'); ?></h4>
                <ul class="mailoptin-design-options">
                    <li>
                        <a href="<?php echo MAILOPTIN_CONNECTIONS_SETTINGS_PAGE; ?>" class="<?php echo $all_menu_active; ?>">
                            <?php _e('All', 'mailoptin'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $emailmarketing_url; ?>" class="<?php echo $emailmarketing_menu_active; ?>">
                            <?php _e('Email Marketing', 'mailoptin'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $social_url; ?>" class="<?php echo $social_menu_active; ?>">
                            <?php _e('Social', 'mailoptin'); ?>
                        </a>
                    </li>
                    <li>
                    <li>
                        <a href="<?php echo $analytics_url; ?>" class="<?php echo $analytics_menu_active; ?>">
                            <?php _e('Analytics', 'mailoptin'); ?>
                        </a></li>
                    <li>
                        <a href="<?php echo $crm_url; ?>" class="<?php echo $crm_menu_active; ?>">
                            <?php _e('CRM', 'mailoptin'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $other_url; ?>" class="<?php echo $other_menu_active; ?>">
                            <?php _e('Other', 'mailoptin'); ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <?php
    }

    public function settings_admin_page_callback()
    {
        do_action('mailoptin_before_connections_settings_page', MAILOPTIN_CONNECTIONS_DB_OPTION_NAME);
        $connection_args = apply_filters('mailoptin_connections_settings_page', array());
        usort($connection_args, function ($a, $b) {
            return strcmp($a["section_title"], $b["section_title"]);
        });

        $nav_tabs         = '';
        $tab_content_area = '';
        if ( ! empty($connection_args)) {
            $instance = Custom_Settings_Page_Api::instance([], MAILOPTIN_CONNECTIONS_DB_OPTION_NAME, __('Integrations', 'mailoptin'));
            foreach ($connection_args as $key => $connection_arg) {
                $type = isset($connection_arg['type']) ? $connection_arg['type'] : '';
                if (isset($_GET['connect-type']) && $type != $_GET['connect-type']) {
                    unset($connection_args[$key]);
                    continue;
                }

                unset($connection_arg['type']);

                $section_title = $connection_arg['section_title'];
                // remove "Connection" + connected status from section title
                $section_title_without_status = isset($connection_arg['section_title_without_status']) ? $connection_arg['section_title_without_status'] : preg_replace('/[\s]?Connection.+<\/span>/', '', $connection_arg['section_title']);
                unset($connection_arg['section_title']);
                unset($connection_arg['section_title_without_status']);
                $key = key($connection_arg);
                // re-add section title after we've gotten key.
                $connection_arg['section_title'] = $section_title;
                $nav_tabs                        .= sprintf('<a href="#%1$s" class="nav-tab" id="%1$s-tab"><span class="dashicons dashicons-admin-settings"></span> %2$s</a>', $key, $section_title_without_status);
                $tab_content_area                .= sprintf('<div id="%s" class="mailoptin-group-wrapper">', $key);
                $tab_content_area                .= $instance->metax_box_instance($connection_arg);
                $tab_content_area                .= '</div>';
            }

            $instance->persist_plugin_settings();
            $this->register_core_settings($instance);
            $instance->do_settings_errors();
            settings_errors('wp_csa_notice');
            echo '<div class="wrap">';
            $instance->settings_page_heading();
            $this->filter_sub_menu();

            if ( ! empty($connection_args)) {
                echo '<div class="mailoptin-settings-wrap" data-option-name="' . MAILOPTIN_CONNECTIONS_DB_OPTION_NAME . '">';
                echo '<h2 class="nav-tab-wrapper">' . $nav_tabs . '</h2>';
                echo '<div class="metabox-holder mailoptin-tab-settings">';
                echo '<form method="post">';
                $instance->nonce_field();
                echo $tab_content_area;
                echo '</form>';
                echo '</div>';
                echo '</div>';
                echo '</div>';

                do_action('mailoptin_after_connections_settings_page', MAILOPTIN_CONNECTIONS_DB_OPTION_NAME);
            }
        }
    }

    public function admin_notices()
    {
        // handle oauth errors.
        if (isset($_GET['mo-oauth-provider'], $_GET['mo-oauth-error'])) {
            $provider      = ucfirst(sanitize_text_field($_GET['mo-oauth-provider']));
            $error_message = strtolower(sanitize_text_field($_GET['mo-oauth-error']));

            echo '<div id="message" class="updated notice is-dismissible">';
            echo '<p>';
            echo "$provider $error_message";
            echo '</p>';
            echo '</div>';
        }
    }

    public function removable_query_args($args)
    {
        $args[] = 'mo-oauth-provider';
        $args[] = 'mo-oauth-error';
        $args[] = 'code';

        return $args;
    }

    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}