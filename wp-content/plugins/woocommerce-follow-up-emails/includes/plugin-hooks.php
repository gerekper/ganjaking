<?php

// front styles
add_action( 'wp_enqueue_scripts',   'Follow_Up_Emails::front_css' );

// dashboard widget
add_action('wp_dashboard_setup',    'FUE_Admin_Controller::dashboard_widget');

// menu
add_action('admin_menu',            'FUE_Admin_Controller::add_menu', 20);

// replace custom menu URLs with their actual values
add_filter('clean_url',             'FUE_Admin_Controller::replace_email_form_url', 0, 3);

// highlight the correct submenu item in the admin nav menu
add_filter('parent_file',           'FUE_Admin_Controller::set_active_submenu' );

// settings styles and scripts
add_action( 'admin_enqueue_scripts', 'FUE_Admin_Controller::register_scripts', 11 );
add_action( 'admin_enqueue_scripts', 'FUE_Admin_Controller::settings_scripts', 19 );

// load addons
add_action('plugins_loaded',        'Follow_Up_Emails::load_addons');

// after user signs up
add_action('user_register',         array( 'FUE_Sending_Scheduler', 'queue_signup_emails' ) );
add_action( 'fue_newsletter_added_subscriber', array( 'FUE_Sending_Scheduler', 'queue_list_emails_signup'), 10, 2 );
add_action( 'fue_subscriber_added_to_list', array('FUE_Sending_Scheduler', 'queue_list_emails_added_to_list'), 10, 2);

// cron actions
add_action('sfn_followup_emails',   array( 'FUE_Sending_Scheduler', 'send_scheduled_emails' ) );
add_action('fue_bounce_handler',   array( 'FUE_Admin_Actions', 'handle_bounced_emails' ) );

// usage report
add_action('sfn_send_usage_report', 'FUE_Reports::send_usage_data');

// daily summary requeuing
add_action( 'fue_adhoc_email_sent', array( 'FUE_Sending_Scheduler', 'queue_daily_summary_email' ) );

// send manual emails
add_action( 'admin_post_fue_followup_send_manual',      array( 'FUE_Admin_Actions', 'send_manual' ) );


add_action( 'admin_post_fue_followup_delete',           array('FUE_Admin_Actions', 'delete_email') );
add_action( 'admin_post_fue_followup_export_list',      array('FUE_Admin_Actions', 'export_list') );

// FUE Settings
add_action( 'admin_post_fue_followup_save_settings',    array('FUE_Admin_Actions', 'update_settings') );

// Restore optout email
add_action( 'admin_post_fue_optout_manage',             array('FUE_Admin_Actions', 'manage_optout') );
add_action( 'admin_init',                               array('FUE_Admin_Actions', 'optout_bulk_actions') );
add_action( 'admin_post_fue_optout_remove',             array('FUE_Admin_Actions', 'optout_delete_email') );

// subscribers
add_action( 'admin_post_fue_subscribers_manage',    array('FUE_Admin_Actions', 'manage_subscribers'));
add_action( 'admin_init',                           array('FUE_Admin_Actions', 'subscribers_bulk_actions'));
add_action( 'admin_init',                       array('FUE_Admin_Actions', 'process_subscribers_lists_bulk_action'));
add_action( 'admin_init',                       array('FUE_Admin_Actions', 'process_subscribers_lists_delete_all'));

// reset report data
add_action('admin_post_fue_reset_reports',              array('FUE_Admin_Actions', 'reset_reports') );

// backup and restore
add_action('admin_post_fue_backup_settings',            array('FUE_Admin_Actions', 'backup_settings') );

// queue actions
add_action('admin_post_fue_update_queue_status',        array('FUE_Admin_Actions', 'update_queue_item_status') );
add_action('admin_post_fue_delete_queue',               array('FUE_Admin_Actions', 'delete_queue_item') );
add_action('admin_post_fue_send_queue_item',            array('FUE_Admin_Actions', 'send_queue_item') );
add_action('admin_init',                                array('FUE_Admin_Actions', 'process_queue_bulk_action') );
add_action('admin_init',                                array('FUE_Admin_Actions', 'process_queue_delete_all') );

// DKIM
add_action( 'phpmailer_init', 'FUE_Sending_Mailer::set_dkim' );

// Action-Scheduler
add_filter( 'action_scheduler_logger_class', 'fue_add_logger_class' );
add_filter( 'action_scheduler_queue_runner_batch_size', 'fue_action_scheduler_batch_size' );
add_filter( 'action_scheduler_queue_runner_concurrent_batches', 'fue_action_scheduler_concurrent_batches' );

// TinyMCE settings for templates
add_filter('mce_external_plugins', 'FUE_Admin_Controller::register_mce_plugins' );

add_filter( 'comments_clauses', array( 'FUE_Followup_Logger', 'exclude_fue_comments' ), 10, 1 );
add_action( 'comment_feed_join', array( 'FUE_Followup_Logger', 'exclude_fue_comments_from_feed_join' ) );
add_action( 'comment_feed_where', array( 'FUE_Followup_Logger', 'exclude_fue_comments_from_feed_where' ) );

global $fue_key;
$fue_key = base64_decode(FUE_KEY.'A=');
