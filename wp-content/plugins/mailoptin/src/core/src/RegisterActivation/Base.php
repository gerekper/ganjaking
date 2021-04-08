<?php

namespace MailOptin\Core\RegisterActivation;

class Base
{
    public static function run_install($networkwide)
    {
        if (is_multisite() && $networkwide) {

            $site_ids = get_sites(['fields' => 'ids', 'number' => 0]);

            foreach ($site_ids as $site_id) {
                switch_to_blog($site_id);
                self::mo_install();
                restore_current_blog();
            }
        } else {
            self::mo_install();
        }
    }

    /**
     * Run plugin install / activation action when new blog is created in multisite setup.
     *
     * @param int $blog_id
     */
    public static function multisite_new_blog_install($blog_id)
    {
        if ( ! function_exists('is_plugin_active_for_network')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        if (is_plugin_active_for_network('mailoptin/mailoptin.php')) {
            switch_to_blog($blog_id);
            self::mo_install();
            restore_current_blog();
        }
    }

    /**
     * Perform plugin activation / installation.
     */
    public static function mo_install()
    {
        if ( ! current_user_can('activate_plugins') || get_option('mo_plugin_activated') == 'true') {
            return;
        }

        CreateDBTables::make();
        self::setting_settings();

        add_option('mo_install_date', current_time('mysql'));
        add_option('mo_plugin_activated', 'true');
    }

    /**
     * Default values for settings
     */
    public static function setting_settings()
    {
        add_option(MAILOPTIN_SETTINGS_DB_OPTION_NAME, array(
                'from_name'  => get_bloginfo(),
                'from_email' => get_bloginfo('admin_email'),
                'reply_to'   => get_bloginfo('admin_email')
            )
        );
    }
}