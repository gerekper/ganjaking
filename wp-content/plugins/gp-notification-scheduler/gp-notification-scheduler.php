<?php
/*
Plugin Name: GP Notification Scheduler
Plugin URI: http://gravitywiz.com/documentation/gravity-forms-notification-scheduler
Description: Schedule your notifications.
Version: 1.1.2
Author: Gravity Wiz
Author URI: http://gravitywiz.com
License: GPL-3.0+
Perk: True
Text Domain: gp-notification-scheduler
Domain Path: /languages
*/

// Defines the current version of the Gravity Forms Notification Scheduler Add-On
define( 'GP_NOTIFICATION_SCHEDULER_VERSION', '1.1.2' );

require plugin_dir_path( __FILE__ ) . 'includes/class-gp-bootstrap.php';

$gp_notification_scheduler_bootstrap = new GP_Bootstrap( 'class-gp-notification-scheduler.php', __FILE__ );
