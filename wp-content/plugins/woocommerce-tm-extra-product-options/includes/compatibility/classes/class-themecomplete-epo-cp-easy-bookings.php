<?php
/**
 * Compatibility class
 *
 * @package Extra Product Options/Compatibility
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * WooCommerce Easy Booking
 * https://wordpress.org/plugins/woocommerce-easy-booking-system/
 *
 * @package Extra Product Options/Compatibility
 * @version 6.0
 */
final class THEMECOMPLETE_EPO_CP_Easy_Bookings {

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_CP_Easy_Bookings|null
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'add_compatibility' ], 2 );
	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @since 1.0
	 */
	public function add_compatibility() {
		if ( ! class_exists( 'Easy_booking' ) ) {
			return;
		}
		add_filter( 'wc_epo_get_settings', [ $this, 'wc_epo_get_settings' ], 10, 1 );
		add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ], 4 );
		add_filter( 'wc_epo_cart_options_prices_before', [ $this, 'wc_epo_cart_options_prices_before' ], 10, 2 );
		add_filter( 'easy_booking_set_booking_price', [ $this, 'easy_booking_set_booking_price' ], 10, 2 );
		add_filter( 'wc_epo_adjust_cart_item', [ $this, 'wc_epo_adjust_cart_item' ], 10, 1 );
		add_filter( 'tm_epo_settings_headers', [ $this, 'tm_epo_settings_headers' ], 10, 1 );
		add_filter( 'tm_epo_settings_settings', [ $this, 'tm_epo_settings_settings' ], 10, 1 );

		add_filter( 'easy_booking_get_new_item_price', [ $this, 'easy_booking_get_new_item_price' ], 10, 4 );
		add_filter( 'easy_booking_one_date_price', [ $this, 'easy_booking_one_two_date_price' ], 10, 4 );
		add_filter( 'easy_booking_two_dates_price', [ $this, 'easy_booking_one_two_date_price' ], 10, 4 );
		add_filter( 'easy_booking_fragments', [ $this, 'easy_booking_fragments' ], 10, 1 );

		// >=2.09
		add_filter( 'easy_booking_booking_price_details', [ $this, 'easy_booking_booking_price_details' ], 10, 3 );

	}

	/**
	 * Add setting in main THEMECOMPLETE_EPO class
	 *
	 * @param array $settings Array of settings.
	 * @since 1.0
	 */
	public function wc_epo_get_settings( $settings = [] ) {
		if ( class_exists( 'Easy_booking' ) ) {
			$settings['tm_epo_easy_bookings_block'] = 'yes';
		}

		return $settings;
	}

	/**
	 * Alter cart fragments.
	 *
	 * @param array $fragments the cart fragments.
	 * @return array
	 */
	public function easy_booking_fragments( $fragments ) {
		$epo_price                   = floatval( THEMECOMPLETE_EPO()->easy_bookings_epo_price );
		$booking_price               = $fragments['booking_price'] + $epo_price;
		$fragments['booking_price']  = $booking_price;
		$fragments['epo_price']      = $epo_price;
		$fragments['epo_duration']   = THEMECOMPLETE_EPO()->easy_bookings_duration;
		$fragments['epo_base_price'] = $booking_price;
		if ( 'disable' === THEMECOMPLETE_EPO()->tm_epo_final_total_box ) {
			$fragments['epo_base_price'] = $booking_price - $epo_price;
		}

		return $fragments;
	}

	/**
	 * Alter booking details
	 *
	 * @param string $details The booking details.
	 * @param object $product The product object.
	 * @param array  $booking_data The booking data array.
	 * @return string
	 */
	public function easy_booking_booking_price_details( $details, $product, $booking_data ) {
		$extra_price = 0;
		if ( isset( $_REQUEST['epo_data'] ) && 'disable' === THEMECOMPLETE_EPO()->tm_epo_final_total_box ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$posted = [];
			parse_str( wp_unslash( $_REQUEST['epo_data'] ), $posted ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$epos = THEMECOMPLETE_EPO_CART()->tm_add_cart_item_data( [], themecomplete_get_id( $product ), $posted );

			$wc_booking_block_qty_multiplier = ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_easy_bookings_block ) ? 1 : 0;
			if ( ! empty( $epos ) && ! empty( $epos['tmcartepo'] ) ) {
				foreach ( $epos['tmcartepo'] as $key => $value ) {
					if ( ! empty( $value['price'] ) ) {

						$price        = floatval( $value['price'] );
						$option_price = 0;

						if ( ! empty( $wc_booking_block_qty_multiplier ) && ! empty( $duration ) ) {
							$option_price += $price * $duration;
						}
						if ( ! $option_price ) {
							$option_price += $price;
						}
						$extra_price += $option_price;
					}
				}
			}
		}

		THEMECOMPLETE_EPO()->easy_bookings_duration  = $booking_data['duration'];
		THEMECOMPLETE_EPO()->easy_bookings_epo_price = $extra_price;

		return $details;
	}

	/**
	 * Save booking data
	 *
	 * @param float  $booking_price The booking price.
	 * @param object $product The product object.
	 * @param object $_product The child product object.
	 * @param array  $data The booking data.
	 */
	public function easy_booking_one_two_date_price( $booking_price, $product, $_product, $data ) {
		$extra_price = 0;
		if ( isset( $_REQUEST['epo_data'] ) && 'disable' === THEMECOMPLETE_EPO()->tm_epo_final_total_box ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$posted   = [];
			$duration = $data['duration'];
			parse_str( wp_unslash( $_REQUEST['epo_data'] ), $posted ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$epos = THEMECOMPLETE_EPO_CART()->tm_add_cart_item_data( [], themecomplete_get_id( $_product ), $posted );

			$wc_booking_block_qty_multiplier = ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_easy_bookings_block ) ? 1 : 0;
			if ( ! empty( $epos ) && ! empty( $epos['tmcartepo'] ) ) {
				foreach ( $epos['tmcartepo'] as $key => $value ) {
					if ( ! empty( $value['price'] ) ) {

						$price        = floatval( $value['price'] );
						$option_price = 0;

						if ( ! empty( $wc_booking_block_qty_multiplier ) && ! empty( $duration ) ) {
							$option_price += $price * $duration;
						}
						if ( ! $option_price ) {
							$option_price += $price;
						}
						$extra_price += $option_price;
					}
				}
			}
		}

		THEMECOMPLETE_EPO()->easy_bookings_duration  = $duration;
		THEMECOMPLETE_EPO()->easy_bookings_epo_price = $extra_price;

		return $booking_price;
	}

	/**
	 * Save booking data (deprecated)
	 *
	 * @param float   $booking_price The booking price.
	 * @param object  $product The product object.
	 * @param object  $_product The child product object.
	 * @param integer $duration The booking duration.
	 */
	public function easy_booking_get_new_item_price( $booking_price, $product, $_product, $duration ) {
		$extra_price = 0;
		if ( isset( $_REQUEST['epo_data'] ) && 'disable' === THEMECOMPLETE_EPO()->tm_epo_final_total_box ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$posted = [];
			parse_str( wp_unslash( $_REQUEST['epo_data'] ), $posted ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$epos = THEMECOMPLETE_EPO_CART()->tm_add_cart_item_data( [], themecomplete_get_id( $_product ), $posted );

			$wc_booking_block_qty_multiplier = ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_easy_bookings_block ) ? 1 : 0;
			if ( ! empty( $epos ) && ! empty( $epos['tmcartepo'] ) ) {
				foreach ( $epos['tmcartepo'] as $key => $value ) {
					if ( ! empty( $value['price'] ) ) {

						$price        = floatval( $value['price'] );
						$option_price = 0;

						if ( ! empty( $wc_booking_block_qty_multiplier ) && ! empty( $duration ) ) {
							$option_price += $price * $duration;
						}
						if ( ! $option_price ) {
							$option_price += $price;
						}
						$extra_price += $option_price;
					}
				}
			}
		}

		THEMECOMPLETE_EPO()->easy_bookings_duration  = $duration;
		THEMECOMPLETE_EPO()->easy_bookings_epo_price = $extra_price;

		return $booking_price;
	}

	/**
	 * Enqueue scripts
	 *
	 * @since 1.0
	 */
	public function wp_enqueue_scripts() {
		if ( THEMECOMPLETE_EPO()->can_load_scripts() ) {
			wp_enqueue_script( 'themecomplete-comp-easy-bookings', THEMECOMPLETE_EPO_COMPATIBILITY_URL . 'assets/js/cp-easy-bookings.js', [ 'jquery' ], THEMECOMPLETE_EPO_VERSION, true );
			$args = [
				'wc_booking_block_qty_multiplier' => isset( THEMECOMPLETE_EPO()->tm_epo_easy_bookings_block ) && ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_easy_bookings_block ) ? 1 : 0,
			];
			wp_localize_script( 'themecomplete-comp-easy-bookings', 'TMEPOEASYBOOKINGSJS', $args );
		}
	}

	/**
	 * Add plugin setting (header)
	 *
	 * @param array $headers Array of settings.
	 * @since 1.0
	 */
	public function tm_epo_settings_headers( $headers = [] ) {
		$headers['easybookings'] = [ 'tcfa tcfa-calculator', esc_html__( 'WooCommerce Easy Bookings', 'woocommerce-tm-extra-product-options' ) ];

		return $headers;
	}

	/**
	 * Add plugin setting (setting)
	 *
	 * @param array $settings Array of settings.
	 * @since 1.0
	 */
	public function tm_epo_settings_settings( $settings = [] ) {
		$label                    = esc_html__( 'WooCommerce Easy Bookings', 'woocommerce-tm-extra-product-options' );
		$settings['easybookings'] = [
			[
				'type'  => 'tm_title',
				'id'    => 'epo_page_options',
				'title' => $label,
			],
			[
				'title'    => esc_html__( 'Multiply cost by block count', 'woocommerce-tm-extra-product-options' ),
				'desc'     => esc_html__( 'Enabling this will multiply the options price by the block count.', 'woocommerce-tm-extra-product-options' ),
				'id'       => 'tm_epo_easy_bookings_block',
				'class'    => 'chosen_select',
				'css'      => 'min-width:300px;',
				'default'  => 'yes',
				'type'     => 'select',
				'options'  => [
					'no'  => esc_html__( 'Disable', 'woocommerce-tm-extra-product-options' ),
					'yes' => esc_html__( 'Enable', 'woocommerce-tm-extra-product-options' ),
				],
				'desc_tip' => false,
			],
			[
				'type' => 'tm_sectionend',
				'id'   => 'epo_page_options',
			],
		];

		return $settings;
	}

	/**
	 * Sets custom price to the cart item
	 *
	 * @param float $booking_price The booking price.
	 * @param array $cart_item The cart item.
	 * @since 1.0
	 */
	public function easy_booking_set_booking_price( $booking_price, $cart_item ) {
		if ( ! empty( $cart_item['tmcartepo'] ) && isset( $cart_item['tm_epo_options_prices'] ) ) {
			$booking_price = $booking_price + $cart_item['tm_epo_options_prices'];
		}

		return $booking_price;
	}

	/**
	 * Set product original price in cart
	 *
	 * @param array $cart_item The cart item.
	 * @since 1.0
	 */
	public function wc_epo_adjust_cart_item( $cart_item ) {
		if (
			isset( $cart_item['data'] )
			&& is_object( $cart_item['data'] )
			&& property_exists( $cart_item['data'], 'id' )
			&& themecomplete_get_id( $cart_item['data'] )
		) {
			if ( isset( $cart_item['_booking_price'] ) && isset( $cart_item['_booking_duration'] ) ) {

				if ( ! empty( $cart_item['tmcartepo'] ) ) {
					$cart_item['tm_epo_product_original_price'] = $cart_item['tm_epo_product_original_price'] - $cart_item['tm_epo_options_prices'];
				}
			}
		}

		return $cart_item;
	}

	/**
	 * Adjust options when adding to cart
	 *
	 * @param array $price The price to adjust.
	 * @param array $cart_item The cart item.
	 * @since 1.0
	 */
	public function wc_epo_cart_options_prices_before( $price, $cart_item ) {
		$wc_booking_block_qty_multiplier = ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_easy_bookings_block ) ? 1 : 0;

		if (
			! $wc_booking_block_qty_multiplier
			|| ! ( isset( $cart_item['_booking_price'] ) && isset( $cart_item['_booking_duration'] ) )
			|| ! isset( $cart_item['data'] )
			|| ! is_object( $cart_item['data'] )
			|| ! property_exists( $cart_item['data'], 'id' )
			|| ! themecomplete_get_id( $cart_item['data'] )
		) {
			return $price;
		}

		$duration = ! empty( $cart_item['_booking_duration'] ) ? $cart_item['_booking_duration'] : 0;

		$c = $duration;

		$price = $c * $price;

		return $price;

	}

}
