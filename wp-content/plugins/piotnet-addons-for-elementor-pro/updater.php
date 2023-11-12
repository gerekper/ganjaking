<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class PAFE_Updater
{
    // piotnet-addons-for-elementor-pro/piotnet-addons-for-elementor-pro.php
    private $plugin;

    // piotnet-addons-for-elementor-pro
    private $slug;

    private $version;

    public function __construct($plugin, $version)
    {
        $this->plugin = $plugin;

        list ($t1, $t2) = explode('/', $plugin);
        $this->slug = str_replace('.php', '', $t2);

        $this->version = $version;

        $this->maybe_delete_transients();
        $this->setup_hooks();
    }

    private function setup_hooks()
    {
        add_filter('site_transient_update_plugins', array($this, 'check_update'));
        add_filter('pre_set_site_transient_update_plugins', [$this, 'check_update']);

        // Define the alternative response for information checking
        add_filter('plugins_api', [$this, 'check_info'], 10, 3);

        remove_action('after_plugin_row_' . $this->plugin, 'wp_plugin_update_row');
        add_action('after_plugin_row_' . $this->plugin, [$this, 'show_update_notification'], 10, 2);

        add_action('update_option_WPLANG', function () {
            $this->clean_get_info_cache();
        });

        add_action('upgrader_process_complete', function () {
            $this->clean_get_info_cache();
        });
    }

    private function maybe_delete_transients()
    {
        global $pagenow;

        if ('update-core.php' === $pagenow && isset($_GET['force-check'])) {
            $this->clean_get_info_cache();
        }
    }

    public function check_update($_transient_data)
    {
        global $pagenow;

        if (!is_object($_transient_data)) {
            $_transient_data = new \stdClass();
        }

        if ('plugins.php' === $pagenow && is_multisite()) {
            return $_transient_data;
        }

        return $this->check_transient_data($_transient_data);
    }

    public function check_info($obj, $action, $arg)
    {
        if ('plugin_information' !== $action) {
            return $obj;
        }

        // do nothing if it is not our plugin
        if ( !isset($arg->slug) || $this->slug !== $arg->slug) {
            return $obj;
        }

        $transient = PAFE_License_Service::get_info_cache();
        if ( !empty( $transient ) ) {
            return $transient;
        }

        $remote = $this->get_version(true, 'check_info');

        if ( is_wp_error( $remote ) ) {
            return $obj;
        }

        $remote->sections = json_decode(json_encode($remote->sections), true);
        $remote->banners = json_decode(json_encode($remote->banners), true);
        $remote->icons = json_decode(json_encode($remote->icons), true);

        PAFE_License_Service::set_info_cache( $remote, 3 * MINUTE_IN_SECONDS );
        return $remote;
    }

    public function show_update_notification($file, $plugin)
    {
        if (is_network_admin()) {
            return;
        }

        if (!current_user_can('update_plugins')) {
            return;
        }

        if ($this->plugin !== $file) {
            return;
        }

        // Remove our filter on the site transient
        remove_filter('pre_set_site_transient_update_plugins', [$this, 'check_update']);

        $update_cache = get_site_transient('update_plugins');
        $update_cache = $this->check_transient_data($update_cache);
        set_site_transient('update_plugins', $update_cache);

        // Restore our filter
        add_filter('pre_set_site_transient_update_plugins', [$this, 'check_update']);
    }

    private function check_transient_data($_transient_data)
    {
        if (!is_object($_transient_data)) {
            $_transient_data = new \stdClass();
        }

        $has_response = !empty($_transient_data->response) && !empty($_transient_data->response[$this->plugin]);
        $has_noupdate = !empty($_transient_data->no_update) && !empty($_transient_data->no_update[$this->plugin]);
        $has_checked = !empty($_transient_data->checked) && !empty($_transient_data->checked[$this->plugin]) && $_transient_data->checked[$this->plugin] == $this->version;

        if ($has_checked && ($has_response || $has_noupdate)) {
            return $_transient_data;
        }

        $info = $this->get_version(false, 'check_update');

        if (is_wp_error($info)) {
            return $_transient_data;
        }

        // include an unmodified $wp_version
        include(ABSPATH . WPINC . '/version.php');

        if (isset($info->requires) && version_compare($wp_version, $info->requires, '<')) {
            return $_transient_data;
        }

        $obj = new stdClass();
        $obj->slug = $this->slug;
        $obj->plugin = $this->plugin;
        $obj->new_version = $info->version;
        $obj->url = $info->url;
        $obj->package = $info->download_url;

        $obj->icons = array(
            '1x' => $info->icons->oneX,
            '2x' => $info->icons->twoX,
            'svg' => $info->icons->svg,
        );
        $obj->banners = array(
            '1x' => $info->banners->low,
            '2x' => $info->banners->high
        );
        $obj->banners_rtl = [];

        $obj->requires = $info->requires;
        $obj->tested = $info->tested;
        $obj->requires_php = $info->requires_php;

        if (isset($info->upgrade_notice) && !empty($info->upgrade_notice)) {
            $obj->upgrade_notice = $info->upgrade_notice;
        }

        if (isset($info->version) && version_compare($this->version, $info->version, '<')) {
            $_transient_data->response[$this->plugin] = $obj;
            if (isset($_transient_data->no_update[$this->plugin])) {
                unset($_transient_data->no_update[$this->plugin]);
            }
        } else {
            if (isset($_transient_data->response[$this->plugin])) {
                unset($_transient_data->response[$this->plugin]);
            }
            if (!isset($_transient_data->no_update[$this->plugin])) {
                $_transient_data->no_update[$this->plugin] = $obj;
            }
        }

        $_transient_data->last_checked = current_time('timestamp');
        $_transient_data->checked[$this->plugin] = $this->version;

        return $_transient_data;
    }

    public function get_version($force_update, $action)
    {
        $info_data = PAFE_License_Service::get_version_cache();

        if ($force_update || empty( $info_data ) ) {
            $res = PAFE_License_Service::get_plugin_info($action);

            if (!is_wp_error($res) && isset($res->data) && isset($res->data->plugin)) {
                $info_data = $res->data->plugin;
                PAFE_License_Service::set_version_cache( $info_data, 6 * HOUR_IN_SECONDS  );
            }
        }

        return $info_data;
    }

    public function clean_get_info_cache()
    {
        PAFE_License_Service::clean_get_info_cache();
    }
}
