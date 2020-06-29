<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH WooCommerce Request A Quote Premium
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWRAQ_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Implements the YITH_YWRAQ_Cron class.
 *
 * @class    YITH_YWRAQ_Cron
 * @package  YITH
 * @since    1.4.9
 * @author   YITH
 */
class YITH_YWRAQ_Cron {

	/**
	 * Single instance of the class
	 *
	 * @var \YITH_YWRAQ_Cron
	 */
	protected static $instance;

	/**
	 * Returns single instance of the class
	 *
	 * @return \YITH_YWRAQ_Cron
	 * @since 1.0.0
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 *
	 * Initialize plugin and registers actions and filters to be used
	 *
	 * @since  1.0.0
	 * @author Emanuela Castorina
	 */
	public function __construct() {

		add_action( 'wp_loaded', array( $this, 'ywraq_set_cron' ) );

		if ( 'yes' === get_option( 'ywraq_automate_send_quote' ) && '0' !== get_option( 'ywraq_cron_time' ) ) {
			add_filter( 'cron_schedules', array( $this, 'cron_schedule' ), 50 );
			add_action( 'ywraq_automatic_quote', array( $this, 'send_automatic_quote' ) );
		}

		add_action( 'ywraq_clean_cron', array( $this, 'clean_session' ) );
		add_action( 'ywraq_time_validation', array( $this, 'time_validation' ) );

	}

	/**
	 * Set Cron
	 */
	public function ywraq_set_cron() {

		if ( ! wp_next_scheduled( 'ywraq_time_validation' ) ) {
			$ve = get_option( 'gmt_offset' ) > 0 ? '+' : '-';
			wp_schedule_event( strtotime( '00:00 tomorrow ' . $ve . get_option( 'gmt_offset' ) . ' HOURS' ), 'daily', 'ywraq_time_validation' );
		}

		if ( ! wp_next_scheduled( 'ywraq_clean_cron' ) ) {
			wp_schedule_event( time(), 'daily', 'ywraq_clean_cron' );
		}

		if ( ! wp_next_scheduled( 'ywraq_automatic_quote' ) && 'yes' === get_option( 'ywraq_automate_send_quote' ) && '0' !== get_option( 'ywraq_cron_time' ) ) {
			wp_schedule_event( current_time( 'timestamp', 1 ), 'ywraq_gap', 'ywraq_automatic_quote' );
		}
	}

	/**
	 * Cron Schedule
	 *
	 * Add new schedules to WordPress.
	 *
	 * @since  1.0.0
	 * @author Emanuela Castorina
	 *
	 * @param $schedules
	 *
	 * @return mixed
	 */
	public function cron_schedule( $schedules ) {

		$interval  = 0;
		$cron_type = get_option( 'ywraq_cron_time_type' );
		$cron_time = get_option( 'ywraq_cron_time' );


		if ( 'hours' === $cron_type ) {
			$interval = 60 * 60 * $cron_time;
		} elseif ( 'days' === $cron_type ) {
			$interval = 24 * 60 * 60 * $cron_time;
		} elseif ( 'minutes' === $cron_type ) {
			$interval = 60 * $cron_time;
		}

		$schedules['ywraq_gap'] = array(
			'interval' => $interval,
			'display'  => esc_html__( 'YITH WooCommerce Request a Quote Cron', 'yith-woocommerce-request-a-quote' ),
		);

		return $schedules;
	}

	/**
	 * Clean the session on database
	 */
	public function clean_session() {
		global $wpdb;

		$cookie_name = '_' . ywraq_get_cookie_name() . '_%';
		$wpdb->query( $wpdb->prepare( "DELETE FROM " . $wpdb->prefix . "options  WHERE option_name LIKE %s", $cookie_name ) );

	}

	/**
	 * Function called from Cron to swich in
	 * ywraq-expired order status the request expired
	 *
	 * @since   1.4.9
	 * @author  Emanuela Castorina
	 * @return  void
	 */
	public function time_validation() {
		// todo:replace get_posts with wc_get_orders.
		$orders = get_posts(
			array(
				'numberposts' => - 1,
				'meta_query'  => array(
					array(
						'key'     => '_ywcm_request_expire',
						'value'   => '',
						'compare' => '!=',
					),
				),
				'post_type'   => 'shop_order',
				'post_status' => array( 'wc-ywraq-pending' ),
			)
		);

		foreach ( $orders as $order ) {
			$expired_data  = strtotime( get_post_meta( $order->ID, '_ywcm_request_expire', true ) );
			$expired_data += ( 24 * 60 * 60 ) - 1;

			do_action( 'send_reminder_quote_mail', $order->ID, $expired_data );

			if ( $expired_data < time() && 'wc-ywraq-pending' === $order->post_status ) {
				wp_update_post(
					array(
						'ID'          => $order->ID,
						'post_status' => 'wc-ywraq-expired',
					)
				);
			}
		}
	}

	/**
	 * Send automatic quote
	 *
	 * @since   1.4.9
	 * @author  Emanuela Castorina
	 * @return  void
	 */
	public function send_automatic_quote() {

		$orders = wc_get_orders(
			array(
				'numberposts' => - 1,
				'status'      => array( 'wc-ywraq-new' ),
			)
		);

		if ( $orders ) {
			foreach ( $orders as $order ) {
				$order_id = yit_get_prop( $order, 'id', true );
				do_action( 'create_pdf', $order_id );
				do_action( 'send_quote_mail', $order_id );
				$order->update_status( 'ywraq-pending' );
			}
		}

	}
}


/**
 * Unique access to instance of YITH_YWRAQ_Cron class
 *
 * @return \YITH_YWRAQ_Cron
 */
function YITH_YWRAQ_Cron() {
	return YITH_YWRAQ_Cron::get_instance();
}

YITH_YWRAQ_Cron();