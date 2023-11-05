<?php

namespace Smush\Core\Media_Library;

use Smush\Core\Array_Utils;
use Smush\Core\Controller;
use Smush\Core\Helper;

class Media_Library_Watcher extends Controller {
	const WP_SMUSH_IMAGE_SIZES_STATE = 'wp_smush_image_sizes_state';
	/**
	 * @var Array_Utils
	 */
	private $array_utils;

	public function __construct() {
		$this->array_utils = new Array_Utils();
	}

	public function init() {
		parent::init();

		add_action( 'add_attachment', array( $this, 'wait_for_generate_metadata' ) );
		add_action( 'admin_init', array( $this, 'watch_image_sizes' ), PHP_INT_MAX );
	}

	public function wait_for_generate_metadata() {
		add_action( 'wp_generate_attachment_metadata', array( $this, 'trigger_custom_add_attachment' ), 10, 2 );
	}

	public function trigger_custom_add_attachment( $metadata, $attachment_id ) {
		do_action( 'wp_smush_after_attachment_upload', $attachment_id );

		remove_action( 'wp_generate_attachment_metadata', array( $this, 'trigger_custom_add_attachment' ) );

		return $metadata;
	}

	public function watch_image_sizes() {
		$skip = get_transient( 'wp_smush_skip_image_sizes_recheck' );
		if ( $skip ) {
			return;
		}

		$new_sizes = Helper::fetch_image_sizes();
		$new_hash  = $this->array_utils->array_hash( $new_sizes );
		$old_state = $this->get_image_sizes_state();
		$old_sizes = $old_state['sizes'];
		$old_hash  = $old_state['hash'];
		if ( $new_hash !== $old_hash ) {
			do_action( 'wp_smush_image_sizes_changed', $old_sizes, $new_sizes );

			$this->update_image_sizes_state( $new_sizes, $new_hash );
		}

		set_transient( 'wp_smush_skip_image_sizes_recheck', true, HOUR_IN_SECONDS );
	}

	private function get_image_sizes_state() {
		$state = get_option( self::WP_SMUSH_IMAGE_SIZES_STATE );
		if ( empty( $state ) ) {
			$state = array();
		}

		if ( empty( $state['sizes'] ) || ! is_array( $state['sizes'] ) ) {
			$state['sizes'] = array();
		}

		if ( empty( $state['hash'] ) ) {
			$state['hash'] = '';
		}

		return $state;
	}

	private function update_image_sizes_state( $sizes, $hash ) {
		update_option( self::WP_SMUSH_IMAGE_SIZES_STATE, array(
			'sizes' => empty( $sizes ) || ! is_array( $sizes ) ? array() : $sizes,
			'hash'  => empty( $hash ) ? '' : $hash,
		) );
	}
}