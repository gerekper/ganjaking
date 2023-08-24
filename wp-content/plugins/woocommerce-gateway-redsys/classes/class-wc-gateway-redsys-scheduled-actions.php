<?php
/**
 * Add extra profile fields for users in admin
 *
 * @package WooCommerce Redsys Gateway WooCommerce.com > https://woocommerce.com/products/redsys-gateway/
 * @since 13.0.0
 * @author José Conti.
 * @link https://joseconti.com
 * @license GNU General Public License v3.0
 * @license URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright 2013-2023 José Conti.
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Gateway_Redsys_Scheduled_Actions Class.
 */
class WC_Gateway_Redsys_Scheduled_Actions {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'redsys_schedule_actions' ) );
		add_action( 'resdys_clean_transients', array( $this, 'redsys_clean_transients_action' ) );
		add_action( 'resdys_clean_tokens', array( $this, 'redsys_clean_tokens_action' ) );
		add_action( 'resdys_send_expired_cards', array( $this, 'send_expired_credit_card_email_action' ) );
		add_action( 'redsys_remove_expired_card', array( $this, 'remove_expired_card_action' ) );
	}
	/**
	 * Debug
	 *
	 * @param string $log Log.
	 */
	public function debug( $log ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$debug = new WC_Logger();
			$debug->add( 'redsys-scheduled-actions', $log );
		}
	}
	/**
	 * Schedule actions
	 */
	public function redsys_schedule_actions() {
		// Add conditional using settings. Active or not active.
		if ( false === as_next_scheduled_action( 'resdys_clean_transients' ) ) {
			as_schedule_recurring_action( strtotime( 'now' ), DAY_IN_SECONDS, 'resdys_clean_transients' );
		}
		// Add recurring para eliminar datos asociados a tokens que ya no existen.
		if ( false === as_next_scheduled_action( 'resdys_clean_tokens' ) ) {
			as_schedule_recurring_action( strtotime( 'now' ), DAY_IN_SECONDS, 'resdys_clean_tokens' );
		}
		if ( false === as_next_scheduled_action( 'resdys_send_expired_cards' ) ) {
			as_schedule_recurring_action( strtotime( 'now' ), DAY_IN_SECONDS * 7, 'resdys_send_expired_cards' );
		}
		$timestamp = strtotime( '2nd of next month' );
		if ( false === as_next_scheduled_action( 'redsys_remove_expired_card' ) ) {
			as_schedule_recurring_action( $timestamp, MONTH_IN_SECONDS, 'redsys_remove_expired_card' );
		}
	}
	/**
	 * Clean transients
	 */
	public function redsys_clean_transients_action() {
		global $wpdb;

		$expired = $wpdb->get_col( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout%' AND option_value < UNIX_TIMESTAMP()" );
		foreach ( $expired as $transient ) {
			$key = str_replace( '_transient_timeout_', '', $transient );
			delete_transient( $key );
			$this->debug( 'Transient deleted: ' . $key );
		}
	}
	/**
	 * Clean tokens
	 */
	public function redsys_clean_tokens_action() {
		global $wpdb;

		$texids = $wpdb->get_col( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '%txnid_%'" );
		foreach ( $texids as $texid ) {
			$key = str_replace( 'txnid_', '', $texid );
			$this->debug( 'Texid_id: ' . $key );
			$this->debug( 'Checking if Token exist' );
			$token = WC_Payment_Tokens::get( $key );
			if ( ! $token ) {
				$this->debug( 'Token not exist' );
				$this->debug( 'Deleting information related to token ID: ' . $key );
				delete_option( 'token_type_' . $key );
				delete_option( 'txnid_' . $key );
				$this->debug( 'Deleted: token_type_' . $key );
				$this->debug( 'Deleted: txnid_' . $key );
			} else {
				$this->debug( 'Token exist' );
			}
		}
	}
	/**
	 * Send email to users with expired cards
	 */
	function send_expired_credit_card_email_action() {
		global $wpdb;

		$this->debug( 'Function send_expired_credit_card_email_action' );

		if ( 'yes' !== get_option( 'send_emails_to_customer_expired_card' ) ) {
			return;
		}
		$this->debug( 'Send Email is active' );

		$tokens_table    = $wpdb->prefix . 'woocommerce_payment_tokens';
		$tokenmeta_table = $wpdb->prefix . 'woocommerce_payment_tokenmeta';

		$current_year  = date( 'Y' );
		$current_month = date( 'm' );

		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT token.user_id
				FROM {$tokens_table} AS token
				INNER JOIN {$tokenmeta_table} AS expiry_year ON token.token_id = expiry_year.payment_token_id
				INNER JOIN {$tokenmeta_table} AS expiry_month ON token.token_id = expiry_month.payment_token_id
				WHERE expiry_year.meta_key = 'expiry_year' AND expiry_year.meta_value = %d
				AND expiry_month.meta_key = 'expiry_month' AND expiry_month.meta_value = %d",
				$current_year,
				$current_month
			)
		);
		$this->debug( 'print_r $results: ' . print_r( $results, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
		// Si hay tarjetas caducadas, envía un correo a cada usuario.
		if ( ! empty( $results ) ) {
			$payment_methods_url = wc_get_endpoint_url( 'payment-methods', '', wc_get_page_permalink( 'myaccount' ) );
			$this->debug( 'Your credit card expires this month' );
			$this->debug( 'print_r $results: ' . print_r( $results, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			$this->debug( 'Your credit card expires this month' );
			$this->debug( '$payment_methods_url: ' . $payment_methods_url );
			foreach ( $results as $result ) {
				$user_id    = $result->user_id;
				$user_email = get_user_meta( $user_id, 'billing_email', true );
				$nombre     = get_user_meta( $user_id, 'billing_first_name', true );
				$apellido   = get_user_meta( $user_id, 'billing_last_name', true );
				$this->debug( 'User ID: ' . $user_id );
				$this->debug( 'User Email: ' . $user_email );

				$subject  = esc_html__( 'Your credit card expires this month at ', 'redsys-woocommerce' ) . get_bloginfo( 'name' );
				$message  = '<html><body>';
				$message .= esc_html__( 'Dear ', 'redsys-woocommerce' ) . $nombre . ' ' . $apellido . ',<br />';
				$message .= esc_html__( 'Your payment credit card expires this month. Please update your payment information to continue enjoying our services.', 'redsys-woocommerce' ) . '<br />';
				$message .= '<a href="' . esc_url( $payment_methods_url ) . '">' . esc_html__( 'Update your payment information', 'redsys-woocommerce' ) . '</a>';
				$message .= '</body></html>';
				$data     = array(
					'user_id'    => $user_id,
					'user_email' => $user_email,
					'subject'    => $subject,
					'message'    => $message,
				);

				/**
				 * Filter the email message sent to the user when the credit card expires.
				 *
				 * @since 22.1.0
				 */
				$data_filter = apply_filters( 'redsys_send_expired_credit_card_email', $data );

				$user_email = $data_filter['user_email'];
				$subject    = $data_filter['subject'];
				$message    = $data_filter['message'];

				// Establecer las cabeceras para enviar el correo como HTML.
				$headers = array( 'Content-Type: text/html; charset=UTF-8' );

				// Envía el correo.
				wp_mail( $user_email, $subject, $message, $headers );
				$this->debug( 'Email send' );
			}
		}
	}
	/**
	 * Clean tokens
	 */
	function remove_expired_card_action() {
		global $wpdb;

		$this->debug( 'Function remove_expired_card_action' );

		if ( 'yes' !== get_option( 'remove_expired_card' ) ) {
			return;
		}
		$this->debug( 'Send Email is active' );

		$tokens_table    = $wpdb->prefix . 'woocommerce_payment_tokens';
		$tokenmeta_table = $wpdb->prefix . 'woocommerce_payment_tokenmeta';

		$current_year  = date( 'Y' );
		$current_month = date( 'm' );

		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT token.token_id, token.user_id
				FROM {$tokens_table} AS token
				INNER JOIN {$tokenmeta_table} AS expiry_year ON token.token_id = expiry_year.payment_token_id
				INNER JOIN {$tokenmeta_table} AS expiry_month ON token.token_id = expiry_month.payment_token_id
				WHERE expiry_year.meta_key = 'expiry_year' AND expiry_year.meta_value <= %d
				AND expiry_month.meta_key = 'expiry_month' AND expiry_month.meta_value < %d",
				$current_year,
				$current_month
			)
		);
		$this->debug( 'print_r $results: ' . print_r( $results, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
		// Si hay tarjetas caducadas, envía un correo a cada usuario.
		if ( ! empty( $results ) ) {
			$payment_methods_url = wc_get_endpoint_url( 'payment-methods', '', wc_get_page_permalink( 'myaccount' ) );
			$this->debug( 'Expired credit card found' );
			$this->debug( 'print_r $results: ' . print_r( $results, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			$this->debug( '$payment_methods_url: ' . $payment_methods_url );
			foreach ( $results as $result ) {
				$user_id    = $result->user_id;
				$user_email = get_user_meta( $user_id, 'billing_email', true );
				$token_id   = $result->token_id;

				$this->debug( 'User ID: ' . $user_id );
				$this->debug( 'User Email: ' . $user_email );

				$subject  = esc_html__( 'Your credit card has expired at ', 'redsys-woocommerce' ) . get_bloginfo( 'name' );
				$message  = '<html><body>';
				$message .= esc_html__( 'Dear user, You credit card has expired and has been deleted. Please update your payment information to continue enjoying our services.', 'redsys-woocommerce' ) . '<br />';
				$message .= '<a href="' . esc_url( $payment_methods_url ) . '">' . esc_html__( 'Update your payment information', 'redsys-woocommerce' ) . '</a>';
				$message .= '</body></html>';

				/**
				 * Filter the email message sent to the user when the credit card expires.
				 *
				 * @since 22.1.0
				 */
				$message_filter = apply_filters( 'redsys_send_deleted_credit_card_email', $message, $user_id );

				// Establecer las cabeceras para enviar el correo como HTML.
				$headers = array( 'Content-Type: text/html; charset=UTF-8' );

				// Envía el correo.
				wp_mail( $user_email, $subject, $message_filter, $headers );
				$this->debug( 'Email sent' );

				// Remove the expired token using WooCommerce tokenization API.
				$token = WC_Payment_Tokens::get( $token_id );
				if ( $token ) {
					$token->delete();
					$this->debug( 'Token removed' );
				}
			}
		}
	}
}
return new WC_Gateway_Redsys_Scheduled_Actions();
