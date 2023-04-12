<?php // phpcs:disable WordPress.Security.NonceVerification
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
 * WooCommerce Bookings
 * https://woocommerce.com/products/woocommerce-bookings/
 *
 * @package Extra Product Options/Compatibility
 * @version 6.0
 */
final class THEMECOMPLETE_EPO_CP_Bookings {

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_CP_Bookings|null
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded
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

		if ( ! class_exists( 'WC_Bookings' ) ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ], 11 );

		add_filter( 'woocommerce_add_cart_item_data', [ $this, 'add_cart_item_data' ], 11, 1 );
		add_filter( 'wc_epo_get_settings', [ $this, 'wc_epo_get_settings' ], 10, 1 );
		add_filter( 'wc_epo_cart_options_prices_before', [ $this, 'wc_epo_cart_options_prices_before' ], 10, 2 );
		add_filter( 'wc_epo_adjust_price', [ $this, 'wc_epo_adjust_price' ], 10, 2 );

		add_filter( 'booking_form_calculated_booking_cost', [ $this, 'adjust_booking_cost_old' ], 10, 3 );
		// for 1.15x.
		add_filter( 'woocommerce_bookings_calculated_booking_cost', [ $this, 'adjust_booking_cost' ], 10, 3 );
		add_filter( 'wc_epo_adjust_cart_item_before', [ $this, 'wc_epo_adjust_cart_item_before' ], 10, 1 );
		add_filter( 'wc_epo_adjust_cart_item', [ $this, 'wc_epo_adjust_cart_item' ], 10, 1 );

		add_filter( 'tm_epo_settings_headers', [ $this, 'tm_epo_settings_headers' ], 10, 1 );
		add_filter( 'tm_epo_settings_settings', [ $this, 'tm_epo_settings_settings' ], 10, 1 );

		add_filter( 'wcml_cart_contents_not_changed', [ $this, 'filter_bundled_product_in_cart_contents' ], 9999, 3 );

		add_filter( 'woocommerce_bookings_calculated_booking_cost_success_output', [ $this, 'filter_output_cost' ], 10, 3 );
		add_action( 'wp_ajax_wc_bookings_calculate_costs', [ $this, 'filter_cost' ], 1 );
		add_action( 'wp_ajax_nopriv_wc_bookings_calculate_costs', [ $this, 'filter_cost' ], 1 );
	}

	/**
	 * Adjust the booking cost display
	 */
	public function filter_cost() {
		if ( THEMECOMPLETE_EPO()->tm_epo_bookings_add_options_display_cost !== 'yes' ) {
			if ( ! defined( 'WC_EPO_BOOKINGS_CALCULATED_BOOKING_COST_SUCCESS_OUTPUT' ) ) {
				define( 'WC_EPO_BOOKINGS_CALCULATED_BOOKING_COST_SUCCESS_OUTPUT', true );
			}
		}
	}

	/**
	 * Filter the cost display of bookings after booking selection.
	 * This only filters on success.
	 *
	 * @since 5.1
	 * @param string $output The hml output.
	 * @param string $display_price The displayed price.
	 * @param object $product The product object.
	 */
	public function filter_output_cost( $output, $display_price, $product ) {
		if ( isset( $_REQUEST['form'] ) ) {
			parse_str( wp_unslash( $_REQUEST['form'] ), $posted ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if ( ! defined( 'WC_EPO_BOOKINGS_CALCULATED_BOOKING_COST_SUCCESS_OUTPUT' ) ) {
				define( 'WC_EPO_BOOKINGS_CALCULATED_BOOKING_COST_SUCCESS_OUTPUT', true );
			}
			$booking_data = wc_bookings_get_posted_data( $posted, $product );
			$cost         = WC_Bookings_Cost_Calculation::calculate_booking_cost( $booking_data, $product );

			wp_send_json(
				[
					'result'    => 'SUCCESS',
					'html'      => $output,
					'raw_price' => (float) wc_get_price_to_display( $product, [ 'price' => $cost ] ),
				]
			);
		}
	}

	/**
	 * Add setting in main THEMECOMPLETE_EPO class
	 *
	 * @param array $settings Array of settings.
	 * @since 1.0
	 */
	public function wc_epo_get_settings( $settings = [] ) {
		if ( class_exists( 'WC_Bookings' ) ) {
			$settings['tm_epo_bookings_person']                   = 'yes';
			$settings['tm_epo_bookings_block']                    = 'yes';
			$settings['tm_epo_bookings_add_options_display_cost'] = 'yes';
		}

		return $settings;
	}

	/**
	 * Enqueue booking js
	 *
	 * @return void
	 */
	public function wp_enqueue_scripts() {
		if ( THEMECOMPLETE_EPO()->can_load_scripts() ) {
			global $post;
			$product                            = $post ? wc_get_product( $post->ID ) : false;
			$booking_has_person_cost_multiplier = is_callable( [ $product, 'get_has_person_cost_multiplier' ] ) && $product->get_has_person_cost_multiplier() ? 1 : 0;
			$booking_has_person_qty_multiplier  = is_callable( [ $product, 'get_has_person_qty_multiplier' ] ) && $product->get_has_person_qty_multiplier() ? 1 : 0;

			$tm_epo_bookings_person = isset( THEMECOMPLETE_EPO()->tm_epo_bookings_person );
			$tm_epo_bookings_block  = isset( THEMECOMPLETE_EPO()->tm_epo_bookings_block );

			if ( $tm_epo_bookings_person ) {
				if ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_bookings_person ) {
					$tm_epo_bookings_person = 1;
				} elseif ( 'own' === THEMECOMPLETE_EPO()->tm_epo_bookings_person ) {
					$tm_epo_bookings_person = $booking_has_person_cost_multiplier;
				} else {
					$tm_epo_bookings_person = 0;
				}
			} else {
				$tm_epo_bookings_person = 0;
			}
			if ( $tm_epo_bookings_block ) {
				if ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_bookings_block ) {
					$tm_epo_bookings_block = 1;
				} elseif ( 'own' === THEMECOMPLETE_EPO()->tm_epo_bookings_block ) {
					$tm_epo_bookings_block = $booking_has_person_qty_multiplier;
				} else {
					$tm_epo_bookings_block = 0;
				}
			} else {
				$tm_epo_bookings_block = 0;
			}

			wp_enqueue_script( 'themecomplete-comp-bookings', THEMECOMPLETE_EPO_COMPATIBILITY_URL . 'assets/js/cp-bookings.js', [ 'jquery' ], THEMECOMPLETE_EPO_VERSION, true );
			$args = [
				'booking_person_qty_multiplier'        => $booking_has_person_cost_multiplier,
				'booking_block_qty_multiplier'         => $booking_has_person_qty_multiplier,
				'wc_booking_person_qty_multiplier'     => $tm_epo_bookings_person,
				'wc_booking_block_qty_multiplier'      => $tm_epo_bookings_block,
				'wc_bookings_add_options_display_cost' => THEMECOMPLETE_EPO()->tm_epo_bookings_add_options_display_cost,
			];
			wp_localize_script( 'themecomplete-comp-bookings', 'TMEPOBOOKINGSJS', $args );
		}
	}

	/**
	 * Add to cart item
	 *
	 * @param array $cart_item The cart item.
	 * @return array
	 */
	public function add_cart_item_data( $cart_item ) {
		if ( ! isset( $cart_item['tc_booking_original_price'] ) && isset( $cart_item['booking'] ) && isset( $cart_item['booking']['_cost'] ) ) {
			$cart_item['tc_booking_original_price'] = $cart_item['booking']['_cost'];
		}

		return $cart_item;
	}

	/**
	 * Set price for product in cart
	 *
	 * @param array  $cart_item The cart iterm.
	 * @param string $key The cart key.
	 * @param string $current_language Current language.
	 * @return array
	 */
	public function filter_bundled_product_in_cart_contents( $cart_item, $key, $current_language ) {
		global $woocommerce_wpml;

		if ( defined( 'WCML_MULTI_CURRENCIES_INDEPENDENT' ) && $cart_item['data'] instanceof WC_Product_Booking && isset( $cart_item['booking'] ) ) {

			$current_id      = apply_filters( 'translate_object_id', $cart_item['product_id'], 'product', true, $current_language );
			$cart_product_id = $cart_item['product_id'];

			if ( WCML_MULTI_CURRENCIES_INDEPENDENT === $woocommerce_wpml->settings['enable_multi_currency'] || (int) $current_id !== (int) $cart_product_id ) {

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
	 * @param array $headers Array of settings.
	 * @since 1.0
	 */
	public function tm_epo_settings_headers( $headers = [] ) {
		$headers['bookings'] = [ 'tcfa tcfa-calendar-alt', esc_html__( 'WooCommerce Bookings', 'woocommerce-tm-extra-product-options' ) ];

		return $headers;
	}

	/**
	 * Add plugin setting (setting)
	 *
	 * @param array $settings Array of settings.
	 * @since 1.0
	 */
	public function tm_epo_settings_settings( $settings = [] ) {
		$label                = esc_html__( 'WooCommerce Bookings', 'woocommerce-tm-extra-product-options' );
		$settings['bookings'] = [
			[
				'type'  => 'tm_title',
				'id'    => 'epo_page_options',
				'title' => $label,
			],
			[
				'title'    => esc_html__( 'Multiply cost by person count', 'woocommerce-tm-extra-product-options' ),
				'desc'     => esc_html__( 'Enabling this will multiply the options price by the person count.', 'woocommerce-tm-extra-product-options' ),
				'id'       => 'tm_epo_bookings_person',
				'class'    => 'chosen_select',
				'css'      => 'min-width:300px;',
				'default'  => 'yes',
				'type'     => 'select',
				'options'  => [
					'no'  => esc_html__( 'Disable', 'woocommerce-tm-extra-product-options' ),
					'yes' => esc_html__( 'Enable', 'woocommerce-tm-extra-product-options' ),
					'own' => esc_html__( 'Using booking setting', 'woocommerce-tm-extra-product-options' ),
				],
				'desc_tip' => false,
			],
			[
				'title'    => esc_html__( 'Multiply cost by block count', 'woocommerce-tm-extra-product-options' ),
				'desc'     => esc_html__( 'Enabling this will multiply the options price by the block count.', 'woocommerce-tm-extra-product-options' ),
				'id'       => 'tm_epo_bookings_block',
				'class'    => 'chosen_select',
				'css'      => 'min-width:300px;',
				'default'  => 'yes',
				'type'     => 'select',
				'options'  => [
					'no'  => esc_html__( 'Disable', 'woocommerce-tm-extra-product-options' ),
					'yes' => esc_html__( 'Enable', 'woocommerce-tm-extra-product-options' ),
					'own' => esc_html__( 'Using booking setting', 'woocommerce-tm-extra-product-options' ),
				],
				'desc_tip' => false,
			],
			[
				'title'   => esc_html__( 'Add option cost to booking display price', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enabling this will add the option prices to the calculated booking cost.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_bookings_add_options_display_cost',
				'default' => 'yes',
				'type'    => 'checkbox',
			],
			[
				'type' => 'tm_sectionend',
				'id'   => 'epo_page_options',
			],
		];

		return $settings;
	}

	/**
	 * Set booking flag
	 *
	 * @param array $cart_item The cart item.
	 * @since 1.0
	 */
	public function wc_epo_adjust_cart_item_before( $cart_item ) {
		$cart_item['tc_epo_booking_original_price_adjustment'] = 0;

		return $cart_item;
	}

	/**
	 * Set original product price in cart
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
			&& empty( $cart_item['tc_epo_booking_original_price_adjustment'] )
		) {
			if ( $cart_item['data']->is_type( 'booking' ) ) {

				if ( ! empty( $cart_item['tmcartepo'] ) ) {
					$cart_item['tm_epo_product_original_price']            = $cart_item['tm_epo_product_original_price'] - $cart_item['tm_epo_options_prices'];
					$cart_item['tc_epo_booking_original_price_adjustment'] = 1;
				}
			}
		}

		return $cart_item;
	}

	/**
	 * Adjust price in cart
	 *
	 * @param boolean $adjust true or false.
	 * @param array   $cart_item The cart item.
	 * @return boolean
	 * @since 1.0
	 */
	public function wc_epo_adjust_price( $adjust, $cart_item ) {
		if (
			isset( $cart_item['data'] )
			&& is_object( $cart_item['data'] )
			&& property_exists( $cart_item['data'], 'id' )
			&& themecomplete_get_id( $cart_item['data'] )
		) {
			if ( $cart_item['data']->is_type( 'booking' ) ) {
				return false;
			}
		}

		return $adjust;
	}

	/**
	 * Adjust options when adding to cart
	 *
	 * @param array $price The price to adjust.
	 * @param array $cart_item The cart item.
	 * @since 1.0
	 */
	public function wc_epo_cart_options_prices_before( $price, $cart_item ) {
		$wc_booking_person_qty_multiplier = ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_bookings_person ) ? 1 : 0;
		$wc_booking_block_qty_multiplier  = ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_bookings_block ) ? 1 : 0;

		if (
			( ! $wc_booking_person_qty_multiplier && ! $wc_booking_block_qty_multiplier )
			|| ! isset( $cart_item['booking'] )
			|| ! isset( $cart_item['data'] )
			|| ! is_object( $cart_item['data'] )
			|| ! property_exists( $cart_item['data'], 'id' )
			|| ! themecomplete_get_id( $cart_item['data'] )
		) {
			return $price;
		}

		$person   = ( ! empty( $cart_item['booking']['_persons'] ) && array_sum( $cart_item['booking']['_persons'] ) ) ? array_sum( $cart_item['booking']['_persons'] ) : 0;
		$duration = ! empty( $cart_item['booking']['_duration'] ) ? $cart_item['booking']['_duration'] : 0;

		$c = 0;
		if ( $wc_booking_person_qty_multiplier ) {
			$c = $person + $c;
		}
		if ( $wc_booking_block_qty_multiplier ) {
			$c = $duration + $c;
		}
		if ( ! empty( $c ) ) {
			$price = $c * $price;
		}

		return $price;

	}

	/**
	 * Adjust the final booking cost
	 *
	 * @param array  $booking_cost Booking cost.
	 * @param object $product The product object.
	 * @param array  $posted Posted data.
	 * @since 4.9.7
	 */
	public function adjust_booking_cost( $booking_cost, $product, $posted ) {

		if ( defined( 'WC_EPO_BOOKINGS_CALCULATED_BOOKING_COST_SUCCESS_OUTPUT' ) ) {
			return $booking_cost;
		}
		if ( isset( $_POST ) ) {
			if ( isset( $_POST['form'] ) ) {
				$posted = [];
				parse_str( wp_unslash( $_POST['form'] ), $posted ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$posted['cpf_product_price'] = $booking_cost;
			} else {
				$_POST['cpf_product_price'] = $booking_cost;
				$posted                     = $_POST;
			}
		}

		if ( isset( $posted['tc_suppress_filter_booking_cost'] ) ) {
			return $booking_cost;
		}

		$epos         = THEMECOMPLETE_EPO_CART()->tm_add_cart_item_data( [], themecomplete_get_id( $product ), $posted );
		$extra_price  = 0;
		$booking_data = wc_bookings_get_posted_data( $posted, $product );

		$wc_booking_person_qty_multiplier = ( THEMECOMPLETE_EPO()->tm_epo_bookings_person === 'yes' ) ? 1 : 0;
		$wc_booking_block_qty_multiplier  = ( THEMECOMPLETE_EPO()->tm_epo_bookings_block === 'yes' ) ? 1 : 0;
		if ( ! empty( $epos ) && ! empty( $epos['tmcartepo'] ) ) {
			foreach ( $epos['tmcartepo'] as $key => $value ) {
				if ( ! empty( $value['price'] ) ) {

					$price        = floatval( $value['price'] );
					$option_price = 0;

					if ( ! empty( $wc_booking_person_qty_multiplier ) && ! empty( $booking_data['_persons'] ) && array_sum( $booking_data['_persons'] ) ) {
						$option_price += $price * array_sum( $booking_data['_persons'] );
					}
					if ( ! empty( $wc_booking_block_qty_multiplier ) && ! empty( $booking_data['_duration'] ) ) {
						if ( empty( $option_price ) ) {
							$option_price = $option_price * $booking_data['_duration'];
						} else {
							$option_price += $price * $booking_data['_duration'];
						}
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
	 * @param array  $booking_cost Booking cost.
	 * @param object $booking_form Booking form object.
	 * @param array  $posted Posted data.
	 * @since 1.0
	 */
	public function adjust_booking_cost_old( $booking_cost, $booking_form, $posted ) {
		if ( isset( $posted['tc_suppress_filter_booking_cost'] ) ) {
			return $booking_cost;
		}
		$epos         = THEMECOMPLETE_EPO_CART()->tm_add_cart_item_data( [], themecomplete_get_id( $booking_form->product ), $posted );
		$extra_price  = 0;
		$booking_data = $booking_form->get_posted_data( $posted );

		$wc_booking_person_qty_multiplier = ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_bookings_person ) ? 1 : 0;
		$wc_booking_block_qty_multiplier  = ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_bookings_block ) ? 1 : 0;
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
