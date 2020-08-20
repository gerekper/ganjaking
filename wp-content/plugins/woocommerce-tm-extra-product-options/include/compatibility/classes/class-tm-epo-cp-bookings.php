<?php
/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * WooCommerce Bookings 
 * https://woocommerce.com/products/woocommerce-bookings/
 *
 * @package Extra Product Options/Compatibility
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

final class THEMECOMPLETE_EPO_CP_bookings {

	/**
	 * The single instance of the class
	 *
	 * @since 1.0
	 */
	protected static $_instance = NULL;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded
	 *
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'add_compatibility' ), 2 );
	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @since 1.0
	 */
	public function add_compatibility() {

		if ( ! class_exists( 'WC_Bookings' ) ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 4 );

		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 11, 2 );
		add_filter( 'wc_epo_get_settings', array( $this, 'wc_epo_get_settings' ), 10, 1 );
		add_filter( 'wc_epo_cart_options_prices', array( $this, 'wc_epo_cart_options_prices' ), 10, 2 );
		add_filter( 'wc_epo_adjust_price', array( $this, 'wc_epo_adjust_price' ), 10, 2 );

		add_filter( 'booking_form_calculated_booking_cost', array( $this, 'adjust_booking_cost_old' ), 10, 3 );
		//for 1.15x
		add_filter( 'woocommerce_bookings_calculated_booking_cost', array( $this, 'adjust_booking_cost' ), 10, 3 );

		add_filter( 'wc_epo_adjust_cart_item', array( $this, 'wc_epo_adjust_cart_item' ), 10, 1 );

		add_filter( 'tm_epo_settings_headers', array( $this, 'tm_epo_settings_headers' ), 10, 1 );
		add_filter( 'tm_epo_settings_settings', array( $this, 'tm_epo_settings_settings' ), 10, 1 );

		add_filter( 'wcml_cart_contents_not_changed', array( $this, 'filter_bundled_product_in_cart_contents' ), 9999, 3 );
	}

	/**
	 * Add setting in main THEMECOMPLETE_EPO class
	 *
	 * @since 1.0
	 */
	public function wc_epo_get_settings( $settings = array() ) {
		if ( class_exists( 'WC_Bookings' ) ) {
			$settings["tm_epo_bookings_person"] = "yes";
			$settings["tm_epo_bookings_block"]  = "yes";
		}

		return $settings;
	}

	public function wp_enqueue_scripts() {
		if ( THEMECOMPLETE_EPO()->can_load_scripts() ) {
			global $post;
			$product                            = wc_get_product( $post->ID );
			$booking_has_person_cost_multiplier = is_callable( array( $product, 'get_has_person_cost_multiplier' ) ) && $product->get_has_person_cost_multiplier() ? 1 : 0;
			$booking_has_person_qty_multiplier  = is_callable( array( $product, 'get_has_person_qty_multiplier' ) ) && $product->get_has_person_qty_multiplier() ? 1 : 0;

			$tm_epo_bookings_person = isset( THEMECOMPLETE_EPO()->tm_epo_bookings_person );
			$tm_epo_bookings_block  = isset( THEMECOMPLETE_EPO()->tm_epo_bookings_block );

			if ( $tm_epo_bookings_person ) {
				if ( THEMECOMPLETE_EPO()->tm_epo_bookings_person == "yes" ) {
					$tm_epo_bookings_person = 1;
				} elseif ( THEMECOMPLETE_EPO()->tm_epo_bookings_person == "own" ) {
					$tm_epo_bookings_person = $booking_has_person_cost_multiplier;
				} else {
					$tm_epo_bookings_person = 0;
				}
			} else {
				$tm_epo_bookings_person = 0;
			}
			if ( $tm_epo_bookings_block ) {
				if ( THEMECOMPLETE_EPO()->tm_epo_bookings_block == "yes" ) {
					$tm_epo_bookings_block = 1;
				} elseif ( THEMECOMPLETE_EPO()->tm_epo_bookings_block == "own" ) {
					$tm_epo_bookings_block = $booking_has_person_qty_multiplier;
				} else {
					$tm_epo_bookings_block = 0;
				}
			} else {
				$tm_epo_bookings_block = 0;
			}

			wp_enqueue_script( 'themecomplete-comp-bookings', THEMECOMPLETE_EPO_PLUGIN_URL . '/include/compatibility/assets/js/cp-bookings.js', array( 'jquery' ), THEMECOMPLETE_EPO_VERSION, TRUE );
			$args = array(
				'wc_booking_person_qty_multiplier' => $tm_epo_bookings_person,
				'wc_booking_block_qty_multiplier'  => $tm_epo_bookings_block,
			);
			wp_localize_script( 'themecomplete-comp-bookings', 'TMEPOBOOKINGSJS', $args );
		}
	}

	public function add_cart_item_data( $cart_item, $product_id ) {
		if ( ! isset( $cart_item['tc_booking_original_price'] ) && isset( $cart_item['booking'] ) && isset( $cart_item['booking']['_cost'] ) ) {
			$cart_item['tc_booking_original_price'] = $cart_item['booking']['_cost'];
		}

		return $cart_item;
	}

	public function filter_bundled_product_in_cart_contents( $cart_item, $key, $current_language ) {
		global $woocommerce_wpml;

		if ( defined( 'WCML_MULTI_CURRENCIES_INDEPENDENT' ) && $cart_item['data'] instanceof WC_Product_Booking && isset( $cart_item['booking'] ) ) {

			$current_id      = apply_filters( 'translate_object_id', $cart_item['product_id'], 'product', TRUE, $current_language );
			$cart_product_id = $cart_item['product_id'];

			if ( $woocommerce_wpml->settings['enable_multi_currency'] == WCML_MULTI_CURRENCIES_INDEPENDENT || $current_id != $cart_product_id ) {

				$tm_epo_options_prices = floatval( $cart_item['tm_epo_options_prices'] );
				$current_cost          = floatval( $cart_item['data']->get_price() );

				$cart_item['data']->set_price( $current_cost + $tm_epo_options_prices );

			}

		}

		return $cart_item;
	}

	/**
	 * Add plugin setting (header)
	 *
	 * @since 1.0
	 */
	public function tm_epo_settings_headers( $headers = array() ) {
		$headers["bookings"] = array( "tcfa tcfa-calendar-alt", esc_html__( 'WooCommerce Bookings', 'woocommerce-tm-extra-product-options' ) );

		return $headers;
	}

	/**
	 * Add plugin setting (setting)
	 *
	 * @since 1.0
	 */
	public function tm_epo_settings_settings( $settings = array() ) {
		$label                = esc_html__( 'WooCommerce Bookings', 'woocommerce-tm-extra-product-options' );
		$settings["bookings"] = array(
			array(
				'type'  => 'tm_title',
				'id'    => 'epo_page_options',
				'title' => $label,
			),
			array(
				'title'    => esc_html__( 'Multiply cost by person count', 'woocommerce-tm-extra-product-options' ),
				'desc'     => esc_html__( 'Enabling this will multiply the options price by the person count.', 'woocommerce-tm-extra-product-options' ),
				'id'       => 'tm_epo_bookings_person',
				'class'    => 'chosen_select',
				'css'      => 'min-width:300px;',
				'default'  => 'yes',
				'type'     => 'select',
				'options'  => array(
					'no'  => esc_html__( 'Disable', 'woocommerce-tm-extra-product-options' ),
					'yes' => esc_html__( 'Enable', 'woocommerce-tm-extra-product-options' ),
					'own' => esc_html__( 'Using booking setting', 'woocommerce-tm-extra-product-options' ),
				),
				'desc_tip' => FALSE,
			),
			array(
				'title'    => esc_html__( 'Multiply cost by block count', 'woocommerce-tm-extra-product-options' ),
				'desc'     => esc_html__( 'Enabling this will multiply the options price by the block count.', 'woocommerce-tm-extra-product-options' ),
				'id'       => 'tm_epo_bookings_block',
				'class'    => 'chosen_select',
				'css'      => 'min-width:300px;',
				'default'  => 'yes',
				'type'     => 'select',
				'options'  => array(
					'no'  => esc_html__( 'Disable', 'woocommerce-tm-extra-product-options' ),
					'yes' => esc_html__( 'Enable', 'woocommerce-tm-extra-product-options' ),
					'own' => esc_html__( 'Using booking setting', 'woocommerce-tm-extra-product-options' ),
				),
				'desc_tip' => FALSE,
			),
			array( 'type' => 'tm_sectionend', 'id' => 'epo_page_options' ),
		);

		return $settings;
	}

	/**
	 * Set original product price in cart
	 *
	 * @since 1.0
	 */
	public function wc_epo_adjust_cart_item( $cart_item ) {
		if (
			isset( $cart_item['data'] )
			&& is_object( $cart_item['data'] )
			&& property_exists( $cart_item['data'], "id" )
			&& themecomplete_get_id( $cart_item['data'] )
		) {
			if ( $cart_item['data']->is_type( 'booking' ) ) {

				if ( ! empty( $cart_item['tmcartepo'] ) ) {
					$cart_item['tm_epo_product_original_price'] = $cart_item['tm_epo_product_original_price'] - $cart_item['tm_epo_options_prices'];
				}

			}
		}

		return $cart_item;
	}

	/**
	 * Adjust price in cart
	 *
	 * @since 1.0
	 */
	public function wc_epo_adjust_price( $adjust, $cart_item ) {
		if (
			isset( $cart_item['data'] )
			&& is_object( $cart_item['data'] )
			&& property_exists( $cart_item['data'], "id" )
			&& themecomplete_get_id( $cart_item['data'] )
		) {
			if ( $cart_item['data']->is_type( 'booking' ) ) {
				return FALSE;
			}
		}

		return $adjust;
	}

	/**
	 * Adjust options when adding to cart
	 *
	 * @since 1.0
	 */
	public function wc_epo_cart_options_prices( $price, $cart_data ) {
		$wc_booking_person_qty_multiplier = ( THEMECOMPLETE_EPO()->tm_epo_bookings_person == "yes" ) ? 1 : 0;
		$wc_booking_block_qty_multiplier  = ( THEMECOMPLETE_EPO()->tm_epo_bookings_block == "yes" ) ? 1 : 0;

		if (
			( ! $wc_booking_person_qty_multiplier && ! $wc_booking_block_qty_multiplier )
			|| ! isset( $cart_data['booking'] )
			|| ! isset( $cart_data['data'] )
			|| ! is_object( $cart_data['data'] )
			|| ! property_exists( $cart_data['data'], "id" )
			|| ! themecomplete_get_id( $cart_data['data'] )
		) {
			return $price;
		}

		$person   = ( ! empty( $cart_data['booking']['_persons'] ) && array_sum( $cart_data['booking']['_persons'] ) ) ? array_sum( $cart_data['booking']['_persons'] ) : 0;
		$duration = ! empty( $cart_data['booking']['_duration'] ) ? $cart_data['booking']['_duration'] : 0;

		$c = $person + $duration;
		if ( ! empty( $c ) ) {
			$price = $c * $price;
		}

		return $price;

	}

	/**
	 * Adjust the final booking cost
	 *
	 * @since 4.9.7
	 */
	public function adjust_booking_cost( $booking_cost, $product, $posted ) {

		if ( isset( $_POST ) && isset( $_POST['form'] ) ) {
			$posted = array();
			parse_str( $_POST['form'], $posted );
		} elseif ( isset( $_POST ) && ! isset( $_POST['form'] ) ) {
			$posted = $_POST;
		}

		if ( isset( $posted['tc_suppress_filter_booking_cost'] ) ) {
			return $booking_cost;
		}

		$epos         = THEMECOMPLETE_EPO_CART()->tm_add_cart_item_data( array(), themecomplete_get_id( $product ), $posted, TRUE );
		$extra_price  = 0;
		$booking_data = wc_bookings_get_posted_data( $posted, $product );

		$wc_booking_person_qty_multiplier = ( THEMECOMPLETE_EPO()->tm_epo_bookings_person == "yes" ) ? 1 : 0;
		$wc_booking_block_qty_multiplier  = ( THEMECOMPLETE_EPO()->tm_epo_bookings_block == "yes" ) ? 1 : 0;
		if ( ! empty( $epos ) && ! empty( $epos['tmcartepo'] ) ) {
			foreach ( $epos['tmcartepo'] as $key => $value ) {
				if ( ! empty( $value['price'] ) ) {

					$price        = floatval( $value['price'] );
					$option_price = 0;

					if ( ! empty( $wc_booking_person_qty_multiplier ) && ! empty( $booking_data['_persons'] ) && array_sum( $booking_data['_persons'] ) ) {
						$option_price += $price * array_sum( $booking_data['_persons'] );
					}
					if ( ! empty( $wc_booking_block_qty_multiplier ) && ! empty( $booking_data['_duration'] ) ) {
						$option_price += $price * $booking_data['_duration'];
					}
					if ( ! $option_price ) {
						$option_price += $price;
					}
					$extra_price += $option_price;
				}
			}

		}

		$extra_price  = floatval( $extra_price );
		$booking_cost = floatval( $booking_cost );
		$booking_cost = $booking_cost + $extra_price;

		return $booking_cost;
	}

	/**
	 * Adjust the final booking cost
	 *
	 * @since 1.0
	 */
	public function adjust_booking_cost_old( $booking_cost, $booking_form, $posted ) {
		if ( isset( $posted['tc_suppress_filter_booking_cost'] ) ) {
			return $booking_cost;
		}
		$epos         = THEMECOMPLETE_EPO_CART()->tm_add_cart_item_data( array(), themecomplete_get_id( $booking_form->product ), $posted, TRUE );
		$extra_price  = 0;
		$booking_data = $booking_form->get_posted_data( $posted );

		$wc_booking_person_qty_multiplier = ( THEMECOMPLETE_EPO()->tm_epo_bookings_person == "yes" ) ? 1 : 0;
		$wc_booking_block_qty_multiplier  = ( THEMECOMPLETE_EPO()->tm_epo_bookings_block == "yes" ) ? 1 : 0;
		if ( ! empty( $epos ) && ! empty( $epos['tmcartepo'] ) ) {
			foreach ( $epos['tmcartepo'] as $key => $value ) {
				if ( ! empty( $value['price'] ) ) {

					$price        = floatval( $value['price'] );
					$option_price = 0;

					if ( ! empty( $wc_booking_person_qty_multiplier ) && ! empty( $booking_data['_persons'] ) && array_sum( $booking_data['_persons'] ) ) {
						$option_price += $price * array_sum( $booking_data['_persons'] );
					}
					if ( ! empty( $wc_booking_block_qty_multiplier ) && ! empty( $booking_data['_duration'] ) ) {
						$option_price += $price * $booking_data['_duration'];
					}
					if ( ! $option_price ) {
						$option_price += $price;
					}
					$extra_price += $option_price;
				}
			}

		}

		$extra_price  = floatval( $extra_price );
		$booking_cost = floatval( $booking_cost );
		$booking_cost = $booking_cost + $extra_price;

		return $booking_cost;
	}
}
