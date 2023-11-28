<?php

namespace Essential_Addons_Elementor\Pro\Traits;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

use \Essential_Addons_Elementor\Pro\Classes\License\EAELicense;

trait Core
{
    /**
     * Save default values on first install
     *
     * @since v3.0.0
     */
    public function set_default_values()
    {
        $defaults = array_fill_keys([
            'img-comparison',
            'instagram-gallery',
            'interactive-promo',
            'lightbox',
            'post-block',
            'testimonial-slider',
            'static-product',
            'adv-google-map',
            'flip-carousel',
            'interactive-cards',
            'content-timeline',
            'twitter-feed-carousel',
            'dynamic-filter-gallery',
            'post-list',
            'toggle',
            'mailchimp',
            'divider',
            'price-menu',
            'image-hotspots',
            'one-page-navigation',
            'counter',
            'post-carousel',
            'team-member-carousel',
            'logo-carousel',
            'protected-content',
            'offcanvas',
            'advanced-menu',
            'image-scroller',
            'learn-dash-elements',
            'woo-collections',
            'dismissible-section',
            'section-parallax',
            'section-particles',
            'eael-tooltip-section',
        ], 1);

        $values = get_option('eael_save_settings');

        return update_option('eael_save_settings', wp_parse_args($values, $defaults));
    }

    /**
     * Make lite version available in Pro
     *
     * @since 3.0.0
     */
    public function make_lite_available()
    {
        $basename    = 'essential-addons-for-elementor-lite/essential_adons_elementor.php';
        $plugin_data = $this->get_plugin_data('essential-addons-for-elementor-lite');

        if ($this->is_plugin_installed($basename)) {
            // upgrade plugin - attempt for once
            if (isset($plugin_data->version) && $this->get_plugin_version($basename) != $plugin_data->version) {
                $this->upgrade_plugin($basename);
            }

            // activate plugin
            if (is_plugin_active($basename)) {
                return delete_transient('eael_install_lite');
            } else {
                activate_plugin($this->safe_path(WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $basename), '', false, false);
                return delete_transient('eael_install_lite');
            }
        } else {
            // install & activate plugin
            if (isset($plugin_data->download_link)) {
                if ($this->install_plugin($plugin_data->download_link)) {
                    return delete_transient('eael_install_lite');
                }
            }
        }

        return false;
    }

    /**
     * Creates an action menu
     *
     * @since 3.0.0
     */
    public function insert_plugin_links($links)
    {
        // settings
        $links[] = sprintf('<a href="admin.php?page=eael-settings">' . __('Settings') . '</a>');

        return $links;
    }

    /**
     * Plugin Licensing
     *
     * @since v1.0.0
     */
    public function plugin_licensing()
    {
        if (is_admin()) {
            // Setup the settings page and validation
            new EAELicense(
                EAEL_SL_ITEM_SLUG,
                EAEL_SL_ITEM_NAME,
                'essential-addons-elementor'
            );
        }
    }
}
