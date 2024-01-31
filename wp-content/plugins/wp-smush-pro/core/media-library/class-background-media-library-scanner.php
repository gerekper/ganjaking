<?php

namespace Smush\Core\Media_Library;

use Smush\Core\Controller;
use Smush\Core\Helper;
use Smush\Core\Media\Media_Item_Query;
use Smush\Core\Stats\Global_Stats;
use WP_Smush;

class Background_Media_Library_Scanner extends Controller {
	const OPTIMIZE_ON_COMPLETED_OPTION_KEY = 'wp_smush_run_optimize_on_scan_completed';
	/**
	 * @var Media_Library_Scanner
	 */
	private $scanner;
	/**
	 * @var Media_Library_Scan_Background_Process
	 */
	private $background_process;

	private $logger;

	/**
	 * @var bool
	 */
	private $optimize_on_scan_completed;
	/**
	 * @var Global_Stats
	 */
	private $global_stats;

	/**
	 * Static instance
	 *
	 * @var self
	 */
	private static $instance;

	public static function get_instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		$this->scanner            = new Media_Library_Scanner();
		$this->logger             = Helper::logger();
		$this->global_stats       = Global_Stats::get();
		$identifier               = $this->make_identifier();
		$this->background_process = new Media_Library_Scan_Background_Process( $identifier, $this->scanner );
		$this->background_process->set_logger( Helper::logger() );

		$this->register_action( 'wp_ajax_wp_smush_start_background_scan', array( $this, 'start_background_scan' ) );
		$this->register_action( 'wp_ajax_wp_smush_cancel_background_scan', array( $this, 'cancel_background_scan' ) );
		$this->register_action( 'wp_ajax_wp_smush_get_background_scan_status', array( $this, 'send_status' ) );
		$this->register_action( "{$identifier}_completed", array( $this, 'background_process_completed' ) );
		$this->register_action( "{$identifier}_dead", array( $this, 'background_process_dead' ) );

		add_filter( 'wp_smush_script_data', array( $this, 'localize_media_library_scan_script_data' ) );
	}

	public function start_background_scan() {
		check_ajax_referer( 'wp_smush_media_library_scanner' );

		if ( ! Helper::is_user_allowed() ) {
			wp_send_json_error();
		}

		$in_processing = $this->background_process->get_status()->is_in_processing();
		if ( $in_processing ) {
			// Already in progress
			wp_send_json_error( array( 'message' => __( 'Background scan is already in processing.', 'wp-smushit' ) ) );
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
			wp_send_json_error(
				array(
					'message' => sprintf(
						/* translators: %s Doc link */
						esc_html__( 'Your site seems to have an issue with loopback requests. Please try again and if the problem persists find out more %s.', 'wp-smushit' ),
						sprintf( '<a target="_blank" href="%1$s">%2$s</a>', $doc_link, esc_html__( 'here', 'wp-smushit' ) )
					),
				)
			);
		} else {
			$this->logger->notice( 'Loopback check successful.' );
		}

		$this->set_optimize_on_scan_completed( ! empty( $_REQUEST['optimize_on_scan_completed'] ) );

		if ( $this->background_process->get_status()->is_dead() ) {
			$this->scanner->reduce_slice_size_option();
		}

		$this->scanner->before_scan_library();

		$slice_size  = $this->scanner->get_slice_size();
		$query       = new Media_Item_Query();
		$slice_count = $query->get_slice_count( $slice_size );
		$tasks       = range( 1, $slice_count );
		$this->background_process->start( $tasks );

		wp_send_json_success( $this->get_scan_status() );
	}

	public function cancel_background_scan() {
		check_ajax_referer( 'wp_smush_media_library_scanner' );

		if ( ! Helper::is_user_allowed() ) {
			wp_send_json_error();
		}

		$this->background_process->cancel();
		$this->set_optimize_on_scan_completed( false );

		wp_send_json_success( $this->get_scan_status() );
	}

	public function send_status() {
		check_ajax_referer( 'wp_smush_media_library_scanner' );

		if ( ! Helper::is_user_allowed() ) {
			wp_send_json_error();
		}

		wp_send_json_success( $this->get_scan_status() );
	}

	public function background_process_completed() {
		$this->scanner->after_scan_library();

		if ( $this->enabled_optimize_on_scan_completed() ) {
			$bg_optimization = WP_Smush::get_instance()->core()->mod->bg_optimization;
			$bg_optimization->start_bulk_smush_direct();
		}
	}

	public function background_process_dead() {
		$this->global_stats->mark_as_outdated();
	}

	private function make_identifier() {
		$identifier = 'wp_smush_background_scan_process';
		if ( is_multisite() ) {
			$post_fix   = '_' . get_current_blog_id();
			$identifier .= $post_fix;
		}

		return $identifier;
	}

	public function localize_media_library_scan_script_data( $script_data ) {
		$scan_script_data                  = $this->background_process->get_status()->to_array();
		$scan_script_data['nonce']         = wp_create_nonce( 'wp_smush_media_library_scanner' );
		$script_data['media_library_scan'] = $scan_script_data;

		return $script_data;
	}

	private function set_optimize_on_scan_completed( $status ) {
		$this->optimize_on_scan_completed = $status;
		if ( $this->optimize_on_scan_completed ) {
			update_option( self::OPTIMIZE_ON_COMPLETED_OPTION_KEY, 1, false );
		} else {
			delete_option( self::OPTIMIZE_ON_COMPLETED_OPTION_KEY );
		}
	}

	private function enabled_optimize_on_scan_completed() {
		if ( null === $this->optimize_on_scan_completed ) {
			$this->optimize_on_scan_completed = get_option( self::OPTIMIZE_ON_COMPLETED_OPTION_KEY );
		}

		return ! empty( $this->optimize_on_scan_completed );
	}

	private function get_scan_status() {
		$is_completed = $this->background_process->get_status()->is_completed();
		$is_cancelled = $this->background_process->get_status()->is_cancelled();
		$status       = $this->background_process->get_status()->to_array();

		$status['optimize_on_scan_completed'] = $this->enabled_optimize_on_scan_completed();

		// Add global stats on completed/cancelled.
		if ( $is_completed || $is_cancelled ) {
			$status['global_stats'] = WP_Smush::get_instance()->admin()->get_global_stats_with_bulk_smush_content_and_notice();
		}

		if ( $is_completed ) {
			$bg_optimization                      = WP_Smush::get_instance()->core()->mod->bg_optimization;
			$status['enabled_background_process'] = $bg_optimization->should_use_background();
		}

		return $status;
	}

	public function get_background_process() {
		return $this->background_process;
	}
}