<?php

namespace WCML\Compatibility\WcBookings;

use WC_Product;

class SharedHooks implements \IWPML_Action {

	/** @var \wpdb $wpdb */
	private $wpdb;

	/**
	 * @param \wpdb $wpdb
	 */
	public function __construct( \wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	public function add_hooks() {
		add_action( 'init', [ __CLASS__, 'load_assets' ] );
		add_filter( 'wcml_multi_currency_ajax_actions', [ __CLASS__, 'wcml_multi_currency_is_ajax' ] );

		$this->clear_transient_fields();
	}

	/**
	 * @param string|false $externalProductType
	 *
	 * @return void
	 */
	public static function load_assets( $externalProductType = false ) {
		global $pagenow;

		$productId = $pagenow == 'post.php' && isset( $_GET['post'] ) ? (int) $_GET['post'] : false;

		if ( $productId && get_post_type( $productId ) === 'product' ) {
			$product     = wc_get_product( $productId );
			$productType = $product->get_type();

			if ( ( self::isBooking( $product ) || $productType === $externalProductType ) || $pagenow == 'post-new.php' ) {
				wp_register_style( 'wcml-bookings-css', WCML_PLUGIN_URL . '/compatibility/res/css/wcml-bookings.css', [], WCML_VERSION );
				wp_enqueue_style( 'wcml-bookings-css' );

				wp_register_script( 'wcml-bookings-js', WCML_PLUGIN_URL . '/compatibility/res/js/wcml-bookings.js', [ 'jquery' ], WCML_VERSION, true );
				wp_enqueue_script( 'wcml-bookings-js' );
			}
		}
	}

	/**
	 * @param array $actions
	 *
	 * @return array
	 */
	public static function wcml_multi_currency_is_ajax( $actions ) {
		$actions[] = 'wc_bookings_calculate_costs';

		return $actions;
	}

	public function clear_transient_fields() {
		if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'wc_booking' && isset( $_GET['page'] ) && $_GET['page'] == 'booking_calendar' ) {

			// delete transient fields
			$this->wpdb->query(
				"
                DELETE FROM {$this->wpdb->options}
		        WHERE option_name LIKE '%book_dr_%'
		    "
			);
		}
	}

	/**
	 * @param WC_Product|int|string $product
	 *
	 * @return bool
	 */
	public static function isBooking( $product ) {
		if ( ! $product instanceof WC_Product ) {
			$product = wc_get_product( $product );
		}

		return $product && $product->get_type() === 'booking';
	}
}
