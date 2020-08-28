<?php
namespace WeDevs\PM_Pro\Core\Upgrades;

/**
*   Upgrade project manager 3.0
*/
class Upgrade_0_3 {
    /*initialize */
    public function upgrade_init() {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $this->integration_table();
    }

    function integration_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'pm_integrations';

        // `status` COMMENT '0: incomplete; 1: complete; 2: pending; 3: archived'

        $sql = "CREATE TABLE IF NOT EXISTS  {$table_name} (
          `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
          `project_id` int(20) DEFAULT NULL,
          `primary_key` int(20) DEFAULT NULL,
          `foreign_key` int(20) DEFAULT NULL,
          `type` varchar(25) DEFAULT NULL,
          `source` varchar(30) DEFAULT NULL,
          `username` varchar(40) DEFAULT NULL,
          `created_at` timestamp NULL DEFAULT NULL,
          `updated_at` timestamp NULL DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) DEFAULT CHARSET=utf8";
        dbDelta($sql);
    }
}
