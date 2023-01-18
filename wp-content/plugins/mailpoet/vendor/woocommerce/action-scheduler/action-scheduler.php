<?php
if (!defined('ABSPATH')) exit;
if ( ! function_exists( 'action_scheduler_register_3_dot_5_dot_2' ) && function_exists( 'add_action' ) ) {
 if ( ! class_exists( 'ActionScheduler_Versions', false ) ) {
 require_once __DIR__ . '/classes/ActionScheduler_Versions.php';
 add_action( 'plugins_loaded', array( 'ActionScheduler_Versions', 'initialize_latest_version' ), 1, 0 );
 }
 add_action( 'plugins_loaded', 'action_scheduler_register_3_dot_5_dot_2', 0, 0 );
 function action_scheduler_register_3_dot_5_dot_2() {
 $versions = ActionScheduler_Versions::instance();
 $versions->register( '3.5.2', 'action_scheduler_initialize_3_dot_5_dot_2' );
 }
 function action_scheduler_initialize_3_dot_5_dot_2() {
 // A final safety check is required even here, because historic versions of Action Scheduler
 // followed a different pattern (in some unusual cases, we could reach this point and the
 // ActionScheduler class is already defined—so we need to guard against that).
 if ( ! class_exists( 'ActionScheduler', false ) ) {
 require_once __DIR__ . '/classes/abstracts/ActionScheduler.php';
 ActionScheduler::init( __FILE__ );
 }
 }
 // Support usage in themes - load this version if no plugin has loaded a version yet.
 if ( did_action( 'plugins_loaded' ) && ! doing_action( 'plugins_loaded' ) && ! class_exists( 'ActionScheduler', false ) ) {
 action_scheduler_initialize_3_dot_5_dot_2();
 do_action( 'action_scheduler_pre_theme_init' );
 ActionScheduler_Versions::initialize_latest_version();
 }
}
