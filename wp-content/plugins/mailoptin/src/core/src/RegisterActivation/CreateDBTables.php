<?php

namespace MailOptin\Core\RegisterActivation;


use MailOptin\Core\Core;

class CreateDBTables
{
    public static function make()
    {
        global $wpdb;

        $collate = '';
        if ($wpdb->has_cap('collation')) {
            $collate = $wpdb->get_charset_collate();
        }

        $optin_campaigns_table = $wpdb->prefix . Core::optin_campaigns_table_name;
        $email_campaigns_table = $wpdb->prefix . Core::email_campaigns_table_name;
        $email_campaign_log_table = $wpdb->prefix . Core::campaign_log_table_name;
        $optin_conversions_table = $wpdb->prefix . Core::conversions_table_name;
        $optin_campaign_meta_table = $wpdb->prefix . Core::optin_campaign_meta_table_name;
        $campaign_log_meta_table = $wpdb->prefix . Core::campaign_log_meta_table_name;

        $sqls[] = "CREATE TABLE IF NOT EXISTS $optin_campaign_meta_table (
                  meta_id bigint(20) NOT NULL AUTO_INCREMENT,
                  PRIMARY KEY  (meta_id),
                  optin_campaign_id bigint(20) NOT NULL,
                  meta_key varchar(255) NULL,
                  meta_value longtext NULL
				) $collate;
				";
        $sqls[] = "CREATE TABLE IF NOT EXISTS $campaign_log_meta_table (
                  meta_id bigint(20) NOT NULL AUTO_INCREMENT,
                  PRIMARY KEY  (meta_id),
                  campaign_log_id bigint(20) NOT NULL,
                  meta_key varchar(255) NULL,
                  meta_value longtext NULL
				) $collate;
				";
        $sqls[] = "CREATE TABLE IF NOT EXISTS $optin_campaigns_table (
                  id bigint(20) NOT NULL AUTO_INCREMENT,
                  name varchar(50) NOT NULL,
                  uuid char(10) NOT NULL,
                  optin_class varchar(50) NOT NULL,
                  optin_type varchar(50) NOT NULL,
                  activated char(6) NOT NULL,
                  PRIMARY KEY (id),
                  UNIQUE KEY name (name),
                  UNIQUE KEY uuid (uuid)
                ) $collate;
				";

        $sqls[] = "CREATE TABLE IF NOT EXISTS $optin_conversions_table (
                  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                  optin_id int(8) unsigned NOT NULL DEFAULT '0',
                  optin_type varchar(50) NOT NULL DEFAULT '',
                  name varchar(128) DEFAULT '',
                  email varchar(128) NOT NULL DEFAULT '',
                  user_agent varchar(150) DEFAULT '',
                  conversion_page varchar(256) DEFAULT '',
                  referrer varchar(256) DEFAULT '',
                  date_added datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
                  PRIMARY KEY (id)
                ) $collate;
				";

        $sqls[] = "CREATE TABLE IF NOT EXISTS $email_campaign_log_table (
                  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                  email_campaign_id bigint(20) unsigned NOT NULL,
                  title text NOT NULL,
                  content_html text NOT NULL,
                  content_text longtext NOT NULL,
                  status varchar(20) NOT NULL DEFAULT 'draft',
                  status_time datetime DEFAULT NULL,
                  note text,
                  PRIMARY KEY (id)
                ) $collate;
				";

        $sqls[] = "CREATE TABLE IF NOT EXISTS $email_campaigns_table (
                  id bigint(20) NOT NULL AUTO_INCREMENT,
                  name varchar(50) NOT NULL,
                  campaign_type varchar(40) NOT NULL,
                  template_class varchar(50) NOT NULL,
                  PRIMARY KEY (id),
                  UNIQUE KEY name (name)
                ) $collate;
				";

        $sqls = apply_filters('mo_create_database_tables', $sqls, $collate);

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        foreach ($sqls as $sql) {
            dbDelta($sql);
        }
    }
}