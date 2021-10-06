<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Handle frontend actions.
 */
class FUE_Front_Handler {

	public static function init() {
		// Catch unsubscribe request.
		add_action( 'wp', 'FUE_Front_Handler::process_unsubscribe_request' );
		add_action( 'template_redirect', 'FUE_Front_Handler::process_optout_request' );

		// FUE subscriptions.
		add_action( 'wp', 'FUE_Front_Handler::process_subscription_request' );

		// Email preview.
		add_action( 'template_redirect', 'FUE_Front_Handler::preview_email' );

		// Web version.
		add_action( 'template_redirect', 'FUE_Front_Handler::web_version' );

		add_action( 'wp_enqueue_scripts', 'FUE_Front_Handler::account_subscription_script' );
	}

	/**
	 * Process unsubscribe request.
	 *
	 * Add the submitted email address to the Excluded Emails list.
	 */
	public static function process_unsubscribe_request() {
		global $wpdb;

		if ( isset( $_POST['fue_action'] ) &&  'fue_unsubscribe' === $_POST['fue_action'] ) {
			$email      = sanitize_email( str_replace( ' ', '+', $_POST['fue_email'] ) );
			$email_id   = intval( $_POST['fue_eid'] );
			$error      = '';

			if ( empty( $email ) || ! is_email( $email ) ) {
				$error = urlencode( __( 'Please enter a valid email address', 'follow_up_emails' ) );
			}

			$email_hash = ( ! empty( $_POST['fue_hqid'] ) ) ? $_POST['fue_hqid'] : '';
			if ( $email_hash && ! hash_equals( fue_email_hash( $email ), $email_hash ) ) {
				$error = urlencode( __( 'Please enter the correct recipient email address', 'follow_up_emails' ) );
			}

			$order_id    = ( ! empty( $_POST['unsubscribe_order_id'] ) ) ? absint( $_POST['unsubscribe_order_id'] ) : 0;
			$unsubscribe = ( ! empty( $_POST['unsubscribe_all'] ) && 'yes' === $_POST['unsubscribe_all'] ) ? true : false;

			if ( ! $error && fue_is_email_excluded( $email, 0, $order_id ) ) {
				if ( $order_id > 0 ) {
					$error = sprintf( __( 'The email (%1$s) is already unsubscribed from receiving emails regarding Order %2$d', 'follow_up_emails' ), $email, $order_id );
				} else {
					$error = sprintf( __( 'The email (%s) is already unsubscribed from receiving emails', 'follow_up_emails' ), $email );
				}
			}

			if ( ! empty( $error ) ) {
				$url = add_query_arg( array(
					'fueid' => intval( $_POST['fue_eid'] ),
					'qid'   => ( ! empty( $_POST['fue_qid'] ) ) ? intval( $_POST['fue_qid'] ) : '',
					'hqid'  => $email_hash,
					'error' => urlencode( $error ),
				), fue_get_unsubscribe_url());

				wp_redirect( $url );
				exit;
			}

			if ( $unsubscribe ) {
				fue_exclude_email_address( $email, $email_id, 0 );

				if ( isset( $_GET['fue'] ) ) {
					do_action( 'fue_user_unsubscribed', $_GET['fue'] );
				}

				// Remove the email from the subscriber table if it exists.
				fue_remove_subscriber( $email );

			} elseif ( $order_id > 0 ) {
				fue_exclude_email_address( $email, $email_id, $order_id );

				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}followup_email_orders WHERE user_email = %s AND order_id = %d AND is_sent = 0", $email, $order_id ) );
			}

			wp_redirect( add_query_arg( 'fue_unsubscribed', 1, Follow_Up_Emails::get_account_url() ) );
			exit;

		} elseif ( isset( $_GET['fue_unsubscribed'] ) ) {
			Follow_Up_Emails::show_message( __( 'Thank you. Your email settings have been saved.', 'follow_up_emails' ) );
		}
	}

	/**
	 * Handle opt-in and opt-out requests.
	 */
	public static function process_optout_request() {
		if ( isset( $_POST['fue_action'] ) && 'fue_save_myaccount' === $_POST['fue_action'] ) {
			$opted_out  = ( isset( $_POST['fue_opt_out'] ) && 1 == $_POST['fue_opt_out'] ) ? true : false;
			$user       = wp_get_current_user();

			if ( $opted_out ) {
				// Unsubscribe this user using his/her email.
				fue_add_user_opt_out( $user->ID );
			} else {
				fue_remove_user_opt_out( $user->ID );
			}

			wp_redirect( add_query_arg( 'fue_updated', 1, Follow_Up_Emails::get_account_url() ) );
			exit;
		} elseif ( isset( $_GET['fue_updated'] ) ) {
			Follow_Up_Emails::show_message( __( 'Account updated', 'follow_up_emails' ) );
		}
	}

	/**
	 * Handle newsletter subscription requests.
	 */
	public static function process_subscription_request() {
		if ( empty( $_POST['fue_action'] ) || 'subscribe' !== $_POST['fue_action'] ) {
			return;
		}

		if (
			! isset( $_POST['_wpnonce'] )
			|| ! wp_verify_nonce( $_POST['_wpnonce'], 'fue_subscribe' )
		) {
			wp_die( 'Sorry, your browser submitted an invalid request. Please try again.' );
		}

		$back       = ! empty( $_POST['_wp_http_referer'] ) ? fue_clean( $_POST['_wp_http_referer'] ) : site_url();
		$email      = ! empty( $_POST['fue_subscriber_email'] ) ? fue_clean( $_POST['fue_subscriber_email'] ) : '';
		$first_name = ! empty( $_POST['fue_first_name'] ) ? fue_clean( $_POST['fue_first_name'] ) : '';
		$last_name  = ! empty( $_POST['fue_last_name'] ) ? fue_clean( $_POST['fue_last_name'] ) : '';
		$list       = ! empty( $_POST['fue_email_list'] ) ? fue_clean( $_POST['fue_email_list'] ) : '';

		$posted_lists = explode( ',', $list );

		// Validate lists.
		$valid_lists = fue_get_subscription_lists();

		$valid_list_names = array();
		foreach ( $valid_lists as $valid_list ) {
			$valid_list_names[] = $valid_list['list_name'];
		}

		foreach ( $posted_lists as $i => $posted_list ) {
			if ( ! in_array( $posted_list, $valid_list_names ) ) {
				unset( $posted_lists[ $i ] );
			}
		}

		$id = fue_add_subscriber_to_list( $list, array(
			'email'      => $email,
			'first_name' => $first_name,
			'last_name'  => $last_name,
		) );

		if ( is_wp_error( $id ) ) {
			$args = array(
				'error' => urlencode( $id->get_error_message() ),
				'email' => urlencode( $email ),
			);
		} else {
			$args = array(
				'error'          => '',
				'fue_subscribed' => 'yes',
			);
		}

		wp_redirect( add_query_arg( $args, $back ) );
		exit;
	}

	/**
	 * Show email preview
	 */
	public static function preview_email() {
		if ( empty( $_GET['fue-preview'] ) ) {
			return;
		}

		$email_id = absint( $_GET['email'] );
		$email  = new FUE_Email( $email_id );

		if ( empty( $_GET['key'] ) || md5( $email->post->post_title ) !== $_GET['key'] ) {
			wp_die( 'Sorry, your browser submitted an invalid request. Please try again.' );
		}

		$data = array(
			'test'             => true,
			'username'         => 'johndoe',
			'first_name'       => 'John',
			'last_name'        => 'Doe',
			'cname'            => 'John Doe',
			'user_id'          => '0',
			'order_id'         => '',
			'product_id'       => '',
			'email_to'         => '',
			'tracking_code'    => '',
			'store_url'        => home_url(),
			'store_url_secure' => home_url( null, 'https' ),
			'store_name'       => get_bloginfo( 'name' ),
			'unsubscribe'      => fue_get_unsubscribe_url(),
			'subject'          => $email->subject,
			'message'          => $email->message,
			'meta'             => array(),
		);

		$html = Follow_Up_Emails::instance()->mailer->get_email_preview_html( $data, $email );

		die( $html );
	}

	/**
	 * Display the web version of an email.
	 */
	public static function web_version() {
		if ( empty( $_GET['fue-web-version'] ) ) {
			return;
		}

		$id   = absint( $_GET['email-id'] );
		$key  = $_GET['key'];
		$item = new FUE_Sending_Queue_Item( $id );

		if ( ! $item->exists() || 1 != $item->is_sent ) {
			wp_die( __( 'Email could not be found', 'follow_up_emails' ) );
		}

		$item_key = md5( $item->user_email . '.' . $item->email_id . '.' . $item->send_on );

		if ( $item_key !== $key ) {
			wp_die( __( 'Invalid request. Please try again.', 'follow_up_emails' ) );
		}

		// Track pageview.
		$tracker = new FUE_Report_Email_Tracking( Follow_Up_Emails::instance() );
		$tracker->log_event( 'web_open', array(
			'event'      => 'web_open',
			'queue_id'   => $id,
			'email_id'   => $item->email_id,
			'user_id'    => $item->user_id,
			'user_email' => $item->user_email,
		) );

		$html = Follow_Up_Emails::instance()->mailer->get_email_web_version( $item );
		$html .= '<style>a.webversion {display:none;}</style>';
		echo $html;
		exit;
	}

	/**
	 * Register script that handles the updating of email subscriptions from the account page.
	 */
	public static function account_subscription_script() {
		wp_enqueue_script( 'fue-account-subscriptions', FUE_TEMPLATES_URL . '/js/fue-account-subscriptions.js', array( 'jquery' ), FUE_VERSION );
		wp_localize_script( 'fue-account-subscriptions', 'FUE', array(
			'ajaxurl'       => admin_url( 'admin-ajax.php' ),
			'ajax_loader'   => plugins_url() . '/woocommerce-follow-up-emails/templates/images/ajax-loader.gif',
		) );

		wp_enqueue_script( 'fue-front-script', FUE_TEMPLATES_URL . '/js/fue-front.js', array( 'jquery' ), FUE_VERSION, true );
		wp_localize_script( 'fue-front-script', 'FUE_Front', array(
			'is_logged_in'  => is_user_logged_in(),
			'ajaxurl'       => admin_url( 'admin-ajax.php' ),
		) );
	}
}

FUE_Front_Handler::init();
