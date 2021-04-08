<?php

namespace MailOptin\Core;

use MailOptin\AdvanceAnalytics\AdvanceAnalytics;
use MailOptin\Core\Repositories\OptinCampaignsRepository;

class DBUpdates
{
    public static $instance;

    const DB_VER = 11;

    public function init_options()
    {
        add_option('mo_db_ver', 0);
    }

    public function maybe_update()
    {
        $this->init_options();

        if (get_option('mo_db_ver') >= self::DB_VER) {
            return;
        }

        // update plugin
        $this->update();
    }

    public function update()
    {
        // no PHP timeout for running updates
        set_time_limit(0);

        // this is the current database schema version number
        $current_db_ver = get_option('mo_db_ver');

        // this is the target version that we need to reach
        $target_db_ver = self::DB_VER;

        // run update routines one by one until the current version number
        // reaches the target version number
        while ($current_db_ver < $target_db_ver) {
            // increment the current db_ver by one
            $current_db_ver++;

            // each db version will require a separate update function
            $update_method = "update_routine_{$current_db_ver}";

            if (method_exists($this, $update_method)) {
                call_user_func(array($this, $update_method));
            }

            // update the option in the database, so that this process can always
            // pick up where it left off
            update_option('mo_db_ver', $current_db_ver);
        }
    }

    public function update_routine_1()
    {
        global $wpdb;

        $table = $wpdb->prefix . Core::conversions_table_name;

        $sql = "ALTER TABLE $table CHANGE date_added date_added DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00';";

        $wpdb->query($sql);
    }

    public function update_routine_2()
    {
        OptinCampaignsRepository::burst_all_cache();
    }

    public function update_routine_3()
    {
        $db_options                       = get_option(MAILOPTIN_CONNECTIONS_DB_OPTION_NAME);
        $db_options['elementor_activate'] = 'true';
        update_option(MAILOPTIN_CONNECTIONS_DB_OPTION_NAME, $db_options);
    }

    public function update_routine_4()
    {
        $all_optin_settings = OptinCampaignsRepository::get_settings();

        // UPGRADE routing for Lite version where all previous created optin are set to show globally.
        if ( ! defined('MAILOPTIN_DETACH_LIBSODIUM')) {
            $all_optin_settings = OptinCampaignsRepository::get_settings();

            foreach ($all_optin_settings as $optin_campaign_id => $setting) {
                $all_optin_settings[$optin_campaign_id]['load_optin_globally'] = true;
            }
            OptinCampaignsRepository::updateSettings($all_optin_settings);
        } else {
            $sidebar_ids = OptinCampaignsRepository::get_sidebar_optin_ids();

            foreach ($sidebar_ids as $sidebar_id) {
                $all_optin_settings[$sidebar_id]['load_optin_globally'] = true;
            }
            OptinCampaignsRepository::updateSettings($all_optin_settings);
        }
    }

    public function update_routine_5()
    {
        // All previous optin with global load empty (which means false) should explicitly be false.
        if (defined('MAILOPTIN_DETACH_LIBSODIUM')) {

            $all_optin_settings = OptinCampaignsRepository::get_settings();

            foreach ($all_optin_settings as $optin_campaign_id => $setting) {
                if ( ! isset($all_optin_settings[$optin_campaign_id]['load_optin_globally']) || $all_optin_settings[$optin_campaign_id]['load_optin_globally'] === '') {
                    $all_optin_settings[$optin_campaign_id]['load_optin_globally'] = false;
                }
            }

            OptinCampaignsRepository::updateSettings($all_optin_settings);
        }
    }

    public function update_routine_6()
    {
        // migrate old integration settings to new one
        $all_optin_settings = OptinCampaignsRepository::get_settings();

        foreach ($all_optin_settings as $optin_campaign_id => $settings) {

            $new_integration_data = [];

            if ( ! isset($all_optin_settings[$optin_campaign_id]['connection_service'])) continue;

            $connection_service                            = $all_optin_settings[$optin_campaign_id]['connection_service'];
            $new_integration_data[0]['connection_service'] = $connection_service;
            unset($all_optin_settings[$optin_campaign_id]['connection_service']);

            if (isset($all_optin_settings[$optin_campaign_id]['connection_email_list'])) {
                $connection_email_list                            = $all_optin_settings[$optin_campaign_id]['connection_email_list'];
                $new_integration_data[0]['connection_email_list'] = $connection_email_list;
                unset($all_optin_settings[$optin_campaign_id]['connection_email_list']);
            }

            // migrate connection metadata
            if ( ! is_array($all_optin_settings[$optin_campaign_id])) continue;
            foreach ($settings as $key => $value) {
                if (strpos($key, $connection_service) !== false) {
                    if ($key == 'MailChimpConnect_user_input_interests') {
                        $new_integration_data[0]['MailChimpConnect_interests'] = $value;
                    } elseif ($key == 'MailChimpConnect_automatic_interests') {
                        $new_integration_data[0]['MailChimpConnect_interests'] = array_fill_keys($value, '');
                    } else {
                        $new_integration_data[0][$key] = $value;
                    }

                    unset($all_optin_settings[$optin_campaign_id][$key]);
                }
            }

            $all_optin_settings[$optin_campaign_id]['integrations'] = json_encode($new_integration_data);
        }

        OptinCampaignsRepository::updateSettings($all_optin_settings);
    }

    public function update_routine_7()
    {
        global $wpdb;

        $collate = '';
        if ($wpdb->has_cap('collation')) {
            $collate = $wpdb->get_charset_collate();
        }

        $email_campaign_meta_table = $wpdb->prefix . Core::email_campaign_meta_table_name;

        $sql = "CREATE TABLE IF NOT EXISTS $email_campaign_meta_table (
                  meta_id bigint(20) NOT NULL AUTO_INCREMENT,
                  PRIMARY KEY  (meta_id),
                  email_campaign_id bigint(20) NOT NULL,
                  meta_key varchar(255) NULL,
                  meta_value longtext NULL
				) $collate;
				";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    public function update_routine_8()
    {
        OptinCampaignsRepository::burst_all_cache();
    }

    public function update_routine_9()
    {
        global $wpdb;

        $table = $wpdb->prefix . Core::conversions_table_name;

        $sql = "ALTER TABLE $table ADD custom_fields LONGTEXT NULL AFTER email;";

        $wpdb->query($sql);
    }

    public function update_routine_10()
    {
        global $wpdb;

        $table = $wpdb->prefix . Core::campaign_log_table_name;

        $sql = "ALTER TABLE $table CHANGE content_html content_html LONGTEXT NOT NULL";

        $wpdb->query($sql);
    }

    public function update_routine_11()
    {
        global $wpdb;

        if (class_exists('MailOptin\AdvanceAnalytics\AdvanceAnalytics')) {

            $table = AdvanceAnalytics::advance_stat_table_name();

            if ($wpdb->query("SHOW COLUMNS FROM $table LIKE 'id'") === 0) {
                $wpdb->query("ALTER TABLE $table ADD id BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT FIRST");
            }
        }
    }

    public static function get_instance()
    {
        if ( ! isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}