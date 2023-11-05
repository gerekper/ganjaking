<?php

namespace Smush\Core\Media_Library;

use Smush\Core\Controller;
use Smush\Core\Helper;
use Smush\Core\Media\Media_Item_Query;

class Ajax_Media_Library_Scanner extends Controller {
	const PARALLEL_REQUESTS = 5;

	/**
	 * @var Media_Library_Scanner
	 */
	private $scanner;

	public function __construct() {
		$this->scanner = new Media_Library_Scanner();

		$this->register_action( 'wp_ajax_wp_smush_before_scan_library', array( $this, 'before_scan_library' ) );
		$this->register_action( 'wp_ajax_wp_smush_scan_library_slice', array( $this, 'scan_library_slice' ) );
		$this->register_action( 'wp_ajax_wp_smush_after_scan_library', array( $this, 'after_scan_library' ) );
	}

	public function before_scan_library() {
		check_ajax_referer( 'wp_smush_media_library_scanner' );

		if ( ! Helper::is_user_allowed() ) {
			wp_send_json_error();
		}

		$this->scanner->before_scan_library();

		$slice_size             = $this->scanner->get_slice_size();
		$parallel_requests      = $this->get_parallel_requests();
		$query                  = new Media_Item_Query();
		$image_attachment_count = $query->get_image_attachment_count();
		$slice_count            = $query->get_slice_count( $slice_size );

		wp_send_json_success( array(
			'image_attachment_count' => $image_attachment_count,
			'slice_count'            => $slice_count,
			'slice_size'             => $slice_size,
			'parallel_requests'      => $parallel_requests,
		) );
	}

	public function scan_library_slice() {
		check_ajax_referer( 'wp_smush_media_library_scanner' );

		if ( ! Helper::is_user_allowed() ) {
			wp_send_json_error();
		}

		$data = stripslashes_deep( $_POST );
		if ( ! isset( $data['slice'] ) ) {
			wp_send_json_error();
		}

		$slice = (int) $data['slice'];
		wp_send_json_success( $this->scanner->scan_library_slice( $slice ) );
	}

	public function after_scan_library() {
		check_ajax_referer( 'wp_smush_media_library_scanner' );

		if ( ! Helper::is_user_allowed() ) {
			wp_send_json_error();
		}

		$this->scanner->after_scan_library();

		wp_send_json_success();
	}

	public function get_parallel_requests() {
		return self::PARALLEL_REQUESTS;
	}
}