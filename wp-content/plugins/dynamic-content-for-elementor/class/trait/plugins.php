<?php

namespace DynamicContentForElementor;

trait Plugins
{
    protected static $plugin_dependency_names = ['acf' => 'Advanced Custom Fields', 'advanced-custom-fields-pro' => 'Advanced Custom Fields Pro', 'elementor-pro' => 'Elementor Pro', 'jet-engine' => 'JetEngine', 'metabox' => 'Meta Box', 'pods' => 'Pods', 'search-filter-pro' => 'Search & Filter Pro', 'timber' => 'Timber', 'types' => 'Toolset', 'woocommerce' => 'WooCommerce'];
    public static $checked_plugins = [];
    public static function get_plugin_dependency_names($plugin)
    {
        if (isset(self::$plugin_dependency_names[$plugin])) {
            return self::$plugin_dependency_names[$plugin];
        }
        return $plugin;
    }
    public static function is_plugin_active($plugin)
    {
        if (isset(self::$checked_plugins[$plugin])) {
            return self::$checked_plugins[$plugin];
        }
        if ($plugin === 'elementor-pro') {
            $is_active = self::is_elementorpro_active();
        } else {
            $is_active = self::is_acf_pro($plugin) || self::is_plugin_must_use($plugin) || self::is_plugin_active_for_local($plugin) || self::is_plugin_active_for_network($plugin);
        }
        self::$checked_plugins[$plugin] = $is_active;
        return $is_active;
    }
    public static function is_acf_pro($plugin)
    {
        if ($plugin == 'acf') {
            if (\defined('ACF')) {
                return ACF;
            }
        }
        if ($plugin == 'advanced-custom-fields-pro') {
            if (\defined('ACF_PRO')) {
                return ACF_PRO;
            }
        }
        return \false;
    }
    public static function is_plugin_must_use($plugin)
    {
        $mu_plugins = wp_get_mu_plugins();
        // Must Use
        if (\is_dir(WPMU_PLUGIN_DIR)) {
            $mu_dir_plugins = \glob(WPMU_PLUGIN_DIR . '/*/*.php');
            // Must Use
            if (!empty($mu_dir_plugins)) {
                foreach ($mu_dir_plugins as $aplugin) {
                    $mu_plugins[] = $aplugin;
                }
            }
        }
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        if (!empty($mu_plugins)) {
            foreach ($mu_plugins as $aplugin) {
                $plugin_data = get_plugin_data($aplugin);
                if (!empty($plugin_data['Name']) && $plugin_data['Name'] == 'Advanced Custom Fields PRO') {
                    $mu_plugins[] = \str_replace('acf.php', 'advanced-custom-fields-pro.php', $aplugin);
                    break;
                }
            }
        }
        return self::check_plugin($plugin, $mu_plugins);
    }
    public static function is_plugin_active_for_local($plugin)
    {
        $active_plugins = get_option('active_plugins', array());
        return self::check_plugin($plugin, $active_plugins);
    }
    public static function is_plugin_active_for_network($plugin)
    {
        $active_plugins = get_site_option('active_sitewide_plugins');
        if (!empty($active_plugins)) {
            $active_plugins = \array_keys($active_plugins);
            return self::check_plugin($plugin, $active_plugins);
        }
        return \false;
    }
    public static function check_plugin($plugin, $active_plugins = array())
    {
        if (\in_array($plugin, (array) $active_plugins)) {
            return \true;
        }
        if (!empty($active_plugins)) {
            foreach ($active_plugins as $aplugin) {
                $tmp = \basename($aplugin);
                $tmp = \pathinfo($tmp, \PATHINFO_FILENAME);
                if ($plugin == $tmp) {
                    return \true;
                }
            }
        }
        if (!empty($active_plugins)) {
            foreach ($active_plugins as $aplugin) {
                $pezzi = \explode('/', $aplugin);
                $tmp = \reset($pezzi);
                if ($plugin == $tmp) {
                    return \true;
                }
            }
        }
        return \false;
    }
    public static function is_woocommerce_active()
    {
        if (\class_exists('woocommerce')) {
            return \true;
        }
        return \false;
    }
    public static function is_memberpress_active()
    {
        if (\defined('MEPR_PLUGIN_NAME')) {
            return \true;
        }
        return \false;
    }
    public static function is_myfastapp_active()
    {
        if (\defined('TOA_MYFASTAPP_VERSION')) {
            return \true;
        }
        return \false;
    }
    /**
     * Check if Geolocation IP Detection is active
     * https://wordpress.org/plugins/geoip-detect/
     *
     * @return boolean
     */
    public static function is_geoipdetect_active()
    {
        return \DynamicContentForElementor\Helper::is_plugin_active('geoip-detect') && \function_exists('geoip_detect2_get_info_from_current_ip');
    }
    /**
     * Check if WPML is active
     *
     * @return boolean
     */
    public static function is_wpml_active()
    {
        if (\class_exists('SitePress')) {
            return \true;
        }
        return \false;
    }
    public static function is_acf_active()
    {
        if (\class_exists('ACF') && \defined('ACF')) {
            return \true;
        }
        return \false;
    }
    public static function is_acfpro_active()
    {
        if (\class_exists('ACF') && \defined('ACF_PRO')) {
            return \true;
        }
        return \false;
    }
    /**
     * Check if Jet Engine is active
     *
     * @return boolean
     */
    public static function is_jetengine_active()
    {
        if (\class_exists('Jet_Engine')) {
            return \true;
        }
        return \false;
    }
    /**
     * Check if Meta Box is active
     *
     * @return boolean
     */
    public static function is_metabox_active()
    {
        if (\class_exists('RWMB_Core')) {
            return \true;
        }
        return \false;
    }
    public static function is_pods_active()
    {
        if (self::is_plugin_active('pods')) {
            return \true;
        }
        return \false;
    }
    public static function is_searchandfilterpro_active()
    {
        if (\defined('SEARCH_FILTER_PRO_BASE_PATH')) {
            return \true;
        }
        return \false;
    }
    public static function is_elementorpro_active()
    {
        if (\class_exists('ElementorPro\\Plugin')) {
            return \true;
        }
        return \false;
    }
    public static function is_polylang_active()
    {
        if (\class_exists('Polylang') && \function_exists('pll_languages_list')) {
            return \true;
        }
        return \false;
    }
    public static function check_plugin_dependencies($response = \false, $dependencies = [])
    {
        $plugin_disabled = [];
        if (!empty($dependencies)) {
            $is_active = \true;
            foreach ($dependencies as $key => $plugin) {
                if (!\is_numeric($key)) {
                    if (!\DynamicContentForElementor\Helper::is_plugin_active($key)) {
                        $is_active = \false;
                    }
                } else {
                    if (!\DynamicContentForElementor\Helper::is_plugin_active($plugin)) {
                        $is_active = \false;
                    }
                }
                if (!$is_active) {
                    if (!$response) {
                        return \false;
                    }
                    if (\is_numeric($key)) {
                        $plugin_disabled[] = self::get_plugin_dependency_names($plugin);
                    } else {
                        $plugin_disabled[] = $key;
                    }
                }
            }
        }
        if ($response) {
            return $plugin_disabled;
        }
        return \true;
    }
}
