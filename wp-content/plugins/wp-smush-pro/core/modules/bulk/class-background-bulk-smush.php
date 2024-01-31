<?php

namespace Smush\Core\Modules\Bulk;

use Smush\Core\Error_Handler;
use Smush\Core\Helper;
use Smush\Core\Server_Utils;
use Smush\Core\Stats\Global_Stats;
use WP_Smush;

class Background_Bulk_Smush {
	const REQUIRED_MYSQL_VERSION = '5.6';

	/**
	 * @var Bulk_Smush_Background_Process
	 */
	private $background_process;
	private $mail;
	private $logger;
	private $global_stats;
	private $server_utils;

	public function __construct() {
		$process_manager          = new Background_Process_Manager(
			is_multisite(),
			get_current_blog_id()
		);
		$this->background_process = $process_manager->create_process();
		$this->mail               = new Mail( 'wp_smush_background' );
		$this->logger             = Helper::logger();
		$this->global_stats       = Global_Stats::get();
		$this->server_utils       = new Server_Utils();

		if ( ! $this->should_use_background() ) {
			return;
		}

		$this->register_ajax_handler( 'bulk_smush_start', array( $this, 'bulk_smush_start' ) );
		$this->register_ajax_handler( 'bulk_smush_cancel', array( $this, 'bulk_smush_cancel' ) );
		$this->register_ajax_handler( 'bulk_smush_get_status', array( $this, 'bulk_smush_get_status' ) );
		$this->register_ajax_handler( 'bulk_smush_get_global_stats', array( $this, 'bulk_smush_get_global_stats' ) );

		add_filter( 'wp_smush_script_data', array( $this, 'localize_background_stats' ) );
		add_action( 'init', array( $this, 'cancel_programmatically' ) );
	}

	public function cancel_programmatically() {
		$background_disabled = ! $this->is_background_enabled();
		$constant_value      = defined( 'WP_SMUSH_STOP_BACKGROUND_PROCESSING' ) && WP_SMUSH_STOP_BACKGROUND_PROCESSING;
		$filter_value        = apply_filters( 'wp_smush_stop_background_processing', false );
		$capability          = is_multisite() ? 'manage_network' : 'manage_options';
		$param_value         = ! empty( $_GET['wp_smush_stop_background_processing'] ) && current_user_can( $capability );
		$should_cancel       = $background_disabled || $constant_value || $filter_value || $param_value;
		$status              = $this->background_process->get_status();

		if ( $should_cancel && $status->is_in_processing() && ! $status->is_cancelled() ) {
			$this->logger->notice( 'Cancelling background processing because a constant/query param/filter indicated that the process needs to be stopped.' );

			$this->background_process->cancel();
		}
	}

	public function bulk_smush_start() {
		$this->check_ajax_referrer();

		$process       = $this->background_process;
		$in_processing = $process->get_status()->is_in_processing();
		if ( $in_processing ) {
			// Already in progress
			wp_send_json_error();
		}

		if ( ! Helper::loopback_supported() ) {
			$this->logger->error( 'Loopback check failed. Not starting a new background process.' );
			$doc_link = 'https://wpmudev.com/docs/wpmu-dev-plugins/smush/#background-processing';
			if ( ! WP_Smush::is_pro() ) {
				$doc_link = add_query_arg(
					array(
						'utm_source' => 'smush',
						'utm_medium' => 'plugin',
						'utm_campaign' => 'smush_bulksmush_loopback_notice',
					),
					$doc_link
				);
			}
			wp_send_json_error( array(
				'message' => sprintf(
					/* translators: %s: a doc link */
					esc_html__( 'Your site seems to have an issue with loopback requests. Please try again and if the problem persists find out more %s.', 'wp-smushit' ),
					sprintf( '<a target="_blank" href="%1$s">%2$s</a>', esc_html__( 'here', 'wp-smushit' ) )
				),
			) );
		} else {
			$this->logger->notice( 'Loopback check successful.' );
		}

		$tasks = $this->prepare_background_tasks();
		if ( $tasks ) {
			do_action( 'wp_smush_bulk_smush_start' );

			$process->start( $tasks );

			wp_send_json_success( $process->get_status()->to_array() );
		}

		wp_send_json_error();
	}

	public function bulk_smush_cancel() {
		$this->check_ajax_referrer();

		$this->background_process->cancel();
		wp_send_json_success();
	}

	public function bulk_smush_get_status() {
		$this->check_ajax_referrer();

		wp_send_json_success( array_merge(
			$this->background_process->get_status()->to_array(),
			array(
				'in_process_notice' => $this->get_in_process_notice(),
			)
		) );
	}

	public function bulk_smush_get_global_stats() {
		$this->check_ajax_referrer();

		$stats = WP_Smush::get_instance()->admin()->get_global_stats_with_bulk_smush_content();
		wp_send_json_success( $stats );
	}

