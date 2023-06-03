<?php

namespace Smush\Core\Media;

use Smush\Core\Controller;
use Smush\Core\Error_Handler;
use Smush\Core\Helper;
use Smush\Core\Stats\Global_Stats;
use WP_Smush;

/**
 * Performs operations on the media item
 */
class Media_Item_Controller extends Controller {
	public function __construct() {
		$this->register_action( 'wp_ajax_ignore_bulk_image', array( $this, 'ignore_bulk_image' ) );
		$this->register_action( 'wp_ajax_remove_from_skip_list', array( $this, 'remove_from_skip_list' ) );
		$this->register_action( 'wp_ajax_wp_smush_ignore_all_failed_items', array(
			$this,
			'ignore_all_failed_items',
		) );
	}

	public function remove_from_skip_list() {
		check_ajax_referer( 'wp-smush-remove-skipped' );

		if ( ! Helper::is_user_allowed( 'upload_files' ) ) {
			wp_send_json_error( array(
				'error_message' => esc_html__( "You don't have permission to work with uploaded files.", 'wp-smushit' ),
			), 403 );
		}

		if ( ! isset( $_POST['id'] ) ) {
			wp_send_json_error();
		}

		$attachment_id = absint( $_POST['id'] );

		$changed = $this->change_attachment_ignored_status( $attachment_id, false );
		if ( ! $changed ) {
			wp_send_json_error();
		}

		wp_send_json_success(
			array(
				'html' => WP_Smush::get_instance()->library()->generate_markup( $attachment_id ),
			)
		);
	}

	public function ignore_bulk_image() {
		check_ajax_referer( 'wp-smush-ajax' );

		if ( ! Helper::is_user_allowed( 'upload_files' ) ) {
			wp_send_json_error( array(
				'error_msg' => esc_html__( "You don't have permission to work with uploaded files.", 'wp-smushit' ),
			), 403 );
		}

		if ( ! isset( $_POST['id'] ) ) {
			wp_send_json_error();
		}

		$attachment_id = absint( $_POST['id'] );
		$changed       = $this->change_attachment_ignored_status( $attachment_id, true );
		if ( ! $changed ) {
			wp_send_json_error();
		}

		wp_send_json_success( array(
			'html' => WP_Smush::get_instance()->library()->generate_markup( $attachment_id ),
		) );
	}

	public function ignore_all_failed_items() {
		check_ajax_referer( 'wp-smush-ajax' );

		if ( ! Helper::is_user_allowed() ) {
			wp_send_json_error( array(
				'message' => __( "You don't have permission to do this.", 'wp-smushit' ),
			), 403 );
		}

		$failed_images = Error_Handler::get_all_failed_images();
		if ( empty( $failed_images ) ) {
			wp_send_json_error( array( 'message' => __( 'Not found any failed items.', 'wp-smushit' ) ) );
		}

		foreach ( $failed_images as $failed_image_id ) {
			$this->change_attachment_ignored_status( $failed_image_id, true );
		}
		wp_send_json_success();
	}

	private function change_attachment_ignored_status( $attachment_id, $new_status ) {
		$media_item = Media_Item_Cache::get_instance()->get( $attachment_id );
		if ( ! $media_item->is_mime_type_supported() ) {
			return false;
		}

		$media_item->set_ignored( $new_status );
		$media_item->save();

		do_action( 'wp_smush_attachment_ignored_status_changed', $attachment_id, $new_status );

		return true;
	}
}