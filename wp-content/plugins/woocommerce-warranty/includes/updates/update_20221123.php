<?php

namespace WooCommerce\Warranty;

use WooCommerce_Warranty;

defined( 'ABSPATH' ) || exit;

/**
 * Class Update_20221123
 *
 * Purpose of this update:
 * 1. Move all existing warranty request customer uploads and shipping label uploads to the new warranty_uploads folder.
 * 2. Randomize existing warranty request customer uploads' and shipping label uploads' filenames.
 *
 * @package WooCommerce\Warranty
 */
class Update_20221123 {

	/**
	 * Database version.
	 *
	 * @var string
	 */
	private static $db_version = '20221123';

	/**
	 * List of warranty requests.
	 *
	 * @var array
	 */
	private $warranty_request_ids = array();

	/**
	 * List of shipping labels.
	 *
	 * @var array
	 */
	private $shipping_label_ids = array();

	/**
	 * List of upload fields name.
	 *
	 * @var array
	 */
	private $customer_upload_field_meta_keys = array();

	/**
	 * Initialize update.
	 */
	public function __construct() {
		$this->set_warranty_request_ids();
		$this->set_warranty_shipping_label_ids();
		$this->set_customer_upload_field_meta_keys();
		$this->run_updates();
		$this->complete_updates();
	}

	/**
	 * Set warranty request ids.
	 *
	 * @return void
	 */
	private function set_warranty_request_ids() {
		$this->warranty_request_ids = get_posts(
			array(
				'post_type'      => 'warranty_request',
				'posts_per_page' => - 1,
				'fields'         => 'ids',
			)
		);
	}

	/**
	 * A shipping label upload creates a WP attachment and the attachment ID is
	 * stored in a post meta field '_warranty_shipping_label' on the
	 * warranty_request post type.
	 *
	 * So, we can just loop through the warranty request IDs and grab those
	 * attachment IDs. We'll use those to easily find, move each file and
	 * update its path.
	 *
	 * @return void
	 */
	private function set_warranty_shipping_label_ids() {
		if ( empty( $this->warranty_request_ids ) ) {
			return;
		}

		foreach ( $this->warranty_request_ids as $request_id ) {
			$shipping_label_id = intval( get_post_meta( $request_id, '_warranty_shipping_label', true ) );
			if ( empty( $shipping_label_id ) ) {
				continue;
			}

			$this->shipping_label_ids[] = $shipping_label_id;
		}
	}

	/**
	 * Administrators have the option to add file upload fields to the warranty
	 * request form through which customers submit warranty requests.
	 *
	 * Because the warranty request form is flexible (using a form builder UI),
	 * the fields added to the form are saved in the 'warranty_form' WP option.
	 * Each field input is saved with the following structure:
	 * {"key":1828197948,"type":"file"}
	 *
	 * When a file is uploaded from the warranty request form, the path is saved to
	 * the warranty_request post type meta using the field input key prefixed with
	 * '_field_'. So for the example field input above, the post meta key
	 * would be '_field_1828197948'.
	 *
	 * So, we'll save any field inputs of type 'file' so we can search each
	 * warranty_request's post meta for those files and move them to the
	 * new uploads directory.
	 *
	 * @return void
	 */
	private function set_customer_upload_field_meta_keys() {
		$warranty_form = get_option( 'warranty_form' );
		$inputs        = json_decode( $warranty_form['inputs'] );

		if ( empty( $inputs ) ) {
			return;
		}

		foreach ( $inputs as $input ) {
			if ( empty( $input->type ) || empty( $input->key ) ) {
				continue;
			}

			if ( 'file' !== $input->type ) {
				continue;
			}

			$this->customer_upload_field_meta_keys[] = '_field_' . $input->key;
		}
	}

	/**
	 * Run all the necessary updates.
	 *
	 * @return void
	 */
	private function run_updates() {
		if ( empty( $this->warranty_request_ids ) ) {
			return;
		}

		$this->maybe_move_and_rename_shipping_labels();
		$this->maybe_move_and_rename_customer_uploads();
	}

	/**
	 * Find all existing shipping labels that aren't in the
	 * warranty_uploads/labels directory and move them and
	 * update their corresponding WP attachment.
	 *
	 * @return void
	 */
	private function maybe_move_and_rename_shipping_labels() {
		if ( empty( $this->shipping_label_ids ) ) {
			return;
		}

		$subdir = 'labels';

		foreach ( $this->shipping_label_ids as $attachment_id ) {
			$attachment = get_post( $attachment_id );
			if ( ! is_a( $attachment, 'WP_Post' ) ) {
				continue;
			}

			$attached_file = get_attached_file( $attachment_id, true );
			if ( ! $attached_file ) {
				continue;
			}

			$upload_dir = WooCommerce_Warranty::get_warranty_uploads_directory( $subdir );

			// Don't move files that are already in the warranty_uploads directory.
			if ( false !== strpos( $attached_file, $upload_dir ) ) {
				continue;
			}

			// Randomize the filename for added security.
			$filename = WooCommerce_Warranty::get_randomized_filename( $attached_file );

			// Make sure the filename is unique.
			$filename = wp_unique_filename( $upload_dir, $filename );

			// Set the destination path.
			$destination = $upload_dir . $filename;

			if ( rename( $attached_file, $destination ) ) {
				update_attached_file( $attachment_id, $destination );

				// Update the attachment metadata.
				$updated_metadata = wp_generate_attachment_metadata( $attachment_id, $destination );
				wp_update_attachment_metadata( $attachment_id, $updated_metadata );

				// Update the attachment post.
				WooCommerce_Warranty::update_attachment_guid_for_warranty_upload( $attachment_id, $destination, $subdir );
			}
		}
	}

	/**
	 * Find all existing customer uploads that aren't in the
	 * warranty_uploads/customer directory and move them
	 * and update the corresponding post meta field.
	 *
	 * @return void
	 */
	private function maybe_move_and_rename_customer_uploads() {
		if ( empty( $this->customer_upload_field_meta_keys ) ) {
			return;
		}

		foreach ( $this->warranty_request_ids as $request_id ) {
			foreach ( $this->customer_upload_field_meta_keys as $meta_key ) {
				$file = get_post_meta( $request_id, $meta_key, true );

				if ( empty( $file ) ) {
					continue;
				}

				$wp_uploads = wp_get_upload_dir();
				$file       = $wp_uploads['basedir'] . $file;

				if ( ! file_exists( $file ) ) {
					continue;
				}

				$upload_dir = WooCommerce_Warranty::get_warranty_uploads_directory( 'customer' );

				// Don't move files that are already in the warranty_uploads directory.
				if ( false !== strpos( $file, $upload_dir ) ) {
					continue;
				}

				// Randomize the filename for added security.
				$filename = WooCommerce_Warranty::get_randomized_filename( $file );

				// Make sure the filename is unique.
				$filename = wp_unique_filename( $upload_dir, $filename );

				// Set the destination path.
				$destination = $upload_dir . $filename;

				if ( rename( $file, $destination ) ) {
					update_post_meta( $request_id, $meta_key, $destination );
				}
			}
		}
	}

	/**
	 * Set various flags so the plugin doesn't try to
	 * run updates after this is done. Redirects go here
	 * as well.
	 *
	 * @return void
	 */
	private function complete_updates() {
		delete_option( 'warranty_needs_update' );
		update_option( 'warranty_db_version', self::$db_version );
		wp_safe_redirect( wp_nonce_url( admin_url( 'admin.php?page=warranties&warranty-data-updated=true' ), 'wc_warranty_updater' ) );
		exit;
	}

}

new Update_20221123();
