<?php
/**
 * Smush class for storing all Ajax related functionality: Ajax class
 *
 * @package Smush\App
 * @since 2.9.0
 *
 * @copyright (c) 2018, Incsub (http://incsub.com)
 */

namespace Smush\App;

use Smush\Core\Core;
use Smush\Core\Error_Handler;
use Smush\Core\Helper;
use Smush\Core\Configs;
use Smush\Core\Media\Media_Item_Cache;
use Smush\Core\Media\Media_Item_Optimizer;
use Smush\Core\Modules\CDN;
use Smush\Core\Modules\Helpers\Parser;
use Smush\Core\Modules\Smush;
use Smush\Core\Settings;
use WP_Smush;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Ajax for storing all Ajax related functionality.
 *
 * @since 2.9.0
 */
class Ajax {

	/**
	 * Settings instance.
	 *
	 * @since 3.3.0
	 * @var Settings
	 */
	private $settings;

	/**
	 * Ajax constructor.
	 */
	public function __construct() {
		$this->settings = Settings::get_instance();

		/**
		 * QUICK SETUP
		 */
		// Handle skip quick setup action.
		add_action( 'wp_ajax_skip_smush_setup', array( $this, 'skip_smush_setup' ) );
		// Ajax request for quick setup.
		add_action( 'wp_ajax_smush_setup', array( $this, 'smush_setup' ) );

		// Hide tutorials.
		add_action( 'wp_ajax_smush_hide_tutorials', array( $this, 'hide_tutorials' ) );

		/**
		 * NOTICES
		 */
		// Handle the smush pro dismiss features notice ajax.
		add_action( 'wp_ajax_dismiss_upgrade_notice', array( $this, 'dismiss_upgrade_notice' ) );
		// Handle the smush pro dismiss features notice ajax.
		add_action( 'wp_ajax_dismiss_update_info', array( $this, 'dismiss_update_info' ) );
		// Handle ajax request to dismiss the s3 warning.
		add_action( 'wp_ajax_dismiss_s3support_alert', array( $this, 'dismiss_s3support_alert' ) );
		// Hide API message.
		add_action( 'wp_ajax_hide_api_message', array( $this, 'hide_api_message' ) );
		add_action( 'wp_ajax_smush_show_warning', array( $this, 'show_warning_ajax' ) );
		// Detect conflicting plugins.
		add_action( 'wp_ajax_smush_dismiss_notice', array( $this, 'dismiss_notice' ) );

		/**
		 * SMUSH
		 */
		// Handle Smush Single Ajax.
		add_action( 'wp_ajax_wp_smushit_manual', array( $this, 'smush_manual' ) );
		// Handle resmush operation.
		add_action( 'wp_ajax_smush_resmush_image', array( $this, 'resmush_image' ) );
		// Scan images as per the latest settings.
		add_action( 'wp_ajax_scan_for_resmush', array( $this, 'scan_images' ) );
		// Delete ReSmush list.
		add_action( 'wp_ajax_delete_resmush_list', array( $this, 'delete_resmush_list' ), '', 2 );
		// Send smush stats.
		add_action( 'wp_ajax_get_stats', array( $this, 'get_stats' ) );

		/**
		 * BULK SMUSH
		 */
		// Ignore image from bulk Smush.
		add_action( 'wp_ajax_wp_smushit_bulk', array( $this, 'process_smush_request' ) );
		// Remove from skip list.

		/**
		 * DIRECTORY SMUSH
		 */
		// Handle Ajax request for directory smush stats (stats meta box).
		add_action( 'wp_ajax_get_dir_smush_stats', array( $this, 'get_dir_smush_stats' ) );

		/**
		 * CDN
		 */
		// Toggle CDN.
		add_action( 'wp_ajax_smush_toggle_cdn', array( $this, 'toggle_cdn' ) );
		// Update stats box and CDN status.
		add_action( 'wp_ajax_get_cdn_stats', array( new CDN( new Parser() ), 'update_stats' ) );

		/**
		 * WebP
		 */
		// Toggle WebP.
		add_action( 'wp_ajax_smush_webp_toggle', array( $this, 'webp_toggle' ) );
		// Check server configuration status for WebP.
		add_action( 'wp_ajax_smush_webp_get_status', array( $this, 'webp_get_status' ) );
		// Apply apache rules for WebP support into .htaccess file.
		add_action( 'wp_ajax_smush_webp_apply_htaccess_rules', array( $this, 'webp_apply_htaccess_rules' ) );
		// Delete all webp images for all attachments.
		add_action( 'wp_ajax_smush_webp_delete_all', array( $this, 'webp_delete_all' ) );
		// Hide the webp wizard.
		add_action( 'wp_ajax_smush_toggle_webp_wizard', array( $this, 'webp_toggle_wizard' ) );

		/**
		 * LAZY LOADING
		 */
		add_action( 'wp_ajax_smush_toggle_lazy_load', array( $this, 'smush_toggle_lazy_load' ) );
		add_action( 'wp_ajax_smush_remove_icon', array( $this, 'remove_icon' ) );

		/**
		 * Configs
		 */
		add_action( 'wp_ajax_smush_upload_config', array( $this, 'upload_config' ) );
		add_action( 'wp_ajax_smush_save_config', array( $this, 'save_config' ) );
		add_action( 'wp_ajax_smush_apply_config', array( $this, 'apply_config' ) );

		/**
		 * SETTINGS
		 */
		add_action( 'wp_ajax_recheck_api_status', array( $this, 'recheck_api_status' ) );

		/**
		 * MODALS
		 */
		// Hide the new features modal.
		add_action( 'wp_ajax_hide_new_features', array( $this, 'hide_new_features_modal' ) );
	}

