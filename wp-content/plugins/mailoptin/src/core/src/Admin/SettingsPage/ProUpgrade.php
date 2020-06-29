<?php

namespace MailOptin\Core\Admin\SettingsPage;

class ProUpgrade
{
    public function __construct()
    {
        $basename = plugin_basename(MAILOPTIN_SYSTEM_FILE_PATH);
        $prefix = is_network_admin() ? 'network_admin_' : '';
        add_filter("{$prefix}plugin_action_links_$basename", [$this, 'mo_action_links'], 10, 4);
        add_filter('plugin_row_meta', array(__CLASS__, 'plugin_row_meta'), 10, 2);

        add_filter('admin_footer_text', [$this, 'admin_page_rate_us']);

    }

    /**
     * Add rating links to the admin dashboard
     *
     * @param       string $footer_text The existing footer text
     * @return      string
     */
    public function admin_page_rate_us($footer_text)
    {
        if (\MailOptin\Core\is_mailoptin_admin_page()) {
            $rate_text = sprintf(__('Thank you for using <a href="%1$s" target="_blank">MailOptin</a>! Please <a href="%2$s" target="_blank">rate us</a> on <a href="%2$s" target="_blank">WordPress.org</a>', 'mailoptin'),
                'https://mailoptin.io',
                'https://wordpress.org/support/view/plugin-reviews/mailoptin?filter=5#postform',
                'https://mailoptin.io/pricing/'
            );

            return str_replace('</span>', '', $footer_text) . ' | ' . $rate_text . '</span>';
        } else {
            return $footer_text;
        }
    }

    /**
     * Show row meta on the plugin screen.
     *
     * @param    mixed $links Plugin Row Meta
     * @param    mixed $file Plugin Base file
     * @return    array
     */
    public static function plugin_row_meta($links, $file)
    {
        if (strpos($file, 'mailoptin.php') !== false) {
            $row_meta = array(
                'docs' => '<a target="_blank" href="' . esc_url('https://mailoptin.io/docs/') . '" aria-label="' . esc_attr__('View MailOptin documentation', 'mailoptin') . '">' . esc_html__('Docs', 'mailoptin') . '</a>',
                'support' => '<a target="_blank" href="' . esc_url('https://mailoptin.io/support/') . '" aria-label="' . esc_attr__('Visit customer support', 'mailoptin') . '">' . esc_html__('Support', 'mailoptin') . '</a>',
            );

            if (!defined('MAILOPTIN_DETACH_LIBSODIUM')) {
                $url = 'https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=row_meta';
                $row_meta['upgrade_pro'] = '<a target="_blank" style="color:#d54e21;font-weight:bold" href="' . esc_url($url) . '" aria-label="' . esc_attr__('Upgrade to PRO', 'mailoptin') . '">' . esc_html__('Go Premium', 'mailoptin') . '</a>';
            }

            return array_merge($links, $row_meta);
        }

        return (array)$links;
    }

    /**
     * Action links in plugin listing page.
     */
    public function mo_action_links($actions, $plugin_file, $plugin_data, $context)
    {
        $custom_actions = array(
            'mo_settings' => sprintf('<a href="%s">%s</a>', MAILOPTIN_SETTINGS_SETTINGS_PAGE, __('Settings', 'mailoptin')),
        );

        if (!defined('MAILOPTIN_DETACH_LIBSODIUM')) {
            $custom_actions['mo_upgrade'] = sprintf(
                '<a style="color:#d54e21;font-weight:bold" href="%s" target="_blank">%s</a>', 'https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=action_link',
                __('Go Premium', 'mailoptin')
            );
        }

        // add the links to the front of the actions list
        return array_merge($custom_actions, $actions);
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