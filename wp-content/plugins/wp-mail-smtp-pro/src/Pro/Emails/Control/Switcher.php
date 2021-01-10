<?php

namespace WPMailSMTP\Pro\Emails\Control;

use WPMailSMTP\Options;

/**
 * Class Switcher uses DB settings to define which emails should be switched off.
 *
 * @since 1.5.0
 */
class Switcher {

	/**
	 * @since
	 *
	 * @var \WPMailSMTP\Options
	 */
	private $options;

	/**
	 * Disable those emails that were saved as such on options page.
	 *
	 * @since 1.5.0
	 */
	public function __construct() {

		$this->options = Options::init()->get_group( 'control' );

		// Get only those that are truthy - thus, disabled.
		$disabled = array_filter( $this->options );

		foreach ( $disabled as $key => $value ) {
			switch ( $key ) {
				// Comments.
				case 'dis_comments_awaiting_moderation':
					remove_action( 'comment_post', 'wp_new_comment_notify_moderator' );
					break;
				case 'dis_comments_published':
					remove_action( 'comment_post', 'wp_new_comment_notify_postauthor' );
					add_filter( 'notify_post_author', '__return_false', PHP_INT_MAX );
					break;

				// Change of Admin Email.
				case 'dis_admin_email_attempt':
					add_action(
						'admin_init',
						function() {
							remove_action( 'add_option_new_admin_email', 'update_option_new_admin_email' );
							remove_action( 'update_option_new_admin_email', 'update_option_new_admin_email' );
						}
					);
					break;
				case 'dis_admin_email_changed':
					add_filter( 'send_site_admin_email_change_email', '__return_false', PHP_INT_MAX, 4 );
					break;
				case 'dis_admin_email_network_attempt':
					add_action(
						'admin_init',
						function() {
							remove_action( 'add_site_option_new_admin_email', 'update_network_option_new_admin_email' );
							remove_action( 'update_site_option_new_admin_email', 'update_network_option_new_admin_email' );
						}
					);
					break;
				case 'dis_admin_email_network_changed':
					add_filter( 'send_network_admin_email_change_email', '__return_false', PHP_INT_MAX, 4 );
					break;

				// Change of User Email or Password.
				case 'dis_user_details_password_reset_request':
					add_filter( 'retrieve_password_message', '__return_false', PHP_INT_MAX, 4 );
					break;
				case 'dis_user_details_password_reset':
					remove_action( 'after_password_reset', 'wp_password_change_notification' );
					break;
				case 'dis_user_details_password_changed':
					add_filter( 'send_password_change_email', '__return_false', PHP_INT_MAX, 3 );
					break;
				case 'dis_user_details_email_change_attempt':
					add_action(
						'admin_init',
						function() {
							remove_action( 'personal_options_update', 'send_confirmation_on_profile_email' );
						}
					);
					break;
				case 'dis_user_details_email_changed':
					add_filter( 'send_email_change_email', '__return_false', PHP_INT_MAX, 3 );
					break;

				// Personal Data Requests.
				case 'dis_personal_data_user_confirmed':
					remove_action( 'user_request_action_confirmed', '_wp_privacy_send_request_confirmation_notification', 12 );
					break;
				case 'dis_personal_data_sent_export_link':
					add_action(
						'admin_init',
						function() {
							remove_filter( 'wp_privacy_personal_data_export_page', 'wp_privacy_process_personal_data_export_page', 10 );
						}
					);
					break;
				case 'dis_personal_data_erased_data':
					add_action(
						'admin_init',
						function() {
							remove_action( 'wp_privacy_personal_data_erased', '_wp_privacy_send_erasure_fulfillment_notification' );
						}
					);
					break;

				// Automatic Updates.
				case 'dis_auto_updates_plugin_status':
					add_filter( 'auto_plugin_update_send_email', '__return_false' );
					break;
				case 'dis_auto_updates_theme_status':
					add_filter( 'auto_theme_update_send_email', '__return_false' );
					break;
				case 'dis_auto_updates_status':
					add_filter( 'auto_core_update_send_email', '__return_false' );
					add_filter( 'send_core_update_notification_email', '__return_false' );
					break;
				case 'dis_auto_updates_full_log':
					add_filter( 'automatic_updates_send_debug_email ', '__return_false', 1 );
					break;

				// New User.
				case 'dis_new_user_created_to_admin':
					// Processed in Reload class.
					break;
				case 'dis_new_user_created_to_user':
					// Processed in Reload class.
					break;
				case 'dis_new_user_invited_to_site_network':
					add_filter( 'wpmu_signup_user_notification', '__return_false' );
					break;
				case 'dis_new_user_created_network':
					remove_action( 'wpmu_new_user', 'newuser_notify_siteadmin' );
					break;
				case 'dis_new_user_added_activated_network':
					add_filter( 'wpmu_welcome_user_notification', '__return_false' );
					break;

				// New Site.
				case 'dis_new_site_user_registered_site_network':
					add_filter( 'wpmu_signup_blog_notification', '__return_false' );
					break;
				case 'dis_new_site_user_added_activated_site_in_network_to_admin':
					remove_action( 'wp_initialize_site', 'newblog_notify_siteadmin', 100 );
					break;
				case 'dis_new_site_user_added_activated_site_in_network_to_site':
					add_filter( 'wpmu_welcome_notification', '__return_false' );
					break;
			}
		}
	}
}
