<?php

class PM_Pro_Create_Table {
    public function __construct() {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $this->create_label_table();
    }

    public function create_label_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'pm_task_label';

        //`status` inactive: 0, active: 1

        $sql = "CREATE TABLE IF NOT EXISTS  {$table_name} (
          `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
          `title` varchar(255) NOT NULL,
          `description` text,
          `color` varchar(255) NOT NULL,
          `status` tinyint(4) NOT NULL DEFAULT 0,
          `project_id` int(11) UNSIGNED NOT NULL,
          `created_by` int(11) UNSIGNED DEFAULT NULL,
          `updated_by` int(11) UNSIGNED DEFAULT NULL,
          `created_at` timestamp NULL DEFAULT NULL,
          `updated_at` timestamp NULL DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `project_id` (`project_id`)
        ) DEFAULT CHARSET=utf8";

        dbDelta($sql);

        $this->task_label_relational_table();
    }

    function task_label_relational_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'pm_task_label_task';

        // `status` COMMENT '0: incomplete; 1: complete; 2: pending; 3: archived'

        $sql = "CREATE TABLE IF NOT EXISTS  {$table_name} (
          `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
          `task_id` int(11) UNSIGNED NOT NULL,
          `label_id` int(11) UNSIGNED NOT NULL,
          PRIMARY KEY (`id`),
          KEY `task_id` (`task_id`),
          KEY `label_id` (`label_id`)
        ) DEFAULT CHARSET=utf8";

        dbDelta($sql);
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
