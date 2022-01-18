<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * FUE_AJAX
 *
 * AJAX Event Handler
 */
class FUE_AJAX {

	/**
	 * Hook in methods
	 */
	public static function init() {

		// fue_EVENT => nopriv
		$ajax_events = array(
			'send_test_email'           => false,
			'clone_email'               => false,
			'get_post_custom_fields'    => false,
			'user_search'               => false,
			'admin_search'              => false,
			'search_for_email'          => false,
			'json_search_customers'     => false,
			'find_similar_emails'       => false,
			'toggle_email_status'       => false,
			'toggle_queue_status'       => false,
			'load_customer_notes'       => false,
			'add_customer_note'         => false,
			'delete_customer_note'      => false,
			'add_customer_reminder'     => false,
			'delete_customer_reminder'  => false,
			'schedule_manual_email'     => false,
			'archive_email'             => false,
			'unarchive_email'           => false,
			'update_email'              => false,
			'update_email_type'         => false,
			'get_email_variables_list'  => false,
			'get_email_details_html'    => false,
			'get_email_test_html'       => false,
			'load_template_source'      => false,
			'save_template_source'      => false,

			// testing bounce emails
			'bounce_emails_test'        => false,
			'bounce_emails_test_check'  => false,

			'verify_spf_dns'            => false,
			'generate_spf'              => false,
			'generate_dkim_keys'        => false,

			// send manual emails
			'send_manual_emails'        => false,
			'send_manual_email_batches' => false,

			// daily summary posts
			'count_daily_summary_posts' => false,
			'delete_daily_summary'      => false,

			// delete stats data
			'count_stats_data'  => false,
			'delete_stats_data' => false,

			// conversion to action-scheduler
			'scheduler_count_import_rows'       => false,
			'scheduler_do_import'               => false,
			'scheduler_import_start'            => false,
			'scheduler_import_complete'         => false,
			'clear_scheduled_actions'           => false,

			// woocommerce
			'wc_json_search_products_and_variations'=> false,
			'wc_json_search_subscription_products'  => false,
			'wc_json_search_coupons'                => false,
			'wc_product_has_children'               => false,
			'wc_order_import'                       => false,
			'wc_update_customer_order_total'        => false,
			'wc_disable_order_scan'                 => false,
			'wc_subscriptions_update'               => false,
			'wc_set_cart_email'                     => true,

			// sensei
			'sensei_search_courses'         => false,
			'sensei_search_lessons'         => false,
			'sensei_search_quizzes'         => false,
			'sensei_search_questions'       => false,

			// wootickets
			'wc_json_search_ticket_products'    => false,

			// bookings
			'wc_json_search_booking_products'   => false,

			// newsletter
			'create_list'                   => false,
			'update_list'                   => false,
			'delete_list'                   => false,
			'remove_subscriber_from_list'   => false,
			'update_account_subscriptions'  => false,
			'build_export_list'             => false,

			// data updates
			'migrate_logs'      => false,
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_fue_' . $ajax_event, array( __CLASS__, $ajax_event ) );

			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_fue_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			}
		}

	}

	/**
	 * AJAX send test email
	 */
	public static function send_test_email() {
		if ( ! isset( $_POST['nonce'] ) || ! check_ajax_referer( 'fue-send-manual', 'nonce' ) || ! current_user_can( 'manage_follow_up_emails' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$_POST = array_map('stripslashes_deep', $_POST);

		// capture all output
		ob_start();

		$id         = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : '';
		$recipient  = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$email      = new FUE_Email( $id );
		$subject    = (isset($_POST['subject'])) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : $email->subject;
		$message    = isset( $_POST['message'] ) ? wp_kses_post( wp_unslash( $_POST['message'] ) ) : '';
		$order_id   = (isset($_POST['order_id'])) ? absint( wp_unslash( $_POST['order_id'] ) ) : '';
		$product_id = (isset($_POST['product_id'])) ? absint( wp_unslash( $_POST['product_id'] ) ): '';
		$tracking   = (isset($_POST['tracking'])) ? sanitize_text_field( wp_unslash( $_POST['tracking'] ) ): '';

		$email_data = array(
			'test'          => true,
			'username'      => 'jdoe',
			'first_name'    => 'John',
			'last_name'     => 'Doe',
			'cname'         => 'John Doe',
			'user_id'       => '0',
			'order_id'      => $order_id,
			'product_id'    => $product_id,
			'email_to'      => $recipient,
			'tracking_code' => $tracking,
			'store_url'     => home_url(),
			'store_url_secure' => home_url( null, 'https' ),
			'store_name'    => get_bloginfo('name'),
			'unsubscribe'   => fue_get_unsubscribe_url(),
			'subject'       => $subject,
			'message'       => $message,
			'meta'          => array()
		);

		if ( !empty($email_data['tracking_code']) ) {
			parse_str( trim( $email_data['tracking_code'], '?' ), $codes );

			foreach ( $codes as $key => $val ) {
				$codes[$key] = urlencode($val);
			}

			if (! empty($codes) ) {
				$email_data['store_url']        = add_query_arg( $codes, $email_data['store_url'] );
				$email_data['store_url_secure'] = add_query_arg( $codes, $email_data['store_url_secure'] );
				$email_data['unsubscribe']      = add_query_arg( $codes, $email_data['unsubscribe'] );

				// look for links
				$replacer               = new FUE_Sending_Link_Replacement( 0, $email->id, $email_data['user_id'], $email_data['email_to'] );
				$email_data['message']  = preg_replace_callback('|\{link url=([^}]+)\}|', array($replacer, 'replace'), $email_data['message'] );

				// look for store_url with path
				$link_meta = array(
					'email_order_id'    => 0,
					'email_id'          => $email->id,
					'user_id'           => $email_data['user_id'],
					'user_email'        => $email_data['email_to'],
					'codes'             => $codes
				);
				fue_set_link_meta( $link_meta );

				$email_data['message']  = preg_replace_callback('|\{store_url=([^}]+)\}|', array( 'FUE_Sending_Mailer', 'add_test_store_url'), $email_data['message'] );
			}
		}

		Follow_Up_Emails::instance()->mailer->send_test_email( $email_data, $email );
		/* translators: %d Email ID. */
		fue_debug_log( sprintf( __( 'Sent test email for ID %d', 'follow_up_emails' ), $id ), $email_data );

		ob_end_clean();

		die("OK");
	}

	/**
	 * AJAX handler for cloning an email
	 */
	public static function clone_email() {
		if ( ! isset( $_POST['nonce'] ) || ! check_ajax_referer( 'duplicate_email', 'nonce' ) || ! current_user_can( 'manage_follow_up_emails' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$id   = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : '';
		$name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';

		$new_email_id = fue_clone_email($id, $name);

		// Set cloned status to active.
		$email = new FUE_Email( $new_email_id );
		$email->update_status( FUE_Email::STATUS_ACTIVE );

		if (! is_wp_error($new_email_id)) {
			$resp = array(
				'status'    => 'OK',
				'id'        => $new_email_id,
				'url'       => 'post.php?post='. $new_email_id .'&action=edit'
			);
		} else {
			$resp = array(
				'status'    => 'ERROR',
				'message'   => $new_email_id->get_error_message()
			);
		}

		wp_send_json( $resp );
	}

	/**
	 * SEEMS TO BE DEAD CODE.
	 * AJAX handler for getting all custom fields for a particular post.
	 *
	 * The resulting data is JSON-encoded before being sent to the browser
	 */
	public static function get_post_custom_fields() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$id     = isset($_POST['id']) ? absint( $_POST['id'] ) : 0;
		$meta   = get_post_custom($id);
		wp_send_json( $meta );
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
	 *
	 */
	public static function user_search() {
		if ( ! isset( $_GET['nonce'] ) || ! check_ajax_referer( 'customer_search', 'nonce' ) || ! current_user_can( 'manage_follow_up_emails' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		global $wpdb;
		$term       = isset( $_GET['term'] ) ? sanitize_text_field( wp_unslash( $_GET['term'] ) ) : '';
		$results    = array();
		$all_users  = array();

		if ( is_numeric( $term ) ) {
			$results = $wpdb->get_results( $wpdb->prepare("
				SELECT *
				FROM {$wpdb->users}
				WHERE ID LIKE %s",
				$term .'%'
			) );
		} else {
			$results = $wpdb->get_results( $wpdb->prepare("
				SELECT *
				FROM {$wpdb->users}
				WHERE user_email LIKE %s
				OR user_login LIKE %s
				OR display_name LIKE %s",
				'%'. $term .'%',
				'%'. $term .'%',
				'%'. $term .'%'
			) );
		}

		if ( $results ) {
			foreach ( $results as $result ) {
				$all_users[ $result->ID ] = $result->display_name .' (#'. $result->ID .')';
			}
		}

		// guest email search
		$results2 = $wpdb->get_results( $wpdb->prepare("
			SELECT id, email_address
			FROM {$wpdb->prefix}followup_customers
			WHERE user_id = 0
			AND email_address LIKE %s",
			'%'. $term .'%'
		));

		if ( $results2 ) {
			foreach ( $results2 as $result ) {
				$all_users[ $result->email_address ] = $result->email_address .' (Guest #'. $result->id .')';
			}
		}

		$all_users = apply_filters( 'fue_user_search', $all_users, $term );

		wp_send_json( $all_users );
	}

	/**
	 * SEEMS TO BE DEAD CODE.
	 * AJAX handler for searching for admins/managers
	 *
	 * This method looks for partial user_email and/or user ID matches,
	 * formatted as an array of unique customer keys with values being formed as:
	 *
	 *     first_name last_name <user_email>
	 *
	 * The resulting array is then JSON-encoded before it is sent back
	 *
	 */
	public static function admin_search() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$term = isset( $_GET['term'] ) ? sanitize_text_field( wp_unslash( $_GET['term'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

		if ( empty( $term ) ) {
			die();
		}

		$found_users = array();

		$admin_query = new WP_User_Query( apply_filters( 'fue_admin_search_query', array(
			'role'           => 'administrator',
			'fields'         => 'all',
			'orderby'        => 'display_name',
			'search'         => '*' . $term . '*',
			'search_columns' => array( 'ID', 'user_login', 'user_email', 'user_nicename' )
		) ) );

		$wc_manager_query = new WP_User_Query( apply_filters( 'fue_wc_manager_search_query', array(
			'role'           => 'shop_manager',
			'fields'         => 'all',
			'orderby'        => 'display_name',
			'search'         => '*' . $term . '*',
			'search_columns' => array( 'ID', 'user_login', 'user_email', 'user_nicename' )
		) ) );

		$fue_manager_query = new WP_User_Query( apply_filters( 'fue_fue_manager_search_query', array(
			'role'           => 'fue_manager',
			'fields'         => 'all',
			'orderby'        => 'display_name',
			'search'         => '*' . $term . '*',
			'search_columns' => array( 'ID', 'user_login', 'user_email', 'user_nicename' )
		) ) );

		$users = array_merge( $admin_query->get_results(), $wc_manager_query->get_results(), $fue_manager_query->get_results() );

		if ( ! empty( $users ) ) {
			foreach ( $users as $user ) {
				$found_users[ $user->ID ] = $user->display_name . ' (#' . $user->ID . ' &ndash; ' . sanitize_email( $user->user_email ) . ')';
			}
		}

		wp_send_json( $found_users );
	}

	/**
	 * SEEMS TO BE DEAD CODE.
	 * AJAX handler for searching for existing email addresses
	 *
	 * This method looks for partial user_email and/or display_name matches,
	 * as well as partial first_name and last_name matches. The results are
	 * formatted as an array of unique customer keys with values being formed as:
	 *
	 *     first_name last_name <user_email>
	 *
	 * The resulting array is then JSON-encoded before it is sent back
	 *
	 */
	public static function search_for_email() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		global $wpdb;
		$term = isset( $_GET['term'] ) ? sanitize_text_field( wp_unslash( $_GET['term'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$results    = array();
		$all_emails = array();

		// Registered users
		$email_term = $term .'%';
		$name_term  = '%'. $term .'%';

		$email_results = $wpdb->get_results( $wpdb->prepare("
			SELECT DISTINCT u.ID, u.display_name, u.user_email
			FROM {$wpdb->users} u
			WHERE (
				`user_email` LIKE %s OR display_name LIKE %s
			)
			", $email_term, $name_term) );

		if ( $email_results ) {
			foreach ( $email_results as $result ) {
				$all_emails[] = $result->user_email;

				$first_name = get_user_meta( $result->ID, 'billing_first_name', true );
				$last_name  = get_user_meta( $result->ID, 'billing_last_name', true );

				if ( empty($first_name) && empty($last_name) ) {
					$first_name = $result->display_name;
				}

				$key = $result->ID .'|'. $result->user_email .'|'. $first_name .' '. $last_name;

				$results[$key] = $first_name .' '. $last_name .' &lt;'. $result->user_email .'&gt;';
			}
		}

		// Full name (First Last format)
		$name_results = $wpdb->get_results("
			SELECT DISTINCT m1.user_id, u.user_email, m1.meta_value AS first_name, m2.meta_value AS last_name
			FROM {$wpdb->users} u, {$wpdb->usermeta} m1, {$wpdb->usermeta} m2
			WHERE u.ID = m1.user_id
			AND m1.user_id = m2.user_id
			AND m1.meta_key =  'first_name'
			AND m2.meta_key =  'last_name'
			AND CONCAT_WS(  ' ', m1.meta_value, m2.meta_value ) LIKE  '%{$term}%'
		");

		if ( $name_results ) {
			foreach ( $name_results as $result ) {
				if ( in_array($result->user_email, $all_emails) ) continue;

				$all_emails[] = $result->user_email;

				$key = $result->user_id .'|'. $result->user_email .'|'. $result->first_name .' '. $result->last_name;

				$results[$key] = $result->first_name .' '. $result->last_name .' &lt;'. $result->user_email .'&gt;';
			}
		}

		$results = apply_filters( 'fue_email_query', $results, $term, $all_emails );

		wp_send_json( $results );
	}

	/**
	 * AJAX handler for searching for existing email addresses
	 *
	 * This method looks for partial user_email and/or display_name matches,
	 * as well as partial first_name and last_name matches. The results are
	 * formatted as an array of unique customer keys with values being formed as:
	 *
	 *     first_name last_name <user_email>
	 *
	 * The resulting array is then JSON-encoded before it is sent back
	 *
	 */
	public static function json_search_customers() {
		if ( ! isset( $_GET['nonce'] ) || ! check_ajax_referer( 'customer_search', 'nonce' ) || ! current_user_can( 'manage_follow_up_emails' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		global $wpdb;
		$term       = isset( $_GET['term'] ) ? sanitize_text_field( wp_unslash( $_GET['term'] ) ) : '';
		$results    = array();
		$all_emails = array();

		// Registered users
		$email_term = $term .'%';
		$name_term  = '%'. $term .'%';

		$email_results = $wpdb->get_results( $wpdb->prepare("
			SELECT DISTINCT u.ID, u.display_name, u.user_email
			FROM {$wpdb->users} u
			WHERE (
				`user_email` LIKE %s OR display_name LIKE %s
			)
			", $email_term, $name_term) );

		if ( $email_results ) {
			foreach ( $email_results as $result ) {
				$all_emails[] = $result->ID;

				$first_name = get_user_meta( $result->ID, 'billing_first_name', true );
				$last_name  = get_user_meta( $result->ID, 'billing_last_name', true );

				if ( empty($first_name) && empty($last_name) ) {
					$first_name = $result->display_name;
				}

				$results[ $result->ID ] = $first_name .' '. $last_name .' &lt;'. $result->user_email .'&gt;';
			}
		}

		// Full name (First Last format)
		$name_results = $wpdb->get_results("
			SELECT DISTINCT m1.user_id, u.user_email, m1.meta_value AS first_name, m2.meta_value AS last_name
			FROM {$wpdb->users} u, {$wpdb->usermeta} m1, {$wpdb->usermeta} m2
			WHERE u.ID = m1.user_id
			AND m1.user_id = m2.user_id
			AND m1.meta_key =  'first_name'
			AND m2.meta_key =  'last_name'
			AND CONCAT_WS(  ' ', m1.meta_value, m2.meta_value ) LIKE  '%{$term}%'
		");

		if ( $name_results ) {
			foreach ( $name_results as $result ) {
				if ( in_array($result->user_email, $all_emails) ) continue;

				$all_emails[] = $result->user_email;

				$results[ $result->user_id ] = $result->first_name .' '. $result->last_name .' &lt;'. $result->user_email .'&gt;';
			}
		}

		wp_send_json( $results );
	}

	/**
	 * SEEMS TO BE DEAD CODE.
	 * Looks for duplicate and similar emails based on different parameters.
	 *
	 * An email is considered to be a duplicate when the duration, interval type,
	 * interval period, always send setting, and email type are exactly the same.
	 * A similar email will have the same properties as the duplicate email except
	 * for the interval period. Uses @see FUE_Email::has_duplicate_email() and
	 * @see FUE_Email::has_similar_email()
	 *
	 */
	public static function find_similar_emails() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$id             = isset($_POST['id']) ? absint( wp_unslash( $_POST['id'] ) ) : false; // phpcs:ignore WordPress.Security.NonceVerification
		$type           = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$interval       = isset( $_POST['interval'] ) ? absint( wp_unslash( $_POST['interval'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$duration       = isset( $_POST['interval_duration'] ) ? sanitize_text_field( wp_unslash( $_POST['interval_duration'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$interval_type  = isset( $_POST['interval_type'] ) ? sanitize_text_field( wp_unslash( $_POST['interval_type'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$product        = isset( $_POST['product_id'] ) ? absint( wp_unslash( $_POST['product_id'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
		$category       = isset( $_POST['category_id'] ) ? absint( wp_unslash( $_POST['category_id'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
		$always_send    = isset( $_POST['always_send'] ) ? absint( wp_unslash( $_POST['always_send'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification

		// skip manual emails
		if ( $type == 'manual' )
			die('');

		if ( $id ) {
			$email = new FUE_Email( $id );
		} else {
			$email = new FUE_Email();
		}

		$email->type                = $type;
		$email->interval_num        = $interval;
		$email->interval_duration   = $duration;
		$email->interval_type       = $interval_type;
		$email->always_send         = $always_send;
		$email->product_id          = $product;
		$email->category_id         = $category;

		if ( $email->has_duplicate_email() )
			die("DUPE");

		if ( $email->has_similar_email() )
			die("SIMILAR");

	}

	/**
	 * AJAX handler for toggling and email's status
	 */
	public static function toggle_email_status() {
		$email_id = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

		if ( ! isset( $email_id ) || ! isset( $_POST['nonce'] ) || ! check_ajax_referer( 'toggle_activate_email_' . $email_id, 'nonce' ) || ! current_user_can( 'manage_follow_up_emails' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$id     = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : '';
		$email  = new FUE_Email( $id );
		$status = $email->status;
		$resp   = array('ack' => 'OK');

		if ($status == FUE_Email::STATUS_INACTIVE || $status == FUE_Email::STATUS_ARCHIVED) {
			// activate
			$email->update_status( FUE_Email::STATUS_ACTIVE );
			/* translators: %d Email ID. */
			fue_debug_log( __( 'Activated email', 'follow_up_emails' ), $id );
			$resp['new_status'] = __( 'Active', 'follow_up_emails' );
			$resp['new_action'] = __( 'Deactivate', 'follow_up_emails' );
		} else {
			// deactivate
			$email->update_status( FUE_Email::STATUS_INACTIVE );
			/* translators: %d Email ID. */
			fue_debug_log( __( 'Deactivated email', 'follow_up_emails' ), $id );
			$resp['new_status'] = __( 'Inactive', 'follow_up_emails' );
			$resp['new_action'] = __( 'Activate', 'follow_up_emails' );
		}

		/*
		 * Since this action is using AJAX toggle, we need to
		 * generate a new nonce each time this function is called.
		 */
		$resp['new_nonce'] = wp_create_nonce( 'toggle_activate_email_' . $email_id );
		wp_send_json ($resp );
	}

	/**
	 * SEEMS TO BE DEAD CODE.
	 * AJAX handler for toggling an email queue's status
	 */
	public static function toggle_queue_status() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		global $wpdb;
		$id     = absint( $_POST['id'] );
		$status = $wpdb->get_var( $wpdb->prepare("SELECT status FROM {$wpdb->prefix}followup_email_orders WHERE id = %d", $id) );
		$resp   = array('ack' => 'OK');

		if ($status == 0) {
			// activate
			$wpdb->update($wpdb->prefix .'followup_email_orders', array('status' => 1), array('id' => $id));

			// re-create the task
			$param = array( $id );
			$send_time = $wpdb->get_var( $wpdb->prepare("SELECT send_on FROM {$wpdb->prefix}followup_email_orders WHERE id = %d", $id) );
			as_schedule_single_action( $send_time, 'sfn_followup_emails', $param, 'fue' );

			$resp['new_status'] = __('Queued', 'follow_up_emails');
			$resp['new_action'] = __('Do not send', 'follow_up_emails');
		} else {
			// deactivate
			$wpdb->update($wpdb->prefix .'followup_email_orders', array('status' => 0), array('id' => $id));

			// if using action-scheduler, delete the task
			$param = array( $id );
			as_unschedule_action( 'sfn_followup_emails',  $param, 'fue' );

			$resp['new_status'] = __('Suspended', 'follow_up_emails');
			$resp['new_action'] = __('Re-enable', 'follow_up_emails');
		}

		wp_send_json( $resp );
	}

	/**
	 * Get the HTML-formatted customer notes
	 */
	public static function load_customer_notes() {
		if ( ! isset( $_GET['nonce'] ) || ! check_ajax_referer( 'schedule_email', 'nonce' ) || ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$wpdb = Follow_Up_Emails::instance()->wpdb;
		$customer_id = isset( $_GET['customer'] ) ? absint( wp_unslash( $_GET['customer'] ) ) : '';

		$notes = $wpdb->get_results( $wpdb->prepare(
			"SELECT *
			FROM {$wpdb->prefix}followup_customer_notes
			WHERE followup_customer_id = %d
			ORDER BY date_added DESC",
			$customer_id
		) );

		$out = '';
		foreach ( $notes as $note ) {
			$author         = get_user_by( 'id', $note->author_id );
			$pretty_date    = date_i18n( get_option('date_format') .' '. get_option('time_format'), strtotime( $note->date_added ) );

			$out .= '
			<li class="note" rel="'. $note->id .'">
				<div class="note-content">
					<p>'. wp_kses_post( $note->note ) .'</p>
				</div>
				<p class="meta">
					'. sprintf( __('added by %s on <abbr title="%s" class="exact-date">%s</abbr>', 'follow_up_emails'), $author->display_name, $note->date_added, $pretty_date ) .'
					<a class="delete_note" href="#">'. __('Delete note', 'follow_up_emails') .'</a>
				</p>
			</li>';
		}

		wp_die( wp_kses_post( $out ) );

	}

	/**
	 * Store customer notes
	 */
	public static function add_customer_note() {
		if ( ! isset( $_POST['nonce'] ) || ! check_ajax_referer( 'add_notes', 'nonce' ) || ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$note        = isset( $_POST['note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['note'] ) ) : '';
		$customer_id = isset( $_POST['customer'] ) ? absint( wp_unslash( $_POST['customer'] ) ) : '';
		$author      = get_current_user_id();

		$note_id     = fue_add_customer_note( $customer_id, $note, $author );

		$now         = current_time( 'timestamp' );
		$author      = get_user_by( 'id', $author );
		$pretty_date = date_i18n( get_option('date_format') .' '. get_option('time_format'), $now );

		$out = '
			<li class="note" data-id="'. esc_attr( $note_id ) .'">
				<div class="note-content">
					<p>'. $note .'</p>
				</div>
				<p class="meta">
					'. sprintf( __('added by %s on <abbr title="%s" class="exact-date">%s</abbr>', 'follow_up_emails'), $author->display_name, $now, $pretty_date ) .'
					<a class="delete_note" href="#" data-nonce="' . esc_attr( wp_create_nonce( 'delete_note_' . $note_id ) ) . '">'. __('Delete note', 'follow_up_emails') .'</a>
				</p>
			</li>';

		wp_die( wp_kses_post( $out ) );
	}

	/**
	 * Delete Customer Note
	 */
	public static function delete_customer_note() {
		$note_id = isset( $_POST['note_id'] ) ? absint( wp_unslash( $_POST['note_id'] ) ) : ''; // phpcs:ignore

		if ( ! isset( $note_id ) || ! isset( $_POST['nonce'] ) || ! check_ajax_referer( 'delete_note_' . $note_id, 'nonce' ) || ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$wpdb   = Follow_Up_Emails::instance()->wpdb;

		$wpdb->delete( $wpdb->prefix .'followup_customer_notes', array( 'id' => $note_id ) );
		/* translators: %d Note ID. */
		fue_debug_log( __( 'Deleted customer note', 'follow_up_emails' ), $note_id );

		wp_die( 1 );
	}

	/**
	 * Add Customer Reminder
	 */
	public static function add_customer_reminder() {
		if ( ! isset( $_POST['nonce'] ) || ! check_ajax_referer( 'set_reminder', 'nonce' ) || ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$note           = isset( $_POST['note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['note'] ) ) : '';
		$customer_id    = isset( $_POST['customer'] ) ? absint( wp_unslash( $_POST['customer'] ) ) : '';
		$author         = wp_get_current_user();
		$assign         = isset( $_POST['assign'] ) ? sanitize_text_field( wp_unslash( $_POST['assign'] ) ) : false;
		$assignee       = isset( $_POST['assignee'] ) ? absint( wp_unslash( $_POST['assignee'] ) ) : false;
		$timestamp      = current_time( 'timestamp' );
		$interval       = isset( $_POST['interval'] ) ? absint( wp_unslash( $_POST['interval'] ) ) : false;
		$date           = isset( $_POST['date'] ) ? sanitize_text_field( wp_unslash( $_POST['date'] ) ) : false;
		$hour           = isset( $_POST['hour'] ) ? sanitize_text_field( wp_unslash( $_POST['hour'] ) ) : false;
		$minute         = isset( $_POST['minute'] ) ? sanitize_text_field( wp_unslash( $_POST['minute'] ) ) : false;
		$ampm           = isset( $_POST['ampm'] ) ? sanitize_text_field( wp_unslash( $_POST['ampm'] ) ) : false;

		if ( $assign && $assignee ) {
			$assignee = new WP_User( $assignee );
		} else {
			$assignee = $author;
		}

		if ( $interval ) {
			$add        = absint( $interval );
			$ts         = mktime( 9, 0, 0, date('m', $timestamp), date('d', $timestamp) + $add, date('Y', $timestamp) );
			$date       = date( 'm/d/Y', $ts );
			$date       .= ' 09:00';
		} else {
			$date = sprintf('%s %s:%s %s', $date, $hour, $minute, $ampm);
		}

		if ( ! $date ) {
			wp_die( '<li>' . esc_html__( 'Cannot create a reminder without a date', 'follow_up_emails' ) . '</li>' );
		}

		$customer   = fue_get_customer_by_id( $customer_id );
		$user       = get_user_by( 'id', $customer->user_id );
		$url        = admin_url('admin.php?page=followup-emails-reports&tab=reportuser_view&email=music%40test.com&user_id='. $user->ID);
		$subject    = sprintf( __('Reminder for Customer "%s"', 'follow_up_emails'), $user->display_name );
		$message    = sprintf( __('<p>Hello %s,</p><p>This is your reminder about customer "%s".</p>', 'follow_up_emails'), $assignee->display_name, $user->display_name );

		if ( !empty( $note ) ) {
			$message .= '<p>'. $note .'</p>';
		}

		$message .= sprintf(__('<p>To view your customer details for "%s", <a href="%s">click here</a>', 'follow_up_emails'), $user->display_name, $url);

		$scheduler = Follow_Up_Emails::instance()->scheduler;
		$item = new FUE_Sending_Queue_Item();
		$item->user_email   = $assignee->user_email;
		$item->is_sent      = 0;
		$item->send_on      = strtotime( $date );
		$item->meta         = array(
			'adhoc'             => true,
			'customer_reminder' => true,
			'author'            => $author->ID,
			'assignee'          => $assignee->ID,
			'customer'          => $customer_id,
			'note'              => $note,
			'subject'           => $subject,
			'message'           => $message
		);
		$item->save();
		fue_debug_log( __( 'Saved reminder', 'follow_up_emails' ), $item->meta );
		$scheduler->schedule_email( $item->id, strtotime( $date ) );

		$pre = '';
		if ( !empty( $note ) ) {
			$pre = '<pre>'. wp_kses_post( $note ) .'</pre>';
		}

		$ts = strtotime( $date );
		$date = date( get_option( 'date_format' ), $ts );
		$time = date( get_option( 'time_format' ), $ts );

		if ( $assignee->ID == $author->ID ) {
			$meta = sprintf( __('added by %s', 'follow_up_emails'), $author->display_name );
		} else {
			$meta = sprintf( __('assigned to %s by %s', 'follow_up_emails'), $assignee->display_name, $author->display_name );
		}

		$out = '
			<li class="reminder" data-id="'. esc_attr( $item->id ) .'">
				<div class="reminder-content">
					<p>
						'. sprintf( __('Reminder set for %s at %s', 'follow_up_emails'), $date, $time ) .'
					</p>
					'. $pre .'
				</div>
				<p class="meta">
					'. $meta .'
					<a class="delete_reminder" href="#" data-nonce="' . esc_attr( wp_create_nonce( "delete_reminder_" . $item->id ) ) . '">'. __('Delete', 'follow_up_emails') .'</a>
				</p>
			</li>';

		wp_die( wp_kses_post( $out ) );

	}

	/**
	 * Delete customer reminder
	 */
	public static function delete_customer_reminder() {
		$reminder_id = isset( $_POST['reminder_id'] ) ? absint( wp_unslash( $_POST['reminder_id'] ) ) : ''; // phpcs:ignore

		if ( ! isset( $reminder_id ) || ! isset( $_POST['nonce'] ) || ! check_ajax_referer( 'delete_reminder_' . $reminder_id, 'nonce' ) || ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		Follow_Up_Emails::instance()->scheduler->delete_item( $reminder_id );
		wp_die( 1 );
	}

	public static function schedule_manual_email() {
		if ( ! isset( $_POST['nonce'] ) || ! check_ajax_referer( 'schedule_email', 'nonce' ) || ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$data = isset( $_POST ) ? array_map( 'sanitize_text_field', array_map( 'wp_unslash', $_POST ) ) : array();
		$default = array(
			'user'                  => 0,
			'customer'              => 0,
			'email'                 => 0,
			'sending_schedule'      => 'now',
			'send_date'             => '',
			'send_hour'             => '1',
			'send_minute'           => '0',
			'send_ampm'             => 'am',
			'send_again'            => 0,
			'send_again_value'      => 0,
			'send_again_interval'   => 'minutes'
		);

		$data = wp_parse_args( $data, $default );

		if ( empty( $data['email'] ) ) {
			wp_send_json(array(
				'status'    => 'error',
				'message'   => __('Email is empty', 'follow_up_emails')
			));
		}

		if ( empty( $data['user'] ) ) {
			wp_send_json(array(
				'status'    => 'error',
				'message'   => __('Recipient is missing', 'follow_up_emails')
			));
		}

		if ( $data['sending_schedule'] == 'later' && empty($data['send_date']) ) {
			wp_send_json(array(
				'status'    => 'error',
				'message'   => __('Cannot schedule email without a send date', 'follow_up_emails')
			));
		}

		if ( $data['send_again'] && empty( $data['send_again_value'] ) ) {
			wp_send_json(array(
				'status'    => 'error',
				'message'   => __('Invalid resend schedule', 'follow_up_emails')
			));
		}

		$user   = new WP_User( $data['user'] );
		$key    = $user->ID .'|'. $user->user_email .'|';
		$recipients[ $key ] = array( $user->ID, $user->user_email, '' );
		$email  = new FUE_Email( $data['email'] );

		$schedule_email = ($data['sending_schedule'] == 'now') ? false : true;
		$send_again     = ($data['send_again'] == 1) ? true : false;

		$args = apply_filters( 'fue_manual_email_args', array(
			'email_id'          => $data['email'],
			'recipients'        => $recipients,
			'subject'           => $email->subject,
			'message'           => $email->message,
			'tracking'          => $email->tracking_code,
			'schedule_email'    => $schedule_email,
			'schedule_date'     => $data['send_date'],
			'schedule_hour'     => $data['send_hour'],
			'schedule_minute'   => $data['send_minute'],
			'schedule_ampm'     => $data['send_ampm'],
			'send_again'        => $send_again,
			'interval'          => $data['send_again_value'],
			'interval_duration' => $data['send_again_interval']
		), $data );

		$items = FUE_Sending_Scheduler::queue_manual_emails( $args );

		do_action( 'sfn_followup_emails' );

		$errors = array();
		$added  = array();

		if ( is_array( $items ) ) {
			$author = get_current_user_id();
			foreach ( $items as $item_id ) {
				if ( is_wp_error( $item_id ) ) {
					$errors[] = $item_id->get_error_message();
					continue;
				} else {
					$added[]    = $item_id;
					$item       = new FUE_Sending_Queue_Item( $item_id );
					$email      = new FUE_Email( $item->email_id );
					$send_on    = date_i18n( get_option('date_format') .' '. get_option('time_format'), $item->send_on );
					$note       = sprintf(
						__('Email queued: %s scheduled on %s', 'follow_up_emails'),
						$email->name,
						$send_on
					);
				}

				fue_add_customer_note( $data['customer'], $note, $author );
			}
		}

		if ( empty( $errors ) ) {
			wp_send_json(array(
				'status'    => 'ok',
				'message'   => __('Email added to the queue', 'follow_up_emails')
			));
		} else {
			$error = array_pop( $errors );
			wp_send_json(array(
			   'status'     => 'error',
			   'message'    => sprintf(
				   __('An error occurred while trying to schedule the email "%s". %s.', 'follow_up_emails'),
				   $email->name,
				   $error
			   )
			));
		}

	}

	/**
	 * AJAX handler for archiving an email
	 */
	public static function archive_email() {
		$email_id = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : ''; // phpcs:ignore

		if ( ! isset( $email_id ) || ! isset( $_POST['nonce'] ) || ! check_ajax_referer( 'archive_email_' . $email_id, 'nonce' ) || ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$email  = new FUE_Email( $email_id );
		$resp   = array('ack' => 'OK');
		$type   = $email->get_type();

		// deactivate
		$email->update_status( FUE_Email::STATUS_ARCHIVED );

		$resp['status_html'] = __('Archived', 'follow_up_emails') .'<br/><small><a href="#" class="unarchive" data-id="'. esc_attr( $email_id ) .'" data-key="'. $type .'">'. esc_html__('Activate', 'follow_up_emails') .'</a></small>';

		wp_send_json( $resp );
	}

	/**
	 * AJAX handler for unarchiving an email
	 */
	public static function unarchive_email() {
		$email_id = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : ''; // phpcs:ignore

		if ( ! isset( $email_id ) || ! isset( $_POST['nonce'] ) || ! check_ajax_referer( 'unarchive_email_' . $email_id, 'nonce' ) || ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$email  = new FUE_Email( $email_id );
		$resp   = array('ack' => 'OK');

		// activate
		$email->update_status( FUE_Email::STATUS_ACTIVE );

		$resp['status_html'] = esc_html__('Active', 'follow_up_emails') .'<br/><small><a href="#" class="toggle-activation" data-id="'. esc_attr( $email_id ) .'">'. __('Deactivate', 'follow_up_emails') .'</a></small>
		|
		<small><a href="#" class="archive-email" data-id="'. esc_attr( $email_id ) .'" data-key="'. esc_attr( $email->get_type() ) .'">'. esc_html__('Archive', 'follow_up_emails') .'</a></small>';

		wp_send_json( $resp );
	}

	/**
	 * Action that fires when the email updated from the email form
	 */
	public static function update_email() {
		if ( ! isset( $_POST['nonce'] ) || ! check_ajax_referer( 'update_email_template', 'nonce' ) || ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$id     = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : '';
		$email  = new FUE_Email( $id );

		if ( $email->exists() ) {
			$args = array(
				'ID'    => $id
			);

			if ( isset( $_POST['product_id'] ) ) {
				$args['product_id'] = absint( wp_unslash( $_POST['product_id'] ) );

				if ( !empty( $args['product_id'] ) ) {
					$args['category_id'] = 0;
				}
			} elseif ( isset( $_POST['category_id'] ) ) {
				$args['category_id'] = absint( wp_unslash( $_POST['category_id'] ) );

				if ( !empty( $args['category_id'] ) ) {
					$args['product_id'] = 0;
				}
			}

			if ( isset( $_POST['meta'] ) && is_array( $_POST['meta'] ) ) {
				$args['meta'] = wc_clean( array_map( 'wp_unslash', $_POST['meta'] ) );
			}

			if ( isset( $_POST['template'] ) ) {
				$args['template'] = sanitize_text_field( wp_unslash( $_POST['template'] ) );
			}

			fue_update_email( $args );
		}

		$updated_email = new FUE_Email( $id );

		if ( $updated_email->product_id > 0 ) {
			$updated_email->has_variations  = (!empty($updated_email->product_id) && FUE_Addon_Woocommerce::product_has_children($updated_email->product_id)) ? true : false;
			$updated_email->product_files   = FUE_Addon_Woocommerce::get_product_downloadables( $updated_email->product_id );
		}

		self::send_response( array('status' => 'success', 'email' => $updated_email ) );

	}

	/**
	 * Action that fires when the email type is changed in the email form
	 */
	public static function update_email_type() {
		if ( ! isset( $_POST['nonce'] ) || ! check_ajax_referer( 'follow_up_type', 'nonce' ) || ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$id     = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : '';
		$email  = new FUE_Email( $id );

		if ( $email->exists() ) {
			$args = array(
				'ID'    => $id,
				'type'  => isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : ''
			);
			fue_update_email( $args );
		}

		self::send_response( array('status' => 'success') );
	}

	/**
	 * Refresh the email variables list based on the email type.
	 */
	public static function get_email_variables_list() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$id     = isset( $_GET['id'] ) ? absint( wp_unslash( $_GET['id'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$email  = new FUE_Email( $id );

		ob_start();
		include FUE_TEMPLATES_DIR .'/meta-boxes/email-variables.php';
		$html = ob_get_clean();

		self::send_response( array(
			'status'    => 'success',
			'html'      => $html
		) );
	}

	/**
	 * Load the HTML for the Email Details metabox
	 */
	public static function get_email_details_html() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$id     = isset( $_GET['id'] ) ? absint( wp_unslash( $_GET['id'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$email  = new FUE_Email( $id );

		ob_start();
		include FUE_TEMPLATES_DIR .'/meta-boxes/email-details.php';
		$html = ob_get_clean();

		self::send_response( array(
			'status'    => 'success',
			'html'      => $html
		) );
	}

	/**
	 * Load the HTML for the Email Test metabox
	 */
	public static function get_email_test_html() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$id     = isset( $_GET['id'] ) ? absint( wp_unslash( $_GET['id'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$post   = get_post( $id );

		ob_start();
		FUE_Meta_Boxes::email_test_view( $post );
		$html = ob_get_clean();

		self::send_response( array(
			'status'    => 'success',
			'html'      => $html
		) );
	}

	/**
	 * Get the source of the requested template file
	 */
	public static function load_template_source() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$template = isset( $_GET['template'] ) ? sanitize_text_field( wp_unslash( $_GET['template'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

		if ( !wp_verify_nonce( $_GET['security'], 'get_template_html' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			wp_die( esc_html__('Error: Invalid request. Please try again.', 'follow_up_emails'));
		}

		$tpl = new FUE_Email_Template( $template );

		if ( is_wp_error( $tpl ) ) {
			wp_die( esc_html( 'Error: ' . $tpl->get_error_message() ) );
		}

		// Email template contents contains all HTML tags (including a header), so we can't escape the output.
		wp_die( $tpl->get_contents() ); // phpcs:ignore WordPress.Security.EscapeOutput
	}

	/**
	 * Update the specified email template
	 */
	public static function save_template_source() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		if ( ! wp_verify_nonce( $_POST['security'], 'save_template_html' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			self::send_response(array(
				'status'    => 'ERR',
				'error'     => __('Invalid request. Please try again.', 'follow_up_emails')
			));
		}

		$post = stripslashes_deep( $_POST );

		$tpl = new FUE_Email_Template( $post['template'] );

		if ( is_wp_error( $tpl ) ) {
			self::send_response(array(
				'status'    => 'ERR',
				'error'     => $tpl->get_error_message()
			));
		}

		$source = $post['source'];
		$file   = $tpl->get_path();

		file_put_contents( $file, $source );

		self::send_response(array(
			'status' => 'OK'
		));
	}

	/**
	 * Test bounce email server settings
	 */
	public static function bounce_emails_test() {
		if ( ! isset( $_GET['nonce'] ) || ! check_ajax_referer( 'test_bounce', 'nonce' ) || ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$bounce_handler     = new FUE_Bounce_Handler();
		$settings           = $bounce_handler->settings;
		$identifier         = 'fue_bounce_test_'.md5(uniqid());

		$return['success']      = true;
		$return['identifier']   = $identifier;

		$address = $settings['email'];
		$subject = 'Follow-Up Emails Bounce Test Mail';

		Follow_Up_Emails::instance()->mailer->mail( $address, $subject, 'Bounce Email ID: '. $identifier );

		/*
		 * Since this action is using ASYNC presses, we need to
		 * generate a new nonce each time this function is called.
		 */
		$return['new_nonce'] = wp_create_nonce( 'test_bounce' );

		wp_send_json( $return );
	}

	/**
	 * Check if the test bounce email made it to the POP3 email account
	 */
	public static function bounce_emails_test_check() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$bounce     = new FUE_Bounce_Handler();
		$settings   = $bounce->settings;

		$return['success'] = false;
		$return['msg'] = '';

		$passes     = isset( $_POST['passes'] ) ? intval( wp_unslash( $_POST['passes'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$identifier = isset( $_POST['identifier'] ) ? sanitize_text_field( wp_unslash( $_POST['identifier'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

		if( !$settings['handle_bounces'] ){
			$return['complete'] = true;
			wp_send_json( $return );
		}

		$pop3 = $bounce->connect();
		if ( is_wp_error( $pop3 ) ) {
			$return['complete'] = true;
			$return['msg'] = $pop3->get_error_message();
			wp_send_json( $return );
		}

		$return['success'] = true;
		$count = $pop3->COUNT;
		$return['msg'] = __('checking for new messages', 'follow_up_emails').str_repeat('.', $passes);

		if ( $passes > 20 ) {
			$return['complete'] = true;
			$return['msg'] = __('<span class="dashicons dashicons-no"></span> Unable to receive test message! Please check your settings.', 'follow_up_emails');
		}

		if ( false === $count || 0 === $count ) {
			if ( 0 === $count ) {
				$pop3->quit();
			}

			wp_send_json( $return );
		}

		for ( $i = 1; $i <= $count; $i++ ) {
			$message = $pop3->get( $i );

			if ( !$message ) {
				continue;
			}

			$message = implode( $message );

			if ( strpos( $message, $identifier ) !== false ) {
				$pop3->delete($i);
				$pop3->quit();
				$return['complete'] = true;
				$return['msg'] = __('<span class="dashicons dashicons-yes"></span> Your bounce server is configured!', 'follow_up_emails');

				wp_send_json( $return );
			} else {
				//$pop3->reset();
			}

		}

		$pop3->quit();

		wp_send_json( $return );
	}

	/**
	 * Verify that the SPF record is present in the domains DNS
	 */
	public static function verify_spf_dns() {
		if ( ! isset( $_GET['nonce'] ) || ! check_ajax_referer( 'validate_spf', 'nonce' ) || ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$domain = isset( $_GET['domain'] ) ? sanitize_text_field( wp_unslash( $_GET['domain'] ) ) : '';
		$ip     = ! empty( $_GET['ip'] ) ? sanitize_text_field( wp_unslash( $_GET['ip'] ) ) : '8.8.8.8';
		$records = false;
		$found   = false;

		require_once FUE_INC_DIR . '/lib/fue-utils/class-fue-dns-query.php';

		$dns_query  = new FUE_DNS_Query( $ip );
		$result     = $dns_query->Query($domain, 'TXT');

		if ( $result->count ) {
			$records = json_decode( json_encode( $result->results ), true );
		}

		if ( $records ) {
			foreach ( $records as $r ) {
				if( $r['typeid'] === 'TXT' && preg_match( '#v=spf1 #', $r['data'] ) ) {
					$found = $r;
					break;
				}
			}
		}

		if ( $found ) {
			$status = true;
		} else {
			$status = false;
		}

		self::send_response( array(
			'status'    => $status,
			'data'      => $found
		) );
	}

	/**
	 * Generate the SPF TXT entry
	 */
	public static function generate_spf() {
		if ( ! isset( $_GET['nonce'] ) || ! check_ajax_referer( 'generate_spf', 'nonce' ) || ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$domain = isset( $_GET['domain'] ) ? sanitize_text_field( wp_unslash( $_GET['domain'] ) ) : '';

		// In case they still entered absolute URL, convert it to domain
		$domain_parsed = parse_url( $domain, PHP_URL_HOST );
		if ( ! empty( $domain_parsed ) ) {
			$domain = $domain_parsed;
		}

		$ip     = ! empty( $_GET['ip'] ) ? sanitize_text_field( wp_unslash( $_GET['ip'] ) ) : '8.8.8.8';

		require_once FUE_INC_DIR . '/lib/fue-utils/class-fue-dns-query.php';

		$dns_query  = new FUE_DNS_Query( $ip );
		$result     = $dns_query->Query( $domain, 'A' );

		if ( $result ) {
			$result = wp_list_pluck( $result->results, 'data' );

			// The resolver may return a host as a result, so we only want to filter the IPs
			$result = array_filter( $result, function( $ip ) {
				return filter_var( $ip, FILTER_VALIDATE_IP );
			} );

			$spf = '<code>'. $domain .'</code> IN TXT <code>v=spf1 mx a ip4:'. implode( ' ip4:', $result ) .' ~all';
			$return = array(
				'status'    => true,
				'spf'       => $spf
			);
		} else {
			$return = array(
				'status'    => false,
				'error'     => $dns_query->lasterror
			);
		}

		self::send_response( $return );
	}

	public static function generate_dkim_keys() {
		if ( ! isset( $_POST['nonce'] ) || ! check_ajax_referer( 'generate_dkim_keys', 'nonce' ) || ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		if ( !function_exists('openssl_pkey_new') ) {
			self::send_response( array(
				'status' => false,
				'error'  => __('Please enable the OpenSSL extension in your PHP installation', 'follow_up_emails')
			) );
		}

		try {
			$key_size = isset($_POST['size']) ? intval( wp_unslash( $_POST['size']) ) : 1024;
			$result = openssl_pkey_new( array(
				'private_key_bits' => $key_size
			) );

			openssl_pkey_export( $result, $private_key );
			$public_key = openssl_pkey_get_details( $result );
			$public_key = $public_key["key"];

			// save the private key
			$file       = md5( $private_key ) .'.pem';
			$old_file   = get_option( 'fue_dkim_hash_file', false );

			WP_Filesystem();
			global $wp_filesystem;

			//remove old
			if ( $old_file && file_exists( $old_file ) ) {
				$wp_filesystem->delete( $old_file );
			}

			$uploads = wp_upload_dir();
			$path = $uploads['path'] .'/'. $file;

			if ( $wp_filesystem->put_contents( $path, $private_key ) ) {
				update_option( 'fue_dkim_hash_file', $path );
			}

			self::send_response( array(
				'status'    => true,
				'private_key'   => $private_key,
				'public_key'    => $public_key
			) );

		} catch ( Exception $e ) {
			add_settings_error( 'mymail_options', 'mymail_options', __('Not able to create new DKIM keys!', 'mymail'));

		}
	}

	/**
	 * Seems like DEAD CODE.
	 * Send manual emails in batches
	 */
	public static function send_manual_emails() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		if ( empty( $_POST['cmd'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			self::send_response( array( 'error' => 'CMD is missing' ) );
		}

		$cmd        = sanitize_text_field( wp_unslash( $_POST['cmd'] ) );
		$key        = !empty($_POST['key']) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
		$data       = FUE_Transients::get_transient( 'fue_manual_email_'. $key );
		$recipients = FUE_Transients::get_transient( 'fue_manual_email_recipients_'. $key );

		if ( ! $data || ! $recipients ) {
			self::send_response( array( 'error' => 'Data could not be loaded' ) );
		}

		if ( $cmd == 'start' ) {
			self::send_response( array( 'total_emails' => count( $recipients ) ) );
		} else {
			// the number of emails to process in this batch
			$length = round( count($recipients) * .50 );

			if ( $length < 25 ) {
				$length = 25;
			} elseif ( $length > 50 ) {
				$length = 50;
			}

			$recipients_part    = array_splice( $recipients, 0, $length );
			$args               = $data;
			$args['recipients'] = $recipients_part;

			$queue_ids       = FUE_Sending_Scheduler::queue_manual_emails( $args );
			$send_data       = array();
			$schedule_emails = ! empty( $args['schedule_email'] );

			foreach ( $queue_ids as $queue_id ) {
				$queue_item     = new FUE_Sending_Queue_Item( $queue_id );

				if ( $schedule_emails ) {
					$send_data[] = array(
						'status'    => 'queued',
						'email'     => $queue_item->user_email
					);

					continue;
				}

				$sending_result = Follow_Up_Emails::instance()->mailer->send_queue_item( $queue_item, true );

				if ( is_wp_error( $sending_result ) ) {
					$send_data[] = array(
						'status'    => 'error',
						'email'     => $queue_item->user_email,
						'error'     => $sending_result->get_error_message()
					);
				} else {
					$send_data[] = array(
						'status'    => 'success',
						'email'     => $queue_item->user_email
					);
				}
			}

			$status = count($recipients) > 0 ? 'partial' : 'completed';

			if ( $status == 'completed' ) {
				FUE_Transients::delete_transient( 'fue_manual_email_'. $key );
				FUE_Transients::delete_transient( 'fue_manual_email_recipients_'. $key );
			} else {
				// save the modified data
				FUE_Transients::set_transient( 'fue_manual_email_recipients_'. $key, $recipients, 86400 );
			}

			self::send_response( array(
				'status'    => $status,
				'data'      => $send_data
			) );
		}
	}

	/**
	 * Send manual emails in batches when "Single Emails Sending Schedule" has been enabled in the settings.
	 */
	public static function send_manual_email_batches() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		set_time_limit(0);

		if ( empty( $_POST['cmd'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			self::send_response( array( 'error' => 'CMD is missing' ) );
		}

		$cmd        = sanitize_text_field( wp_unslash( $_POST['cmd'] ) );
		$key        = !empty($_POST['key']) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
		$data       = FUE_Transients::get_transient( 'fue_manual_email_'. $key );
		$recipients = FUE_Transients::get_transient( 'fue_manual_email_recipients_'. $key );
		$recipients_count = count( $recipients );

		$emails_per_batch       = get_option( 'fue_emails_per_batch', 100 );
		$email_batch_interval   = get_option( 'fue_batch_interval', 10 );

	    if ( ! $data || ! $recipients ) {
		    self::send_response( array( 'error' => 'Data could not be loaded' ) );
	    }

		if ( $cmd == 'start' ) {
			$batches = ceil( count( $recipients ) / $emails_per_batch );
			self::send_response( array( 'total_items' => $batches ) );
		} else {
			// Keeps track of the number of recipients that are in the current batch
			// where queuing could possibly span several AJAX requests
			if ( empty( $data['recipient_number'] ) ) {
				$data['recipient_number'] = 0;
			}

			// set the send date for the batch
			if ( empty( $data['batch_send_date'] ) ) {
				if ( $data['schedule_email'] ) {
					// set the scheduled date and time if this is a scheduled batch
					$data['batch_send_date'] = strtotime( $data['schedule_date'] . ' ' . $data['schedule_hour'] . ':' . $data['schedule_minute'] . ' ' . $data['schedule_ampm'] );
				} else {
					// otherwise set the send date to now
					$data['batch_send_date'] = current_time( 'timestamp' );
				}
			}

			if ( empty( $data['batch_number'] ) ) {
				$data['batch_number'] = 1;
			}

			if ( !$data ) {
				self::send_response( array( 'error' => 'Data could not be loaded' ) );
			}

			// the number of emails to process in this batch
			$length = round( $recipients_count * .50 );

			if ( $length < 25 ) {
				$length = 25;
			} elseif ( $length > 50 ) {
				$length = 50;
			}

			$send_data          = array();
			$recipients_part    = array_splice( $recipients, 0, $length );

			$args                       = $data;
			$args['recipients']         = array();
			$args['schedule_email']     = true;

			foreach ( $recipients_part as $recipient_key => $recipient ) {
				$data['recipient_number']++;

				$args['recipients'][ $recipient_key ] = $recipient;

				// if the number of recipients per batch is reached,
				// adjust the send time and start a new batch
				if ( $data['recipient_number'] == $emails_per_batch ) {
					$args['schedule_timestamp'] = $data['batch_send_date'];
					FUE_Sending_Scheduler::queue_manual_emails( $args );

					$send_data[] = array(
						'status'        => 'success',
						'status_text'   => sprintf(
							__('Emails in batch %d have been processed', 'follow_up_emails'),
							$data['batch_number']
						)
					);

					$data['batch_number']++;
					$data['batch_send_date'] += ( $email_batch_interval * 60 );
					$data['recipient_number'] = 0;
					$args['recipients'] = array();
				}
			}

			if ( ! empty( $args['recipients'] ) ) {
				// Queue remaining recipients.
				$args['schedule_timestamp'] = $data['batch_send_date'];
				FUE_Sending_Scheduler::queue_manual_emails( $args );

				if ( empty( $recipients ) ) {
					$send_data[] = array(
						'status'        => 'success',
						'status_text'   => sprintf(
							__('Emails in batch %d have been processed', 'follow_up_emails'),
							$data['batch_number']
						)
					);
				}
			}

			if ( empty ( $recipients ) ) {
				$status = 'completed';

				// delete the transient data
				FUE_Transients::delete_transient( 'fue_manual_email_'. $key );
				FUE_Transients::delete_transient( 'fue_manual_email_recipients_'. $key );
			} else {
				$status = 'partial';

				// save the modified data
				FUE_Transients::set_transient( 'fue_manual_email_recipients_'. $key, $recipients, 86400 );
				FUE_Transients::set_transient( 'fue_manual_email_'. $key, $data, 86400 );
			}

			self::send_response( array(
				'status'        => $status,
				'update_data'   => $send_data
			) );
		}
	}

	/**
	 * Count the number of daily summary posts
	 */
	public static function count_daily_summary_posts( $return = false ) {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$wpdb = Follow_Up_Emails::instance()->wpdb;

		$count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_title = 'fue_send_summary'");

		if ( !$return ) {
			self::send_response( array(
				'status'    => 'success',
				'count'     => $count
			) );
		}

		return $count;

	}

	/**
	 * Delete daily summary posts
	 */
	public static function delete_daily_summary() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		// suppress errors
		ob_start();

		$count = self::count_daily_summary_posts( true );

		if ( $count == 0 ) {
			self::send_response( array(
				'status'    => 'success',
				'count'     => $count
			) );
		}

		// figure out the number of posts to delete depending on the total rows found
		// 10% of the total rows, min of 50 and max of 100 rows per run
		$limit = round($count * .10);

		if ( $limit > 100 ) {
			$limit = 100;
		}

		if ( $limit < 50 ) {
			$limit = 50;
		}

		$wpdb = Follow_Up_Emails::instance()->wpdb;

		// Delete action scheduler data
		$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_title = 'fue_send_summary' LIMIT $limit" );
		$wpdb->query( "DELETE FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE wp.ID IS NULL;" );
		ob_clean();

		self::count_daily_summary_posts();
	}

	/**
	 * Clear the stats data one table at a time
	 */
	public static function delete_stats_data() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$wpdb   = Follow_Up_Emails::instance()->wpdb;

		$tables = array(
			'comments',
			'followup_email_logs',
			'followup_email_tracking',
		);

		foreach ( $tables as $table ) {
			if ( $table == 'comments' ) {
				$count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->comments} WHERE comment_type = 'email_history'");

				if ( $count == 0 ) {
					continue;
				}

				$wpdb->query("DELETE FROM {$wpdb->comments} WHERE comment_type = 'email_history'");

				wp_send_json(array(
					'status' => 'processing'
				));
			} elseif ( $table == 'followup_email_logs' ) {
				$count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}followup_email_logs");

				if ( $count == 0 ) {
					continue;
				}

				$wpdb->query("TRUNCATE TABLE {$wpdb->prefix}followup_email_logs");

				wp_send_json(array(
					'status' => 'processing'
				));
			} elseif ( $table == 'followup_email_tracking' ) {
				$count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}followup_email_tracking");

				if ( $count == 0 ) {
					continue;
				}

				$wpdb->query("TRUNCATE TABLE {$wpdb->prefix}followup_email_tracking");

				wp_send_json(array(
					'status' => 'processing'
				));
			}
		}

		// if the code reaches this point, it means we are done
		wp_send_json(array(
			'status' => 'completed'
		));
	}

	/**
	 * Count the number of rows to be imported into Action Scheduler.
	 * Only loads orders that have not been sent yet.
	 */
	public static function scheduler_count_import_rows() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		global $wpdb;

		$count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}followup_email_orders WHERE is_sent = 0");

		wp_send_json( array( 'total' => $count ) );
	}

	/**
	 * Seems like DEAD CODE.
	 * AJAX handler to start email import
	 */
	public static function scheduler_do_import() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$next = intval( $_POST['next'] );

		$next   = FUE_Sending_Scheduler::action_scheduler_import($next, 50);
		$usage  = memory_get_usage(true);
		$limit  = ini_get('memory_limit');

		if ($usage < 1024)
			$usage = $usage." bytes";
		elseif ($usage < 1048576)
			$usage = round($usage/1024,2)." kilobytes";
		else
			$usage = round($usage/1048576,2)." megabytes";

		wp_send_json( array( 'next' => $next, 'usage' => $usage, 'limit' => $limit ) );
	}

	/**
	 * AJAX handler to start import process
	 */
	public static function scheduler_import_start() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		// disable email sending for a maximum of 1 hour
		// while importing all records
		set_transient( 'fue_importing', true, 3600 );
	}

	/**
	 * AJAX handler to complete the importing process
	 */
	public static function scheduler_import_complete() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		global $wpdb;

		// use the action scheduler system
		update_option( 'fue_scheduling_system', 'action-scheduler' );

		// convert all scheduled events to use action-scheduler
		wp_clear_scheduled_hook('sfn_followup_emails');
		wp_clear_scheduled_hook('fue_send_summary');
		wp_clear_scheduled_hook('sfn_optimize_tables');

		// done importing
		delete_transient( 'fue_importing' );
	}

	/**
	 * Clear unnecessary scheduled actions
	 */
	public static function clear_scheduled_actions() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		set_time_limit(0);

		$wpdb = Follow_Up_Emails::instance()->wpdb;

		if ( empty( $_POST['cmd'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			self::send_response( array( 'error' => 'CMD is missing' ) );
		}

		$cmd        = isset( $_POST['cmd'] ) ? sanitize_text_field( wp_unslash( $_POST['cmd'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$session    = !empty($_POST['update_session']) ? sanitize_text_field( wp_unslash( $_POST['update_session'] ) ): ''; // phpcs:ignore WordPress.Security.NonceVerification

		if ( $cmd == 'start' ) {
			// generate a new session id
			$session    = time();
			$actions    = get_posts(array(
				'nopaging'  => true,
				'post_type' => 'scheduled-action',
				'post_status'   => 'pending',
				'fields'        => 'ids',
				'tax_query' => array(
					array(
						'taxonomy'  => 'action-group',
						'field'     => 'name',
						'terms'     => 'fue'
					)
				)
			));

			FUE_Transients::set_transient( 'fue_update_'. $session, $actions, 3600 );

			ob_clean();

			self::send_response( array(
				'update_session'=> $session,
				'total_items'   => count( $actions )
			) );

		} else {
			ob_start();

			$action_ids = FUE_Transients::get_transient( 'fue_update_'. $session );

			if ( $action_ids === false ) {
				$action_ids = array();
			}

			$limit      = 50;
			$runs       = 0;
			$results    = array();

			foreach ( $action_ids as $idx => $action_id ) {
				$runs++;

				if ( $runs > $limit ) {
					break;
				}

				unset( $action_ids[ $idx ] );

				ob_start();

				$action = get_post( $action_id );
				$content = json_decode( $action->post_content );

				if ( !$content || !isset( $content->email_order_id ) ) {
					continue;
				}

				$count = $wpdb->get_var($wpdb->prepare(
					"SELECT COUNT(*)
					FROM {$wpdb->prefix}followup_email_orders
					WHERE id = %d
					AND is_sent = 0
					AND email_trigger <> 'Daily summary'",
					$content->email_order_id
				));

				if ( $count == 0 ) {
					ActionScheduler::store()->cancel_action( $action_id );
				}

				$results[] = array(
					'id'        => $action_id,
					'status'    => 'success'
				);
			}

			FUE_Transients::set_transient( 'fue_update_'. $session, $action_ids );

			$status = ( count($action_ids) > 0 ) ? 'partial' : 'completed';

			ob_clean();

			self::send_response( array(
				'status'            => $status,
				'update_data'       => $results,
				'session'           => $session
			) );

		}
	}

	/**
	 * Search for products and echo json
	 */
	public static function wc_json_search_products_and_variations() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$post_types = array( 'product', 'product_variation' );

		ob_start();

		check_ajax_referer( 'search-products', 'security' );

		$term    = isset( $_GET['term'] ) ? (string) sanitize_text_field( wp_unslash( $_GET['term'] ) ) : '';
		$exclude = array();

		if ( empty( $term ) ) {
			die();
		}

		if ( ! empty( $_GET['exclude'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$exclude = array_map( 'intval', explode( ',', array_map( 'sanitize_text_field', wp_unslash( $_GET['exclude'] ) ) ) );
		}

		$args = array(
			'post_type'      => $post_types,
			'post_status'    => array('publish','private'),
			'posts_per_page' => -1,
			's'              => $term,
			'fields'         => 'ids',
			'exclude'        => $exclude
		);

		if ( is_numeric( $term ) ) {

			if ( false === array_search( $term, $exclude ) ) {
				$posts2 = get_posts( array(
					'post_type'      => $post_types,
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'post__in'       => array( 0, $term ),
					'fields'         => 'ids'
				) );
			} else {
				$posts2 = array();
			}

			$posts3 = get_posts( array(
				'post_type'      => $post_types,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'post_parent'    => $term,
				'fields'         => 'ids',
				'exclude'        => $exclude
			) );

			$posts4 = get_posts( array(
				'post_type'      => $post_types,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'meta_query'     => array(
					array(
						'key'     => '_sku',
						'value'   => $term,
						'compare' => 'LIKE'
					)
				),
				'fields'         => 'ids',
				'exclude'        => $exclude
			) );

			$posts = array_unique( array_merge( get_posts( $args ), $posts2, $posts3, $posts4 ) );

		} else {

			$args2 = array(
				'post_type'      => $post_types,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'meta_query'     => array(
					array(
						'key'     => '_sku',
						'value'   => $term,
						'compare' => 'LIKE'
					)
				),
				'fields'         => 'ids',
				'exclude'        => $exclude
			);

			$posts = array_unique( array_merge( get_posts( $args ), get_posts( $args2 ) ) );

		}

		$found_products = array();

		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				$product = wc_get_product( $post );

				if ( ! current_user_can( 'read_product', $post ) ) {
					continue;
				}

				$found_products[ $post ] = rawurldecode( $product->get_formatted_name() );
			}
		}

		$found_products = apply_filters( 'woocommerce_json_search_found_products', $found_products );

		wp_send_json( $found_products );
	}

	/**
	 * Search for products and return a JSON-encoded string of results
	 * using $_GET['term'] as the search term
	 */
	public static function wc_json_search_subscription_products() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		check_ajax_referer( 'search-products', 'security' );

		header( 'Content-Type: application/json; charset=utf-8' );

		$term       = isset( $_GET['term'] ) ? (string) sanitize_text_field( wp_unslash( $_GET['term'] ) ) : '';
		$post_types = array('product', 'product_variation');

		if (empty($term)) die();

		if ( is_numeric( $term ) ) {

			$args = array(
				'post_type'			=> $post_types,
				'post_status'	 	=> 'publish',
				'posts_per_page' 	=> -1,
				'post__in' 			=> array(0, $term),
				'fields'			=> 'ids'
			);

			$args2 = array(
				'post_type'			=> $post_types,
				'post_status'	 	=> 'publish',
				'posts_per_page' 	=> -1,
				'post_parent' 		=> $term,
				'fields'			=> 'ids'
			);

			$args3 = array(
				'post_type'			=> $post_types,
				'post_status' 		=> 'publish',
				'posts_per_page' 	=> -1,
				'meta_query' 		=> array(
					array(
						'key' 	=> '_sku',
						'value' => $term,
						'compare' => 'LIKE'
					)
				),
				'fields'			=> 'ids'
			);

			$posts = array_unique(array_merge( get_posts( $args ), get_posts( $args2 ), get_posts( $args3 ) ));

		} else {

			$args = array(
				'post_type'			=> $post_types,
				'post_status' 		=> 'publish',
				'posts_per_page' 	=> -1,
				's' 				=> $term,
				'fields'			=> 'ids'
			);

			$args2 = array(
				'post_type'			=> $post_types,
				'post_status' 		=> 'publish',
				'posts_per_page' 	=> -1,
				'meta_query' 		=> array(
					array(
						'key' 	=> '_sku',
						'value' => $term,
						'compare' => 'LIKE'
					)
				),
				'fields'			=> 'ids'
			);

			$posts = array_unique(array_merge( get_posts( $args ), get_posts( $args2 ) ));

		}

		$found_products = array();

		if ( $posts ) foreach ( $posts as $post ) {

			$product = wc_get_product( $post );

			if ( WC_Subscriptions_Product::is_subscription( $product ) )
				$found_products[ $post ] = $product->get_formatted_name();

		}

		$found_products = apply_filters( 'woocommerce_json_search_found_products', $found_products );

		wp_send_json( $found_products );
	}

	/**
	 * Search for shop coupons and return a JSON-encoded string of results
	 * using $_GET['term'] as the search term
	 */
	public static function wc_json_search_coupons() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		check_ajax_referer( 'search-products', 'security' );

		header( 'Content-Type: application/json; charset=utf-8' );

		$term = isset( $_GET['term'] ) ? (string) sanitize_text_field( wp_unslash( $_GET['term'] ) ) : '';

		if (empty($term)) die();

		if ( is_numeric( $term ) ) {

			$args = array(
				'post_type'			=> 'shop_coupon',
				'post_status'	 	=> 'publish',
				'posts_per_page' 	=> -1,
				'post__in' 			=> array(0, $term),
				'fields'			=> 'ids'
			);

			$posts = array_unique( get_posts( $args ) );

		} else {

			$args = array(
				'post_type'			=> 'shop_coupon',
				'post_status' 		=> 'publish',
				'posts_per_page' 	=> -1,
				's' 				=> $term,
				'fields'			=> 'ids'
			);

			$posts = array_unique( get_posts( $args ) );

		}

		$found_coupons = array();

		if ( $posts ) foreach ( $posts as $post ) {

			$coupon = get_post( $post );

			$coupon_title = $coupon->post_title;
			if ( !empty( $coupon->post_excerpt ) ) {
				$coupon_title .= ' ('. $coupon->post_excerpt .')';
			}

			$found_coupons[ $post ] = $coupon_title;

		}

		$found_coupons = apply_filters( 'woocommerce_json_search_found_coupons', $found_coupons );

		wp_send_json( $found_coupons );
	}

	/**
	 * AJAX - check if the given product has any children (variation product)
	 */
	public static function wc_product_has_children() {
		if ( ! isset( $_GET['nonce'] ) || ! check_ajax_referer( 'search_ticket_products', 'nonce' ) || ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$id = isset( $_GET['product_id'] ) ? absint( wp_unslash( $_GET['product_id'] ) ) : '';

		if ( FUE_Addon_Woocommerce::product_has_children($id) ) {
			echo 1;
		} else {
			echo 0;
		}
		exit;
	}

	/**
	 * AJAX sensei_search_courses() function using $_GET['term'] for the search term
	 */
	public static function sensei_search_courses() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		ob_start();

		check_ajax_referer( 'search-courses', 'security' );

		$term = isset( $_GET['term'] ) ? (string) sanitize_text_field( wp_unslash( $_GET['term'] ) ) : '';

		if ( empty( $term ) ) {
			die();
		}

		$args = array(
			'post_type'      => 'course',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			's'              => $term,
			'fields'         => 'ids'
		);

		$posts = get_posts( $args );

		$found_products = array();

		if ( $posts ) {
			foreach ( $posts as $post ) {
				$found_products[ $post ] = get_the_title( $post );
			}
		}

		wp_send_json( $found_products );
	}

	/**
	 * AJAX sensei_search_lessons() function using $_GET['term'] for the search term
	 */
	public static function sensei_search_lessons() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		ob_start();

		check_ajax_referer( 'search-lessons', 'security' );

		$term       = isset( $_GET['term'] ) ? (string) sanitize_text_field( wp_unslash( $_GET['term'] ) ) : '';
		$filters    = (!empty( $_GET['filters'] ) ) ? array_map( 'sanitize_text_field', json_decode( wp_unslash( $_GET['filters'] ), true ) ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

		if ( empty( $term ) ) {
			die();
		}

		$args = array(
			'post_type'      => 'lesson',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			's'              => $term,
			'fields'         => 'ids'
		);

		if ( !empty( $filters ) ) {
			if ( !empty( $filters['course_id'] ) ) {
				$args['meta_query'][] = array(
					'key'   => '_lesson_course',
					'value' => absint( $filters['course_id'] )
				);
			}
		}

		$posts = get_posts( $args );

		$found_products = array();

		if ( $posts ) {
			foreach ( $posts as $post ) {
				$found_products[ $post ] = get_the_title( $post );
			}
		}

		wp_send_json( $found_products );
	}

	/**
	 * AJAX sensei_search_quizzes() function using $_GET['term'] for the search term
	 */
	public static function sensei_search_quizzes() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		ob_start();

		check_ajax_referer( 'search-quizzes', 'security' );

		$term = isset( $_GET['term'] ) ? (string) sanitize_text_field( wp_unslash( $_GET['term'] ) ) : '';

		if ( empty( $term ) ) {
			die();
		}

		$args = array(
			'post_type'      => 'quiz',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			's'              => $term,
			'fields'         => 'ids'
		);

		$posts = get_posts( $args );

		$found_products = array();

		if ( $posts ) {
			foreach ( $posts as $post ) {
				$found_products[ $post ] = get_the_title( $post );
			}
		}

		wp_send_json( $found_products );
	}

	/**
	 * AJAX sensei_search_questions() function using $_GET['term'] for the search term
	 */
	public static function sensei_search_questions() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		ob_start();

		check_ajax_referer( 'search-questions', 'security' );

		$term = isset( $_GET['term'] ) ?  (string) sanitize_text_field( wp_unslash( $_GET['term'] ) ) : '';

		if ( empty( $term ) ) {
			die();
		}

		$args = array(
			'post_type'      => 'question',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			's'              => $term,
			'fields'         => 'ids'
		);

		$posts = get_posts( $args );

		$found_products = array();

		if ( $posts ) {
			foreach ( $posts as $post ) {
				$found_products[ $post ] = get_the_title( $post );
			}
		}

		wp_send_json( $found_products );
	}

	/**
	 * Search for products and echo results as JSON. Uses $_GET['term'] as the search term.
	 */
	public static function wc_json_search_ticket_products() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		check_ajax_referer( 'search-products', 'security' );

		header( 'Content-Type: application/json; charset=utf-8' );

		$term       = isset( $_GET['term'] ) ? (string) sanitize_text_field( wp_unslash( $_GET['term'] ) ) : '';
		$post_types = array('product', 'product_variation');

		if (empty($term)) die();

		if ( is_numeric( $term ) ) {

			$args = array(
				'post_type'			=> $post_types,
				'post_status'	 	=> 'publish',
				'posts_per_page' 	=> -1,
				'post__in' 			=> array(0, $term),
				'fields'			=> 'ids'
			);

			$args2 = array(
				'post_type'			=> $post_types,
				'post_status'	 	=> 'publish',
				'posts_per_page' 	=> -1,
				'post_parent' 		=> $term,
				'fields'			=> 'ids'
			);

			$args3 = array(
				'post_type'			=> $post_types,
				'post_status' 		=> 'publish',
				'posts_per_page' 	=> -1,
				'meta_query' 		=> array(
					array(
						'key' 	=> '_sku',
						'value' => $term,
						'compare' => 'LIKE'
					)
				),
				'fields'			=> 'ids'
			);

			$posts = array_unique(array_merge( get_posts( $args ), get_posts( $args2 ), get_posts( $args3 ) ));

		} else {

			$args = array(
				'post_type'			=> $post_types,
				'post_status' 		=> 'publish',
				'posts_per_page' 	=> -1,
				's' 				=> $term,
				'fields'			=> 'ids'
			);

			$args2 = array(
				'post_type'			=> $post_types,
				'post_status' 		=> 'publish',
				'posts_per_page' 	=> -1,
				'meta_query' 		=> array(
					array(
						'key' 	=> '_sku',
						'value' => $term,
						'compare' => 'LIKE'
					)
				),
				'fields'			=> 'ids'
			);

			$posts = array_unique(array_merge( get_posts( $args ), get_posts( $args2 ) ));

		}

		$found_products = array();

		if ( $posts ) foreach ( $posts as $post ) {

			$event_id = get_post_meta( $post, '_tribe_wooticket_for_event', true );

			if ( $event_id && $event_id > 0 ) {
				$product = wc_get_product( $post );
				$found_products[ $post ] = $product->get_formatted_name();
			}


		}

		$found_products = apply_filters( 'woocommerce_json_search_found_products', $found_products );

		wp_send_json( $found_products );
	}

	/**
	 * Search for products and return a JSON-encoded string of results
	 * using $_GET['term'] as the search term
	 */
	public static function wc_json_search_booking_products() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		check_ajax_referer( 'search-products', 'security' );

		header( 'Content-Type: application/json; charset=utf-8' );

		$term       = isset( $_GET['term'] ) ? (string) sanitize_text_field( wp_unslash( $_GET['term'] ) ) : '';
		$post_types = array('product', 'product_variation');

		if (empty($term)) die();

		if ( is_numeric( $term ) ) {

			$args = array(
				'post_type'			=> $post_types,
				'post_status'	 	=> 'publish',
				'posts_per_page' 	=> -1,
				'post__in' 			=> array(0, $term),
				'fields'			=> 'ids'
			);

			$args2 = array(
				'post_type'			=> $post_types,
				'post_status'	 	=> 'publish',
				'posts_per_page' 	=> -1,
				'post_parent' 		=> $term,
				'fields'			=> 'ids'
			);

			$args3 = array(
				'post_type'			=> $post_types,
				'post_status' 		=> 'publish',
				'posts_per_page' 	=> -1,
				'meta_query' 		=> array(
					array(
						'key' 	=> '_sku',
						'value' => $term,
						'compare' => 'LIKE'
					)
				),
				'fields'			=> 'ids'
			);

			$posts = array_unique(array_merge( get_posts( $args ), get_posts( $args2 ), get_posts( $args3 ) ));

		} else {

			$args = array(
				'post_type'			=> $post_types,
				'post_status' 		=> 'publish',
				'posts_per_page' 	=> -1,
				's' 				=> $term,
				'fields'			=> 'ids'
			);

			$args2 = array(
				'post_type'			=> $post_types,
				'post_status' 		=> 'publish',
				'posts_per_page' 	=> -1,
				'meta_query' 		=> array(
					array(
						'key' 	=> '_sku',
						'value' => $term,
						'compare' => 'LIKE'
					)
				),
				'fields'			=> 'ids'
			);

			$posts = array_unique(array_merge( get_posts( $args ), get_posts( $args2 ) ));

		}

		$found_products = array();

		if ( $posts ) foreach ( $posts as $post ) {

			$product = WC_FUE_Compatibility::wc_get_product( $post );

			if ( $product->is_type( 'booking' ) ) {
				$found_products[ $post ] = $product->get_formatted_name();
			}

		}

		$found_products = apply_filters( 'wc_json_search_found_booking_products', $found_products );

		wp_send_json( $found_products );
	}

	/**
	 * Order importer for WooCommerce. To avoid hitting the memory limit especially for stores
	 * with a huge number of orders, the process is split up into several parts:
	 *  - start
	 *  - filter
	 *  - import
	 * Each step, a session key will be returned which is used to continue an existing import process.
	 */
	public static function wc_order_import() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

	    ob_start();

		set_time_limit(0);

		$wpdb = Follow_Up_Emails::instance()->wpdb;

		if ( empty( $_POST['cmd'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			self::send_response( array( 'error' => 'CMD is missing' ) );
		}

		$cmd            = isset( $_POST['cmd'] ) ? sanitize_text_field( wp_unslash( $_POST['cmd'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$email_id       = !empty($_POST['email_id']) ? absint( wp_unslash( $_POST['email_id'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$session        = !empty($_POST['import_session']) ? sanitize_text_field( wp_unslash( $_POST['import_session'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$wc_importer    = new FUE_Addon_WooCommerce_Order_Importer();

		if ( $session ) {
			$email_id = get_transient( 'fue_import_email_ids_'. $session );
		}

		if ( $email_id ) {

			if ( !is_array( $email_id ) ) {
				$email_id = array( $email_id );
			}

			if ( $cmd == 'start' ) {
				// generate a new session id
				$session = time();

				set_transient( 'fue_import_email_ids_'. $session, $email_id, 86400 );
				set_transient( 'fue_import_filter_email_ids_'. $session, $email_id, 86400 );

				$total_order_count = 0;

				foreach ( $email_id as $id ) {
					$email  = new FUE_Email( $id );

	                // skip emails that aren't active
	                if ( $email->status != FUE_Email::STATUS_ACTIVE ) {
		                continue;
	                }

					$orders = $wc_importer->get_order_ids_matching_email( $email );

					if ( !empty( $orders ) ) {
						$orders = array_values( $orders[ $id ] );
						$total_order_count += count( $orders );
						FUE_Transients::set_transient( 'fue_import_'. $id .'_'. $session, $orders, 86400 );
					}

				}

				$response = array('session' => $session);

				// warn if the allotted memory for PHP is less than 64MB
				// or if the total unfiltered orders is > 3000
				$memory = fue_let_to_num( WP_MAX_MEMORY_LIMIT );

				if ( $memory < 67108864 ) {
					$response['warning'] = __("Warning: Your WordPress memory limit is less than the recommended 64MB. If importing fails, please try increasing this value.\n\nDo you wish to continue?", 'follow_up_emails');
				} else {
					if ( $memory < 134217728 && $total_order_count > 3000 ) {
						$response['warning'] = __("Warning: You are attempting to import a large number of orders. Please set your WordPress memory limit to at least 128MB. Continuing may lead to the importing process getting interrupted due to the lack of memory.\n\nDo you still wish to continue?", 'follow_up_emails');
					}
				}

				self::send_response( $response );

			} elseif ( $cmd == 'filter' ) {
				$email_ids          = get_transient( 'fue_import_filter_email_ids_'. $session );
				$current_email_id   = array_pop( $email_ids );
				set_transient( 'fue_import_filter_email_ids_'. $session, $email_ids );

				$orders         = FUE_Transients::get_transient( 'fue_import_'. $current_email_id .'_'. $session );
				$total_filtered = get_transient( 'fue_import_num_filtered_'. $session );

				if ( !$orders ) {
					self::send_no_orders_response( $session );
				}

				if ( ! $total_filtered ) {
					$total_filtered = 0;
				}

				$filtered = FUE_Transients::get_transient( 'fue_import_filtered_'. $current_email_id .'_'. $session );

				if ( !$filtered ) {
					$filtered = array();
				}

				if ( !empty( $orders ) ) {
					$newly_filtered     = $wc_importer->filter_orders( array( 'email_id' => $current_email_id, 'orders' => $orders ) );
					$filtered           = array_merge( $filtered, $newly_filtered[ $current_email_id ] );

					if ( isset( $newly_filtered[ $current_email_id ] ) ) {
						$total_filtered += count( $newly_filtered[ $current_email_id ] );
					}
				}

				FUE_Transients::delete_transient( 'fue_import_'. $current_email_id .'_'. $session );
				FUE_Transients::set_transient( 'fue_import_filtered_'. $current_email_id .'_'. $session, $filtered, 86400 );
				set_transient( 'fue_import_num_filtered_'. $session, $total_filtered );

				$status = count( $email_ids ) > 0 ? 'partial' : 'completed';
				$return = array(
					'status'    => $status,
					'session'   => $session
				);

				if ( $status == 'completed' ) {
					$return['total_orders'] = $total_filtered;
				}

				self::send_response( $return );

			} else {
				$ids = get_transient( 'fue_import_email_ids_'. $session );

				do {
					$id = array_pop( $ids );

					if ( is_null( $id ) ) {
						self::send_no_orders_response( $session );
					}

					$orders = FUE_Transients::get_transient( 'fue_import_filtered_'. $id .'_'. $session );
				} while ( $orders === false );

				$results = $wc_importer->import_orders( $id, $orders, 50 );

				FUE_Transients::set_transient( 'fue_import_filtered_'. $id .'_'. $session, $results['orders'] );

				$total_orders       = get_transient( 'fue_import_num_filtered_'. $session );
				$remaining_orders   = $total_orders - $results['processed'];

				set_transient( 'fue_import_num_filtered_'. $session, $remaining_orders );

				self::send_response( array(
					'status'            => ($remaining_orders > 0) ? 'partial' : 'completed',
					'import_data'       => $results['imported'],
					'remaining_orders'  => $remaining_orders,
					'session'           => $session
				) );

			}

		} else {
			if ( $cmd == 'start' ) {
				// generate a new session id
				$session = time();

				$tables = $wpdb->get_col( "SHOW TABLES LIKE '{$wpdb->prefix}followup_order_items'" );

				if ( empty( $tables ) ) {
					self::send_response( array( 'error' => 'Database tables are not installed. Please deactivate then reactivate Follow Up Emails' ) );
				}

				if ( ! get_option( 'fue_orders_imported', false ) && !get_transient( 'fue_importing_orders' ) ) {
					// First run of the import script. Clear existing data for a fresh start
					$wpdb->query( "DELETE FROM {$wpdb->prefix}followup_order_items" );
					$wpdb->query( "DELETE FROM {$wpdb->prefix}followup_customers" );
					$wpdb->query( "DELETE FROM {$wpdb->prefix}followup_order_categories" );
					$wpdb->query( "DELETE FROM {$wpdb->prefix}followup_customer_orders" );
					$wpdb->query( "DELETE FROM {$wpdb->prefix}followup_customer_notes" );
				}

				set_transient( 'fue_importing_orders', true, 86400 );

				$sql = "SELECT COUNT( DISTINCT p.id )
				FROM {$wpdb->posts} p, {$wpdb->postmeta} pm
				WHERE p.ID = pm.post_id
				AND p.post_type = 'shop_order'
				AND (SELECT COUNT(*) FROM {$wpdb->postmeta} pm2 WHERE p.ID = pm2.post_id AND pm2.meta_key = '_fue_recorded') = 0";

				$total_orders = $wpdb->get_var( $sql );

				if ( $total_orders == 0 ) {
					update_option( 'fue_orders_imported', true );
					delete_transient( 'fue_importing_orders' );
				}

				set_transient( 'fue_import_num_orders', $total_orders, 3600 );

				self::send_response( array(
					'session' => $session
				) );
			} elseif ( $cmd == 'filter' ) {
				$total_orders = get_transient( 'fue_import_num_orders' );
				self::send_response( array(
					'total_orders'  => $total_orders,
					'session'       => $session,
					'status'        => 'completed'
				) );
			} else {
				$total_orders = get_transient( 'fue_import_num_orders' );

				$sql = "SELECT DISTINCT p.ID
				FROM {$wpdb->posts} p, {$wpdb->postmeta} pm
				WHERE p.ID = pm.post_id
				AND p.post_type = 'shop_order'
				AND (SELECT COUNT(*) FROM {$wpdb->postmeta} pm2 WHERE p.ID = pm2.post_id AND pm2.meta_key = '_fue_recorded') = 0
				LIMIT 50";

				$results = $wpdb->get_results( $sql );

				if ( count($results) == 0 ) {
					update_option( 'fue_orders_imported', true );
					delete_transient('fue_importing_orders');

					self::send_response( array(
						'status'            => 'completed',
						'session'           => $session,
						'remaining_orders'  => 0
					) );
				} else {
					$imported = array();
					foreach ( $results as $row ) {
						$order = WC_FUE_Compatibility::wc_get_order( $row->ID );
						FUE_Addon_Woocommerce::record_order( $order );
						$imported[] = array(
							'id'        => $row->ID,
							'status'    => 'success'
						);
					}

					$remaining_orders = $total_orders - count( $imported );

					self::send_response( array(
						'status'            => 'partial',
						'import_data'       => $imported,
						'session'           => $session,
						'remaining_orders'  => $remaining_orders
					) );

				}
			}

		}


	}

	/**
	 * Update the total purchase amount for all customers
	 */
	public static function wc_update_customer_order_total() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		set_time_limit(0);
		$wpdb = Follow_Up_Emails::instance()->wpdb;
		if ( empty( $_POST['cmd'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			self::send_response( array( 'error' => 'CMD is missing' ) );
		}
		$cmd        = isset( $_POST['cmd'] ) ? sanitize_text_field( wp_unslash( $_POST['cmd'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$session    = ! empty($_POST['update_session']) ? sanitize_text_field( wp_unslash( $_POST['update_session'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		if ( $cmd == 'start' ) {
			// generate a new session id
			$session = time();
			ob_start();
			$args = array(
				'post_type'     => 'shop_order',
				'post_status'   => array( 'wc-on-hold', 'wc-processing', 'wc-completed' ),
				'fields'        => 'ids',
				'nopaging'      => true
			);
			$orders = get_posts( $args );
			FUE_Transients::set_transient( 'fue_update_'. $session, $orders, 3600 );
			ob_clean();
			self::send_response( array(
				'update_session'=> $session,
				'total_items'   => count( $orders )
			) );
		} else {
			ob_start();
			$orders = FUE_Transients::get_transient( 'fue_update_'. $session );
			if ( $orders === false ) {
				self::send_response( array('error' => 'Update data not found') );
			}
			$limit      = 100;
			$runs       = 0;
			$results    = array();
			$customers  = array();
			foreach ( $orders as $idx => $order_id ) {
				unset( $orders[ $idx ] );
				$runs++;
				if ( $runs > $limit ) {
					break;
				}
				$order      = WC_FUE_Compatibility::wc_get_order( $order_id );
				$customer   = fue_get_customer_from_order( $order );
				if ( $customer ) {
					if ( isset( $customers[ $customer->id ] ) ) {
						$customers[ $customer->id ] += WC_FUE_Compatibility::get_order_prop( $order, 'order_total' );
					} else {
						$customers[ $customer->id ] = WC_FUE_Compatibility::get_order_prop( $order, 'order_total' );
					}
				}
				$results[] = array(
					'id'        => $order_id,
					'status'    => 'success'
				);
			}
			foreach ( $customers as $customer_id => $total_purchase_price ) {
				$wpdb->query( $wpdb->prepare(
					"UPDATE {$wpdb->prefix}followup_customers
					SET total_purchase_price = total_purchase_price + %2f
					WHERE id = %d",
					$total_purchase_price,
					$customer_id
				));
			}
			FUE_Transients::set_transient( 'fue_update_'. $session, $orders );
			$status = ( count($orders) > 0 ) ? 'partial' : 'completed';
			ob_clean();
			self::send_response( array(
				'status'            => $status,
				'update_data'       => $results,
				'session'           => $session
			) );
		}
	}

	/**
	 * Set a flag in the DB to stop displaying the Scan Orders prompt
	 */
	public static function wc_disable_order_scan() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		update_option( 'fue_disable_order_scan', true );
		self::send_response( array(
			'status' => 'success'
		) );
	}

	/**
	 * Update the existing subscription follow-ups from using the old
	 * subscription key format (order_product) to the new Order Subscription ID
	 */
	public static function wc_subscriptions_update() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		set_time_limit(0);

		$wpdb = Follow_Up_Emails::instance()->wpdb;

		if ( empty( $_POST['cmd'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			self::send_response( array( 'error' => 'CMD is missing' ) );
		}

		$cmd        = isset( $_POST['cmd'] ) ? sanitize_text_field( wp_unslash( $_POST['cmd'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$session    = !empty($_POST['update_session']) ? sanitize_text_field( wp_unslash( $_POST['update_session'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$scheduler  = Follow_Up_Emails::instance()->scheduler;

		if ( $cmd == 'start' ) {
			// generate a new session id
			$session = time();

			if ( !FUE_Addon_Subscriptions::is_wcs_2() ) {
				FUE_Transients::set_transient( 'fue_update_'. $session, array(), 600 );

				self::send_response( array(
					'update_session'=> $session,
					'total_items'   => 0
				) );
			}

			ob_start();

			$all_items  = $scheduler->get_items(array());
			$filtered   = array();

			foreach ( $all_items as $item ) {
				if ( empty( $item->email_id ) || empty( $item->order_id ) ) {
					continue;
				}

				$terms  = get_the_terms( $item->email_id, 'follow_up_email_type' );

				if ( !$terms ) {
					continue;
				}

				$type   = !empty( $terms ) && isset( current( $terms )->name ) ? sanitize_title( current( $terms )->name ) : '';

				if ( $type == 'subscription' ) {
					$filtered[] = $item->id;
				}
			}

			FUE_Transients::set_transient( 'fue_update_'. $session, $filtered, 600 );

			ob_clean();

			self::send_response( array(
				'update_session'=> $session,
				'total_items'   => count( $filtered )
			) );

		} else {

			ob_start();

			$items = FUE_Transients::get_transient( 'fue_update_'. $session );

			if ( $items === false ) {
				self::send_response( array('error' => 'Update data not found') );
			}

			$limit      = 50;
			$runs       = 0;
			$results    = array();

			foreach ( $items as $idx => $item_id ) {
				unset( $items[ $idx ] );
				$runs++;

				if ( $runs > $limit ) {
					break;
				}

				$item = new FUE_Sending_Queue_Item( $item_id );

				if ( !empty( $item->meta['subs_key'] ) && false !== strpos( $item->meta['subs_key'], '_' ) ) {
					$new_id = $wpdb->get_var($wpdb->prepare(
						"SELECT ID
						FROM {$wpdb->posts}
						WHERE post_parent = %d
						AND post_type = 'shop_subscription'",
						$item->order_id
					));

					if ( !$new_id ) {
						$results[] = array(
							'id'        => $item->id,
							'status'    => 'error',
							'reason'    => __('The new subscription could not be found. Skipping', 'follow_up_email')
						);
						continue;
					}

					$item->meta['subs_key'] = $new_id;
					$item->save();

					$results[] = array(
						'id'        => $item->id,
						'status'    => 'success'
					);
				} else {
					$results[] = array(
						'id'        => $item->id,
						'status'    => 'error',
						'reason'    => __('No subscription key found. Skipping.', 'follow_up_email')
					);
				}
			}

			FUE_Transients::set_transient( 'fue_update_'. $session, $items );

			$status = ( count($items) > 0 ) ? 'partial' : 'completed';

			ob_clean();

			if ( $status == 'completed' ) {
				update_option( 'fue_subscription_2.0_updated', true );
				delete_option( 'fue_subscription_needs_update' );
			}

			self::send_response( array(
				'status'            => $status,
				'update_data'       => $results,
				'session'           => $session
			) );

		}
	}

	/**
	 * Save the guest's email for cart emails
	 */
	public static function wc_set_cart_email() {
		if ( empty( $_POST['email'] ) || !is_email( $_POST['email'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput
			exit;
		}

		$first_name = !empty( $_POST['first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$last_name  = !empty( $_POST['last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	    $email      = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

		WC()->session->set( 'wc_guest_email', $email );
		WC()->session->set( 'wc_guest_name', array($first_name, $last_name) );

	    // copy what's currently in WC's cart to our cart table
		FUE_Addon_Woocommerce_Cart::clone_cart();

	    foreach ( WC()->cart->get_cart() as $cart_item ) {
		    Follow_Up_Emails::instance()->fue_wc->wc_scheduler->queue_cart_emails( WC()->cart->get_cart(), 0, $email, $cart_item['product_id'] );
	    }
	}

	/**
	 * Create a new newsletter list
	 */
	public static function create_list() {
		if ( ! empty( $_POST['security'] ) && ! wp_verify_nonce( $_POST['security'], 'add_new_fue_list' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			wp_die( esc_html__( 'Error: Invalid request. Please try again.', 'follow_up_emails' ) );
		}

		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$list = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

		$list_id = Follow_Up_Emails::instance()->newsletter->add_list( $list );

		wp_send_json(array(
			'status'    => 'success',
			'id'        => $list_id
		));
	}

	/**
	 * Updates an existing newsletter list
	 */
	public static function update_list() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$id     = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$list   = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$access = isset( $_POST['access'] ) ? absint( wp_unslash( $_POST['access'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$list_id = Follow_Up_Emails::instance()->newsletter->edit_list( $id, $list, $access );
		wp_send_json(array(
			'status'    => 'success',
			'id'        => $list_id
		));
	}

	/**
	 * Deletes an existing newsletter list
	 */
	public static function delete_list() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$id = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		Follow_Up_Emails::instance()->newsletter->remove_list( $id );
		wp_send_json(array(
			'status'    => 'success'
		));
	}

	/**
	 * Remove the subscriber from the given list
	 */
	public static function remove_subscriber_from_list() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$subscriber = isset( $_POST['subscriber'] ) ? sanitize_text_field( wp_unslash( $_POST['subscriber'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$list = isset( $_POST['list'] ) ? absint( wp_unslash( $_POST['list'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

		Follow_Up_Emails::instance()->newsletter->remove_from_list( $subscriber, (array) $list );

		self::send_response( array(
			'status' => 'success'
		) );
	}

	/**
	 * Update the customer's subscriptions/lists
	 */
	public static function update_account_subscriptions() {
		if ( ! isset( $_POST['nonce'] ) || ! check_ajax_referer( 'update_email_subscriptions', 'nonce' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$newsletter = new FUE_Newsletter();
		$user       = wp_get_current_user();
		$lists      = isset( $_POST['lists'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['lists'] ) ) : array(); // phpcs:ignore WordPress.Security.NonceVerification
		$subscriber = $newsletter->get_subscriber( $user->user_email );

		if ( !$subscriber ) {
			$subscriber_id  = $newsletter->add_subscriber_to_list( array(), array(
				'email'      => $user->user_email,
				'first_name' => $user->first_name,
				'last_name'  => $user->last_name,
			) );
			$subscriber     = $newsletter->get_subscriber( $subscriber_id );
		}

		$public_lists       = $newsletter->get_public_lists();
		$public_list_ids    = array();

		foreach ( $public_lists as $list ) {
			$public_list_ids[] = $list['id'];
		}

		if ( !empty( $public_list_ids ) ) {
			// remove user from all public lists
			$newsletter->remove_from_list( $subscriber['id'], $public_list_ids );
		}

		// add to new list/s
		foreach ( $lists as $list ) {
			if ( in_array( $list, $public_list_ids ) ) {
				$newsletter->add_to_list( $subscriber['id'], $list );
			}
		}
	}

	/**
	 * Generate the CSV in phases to avoid script timeouts and memory limit issues
	 */
	public static function build_export_list() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		global $wpdb;

		set_time_limit(0);

		$csv            = '';
		$list           = !empty( $_POST['list'] ) ? absint( wp_unslash( $_POST['list'] ) ) : false; // phpcs:ignore WordPress.Security.NonceVerification
		$export_id      = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$export_page    = get_transient( 'fue_list_export_page_'. $export_id );
		$export_file    = sys_get_temp_dir() .'/fue_export_'. $export_id;

		if ( !$export_page ) {
			$export_page = 1;

			$csv    = "id,email,first_name,last_name,date_added\n";
		}

		$per_page   = 1000;
		$page_start = ( $per_page * $export_page ) - $per_page;

		$sql = "SELECT DISTINCT s.id, s.email, s.first_name, s.last_name, s.date_added
				FROM {$wpdb->prefix}followup_subscribers s, {$wpdb->prefix}followup_subscribers_to_lists sl
				WHERE s.id = sl.subscriber_id";

		if ( $list ) {
			$sql .= " AND sl.list_id = $list";
		}

		$sql .= " ORDER BY s.date_added DESC LIMIT {$page_start},{$per_page}";

		$subscribers = $wpdb->get_results( $sql, ARRAY_A );

		if ( empty( $subscribers ) ) {
			wp_send_json(array(
				'status'    => 'complete'
			));
		}

		foreach ( $subscribers as $subscriber ) {
			$csv .= "{$subscriber['id']},{$subscriber['email']},{$subscriber['first_name']},{$subscriber['last_name']},{$subscriber['date_added']}\n";
		}

		$export_page++;

		set_transient( 'fue_list_export_page_'. $export_id, $export_page );

		$fp = fopen( $export_file, 'a+' );
		fputs( $fp, $csv );
		fclose( $fp );

		wp_send_json(array(
			'status'    => 'processing'
		));
	}

	/**
	 * Move follow-up history logs from the comments table to followup_followup_history
	 */
	public static function migrate_logs() {
		if ( ! current_user_can( 'manage_follow_up_emails' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		set_time_limit(0);

		$wpdb = Follow_Up_Emails::instance()->wpdb;

		if ( empty( $_POST['cmd'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			self::send_response( array( 'error' => 'CMD is missing' ) );
		}

		$cmd        = isset( $_POST['cmd'] ) ? sanitize_text_field( wp_unslash( $_POST['cmd'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$session    = !empty($_POST['update_session']) ? sanitize_text_field( wp_unslash( $_POST['update_session'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

		if ( $cmd == 'start' ) {
			// generate a new session id
			$session    = time();
			$logs       = $wpdb->get_results(
				"SELECT comment_ID, comment_post_ID, user_id, comment_date, comment_content
				FROM {$wpdb->comments}
				WHERE comment_type = 'email_history'
				AND user_id > 0",
				ARRAY_A
			);

			ob_start();

			FUE_Transients::set_transient( 'fue_update_'. $session, $logs, 3600 );

			ob_clean();

			self::send_response( array(
				'update_session'=> $session,
				'total_items'   => count( $logs )
			) );

		} else {

			ob_start();

			$logs = FUE_Transients::get_transient( 'fue_update_'. $session );
			$logs_processed = get_transient( 'fue_update_'. $session .'_processed' );

			if ( $logs === false ) {
				$logs = array();
			}

			if ( !$logs_processed ) {
				$logs_processed = 0;
			}

			$limit      = 100;
			$runs       = 0;
			$results    = array();

			foreach ( $logs as $idx => $log ) {
				$runs++;

				if ( $runs > $limit ) {
					break;
				}

				unset( $logs[ $idx ] );

				$insert = array(
					'followup_id'   => $log['comment_post_ID'],
					'user_id'       => $log['user_id'],
					'content'       => $log['comment_content'],
					'date_added'    => $log['comment_date']
				);
				$wpdb->insert( $wpdb->prefix.'followup_followup_history', $insert );

				$logs_processed++;

				$results[] = array(
					'id'        => $logs_processed,
					'status'    => 'success'
				);

			}

			FUE_Transients::set_transient( 'fue_update_'. $session, $logs );
			set_transient( 'fue_update_'. $session .'_processed', $logs_processed, 600 );

			$status = 'partial';

			if ( count( $logs ) == 0 ) {
				$wpdb->query("DELETE FROM {$wpdb->comments} WHERE comment_type = 'email_history'");
				$status = 'completed';
			}

			ob_clean();

			self::send_response( array(
				'status'            => $status,
				'update_data'       => $results,
				'session'           => $session
			) );

		}
	}

	/**
	 * JSON-encode and output the provided array
	 * @param array $array
	 */
	private static function send_response( $array ) {
	    @ob_clean();
		wp_send_json( $array ) ;
	}

	/**
	 * Send response for no orders found.
	 *
	 * @since 4.8.4
	 * @param array $session
	 */
	private static function send_no_orders_response( $session ) {
		self::send_response( array(
			'status'            => 'completed',
			'import_data'       => array(),
			'remaining_orders'  => 0,
			'session'           => $session,
			'total_orders'      => 0,
		) );
	}
}

FUE_AJAX::init();
