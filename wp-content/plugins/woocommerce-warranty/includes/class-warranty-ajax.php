<?php

require_once 'trait-warranty-util.php';

use WooCommerce\Warranty\Warranty_Util;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Warranty_Ajax {

	use Warranty_Util;

	/**
	 * Hook in the AJAX events
	 */
	public static function init() {
		// warranty_EVENT => nopriv
		$events = array(
			'user_search'                => false,
			'search_for_email'           => false,
			'update_request_fragment'    => false,
			'update_request_status'      => false,
			'delete_request'             => false,
			'add_note'                   => false,
			'delete_note'                => false,
			'request_tracking'           => false,
			'set_tracking'               => false,
			'update_inline'              => false,
			'return_inventory'           => false,
			'refund_item'                => false,
			'send_coupon'                => false,
			'product_warranty_update'    => false,
			'update_category_defaults'   => false,
			'migrate_products'           => false,
			'shipping_label_file_upload' => false,
		);

		foreach ( $events as $event => $nopriv ) {
			add_action( 'wp_ajax_warranty_' . $event, array( __CLASS__, $event ) );

			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_warranty_' . $event, array( __CLASS__, $event ) );
			}
		}
	}

	/**
	 * AJAX handler for searching for users
	 *
	 * This method looks for partial user_email and/or user ID matches,
	 * formatted as an array of unique customer keys with values being formed as:
	 *
	 *     first_name last_name <user_email>
	 *
	 * The resulting array is then JSON-encoded before it is sent back
	 */
	public static function user_search() {
		global $wpdb;
		$get_data  = warranty_request_get_data();
		$term      = isset( $get_data['term'] ) ? $get_data['term'] : false;
		$results   = array();
		$all_users = array();

		if ( is_numeric( $term ) ) {
			$results = $wpdb->get_results(
				$wpdb->prepare(
					"
				SELECT *
				FROM {$wpdb->users}
				WHERE ID LIKE %s",
					$term . '%'
				)
			);
		} else {
			$results = $wpdb->get_results(
				$wpdb->prepare(
					"
				SELECT *
				FROM {$wpdb->users}
				WHERE user_email LIKE %s
				OR user_login LIKE %s
				OR display_name LIKE %s",
					'%' . $term . '%',
					'%' . $term . '%',
					'%' . $term . '%'
				)
			);
		}

		if ( $results ) {
			foreach ( $results as $result ) {
				$all_users[ $result->ID ] = $result->display_name . ' (#' . $result->ID . ')';
			}
		}

		// Suppress errors as this table may not exist if they don't use the follow-up-emails extension.
		$wpdb->suppress_errors();

		// guest email search.
		$results2 = $wpdb->get_results(
			$wpdb->prepare(
				"
			SELECT id, email_address
			FROM {$wpdb->prefix}followup_customers
			WHERE user_id = 0
			AND email_address LIKE %s",
				'%' . $term . '%'
			)
		);

		$wpdb->suppress_errors( false );

		if ( $results2 ) {
			foreach ( $results2 as $result ) {
				$all_users[ $result->email_address ] = $result->email_address . ' (Guest #' . $result->id . ')';
			}
		}

		$all_users = apply_filters( 'warranty_user_search', $all_users, $term );

		wp_send_json( $all_users );
	}

	/**
	 * AJAX handler for searching for existing email addresses
	 *
	 * This method looks for partial user_email and/or display_name matches,
	 * as well as fuzzy first_name and last_name matches. The results are
	 * formatted as an array of unique customer keys with values being formed as:
	 *
	 * first_name last_name <user_email>
	 *
	 * The resulting array is then JSON-encoded before it is sent back
	 */
	public static function search_for_email() {
		global $wpdb;
		$get_data   = warranty_request_get_data();
		$term       = isset( $get_data['term'] ) ? $get_data['term'] : false;
		$results    = array();
		$all_emails = array();

		// Registered users.
		$email_term = $term . '%';
		$name_term  = '%' . $term . '%';

		$email_results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT DISTINCT u.ID, u.display_name, u.user_email
				FROM {$wpdb->prefix}users u
				WHERE (
					user_email LIKE %s OR display_name LIKE %s
				)",
				$email_term,
				$name_term
			)
		);

		if ( $email_results ) {
			foreach ( $email_results as $result ) {
				$all_emails[] = $result->user_email;

				$first_name = get_user_meta( $result->ID, 'billing_first_name', true );
				$last_name  = get_user_meta( $result->ID, 'billing_last_name', true );

				if ( empty( $first_name ) && empty( $last_name ) ) {
					$first_name = $result->display_name;
				}

				$results[ $result->user_email ] = $first_name . ' ' . $last_name . ' &lt;' . $result->user_email . '&gt;';
			}
		}

		// Full name (First Last format).
		$name_results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT DISTINCT m1.user_id, u.user_email, m1.meta_value AS first_name, m2.meta_value AS last_name
				FROM {$wpdb->prefix}users u, {$wpdb->prefix}usermeta m1, {$wpdb->prefix}usermeta m2
				WHERE u.ID = m1.user_id
				AND m1.user_id = m2.user_id
				AND m1.meta_key =  'first_name'
				AND m2.meta_key =  'last_name'
				AND CONCAT_WS(  ' ', m1.meta_value, m2.meta_value ) LIKE %s",
				'%' . $wpdb->esc_like( $term ) . '%'
			)
		);

		if ( $name_results ) {
			foreach ( $name_results as $result ) {
				if ( in_array( $result->user_email, $all_emails ) ) {
					continue;
				}

				$all_emails[]                   = $result->user_email;
				$results[ $result->user_email ] = $result->first_name . ' ' . $result->last_name . ' &lt;' . $result->user_email . '&gt;';
			}
		}

		$results = apply_filters( 'warranty_email_query', $results, $term, $all_emails );

		wp_send_json( $results );
	}

	/**
	 * Update a fragment of a warranty request
	 */
	public static function update_request_fragment() {
		$post_data = wc_clean( wp_unslash( $_REQUEST ) );
		$type      = $post_data['type'];
		$message   = '';

		if ( 'change_status' === $type ) {
			$new_status = $post_data['status'];
			$request_id = $post_data['request_id'];

			warranty_update_request( $request_id, array( 'status' => $new_status ) );

			$message = __( 'Request status updated', 'wc_warranty' );
		} elseif ( 'generate_rma' === $type ) {
			// using GET.
			$request_id = $post_data['request_id'];
			$code       = warranty_generate_rma_code();

			warranty_update_request( $request_id, array( 'code' => $code ) );

			$message = __( 'RMA Code generated successfully', 'wc_warranty' );
		} elseif ( 'request_code' === $type ) {
			$request_id = $post_data['request_id'];

			warranty_update_request( $request_id, array( 'request_tracking_code' => 'y' ) );

			warranty_send_emails( $request_id, 'request_tracking' );

			$message = __( 'Tracking code requested', 'wc_warranty' );
		} elseif ( 'set_return_tracking' === $type ) {
			$request_id = $post_data['request_id'];
			$provider   = isset( $post_data['return_tracking_provider'] ) ? $post_data['return_tracking_provider'] : false;
			$code       = $post_data['return_tracking_code'];

			$data['return_tracking_code'] = $code;

			if ( false !== $provider ) {
				$data['return_tracking_provider'] = $provider;
			}

			warranty_update_request( $request_id, $data );

			$message = __( 'Return tracking code updated', 'wc_warranty' );
		}

		if ( $message ) {
			$return = 'admin.php?page=warranties&updated=' . urlencode( $message );
			die( $return );
		}
	}

	/**
	 * Update a request's status and return the available actions
	 * based on the new status
	 */
	public static function update_request_status() {
		check_admin_referer( 'warranty_update_status' );

		$new_status = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : '';
		$request_id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;

		warranty_update_request( $request_id, array( 'status' => $new_status ) );

		wp_send_json(
			array(
				'status'  => 'OK',
				'message' => __( 'Status updated', 'wc_warranty' ),
				'actions' => Warranty_Admin::get_warranty_actions( $request_id, true ),
			)
		);
	}

	/**
	 * Handle delete requests
	 */
	public static function delete_request() {
		check_admin_referer( 'warranty_delete' );

		$request = warranty_request_data();

		$id = isset( $request['id'] ) ? absint( $request['id'] ) : 0;

		if ( ! warranty_user_has_access( wp_get_current_user(), false, $id ) ) {
			return false;
		}
		wp_delete_post( $id, true );

		die( 1 );
	}

	/**
	 * Add a comment to a request
	 */
	public static function add_note() {
		$request    = warranty_request_data();
		$request_id = isset( $request['request'] ) ? absint( $request['request'] ) : 0;
		$user       = wp_get_current_user();
		$note       = isset( $request['note'] ) ? $request['note'] : '';

		if ( ! warranty_load( $request_id ) ) {
			die;
		}

		$data = array(
			'comment_post_ID'      => $request_id,
			'comment_author'       => $user->display_name,
			'comment_author_email' => $user->user_email,
			'comment_author_url'   => '',
			'comment_content'      => $note,
			'comment_type'         => 'wc_warranty_note',
			'comment_parent'       => 0,
			'user_id'              => $user->ID,
			'comment_date'         => current_time( 'mysql' ),
			'comment_approved'     => 1,
		);

		wp_new_comment( $data );

		ob_start();
		include WooCommerce_Warranty::$base_path . 'templates/list-item-notes.php';
		$list = ob_get_clean();

		die( $list );
	}

	/**
	 * Delete a request note
	 */
	public static function delete_note() {
		$request    = warranty_request_data();
		$request_id = isset( $request['request'] ) ? absint( $request['request'] ) : 0;
		$note       = isset( $request['note_id'] ) ? absint( $request['note_id'] ) : 0;

		wp_delete_comment( $note, true );

		ob_start();
		include WooCommerce_Warranty::$base_path . 'templates/list-item-notes.php';
		$list = ob_get_clean();

		die( $list );
	}

	/**
	 * Send a tracking request to the customer
	 */
	public static function request_tracking() {
		ob_start();

		$request_id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		warranty_send_tracking_request( $request_id );

		ob_end_clean();

		wp_send_json( array( 'status' => 'OK' ) );
	}

	/**
	 * Set the return shipping details
	 */
	public static function set_tracking() {
		ob_start();

		$request_id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		$provider   = ! empty( $_POST['return_tracking_provider'] ) ? sanitize_text_field( wp_unslash( $_POST['return_tracking_provider'] ) ) : false;
		$code       = isset( $_POST['tracking'] ) ? sanitize_text_field( wp_unslash( $_POST['tracking'] ) ) : '';

		$data['return_tracking_code'] = $code;

		if ( false !== $provider ) {
			$data['return_tracking_provider'] = $provider;
		}

		warranty_update_request( $request_id, $data );

		ob_end_clean();

		wp_send_json(
			array(
				'status'  => 'OK',
				'message' => __( 'Shipping tracking details saved', 'wc_warranty' ),
			)
		);
	}

	/**
	 * Update an RMA request from the shop order screen via AJAX
	 */
	public static function update_inline() {
		$post_data = warranty_request_post_data();
		$id        = isset( $post_data['id'] ) ? absint( $post_data['id'] ) : 0;
		$rma       = warranty_load( $id );

		if ( ! $rma ) {
			wp_send_json(
				array(
					'status'  => 'ERROR',
					'message' => 'Invalid Warranty Request or You don\'t have access permissions.',
				)
			);
		}
		$data = array();

		if ( ! wp_verify_nonce( $post_data['_wpnonce'], 'warranty_update' ) ) {
			wp_send_json(
				array(
					'status'  => 'ERROR',
					'message' => 'Invalid referrer. Please reload the page and try again',
				)
			);
		}

		if ( ! empty( $post_data['status'] ) && $post_data['status'] !== $rma['status'] ) {
			$data['status'] = $post_data['status'];
		}

		if ( ! empty( $post_data['shipping_label_image_id'] ) ) {
			$data['warranty_shipping_label'] = $post_data['shipping_label_image_id'];
		}

		if ( isset( $post_data['return_tracking_code'] ) ) {
			$data['return_tracking_code'] = $post_data['return_tracking_code'];
		}

		if ( ! empty( $post_data['return_tracking_provider'] ) ) {
			$data['return_tracking_provider'] = $post_data['return_tracking_provider'];
		}

		if ( ! empty( $data ) ) {
			warranty_update_request( $id, $data );
		}

		if ( ! empty( $post_data['request_tracking'] ) ) {
			warranty_send_tracking_request( $id );
		}

		wp_send_json(
			array(
				'status'  => 'OK',
				'message' => __( 'RMA request updated', 'wc_warranty' ),
				'actions' => Warranty_Admin::get_warranty_actions( $id, true ),
			)
		);
	}

	/**
	 * Return the stock if stock management is enabled
	 */
	public static function return_inventory() {
		check_admin_referer( 'warranty_return_inventory' );

		$request_id = absint( $_REQUEST['id'] );

		warranty_return_product_stock( $request_id );

		if ( false === warranty_update_request( $request_id, array( 'returned' => 'yes' ) ) ) {
			wp_send_json(
				array(
					'status'  => 'ERROR',
					'message' => __( 'Refund action failed. Invalid Warranty Request ID given or You don\'t have access permissions.', 'wc_warranty' ),
				)
			);
		}

		wp_send_json(
			array(
				'status'  => 'OK',
				'message' => __( 'Product stock returned.', 'wc_warranty' ),
			)
		);
	}

	/**
	 * Process refund requests
	 */
	public static function refund_item() {
		check_admin_referer( 'warranty_update' );

		$request_id = absint( $_REQUEST['id'] );
		$amount     = ! empty( $_REQUEST['amount'] ) ? $_REQUEST['amount'] : null;

		$refund = warranty_refund_item( $request_id, $amount );

		if ( is_wp_error( $refund ) ) {
			wp_send_json(
				array(
					'status'  => 'ERROR',
					'message' => $refund->get_error_message(),
				)
			);
		} else {
			wp_send_json(
				array(
					'status'  => 'OK',
					'message' => __( 'Item marked as Refunded', 'wc_warranty' ),
				)
			);
		}
	}

	/**
	 * Send coupon as a refund
	 */
	public static function send_coupon() {
		Warranty_Coupons::send_coupon();
	}

	/**
	 * Update a product's warranty details and return the new warranty string/description
	 */
	public function product_warranty_update() {
		$post_data     = warranty_request_post_data();

		// Make sure we have an integer for the product ID.
		$product_id = ! empty( $post_data['id'] ) ? absint( $post_data['id'] ) : false;
		if ( ! $product_id ) {
			wp_send_json(
				array(
					'success' => false,
					'message' => esc_html__( 'Missing Product ID.', 'woocommerce-warranty' ),
				)
			);
			die();
		}

		// Make sure the product ID is legit.
		$product = wc_get_product( $product_id );
		if ( ! $product instanceof WC_Product ) {
			wp_send_json(
				array(
					'success' => false,
					// translators: Product ID.
					'message' => sprintf( esc_html__( 'Provided ID: %d is not valid WooCommerce Product ID', 'woocommerce-warranty' ), $product_id ),
				)
			);
			die();
		}

		// Make sure we have a valid nonce token.
		if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( sanitize_key( $_POST['security'] ), 'warranty_update_product-' . $product_id ) ) {
			wp_send_json(
				array(
					'success' => false,
					'message' => esc_html__( 'Edit failed. Please refresh the page and retry.', 'woocommerce-warranty' ),
				)
			);
			die();
		}

		$default = ! empty( $post_data['warranty_default'] ) ? $post_data['warranty_default'] : array();
		if ( ! empty( $default[ $product_id ] ) && 'yes' === $default[ $product_id ] ) {
			$product->delete_meta_data( '_warranty' );
			$product->save();

			wp_send_json(
				array(
					'success' => true,
					'html'    => esc_html( warranty_get_warranty_string( $product_id ) ),
				)
			);
			die();
		}

		// If warranty_type is empty, we have no warranty info, so exit.
		$type = ! empty( $post_data['warranty_type'] ) ? $post_data['warranty_type'] : array();
		if ( empty( $type ) ) {
			wp_send_json(
				array(
					'success' => false,
					'message' => esc_html__( 'Edit failed. Warranty type is not set.', 'woocommerce-warranty' ),
				)
			);
			die();
		}

		$warranty = self::build_warranty_array_inside_loop( $post_data, $type, $product_id );
		if ( empty( $warranty ) ) {
			wp_send_json(
				array(
					'success' => false,
					'message' => esc_html__( 'Edit failed. Warranty data is not set.', 'woocommerce-warranty' ),
				)
			);
			die();
		}

		$product->update_meta_data( '_warranty', $warranty );

		if ( isset( $post_data['warranty_label'] ) && ! empty( $post_data['warranty_label'][ $product_id ] ) ) {
			$product->update_meta_data( '_warranty_label', $post_data['warranty_label'][ $product_id ] );
		}

		$product->save();

		wp_send_json(
			array(
				'success' => true,
				'html'    => esc_html( warranty_get_warranty_string( $product_id ) ),
			)
		);
		die();
	}

	public static function update_category_defaults() {
		$warranties = Warranty_Settings::get_category_warranties_from_post();
		update_option( 'wc_warranty_categories', $warranties );

		$default_warranty = warranty_get_default_warranty();
		$categories       = get_terms( 'product_cat', array( 'hide_empty' => false ) );
		$strings          = array();

		foreach ( $categories as $category ) {
			$category_id = $category->term_id;
			$warranty    = isset( $warranties[ $category_id ] ) ? $warranties[ $category_id ] : array();

			if ( empty( $warranty ) ) {
				$warranty = $default_warranty;
			}

			$default = isset( $warranty['default'] ) ? $warranty['default'] : false;

			$strings[ $category_id ] = ( $default ) ? '<em>Default warranty</em>' : warranty_get_warranty_string( 0, $warranty );
		}

		wp_send_json( $strings );
	}

	/**
	 * Move RMA products into the new wc_warranty_products table
	 */
	public static function migrate_products() {
		global $wpdb;

		set_time_limit( 0 );

		/*
		 * We need to turn off the object cache temporarily while we deal with transients,
		 * as a workaround to a W3 Total Cache object caching bug.
		*/ global $_wp_using_ext_object_cache;

		$_wp_using_ext_object_cache_previous = $_wp_using_ext_object_cache;
		$_wp_using_ext_object_cache          = false;

		if ( empty( $_POST['cmd'] ) ) {
			wp_send_json( array( 'error' => 'CMD is missing' ) );
		}

		$cmd     = $_POST['cmd'];
		$session = ! empty( $_POST['update_session'] ) ? $_POST['update_session'] : '';

		if ( $cmd == 'start' ) {
			// count the total number of RMA to scan.

			// generate a new session id.
			$session = time();

			$count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'warranty_request'" );

			set_transient( 'warranty_migrate_products_page', 1 );

			// re-enable caching if it was previously enabled.
			$_wp_using_ext_object_cache = $_wp_using_ext_object_cache_previous;

			wp_send_json(
				array(
					'update_session' => $session,
					'total_items'    => $count,
				)
			);
		} else {
			ob_start();

			$page    = get_transient( 'warranty_migrate_products_page' );
			$limit   = 10;
			$results = array();

			if ( ! $page ) {
				$page = 1;
			}

			$items = get_posts(
				array(
					'post_type'      => 'warranty_request',
					'paged'          => $page,
					'posts_per_page' => $limit,
					'fields'         => 'ids',
				)
			);

			if ( empty( $items ) ) {
				$status = 'completed';
			} else {
				foreach ( $items as $request_id ) {
					$item = array(
						'request_id'       => $request_id,
						'product_id'       => get_post_meta( $request_id, '_product_id', true ),
						'order_item_index' => get_post_meta( $request_id, '_index', true ),
						'quantity'         => get_post_meta( $request_id, '_qty', true ),
					);

					$wpdb->insert( $wpdb->prefix . 'wc_warranty_products', $item );

					$results[] = array(
						'id'     => $request_id,
						'status' => 'success',
					);
				}

				$page ++;
				set_transient( 'warranty_migrate_products_page', $page );
				$status = 'partial';
			}

			ob_clean();

			// re-enable caching if it was previously enabled.
			$_wp_using_ext_object_cache = $_wp_using_ext_object_cache_previous;

			wp_send_json(
				array(
					'status'      => $status,
					'update_data' => $results,
					'session'     => $session,
				)
			);
		}
	}

	public static function shipping_label_file_upload() {
		// Define a default response.
		$response = array(
			'success' => false,
			'message' => esc_html__( 'There was an error uploading the file.', 'woocommerce-warranty' ),
		);

		if ( false === check_ajax_referer( 'shipping_label_image_file_upload', 'security', false ) ) {
			wp_send_json( $response );
		}

		// Try to get the uploaded file.
		$file = ! empty( $_FILES['warranty_upload'] ) ? $_FILES['warranty_upload'] : false;
		if ( ! $file || $file['error'] > 0 || ! is_uploaded_file( $file['tmp_name'] ) ) {
			wp_send_json( $response );
		}

		$filename = sanitize_file_name( $file['name'] );

		// Check WooCommerce_Warranty allowed MIME types.
		add_filter( 'upload_mimes', array( 'WooCommerce_Warranty', 'restrict_allowed_mime_types' ) );
		$validate_file = wp_check_filetype_and_ext( $file['tmp_name'], $filename );
		remove_filter( 'upload_mimes', array( 'WooCommerce_Warranty', 'restrict_allowed_mime_types' ) );

		// Check the file type, before we do anything further.
		if ( empty( $validate_file['ext'] ) || empty( $validate_file['type'] ) ) {
			$response['message'] = esc_html__( 'The file you selected is not permitted. Please select another.', 'wc_warranty' );
			wp_send_json( $response );
		}

		// Randomize the filename for added security.
		$filename = WooCommerce_Warranty::get_randomized_filename( $filename );

		$upload_dir  = WooCommerce_Warranty::get_warranty_uploads_directory( 'labels' );
		$filename    = wp_unique_filename( $upload_dir, $filename );
		$destination = $upload_dir . $filename;

		if ( move_uploaded_file( $file['tmp_name'], $destination ) ) {
			$attachment_id = WooCommerce_Warranty::insert_attachment_for_warranty_upload( $destination, 'labels' );
			if ( 0 === $attachment_id ) {
				$response['message'] = esc_html__( 'The file attachment could not be created.', 'wc_warranty' );
				wp_send_json( $response );
			}

			// Generate and update the attachment's metadata.
			$attach_data = wp_generate_attachment_metadata( $attachment_id, $destination );
			wp_update_attachment_metadata( $attachment_id, $attach_data );

			$response['success']  = true;
			$response['file_url'] = WooCommerce_Warranty::get_warranty_uploads_url( 'labels' ) . $filename;
			$response['file_id']  = $attachment_id;
		}

		wp_send_json( $response );
	}

}

Warranty_Ajax::init();