	/***************************************
	 *
	 * QUICK SETUP
	 */

	/**
	 * Process ajax action for skipping Smush setup.
	 */
	public function skip_smush_setup() {
		check_ajax_referer( 'smush_quick_setup' );
		// Check capability.
		if ( ! Helper::is_user_allowed( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'wp-smushit' ), 403 );
		}
		update_option( 'skip-smush-setup', true );
		wp_send_json_success();
	}

	/**
	 * Ajax action to save settings from quick setup.
	 */
	public function smush_setup() {
		check_ajax_referer( 'smush_quick_setup', '_wpnonce' );

		// Check capability.
		if ( ! Helper::is_user_allowed( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'wp-smushit' ), 403 );
		}

		$quick_settings = array();
		// Get the settings from $_POST.
		if ( ! empty( $_POST['smush_settings'] ) ) {
			// Required $quick_settings data is escaped later on in code.
			$quick_settings = json_decode( wp_unslash( $_POST['smush_settings'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		}

		// Check the last settings stored in db.
		$settings = $this->settings->get();

		// Available settings for free/pro version.
		$available           = array( 'auto', 'lossy', 'strip_exif', 'original', 'lazy_load', 'usage' );
		$highest_lossy_level = $this->settings->get_highest_lossy_level();

		foreach ( $settings as $name => $values ) {
			// Update only specified settings.
			if ( ! in_array( $name, $available, true ) ) {
				continue;
			}

			// Skip premium features if not a member.
			if ( ! in_array( $name, Settings::$basic_features, true ) && 'usage' !== $name && ! WP_Smush::is_pro() ) {
				continue;
			}

			// Update value in settings.
			if ( 'lossy' === $name ) {
				$settings['lossy'] = ! empty( $quick_settings->{$name} ) ? $highest_lossy_level : Settings::LEVEL_LOSSLESS;
			} else {
				$settings[ $name ] = (bool) $quick_settings->{$name};
			}

			// If Smush originals is selected, enable backups.
			if ( 'original' === $name && $settings[ $name ] && WP_Smush::is_pro() ) {
				$settings['backup'] = true;
			}

			// If lazy load enabled - init defaults.
			if ( 'lazy_load' === $name && $quick_settings->{$name} ) {
				$this->settings->init_lazy_load_defaults();
			}
		}

		// Update the resize sizes.
		$this->settings->set_setting( 'wp-smush-settings', $settings );

		update_option( 'skip-smush-setup', true );

		wp_send_json_success();
	}

	/**
	 * Hide tutorials.
	 *
	 * @sinde 3.8.6
	 */
	public function hide_tutorials() {
		check_ajax_referer( 'wp-smush-ajax' );

		// Check capability.
		if ( ! Helper::is_user_allowed( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'wp-smushit' ), 403 );
		}

		update_option( 'wp-smush-hide-tutorials', true, false );

		wp_send_json_success();
	}

	/***************************************
	 *
	 * NOTICES
	 */

	/**
	 * Store a key/value to hide the smush features on bulk page
	 *
	 * There is no js code related to this action, it seems we are no longer use it, better to clean it?
	 */
	public function dismiss_upgrade_notice() {
		check_ajax_referer( 'wp-smush-ajax' );

		// Check capability.
		if ( ! Helper::is_user_allowed( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'wp-smushit' ), 403 );
		}
		update_site_option( 'wp-smush-hide_upgrade_notice', true );
		// No Need to send json response for other requests.
		wp_send_json_success();
	}

	/**
	 * Remove the Update info
	 *
	 * @param bool $remove_notice  Remove notice.
	 */
	public function dismiss_update_info( $remove_notice = false ) {
		check_ajax_referer( 'wp-smush-ajax' );

		// Check capability.
		if ( ! Helper::is_user_allowed( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'wp-smushit' ), 403 );
		}
		WP_Smush::get_instance()->core()->mod->smush->dismiss_update_info( $remove_notice );
	}

	/**
	 * Hide S3 support alert by setting a flag.
	 */
	public function dismiss_s3support_alert() {
		check_ajax_referer( 'wp-smush-ajax' );
		// Check capability.
		if ( ! Helper::is_user_allowed( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'wp-smushit' ), 403 );
		}
		// Just set a flag.
		update_site_option( 'wp-smush-hide_s3support_alert', 1 );
		wp_send_json_success();
	}

	/**
	 * Hide API Message
	 */
	public function hide_api_message() {
		check_ajax_referer( 'wp-smush-ajax' );

		// Check capability.
		if ( ! Helper::is_user_allowed( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'wp-smushit' ), 403 );
		}

		$api_message = get_site_option( 'wp-smush-api_message', array() );
		if ( ! empty( $api_message ) && is_array( $api_message ) ) {
			$api_message[ key( $api_message ) ]['status'] = 'hide';
			update_site_option( 'wp-smush-api_message', $api_message );
		}

		wp_send_json_success();
	}

	/**
	 * Send JSON response whether to show or not the warning
	 */
	public function show_warning_ajax() {
		check_ajax_referer( 'wp-smush-ajax' );
		// Check capability.
		if ( ! Helper::is_user_allowed( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'wp-smushit' ), 403 );
		}
		$show = WP_Smush::get_instance()->core()->mod->smush->show_warning();
		wp_send_json( (int) $show );
	}

	/**
	 * Dismiss the plugin conflicts notice.
	 *
	 * @since 3.6.0
	 */
	public function dismiss_notice() {
		check_ajax_referer( 'wp-smush-ajax' );

		// Check capability.
		if ( ! Helper::is_user_allowed( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'wp-smushit' ), 403 );
		}

		if ( empty( $_REQUEST['key'] ) ) {
			wp_send_json_error();
		}

		$this->set_notice_dismissed( sanitize_key( $_REQUEST['key'] ) );
		wp_send_json_success();
	}

	private function set_notice_dismissed( $notice ) {
		$option_id                    = 'wp-smush-dismissed-notices';
		$dismissed_notices            = get_option( $option_id, array() );
		$dismissed_notices[ $notice ] = true;
		update_option( $option_id, $dismissed_notices );
	}

	/***************************************
	 *
	 * SMUSH
	 */

	/**
	 * Handle the Ajax request for smushing single image
	 *
	 * @uses smush_single()
	 */
	public function smush_manual() {
		if ( ! check_ajax_referer( 'wp-smush-ajax', '_nonce', false ) ) {
			wp_send_json_error(
				array(
					'error_msg' => esc_html__( 'Nonce verification failed', 'wp-smushit' ),
				)
			);
		}

		if ( ! Helper::is_user_allowed( 'upload_files' ) ) {
			wp_send_json_error(
				array(
					'error_msg' => esc_html__( "You don't have permission to work with uploaded files.", 'wp-smushit' ),
				)
			);
		}

		if ( ! isset( $_GET['attachment_id'] ) ) {
			wp_send_json_error(
				array(
					'error_msg' => esc_html__( 'No attachment ID was provided.', 'wp-smushit' ),
				)
			);
		}

		$attachment_id = (int) $_GET['attachment_id'];

		// Pass on the attachment id to smush single function.
		WP_Smush::get_instance()->core()->mod->smush->smush_single( $attachment_id );
	}

	/**
	 * Resmush the image
	 *
	 * @uses smush_single()
	 */
	public function resmush_image() {
		// Check empty fields.
		if ( empty( $_POST['attachment_id'] ) || empty( $_POST['_nonce'] ) ) {
			wp_send_json_error(
				array(
					'error_msg' => esc_html__( 'Image not smushed, fields empty.', 'wp-smushit' ),
				)
			);
		}

		// Check nonce.
		if ( ! wp_verify_nonce( wp_unslash( $_POST['_nonce'] ), 'wp-smush-resmush-' . (int) $_POST['attachment_id'] ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			wp_send_json_error(
				array(
					'error_msg' => esc_html__( "Image couldn't be smushed as the nonce verification failed, try reloading the page.", 'wp-smushit' ),
				)
			);
		}

		if ( ! Helper::is_user_allowed( 'upload_files' ) ) {
			wp_send_json_error(
				array(
					'error_msg' => esc_html__( "You don't have permission to work with uploaded files.", 'wp-smushit' ),
				)
			);
		}

		$image_id = (int) $_POST['attachment_id'];

		WP_Smush::get_instance()->core()->mod->smush->smush_single( $image_id );
	}

	/**
	 * Scans all the smushed attachments to check if they need to be resmushed as per the
	 * current settings, as user might have changed one of the configurations "Lossy", "Keep Original", "Preserve Exif"
	 *
	 * @todo: Needs some refactoring big time
	 */
	public function scan_images() {
		check_ajax_referer( 'save_wp_smush_options', 'wp_smush_options_nonce' );

		// Check capability.
		if ( ! Helper::is_user_allowed( 'manage_options' ) ) {
			wp_send_json_error(
				array(
					'notice'     => esc_html__( "You don't have permission to do this.", 'wp-smushit' ),
					'noticeType' => 'error',
				)
			);
		}

		// Scanning for NextGen or Media Library.
		$type = isset( $_REQUEST['type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['type'] ) ) : '';

		/**
		 * @hooked wp_smush_nextgen_scan_stats Smush\Core\Integrations\NextGen\Admin::scan_images()
		 */
		$stats = apply_filters( "wp_smush_{$type}_scan_stats", array() );

		return wp_send_json_success( $stats );
	}

	/**
	 * Delete the resmush list for Nextgen or the Media Library
	 *
	 * Return Stats in ajax response
	 */
	public function delete_resmush_list() {
		$stats = array();

		$key = ! empty( $_POST['type'] ) && 'nextgen' === $_POST['type'] ? 'wp-smush-nextgen-resmush-list' : 'wp-smush-resmush-list';

		// For media Library.
		if ( 'nextgen' !== $_POST['type'] ) {
			$resmush_list = get_option( $key );
			if ( ! empty( $resmush_list ) && is_array( $resmush_list ) ) {
				$stats = WP_Smush::get_instance()->core()->get_stats_for_attachments( $resmush_list );
			}
		} else {
			// For NextGen. Get the stats (get the re-Smush IDs).
			$resmush_ids = get_option( 'wp-smush-nextgen-resmush-list', array() );

			$stats = WP_Smush::get_instance()->core()->nextgen->ng_stats->get_stats_for_ids( $resmush_ids );

			$stats['count_images'] = WP_Smush::get_instance()->core()->nextgen->ng_admin->get_image_count( $resmush_ids, false );
		}

		// Delete the resmush list.
		delete_option( $key );
		wp_send_json_success( array( 'stats' => $stats ) );
	}

	/**
	 * Return Latest stats.
	 */
	public function get_stats() {
		check_ajax_referer( 'wp-smush-ajax', '_nonce' );

		// Check capability.
		if ( ! Helper::is_user_allowed( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'wp-smushit' ), 403 );
		}

		$admin = WP_Smush::get_instance()->admin();
		$stats = $admin->get_global_stats_with_bulk_smush_content();

		wp_send_json_success( $stats );
	}

	/***************************************
	 *
	 * BULK SMUSH
	 */

	/**
	 * Bulk Smushing Handler.
	 *
	 * Processes the Smush request and sends back the next id for smushing.
	 */
	public function process_smush_request() {
		check_ajax_referer( 'wp-smush-ajax', '_nonce' );

		// Check capability.
		if ( ! Helper::is_user_allowed( 'manage_options' ) ) {
			wp_send_json_error(
				array(
					'error'         => 'unauthorized',
					'error_message' => esc_html__( "You don't have permission to do this.", 'wp-smushit' ),
				),
				403
			);
		}

		$new_bulk_smush = ! empty( $_REQUEST['new_bulk_smush_started'] ) && $_REQUEST['new_bulk_smush_started'] !== 'false';
		if ( $new_bulk_smush ) {
			do_action( 'wp_smush_bulk_smush_start' );
		}

		// If the bulk smush needs to be stopped.
		if ( ! WP_Smush::is_pro() && ! Core::check_bulk_limit() ) {
			wp_send_json_error(
				array(
					'error'    => 'limit_exceeded',
					'continue' => false,
				)
			);
		}

		$attachment_id = 0;
		if ( ! empty( $_REQUEST['attachment_id'] ) ) {
			$attachment_id = (int) $_REQUEST['attachment_id'];
		}

		$smush = WP_Smush::get_instance()->core()->mod->smush;

		/**
		 * Smush image.
		 *
		 * @since 3.9.6
		 *
		 * @param int      $attachment_id  Attachment ID.
		 * @param array    $meta Image metadata (passed by reference).
		 * @param WP_Error $errors WP_Error (passed by reference).
		 */
		$smush->smushit( $attachment_id, $meta, $errors );

		$smush_data         = get_post_meta( $attachment_id, Smush::$smushed_meta_key, true );
		$resize_savings     = get_post_meta( $attachment_id, 'wp-smush-resize_savings', true );
		$conversion_savings = Helper::get_pngjpg_savings( $attachment_id );

		$stats = array(
			'count'              => ! empty( $smush_data['sizes'] ) ? count( $smush_data['sizes'] ) : 0,
			'size_before'        => ! empty( $smush_data['stats'] ) ? $smush_data['stats']['size_before'] : 0,
			'size_after'         => ! empty( $smush_data['stats'] ) ? $smush_data['stats']['size_after'] : 0,
			'savings_resize'     => max( $resize_savings, 0 ),
			'savings_conversion' => $conversion_savings['bytes'] > 0 ? $conversion_savings : 0,
			'is_lossy'           => ! empty( $smush_data ['stats'] ) ? $smush_data['stats']['lossy'] : false,
		);

		if ( $errors && is_wp_error( $errors ) && $errors->has_errors() ) {
			$error = Error_Handler::get_error( $errors, Media_Item_Cache::get_instance()->get( $attachment_id ) );
			$response = array(
				'stats'        => $stats,
				'error'        => $error,
				'show_warning' => (int) $smush->show_warning(),
			);

			// Send data.
			wp_send_json_error( $response );
		}

		// Runs after a image is successfully smushed.
		do_action( 'image_smushed', $attachment_id, $stats );

		// Update the bulk Limit count.
		Core::update_smush_count();

		// Send ajax response.
		wp_send_json_success(
			array(
				'stats'        => $stats,
				'show_warning' => (int) $smush->show_warning(),
			)
		);
	}

	/***************************************
	 *
	 * DIRECTORY SMUSH
	 */

	/**
	 * Returns Directory Smush stats and Cumulative stats
	 */
	public function get_dir_smush_stats() {
		check_ajax_referer( 'wp-smush-ajax' );

		// Check capability.
		$capability = is_multisite() ? 'manage_network' : 'manage_options';
		if ( ! Helper::is_user_allowed( $capability ) ) {
			wp_die( esc_html__( 'Unauthorized', 'wp-smushit' ), 403 );
		}

		$result = array();

		// Store the Total/Smushed count.
		$stats = WP_Smush::get_instance()->core()->mod->dir->total_stats();

		$result['dir_smush'] = $stats;

		// Cumulative Stats.
		//$result['combined_stats'] = WP_Smush::get_instance()->core()->mod->dir->combine_stats( $stats );

		// Store the stats in options table.
		update_option( 'dir_smush_stats', $result, false );

		// Send ajax response.
		wp_send_json_success( $result );
	}

	/***************************************
	 *
	 * CDN
	 *
	 * @since 3.0
	 */

	/**
	 * Toggle CDN.
	 *
	 * Handles "Get Started" button press on the disabled CDN meta box.
	 * Handles "Deactivate" button press on the CDN meta box.
	 * Refreshes page on success.
	 *
	 * @since 3.0
	 */
	public function toggle_cdn() {
		check_ajax_referer( 'save_wp_smush_options' );

		if ( ! Helper::is_user_allowed( 'manage_options' ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'User can not modify options', 'wp-smushit' ),
				),
				403
			);
		}

		$enable   = filter_input( INPUT_POST, 'param', FILTER_VALIDATE_BOOLEAN );
		$response = WP_Smush::get_instance()->core()->mod->cdn->toggle_cdn( $enable );

		if ( is_wp_error( $response ) ) {
			wp_send_json_error(
				array( 'message' => $response->get_error_message() )
			);
		}

		wp_send_json_success();
	}

	/***************************************
	 *
	 * WebP
	 *
	 * @since 3.8.0
	 */

	/**
	 * Toggle WebP.
	 *
	 * Handles "Activate" button press on the disabled WebP meta box.
	 * Handles "Deactivate" button press on the WebP meta box.
	 * Refreshes page on success.
	 *
	 * @since 3.8.0
	 */
	public function webp_toggle() {
		check_ajax_referer( 'save_wp_smush_options' );

		$capability = is_multisite() ? 'manage_network' : 'manage_options';
		if ( ! Helper::is_user_allowed( $capability ) ) {
			wp_send_json_error(
				array(
					'message' => __( "You don't have permission to do this.", 'wp-smushit' ),
				),
				403
			);
		}

		$param       = isset( $_POST['param'] ) ? sanitize_text_field( wp_unslash( $_POST['param'] ) ) : '';
		$enable_webp = 'true' === $param;

		WP_Smush::get_instance()->core()->mod->webp->toggle_webp( $enable_webp );

		wp_send_json_success();
	}

	/**
	 * Check server configuration status and other info for WebP.
	 *
	 * Handles "Re-Check Status" button press on the WebP meta box.
	 *
	 * @since 3.8.0
	 */
	public function webp_get_status() {
		if ( ! check_ajax_referer( 'wp-smush-webp-nonce', false, false ) || ! Helper::is_user_allowed( 'manage_options' ) ) {
			wp_send_json_error( esc_html__( "Either the nonce expired or you can't modify options. Please reload the page and try again.", 'wp-smushit' ) );
		}

		$is_configured = WP_Smush::get_instance()->core()->mod->webp->get_is_configured_with_error_message( true );

		if ( true === $is_configured ) {
			wp_send_json_success();
		}

		// The messages are set in React with dangerouslySetInnerHTML so they must be html-escaped.
		wp_send_json_error( esc_html( $is_configured ) );
	}

	/**
	 * Write apache rules for WebP support from .htaccess file.
	 * Handles the "Apply Rules" button press on the WebP meta box.
	 *
	 * @since 3.8.0
	 */
	public function webp_apply_htaccess_rules() {
		if ( ! check_ajax_referer( 'wp-smush-webp-nonce', false, false ) || ! Helper::is_user_allowed( 'manage_options' ) ) {
			wp_send_json_error( "Either the nonce expired or you can't modify options. Please reload the page and try again." );
		}

		$was_written = WP_Smush::get_instance()->core()->mod->webp->save_htaccess();

		if ( true === $was_written ) {
			wp_send_json_success();
		}

		wp_send_json_error( wp_kses_post( $was_written ) );
	}

	/**
	 * Delete all webp images.
	 * Triggered by the "Delete WebP images" button in the webp tab.
	 *
	 * @since 3.8.0
	 */
	public function webp_delete_all() {
		check_ajax_referer( 'save_wp_smush_options' );

		$capability = is_multisite() ? 'manage_network' : 'manage_options';

		if ( ! Helper::is_user_allowed( $capability ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'This user can not delete all WebP images.', 'wp-smushit' ),
				),
				403
			);
		}

		WP_Smush::get_instance()->core()->mod->webp->delete_all();

		wp_send_json_success();
	}

	/**
	 * Toggles the webp wizard.
	 *
	 * @since 3.8.8
	 */
	public function webp_toggle_wizard() {
		if ( check_ajax_referer( 'wp-smush-webp-nonce', false, false ) && Helper::is_user_allowed( 'manage_options' ) ) {
			$is_hidden = get_site_option( 'wp-smush-webp_hide_wizard' );
			update_site_option( 'wp-smush-webp_hide_wizard', ! $is_hidden );
			wp_send_json_success();
		}
	}

	/***************************************
	 *
	 * LAZY LOADING
	 *
	 * @since 3.2.0
	 */

	/**
	 * Toggle lazy loading module.
	 *
	 * Handles "Activate" button press on the disabled lazy loading meta box.
	 * Handles "Deactivate" button press on the lazy loading meta box.
	 * Refreshes page on success.
	 *
	 * @since 3.2.0
	 */
	public function smush_toggle_lazy_load() {
		check_ajax_referer( 'save_wp_smush_options' );

		if ( ! Helper::is_user_allowed( 'manage_options' ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'User can not modify options', 'wp-smushit' ),
				),
				403
			);
		}

		$param = isset( $_POST['param'] ) ? sanitize_text_field( wp_unslash( $_POST['param'] ) ) : false;

		if ( 'true' === $param ) {
			$settings = $this->settings->get_setting( 'wp-smush-lazy_load' );

			// No settings, during init - set defaults.
			if ( ! $settings ) {
				$this->settings->init_lazy_load_defaults();
			}
		}

		$this->settings->set( 'lazy_load', 'true' === $param );

		wp_send_json_success();
	}

	/**
	 * Remove spinner/placeholder icon from lazy-loading.
	 *
	 * @since 3.2.2
	 */
	public function remove_icon() {
		check_ajax_referer( 'save_wp_smush_options' );

		// Check for permission.
		if ( ! Helper::is_user_allowed( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'wp-smushit' ), 403 );
		}

		$id   = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT );
		$type = filter_input( INPUT_POST, 'type', FILTER_SANITIZE_SPECIAL_CHARS );
		if ( $id && $type ) {
			$settings = $this->settings->get_setting( 'wp-smush-lazy_load' );
			if ( false !== ( $key = array_search( $id, $settings['animation'][ $type ]['custom'] ) ) ) {
				unset( $settings['animation'][ $type ]['custom'][ $key ] );
				$this->settings->set_setting( 'wp-smush-lazy_load', $settings );
			}
		}

		wp_send_json_success();
	}

	/***************************************
	 *
	 * CONFIGS
	 *
	 * @since 3.8.5
	 */

	/**
	 * Handles the upload of a config file.
	 *
	 * @since 3.8.5
	 */
	public function upload_config() {
		check_ajax_referer( 'smush_handle_config' );

		$capability = is_multisite() ? 'manage_network' : 'manage_options';
		if ( ! Helper::is_user_allowed( $capability ) ) {
			wp_send_json_error( null, 403 );
		}

		/**
		 * Data escaped and sanitized via \Smush\Core\Configs::save_uploaded_config()
		 *
		 * @see \Smush\Core\Configs::decode_and_validate_config_file()
		 */
		$file = isset( $_FILES['file'] ) ? wp_unslash( $_FILES['file'] ) : false; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		$configs_handler = new Configs();
		$new_config      = $configs_handler->save_uploaded_config( $file );

		if ( ! is_wp_error( $new_config ) ) {
			wp_send_json_success( $new_config );
		}

		wp_send_json_error(
			array( 'error_msg' => $new_config->get_error_message() )
		);
	}
	/**
	 * Handles the upload of a config file.
	 *
	 * @since 3.8.5
	 */
	public function save_config() {
		check_ajax_referer( 'smush_handle_config' );

		$capability = is_multisite() ? 'manage_network' : 'manage_options';
		if ( ! Helper::is_user_allowed( $capability ) ) {
			wp_send_json_error( null, 403 );
		}

		$configs_handler = new Configs();
		wp_send_json_success( $configs_handler->get_config_from_current() );
	}

	/**
	 * Applies the given config.
	 *
	 * @since 3.8.5
	 */
	public function apply_config() {
		check_ajax_referer( 'smush_handle_config' );

		$capability = is_multisite() ? 'manage_network' : 'manage_options';
		if ( ! Helper::is_user_allowed( $capability ) ) {
			wp_send_json_error( null, 403 );
		}

		$id = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT );
		if ( ! $id ) {
			// Abort if no config ID was given.
			wp_send_json_error(
				array( 'error_msg' => esc_html__( 'Missing config ID', 'wp-smushit' ) )
			);
		}

		$configs_handler = new Configs();
		$response        = $configs_handler->apply_config_by_id( $id );

		if ( ! is_wp_error( $response ) ) {
			wp_send_json_success();
		}

		wp_send_json_error(
			array( 'error_msg' => esc_html( $response->get_error_message() ) )
		);
	}

	/***************************************
	 *
	 * SETTINGS
	 *
	 * @since 3.2.0.2
	 */

	/**
	 * Re-check API status.
	 *
	 * @since 3.2.0.2
	 */
	public function recheck_api_status() {
		// Check for permission.
		if ( ! Helper::is_user_allowed( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'wp-smushit' ), 403 );
		}
		WP_Smush::get_instance()->validate_install( true );
		wp_send_json_success();
	}

	/***************************************
	 *
	 * MODALS
	 *
	 * @since 3.7.0
	 */

	/**
	 * Hide the new features modal
	 *
	 * @since 3.7.0
	 */
	public function hide_new_features_modal() {
		check_ajax_referer( 'wp-smush-ajax' );

		// Check for permission.
		if ( ! Helper::is_user_allowed( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'wp-smushit' ), 403 );
		}
		delete_site_option( 'wp-smush-show_upgrade_modal' );
		wp_send_json_success();
	}
}