	private function check_ajax_referrer() {
		check_ajax_referer( 'wp-smush-ajax', '_nonce' );
		// Check capability.
		if ( ! Helper::is_user_allowed( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'wp-smushit' ), 403 );
		}
	}

	private function register_ajax_handler( $action, $handler ) {
		add_action( "wp_ajax_$action", $handler );
	}

	/**
	 * @return Smush_Background_Task[]
	 */
	private function prepare_background_tasks() {
		$smush_tasks   = $this->prepare_smush_tasks();
		$resmush_tasks = $this->prepare_resmush_tasks();

		return array_merge(
			$smush_tasks,
			$resmush_tasks
		);
	}

	private function prepare_smush_tasks() {
		$to_smush = $this->global_stats->get_optimize_list()->get_ids();
		if ( empty( $to_smush ) || ! is_array( $to_smush ) ) {
			$to_smush = array();
		}

		return array_map( function ( $image_id ) {
			return new Smush_Background_Task(
				Smush_Background_Task::TASK_TYPE_SMUSH,
				$image_id
			);
		}, $to_smush );
	}

	private function prepare_resmush_tasks() {
		$to_resmush = $this->global_stats->get_redo_ids();

		return array_map( function ( $image_id ) {
			return new Smush_Background_Task(
				Smush_Background_Task::TASK_TYPE_RESMUSH,
				$image_id
			);
		}, $to_resmush );
	}

	private function prepare_error_tasks() {
		$error_items_to_retry = $this->global_stats->get_error_list()->get_ids();

		return array_map( function ( $image_id ) {
			return new Smush_Background_Task(
				Smush_Background_Task::TASK_TYPE_ERROR,
				$image_id
			);
		}, $error_items_to_retry );
	}

	public function localize_background_stats( $script_data ) {
		global $current_screen;
		$is_bulk_smush_page = isset( $current_screen->id )
		                      && strpos( $current_screen->id, '_page_smush-bulk' ) !== false;

		if ( $is_bulk_smush_page ) {
			$script_data['bo_stats'] = $this->background_process->get_status()->to_array();
		}

		return $script_data;
	}

	/**
	 * Whether BO is in processing or not.
	 *
	 * @return boolean
	 */
	public function is_in_processing() {
		return $this->background_process->get_status()->is_in_processing();
	}

	/**
	 * Whether BO is completed or not.
	 *
	 * @return boolean
	 */
	public function is_completed() {
		return $this->background_process->get_status()->is_completed();
	}

	/**
	 * Get total items.
	 *
	 * @return int
	 */
	public function get_total_items() {
		return $this->background_process->get_status()->get_total_items();
	}

	/**
	 * Get failed items.
	 *
	 * @return int
	 */
	public function get_failed_items() {
		return $this->background_process->get_status()->get_failed_items();
	}

	/**
	 * Get email address of recipient.
	 *
	 * @return string
	 */
	public function get_mail_recipient() {
		$emails = $this->mail->get_mail_recipients();

		return ! empty( $emails ) ? $emails[0] : get_option( 'admin_email' );
	}

	public function get_in_process_notice() {
		return $this->mail->reporting_email_enabled()
			? $this->get_email_enabled_notice()
			: $this->get_email_disabled_notice();
	}

	private function get_email_disabled_notice() {
		$email_setting_link = sprintf(
			'<a href="#background_email-settings-row">%s</a>',
			esc_html__( 'Enable the email notification', 'wp-smushit' )
		);

		/* translators: %s: a link */
		return sprintf( __( 'Feel free to close this page while Smush works its magic in the background. %s to receive an email when the process finishes.', 'wp-smushit' ), $email_setting_link );
	}

	private function get_email_enabled_notice() {
		$mail_recipient = $this->get_mail_recipient();
		/* translators: %s: Email address */
		return sprintf( __( 'Feel free to close this page while Smush works its magic in the background. We’ll email you at <strong>%s</strong> when it’s done.', 'wp-smushit' ), $mail_recipient );
	}

	public function is_background_enabled() {
		if ( ! $this->can_use_background() ) {
			return false;
		}

		return defined( 'WP_SMUSH_BACKGROUND' ) && WP_SMUSH_BACKGROUND;
	}

	public function should_use_background() {
		return $this->is_background_enabled()
		       && $this->is_background_supported();
	}

	public function is_background_supported() {
		return $this->is_mysql_requirement_met();
	}

	public function can_use_background() {
		return WP_Smush::is_pro() || ! empty( get_site_option( 'wp_smush_pre_3_12_6_site' ) );
	}

	/**
	 * We need the right version of MySQL for locks used by the Mutex class
	 * @return bool|int
	 */
	private function is_mysql_requirement_met() {
		return version_compare( $this->get_actual_mysql_version(), $this->get_required_mysql_version(), '>=' );
	}

	public function get_required_mysql_version() {
		return self::REQUIRED_MYSQL_VERSION;
	}

	public function get_actual_mysql_version() {
		return $this->server_utils->get_mysql_version();
	}

	public function start_bulk_smush_direct() {
		if ( ! $this->should_use_background() ) {
			return false;
		}
		$process       = $this->background_process;
		$in_processing = $process->get_status()->is_in_processing();
		if ( $in_processing ) {
			return $process->get_status()->to_array();
		}

		if ( ! Helper::loopback_supported() ) {
			$this->logger->error( 'Loopback check failed. Not starting a new background process.' );

			return false;
		}

		$tasks = $this->prepare_background_tasks();
		if ( $tasks ) {
			do_action( 'wp_smush_bulk_smush_start' );

			$process->start( $tasks );
		}

		return $process->get_status()->to_array();
	}
}