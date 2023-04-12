<?php
/**
 * Extra Product Options Associated Products Functionality
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extra Product Options Associated Products Functionality
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 */
class THEMECOMPLETE_EPO_Associated_Products {

	/**
	 * The product discount
	 *
	 * @var string
	 */
	private $discount = '';

	/**
	 * The product discount type
	 *
	 * @var string
	 */
	private $discount_type = '';

	/**
	 * If the discount is applied to the addons
	 *
	 * @var string
	 */
	private $discount_exclude_addons = '';

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_Associated_Products|null
	 * @since 5.0
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 5.0
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
	 * @since 5.0
	 */
	public function __construct() {

		// Modify cart.
		add_filter( 'woocommerce_add_cart_item', [ $this, 'woocommerce_add_cart_item' ], 11, 1 );
		// Modifies the cart item.
		add_filter( 'woocommerce_before_calculate_totals', [ $this, 'woocommerce_before_calculate_totals' ], 10, 1 );
		// Modify option prices for discounts.
		add_filter( 'associated_tmcp_static_prices', [ $this, 'associated_tmcp_static_prices' ], 10, 2 );
		// Load cart data on every page load.
		add_filter( 'woocommerce_get_cart_item_from_session', [ $this, 'woocommerce_get_cart_item_from_session' ], 9998, 3 );
		// Add associated products (from elements) to the cart.
		add_action( 'woocommerce_add_to_cart', [ $this, 'associated_woocommerce_add_to_cart' ], 8, 6 );
		// Remove associated products when the parent gets removed.
		add_action( 'woocommerce_remove_cart_item', [ $this, 'associated_woocommerce_remove_cart_item' ], 10, 2 );
		// Restore associated products when the parent gets restored.
		add_action( 'woocommerce_restore_cart_item', [ $this, 'associated_woocommerce_restore_cart_item' ], 10, 2 );
		// Clear notices.
		add_action( 'init', [ $this, 'associated_clear_removed_notice' ] );
		// Validate quantity update in cart.
		add_filter( 'woocommerce_update_cart_validation', [ $this, 'woocommerce_update_cart_validation' ], 10, 4 );
		// Sync associated products quantity input.
		add_filter( 'woocommerce_cart_item_quantity', [ $this, 'associated_woocommerce_cart_item_quantity' ], 10, 2 );
		// Sync associated products quantity with main product.
		add_action( 'woocommerce_after_cart_item_quantity_update', [ $this, 'woocommerce_after_cart_item_quantity_update' ], 1, 2 );
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.7', '<' ) ) {
			add_action( 'woocommerce_before_cart_item_quantity_zero', [ $this, 'woocommerce_after_cart_item_quantity_update' ] );
		}
		// Make sure products marked as associated have a parent.
		add_action( 'woocommerce_cart_loaded_from_session', [ $this, 'associated_woocommerce_cart_loaded_from_session' ], 99999 );

		// Associated product cart remove link.
		add_filter( 'woocommerce_cart_item_remove_link', [ $this, 'associated_woocommerce_cart_item_remove_link' ], 10, 2 );
		// Associated product table item classes.
		add_filter( 'woocommerce_cart_item_class', [ $this, 'associated_woocommerce_cart_item_class' ], 10, 2 );
		// Wrap associated products name in cart.
		add_filter( 'woocommerce_cart_item_name', [ $this, 'associated_woocommerce_cart_item_name' ], 99999, 3 );
		// Wrap associated products price in cart.
		add_filter( 'woocommerce_cart_item_price', [ $this, 'associated_woocommerce_cart_item_price' ], 99999, 3 );
		// Wrap associated products subtotal in cart.
		add_filter( 'woocommerce_cart_item_subtotal', [ $this, 'associated_woocommerce_cart_item_price' ], 99999, 3 );
		// Wrap associated products subtotal in checkout.
		add_filter( 'woocommerce_checkout_item_subtotal', [ $this, 'associated_woocommerce_cart_item_price' ], 99999, 3 );
		// Associated product table item classes in mini cart.
		add_filter( 'woocommerce_mini_cart_item_class', [ $this, 'associated_woocommerce_cart_item_class' ], 99999, 2 );
		// Wrap associated products price in mini cart.
		add_filter( 'woocommerce_widget_cart_item_quantity', [ $this, 'associated_woocommerce_widget_cart_item_quantity' ], 99999, 3 );
		// Make cart item count not count associated products.
		add_filter( 'woocommerce_cart_contents_count', [ $this, 'associated_woocommerce_cart_contents_count' ] );

		// Edit cart functionality.
		add_action( 'woocommerce_add_to_cart', [ $this, 'woocommerce_add_to_cart' ], 10, 6 );

		// Add meta to order.
		add_action( 'woocommerce_checkout_create_order_line_item', [ $this, 'woocommerce_checkout_create_order_line_item' ], 50, 3 );

		// Wrap associated products subtotal in order.
		add_filter( 'woocommerce_order_formatted_line_subtotal', [ $this, 'associated_woocommerce_order_formatted_line_subtotal' ], 10, 3 );
		// Add table item classes.
		add_filter( 'woocommerce_order_item_class', [ $this, 'woocommerce_order_item_class' ], 10, 3 );
		// Add the label name to associated products at the order-details template.
		add_filter( 'woocommerce_order_item_name', [ $this, 'woocommerce_order_item_name' ], 10, 2 );
		// Delete associated product quantity from order-details template.
		add_filter( 'woocommerce_order_item_quantity_html', [ $this, 'woocommerce_order_item_quantity_html' ], 10, 2 );
		// Filter order item count removing associated products.
		add_filter( 'woocommerce_get_item_count', [ $this, 'woocommerce_get_item_count' ], 10, 3 );

		// Hook for displaying extra options.
		add_action( 'wc_epo_associated_product_display', [ $this, 'wc_epo_associated_product_display' ], 10, 9 );
		// Hook for displaying extra options.
		add_action( 'wp_ajax_nopriv_wc_epo_get_associated_product_html', [ $this, 'wc_epo_get_associated_product_html' ] );
		add_action( 'wp_ajax_wc_epo_get_associated_product_html', [ $this, 'wc_epo_get_associated_product_html' ] );

		// Fix internal associated data.
		add_action( 'woocommerce_update_cart_action_cart_updated', [ $this, 'woocommerce_update_cart_action_cart_updated' ], 9999, 1 );

		// Hide associated products in the cart.
		add_action( 'woocommerce_cart_item_visible', [ $this, 'woocommerce_cart_item_visible' ], 10, 3 );
		add_action( 'woocommerce_widget_cart_item_visible', [ $this, 'woocommerce_cart_item_visible' ], 10, 3 );

		// Hide associated products in the checkout.
		add_action( 'woocommerce_checkout_cart_item_visible', [ $this, 'woocommerce_checkout_cart_item_visible' ], 10, 3 );

	}

	/**
	 * Returns the price in html format of the associated product.
	 * This is used for the initial prices of the product elements.
	 *
	 * @param object $product The product object.
	 * @param string $discount The product discount.
	 * @param string $discount_type The product discount type.
	 * @param bool   $discount_applied If the discount is already applied.
	 * @return string
	 * @since 6.2
	 */
	public function get_associated_price_html( $product, $discount, $discount_type, $discount_applied = false ) {

		$price_html = $product->get_price_html();
		$type       = themecomplete_get_product_type( $product );
		$free_text  = ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_remove_free_price_label ) ? ( '' !== THEMECOMPLETE_EPO()->tm_epo_replacement_free_price_text ? THEMECOMPLETE_EPO()->tm_epo_replacement_free_price_text : '' ) : esc_attr__( 'Free!', 'woocommerce' );
		$use_from   = ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_use_from_on_price );

		if ( 'variable' === $type ) {
			$price = $product->get_variation_price(); // Min active price.
			if ( ! wc_prices_include_tax() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) ) {
				$price = themecomplete_get_price_including_tax(
					$product,
					[
						'qty'   => 1,
						'price' => $price,
					]
				);
			} elseif ( wc_prices_include_tax() && 'excl' === get_option( 'woocommerce_tax_display_shop' ) ) {
				$price = themecomplete_get_price_excluding_tax(
					$product,
					[
						'qty'   => 1,
						'price' => $price,
					]
				);
			}

			$price             = (float) $price;
			$price             = (float) wc_format_decimal( (float) $price, wc_get_price_decimals() );
			$min_regular_price = $price;

			$original_price = $price;
			// $discount_applied cannot be applied on variable products.
			$price     = $this->get_discounted_price( $price, $discount, $discount_type );
			$price     = (float) apply_filters( 'wc_epo_product_element_initial_variable_price', $price, $price, $product );
			$min_price = (float) $price;
			$max_price = (float) $product->get_variation_price( 'max' ); // Max active price.
			// $discount_applied cannot be applied on variable products.
			$max_price         = (float) $this->get_discounted_price( $max_price, $discount, $discount_type );
			$max_regular_price = (float) $product->get_variation_regular_price( 'max' );

			$is_free = (float) 0 === (float) $min_price && (float) 0 === (float) $max_price;
			if ( $product->is_on_sale() || $price !== $original_price ) {
				$displayed_price = $min_price !== $max_price
				? ( function_exists( 'wc_get_price_to_display' )
					? wc_format_sale_price( $max_price, $min_price )
					: '<del>' . ( is_numeric( $max_price ) ? wc_price( $max_price ) : $max_price ) . '</del> <ins>' . ( is_numeric( $min_price ) ? wc_price( $min_price ) : $min_price ) . '</ins>'
				)
				: themecomplete_price( $min_price );

				$price_html = $min_price !== $max_price
					? ( ! $use_from
						/* translators: %1 %2: from price to price  */
						? sprintf( esc_html_x( '%1$s&ndash;%2$s', 'Price range: from-to', 'woocommerce' ), themecomplete_price( $min_price ), themecomplete_price( $max_price ) )
						: ( function_exists( 'wc_get_price_html_from_text' ) ? wc_get_price_html_from_text() : $product->get_price_html_from_text() ) . $displayed_price )
					: $displayed_price;

				$regular_price = $min_regular_price !== $max_regular_price
					? ( ! $use_from
						? themecomplete_price( $max_regular_price )
						: ( function_exists( 'wc_get_price_html_from_text' ) ? wc_get_price_html_from_text() : $product->get_price_html_from_text() ) . themecomplete_price( $min_regular_price ) )
					: themecomplete_price( $min_regular_price );

				$regular_price = '<del>' . $regular_price . '</del>';
				if ( $min_price === $max_price && $min_regular_price === $max_regular_price ) {
					$price_html = themecomplete_price( $max_price );
				}
				$price_html = ( ! $use_from ? ( $regular_price . ' <ins>' . $price_html . '</ins>' ) : $price_html ) . $product->get_price_suffix();

			} elseif ( $is_free ) {
				$price_html = apply_filters( 'woocommerce_variable_free_price_html', $free_text, $product );
			} else {
				$price_html = $min_price !== $max_price
					? ( ! $use_from
						/* translators: %1 %2: from price to price  */
						? sprintf( esc_html_x( '%1$s &ndash; %2$s', 'Price range: from-to', 'woocommerce' ), themecomplete_price( $min_price ), themecomplete_price( $max_price ) )
						: ( function_exists( 'wc_get_price_html_from_text' ) ? wc_get_price_html_from_text() : $product->get_price_html_from_text() ) . themecomplete_price( $min_price ) )
					: themecomplete_price( $min_price );
				$price_html = $price_html . $product->get_price_suffix();
			}
		} else {
			$price             = (float) $product->get_price();
			$min_regular_price = $product->get_regular_price();
			$product_data      = $product->get_data();

			if ( ! wc_prices_include_tax() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) ) {
				$price = themecomplete_get_price_including_tax(
					$product,
					[
						'qty'   => 1,
						'price' => $price,
					]
				);

				$min_regular_price = themecomplete_get_price_including_tax(
					$product,
					[
						'qty'   => 1,
						'price' => $min_regular_price,
					]
				);
			} elseif ( wc_prices_include_tax() && 'excl' === get_option( 'woocommerce_tax_display_shop' ) ) {
				$price = themecomplete_get_price_excluding_tax(
					$product,
					[
						'qty'   => 1,
						'price' => $price,
					]
				);

				$min_regular_price = themecomplete_get_price_excluding_tax(
					$product,
					[
						'qty'   => 1,
						'price' => $min_regular_price,
					]
				);
			}

			$price             = (float) $price;
			$price             = (float) wc_format_decimal( (float) $price, wc_get_price_decimals() );
			$min_regular_price = (float) wc_format_decimal( (float) $min_regular_price, wc_get_price_decimals() );

			$original_price = $price;
			if ( ! $discount_applied ) {
				$price = $this->get_discounted_price( $price, $discount, $discount_type );
			}
			$min_price = $price;
			$max_price = $price;
			if ( ! $discount_applied ) {
				$max_price = $this->get_discounted_price( $max_price, $discount, $discount_type );
			}
			$max_regular_price     = $min_regular_price;
			$display_price         = $min_price;
			$display_regular_price = $min_regular_price;

			$price_html = '';
			if ( THEMECOMPLETE_EPO()->tc_get_price( $product ) > 0 ) {

				if ( ( $product->is_on_sale() || $price !== $original_price ) && THEMECOMPLETE_EPO()->tc_get_regular_price( $product ) ) {
					if ( $use_from && ( $max_price > 0 || $max_price > $min_price ) ) {

						$displayed_price = ( function_exists( 'wc_get_price_to_display' )
							? wc_format_sale_price( $display_regular_price, $display_price )
							: '<del>' . ( is_numeric( $display_regular_price ) ? wc_price( $display_regular_price ) : $display_regular_price ) . '</del> <ins>' . ( is_numeric( $display_price ) ? wc_price( $display_price ) : $display_price ) . '</ins>'
						);
						$price_html     .= ( function_exists( 'wc_get_price_html_from_text' )
								? wc_get_price_html_from_text()
								: $product->get_price_html_from_text() )
											. $displayed_price;
						$price_html     .= $product->get_price_suffix();
					} else {
						$price_html .= wc_format_sale_price( $display_regular_price, $display_price );
					}
				} else {
					if ( $use_from && ( $max_price > 0 || $max_price > $min_price ) ) {
						$price_html .= ( function_exists( 'wc_get_price_html_from_text' ) ? wc_get_price_html_from_text() : $product->get_price_html_from_text() );
					}
					$price_html .= themecomplete_price( $display_price ) . $product->get_price_suffix();
				}
			} elseif ( THEMECOMPLETE_EPO()->tc_get_price( $product ) === '' ) {

				$price_html = apply_filters( 'woocommerce_empty_price_html', '', $product );

			} elseif ( (float) THEMECOMPLETE_EPO()->tc_get_price( $product ) === (float) 0 ) {
				if ( $product->is_on_sale() && THEMECOMPLETE_EPO()->tc_get_regular_price( $product ) ) {
					if ( $use_from && ( $max_price > 0 || $max_price > $min_price ) ) {
						$price_html .= ( function_exists( 'wc_get_price_html_from_text' ) ? wc_get_price_html_from_text() : $product->get_price_html_from_text() ) . themecomplete_price( ( $min_price > 0 ) ? $min_price : 0 );
					} else {

						$price_html .= $product->get_price_html();

						$price_html = apply_filters( 'woocommerce_free_sale_price_html', $price_html, $product );
					}
				} else {
					if ( $use_from && ( $max_price > 0 || $max_price > $min_price ) ) {
						$price_html .= ( function_exists( 'wc_get_price_html_from_text' ) ? wc_get_price_html_from_text() : $product->get_price_html_from_text() ) . themecomplete_price( ( $min_price > 0 ) ? $min_price : 0 );
					} else {

						$price_html = '<span class="amount">' . $free_text . '</span>';

						$price_html = apply_filters( 'woocommerce_free_price_html', $price_html, $product );
					}
				}
			}
		}

		return $price_html;

	}

	/**
	 * Hide associated products in the cart
	 *
	 * @param boolean $visible If the product should be visible.
	 * @param array   $cart_item The cart item.
	 * @param string  $cart_item_key The cart item key.
	 * @since 6.2
	 */
	public function woocommerce_cart_item_visible( $visible, $cart_item, $cart_item_key ) {

		if ( isset( $cart_item['associated_parent'] ) && ! empty( $cart_item['associated_parent'] ) && isset( $cart_item['hiddenin'] ) && is_array( $cart_item['hiddenin'] ) && in_array( 'cart', $cart_item['hiddenin'], true ) ) {
			$visible = false;
		}

		return $visible;

	}

	/**
	 * Hide associated products in the checkout
	 *
	 * @param boolean $visible If the product should be visible.
	 * @param array   $cart_item The cart item.
	 * @param string  $cart_item_key The cart item key.
	 * @since 6.2
	 */
	public function woocommerce_checkout_cart_item_visible( $visible, $cart_item, $cart_item_key ) {

		if ( isset( $cart_item['associated_parent'] ) && ! empty( $cart_item['associated_parent'] ) && isset( $cart_item['hiddenin'] ) && is_array( $cart_item['hiddenin'] ) && in_array( 'checkout', $cart_item['hiddenin'], true ) ) {
			$visible = false;
		}

		return $visible;

	}

	/**
	 * Include variation attributes
	 *
	 * @param boolean $should_include_attributes If the variation title should include attrbiutes.
	 * @since 6.0
	 */
	public function woocommerce_product_variation_title_include_attributes( $should_include_attributes ) {
		return true;
	}

	/**
	 * Hook for displaying extra options
	 *
	 * @since 5.0
	 */
	public function wc_epo_get_associated_product_html() {

		global $tm_is_ajax;

		$tm_is_ajax  = true;
		$json_result = [
			'result' => 0,
			'html'   => '',
		];

		if ( isset( $_REQUEST['layout_mode'] ) && isset( $_REQUEST['product_id'] ) && isset( $_REQUEST['name'] ) && isset( $_REQUEST['uniqid'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			$name                              = sanitize_text_field( wp_unslash( $_REQUEST['name'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$uniqid                            = sanitize_text_field( wp_unslash( $_REQUEST['uniqid'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$quantity_min                      = isset( $_REQUEST['quantity_min'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['quantity_min'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$quantity_max                      = isset( $_REQUEST['quantity_max'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['quantity_max'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$priced_individually               = isset( $_REQUEST['priced_individually'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['priced_individually'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$discount                          = isset( $_REQUEST['discount'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['discount'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$discount_type                     = isset( $_REQUEST['discount_type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['discount_type'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$discount_exclude_addons           = isset( $_REQUEST['discount_exclude_addons'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['discount_exclude_addons'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$mode                              = isset( $_REQUEST['mode'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['mode'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$layout_mode                       = sanitize_text_field( wp_unslash( $_REQUEST['layout_mode'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$product_id                        = absint( wp_unslash( $_REQUEST['product_id'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$parent_id                         = isset( $_REQUEST['parent_id'] ) ? absint( wp_unslash( $_REQUEST['parent_id'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$product                           = wc_get_product( $product_id );
			$product_list                      = [];
			$product_list_available_variations = [];

			$show_image       = isset( $_REQUEST['show_image'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['show_image'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$show_title       = isset( $_REQUEST['show_title'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['show_title'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$show_price       = isset( $_REQUEST['show_price'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['show_price'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$show_description = isset( $_REQUEST['show_description'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['show_description'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$show_meta        = isset( $_REQUEST['show_meta'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['show_meta'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$counter          = isset( $_REQUEST['counter'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['counter'] ) ) : '0'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$disable_epo      = isset( $_REQUEST['disable_epo'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['disable_epo'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( ! empty( $product ) && is_object( $product ) ) {

				$type                 = themecomplete_get_product_type( $product );
				$attributes           = [];
				$available_variations = [];

				if ( 'variable' === $type ) {
					if ( is_callable( [ $product, 'get_variation_attributes' ] ) ) {
						// workaround to get discounts shownn in the product for variable products.
						$isset_discount_type = false;
						if ( isset( $_REQUEST['discount_type'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
							$isset_discount_type           = sanitize_text_field( wp_unslash( $_REQUEST['discount_type'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
							$isset_discount                = sanitize_text_field( wp_unslash( $_REQUEST['discount'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
							$isset_discount_exclude_addons = sanitize_text_field( wp_unslash( $_REQUEST['discount_exclude_addons'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						}
						$_REQUEST['discount_type']           = $discount_type;
						$_REQUEST['discount']                = $discount;
						$_REQUEST['discount_exclude_addons'] = $discount_exclude_addons;
						$attributes                          = $product->get_variation_attributes();
						$get_variations                      = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );
						$available_variations                = $get_variations ? $product->get_available_variations() : false;

						$product_list[ $product_id ] = $attributes;

						$variations_json                                  = wp_json_encode( $available_variations );
						$variations_attr                                  = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );
						$product_list_available_variations[ $product_id ] = $variations_attr;
						if ( $isset_discount_type ) {
							$_REQUEST['discount_type']           = $isset_discount_type;
							$_REQUEST['discount']                = $isset_discount;
							$_REQUEST['discount_exclude_addons'] = $isset_discount_exclude_addons;
						} else {
							unset( $_REQUEST['discount_type'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
							unset( $_REQUEST['discount'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
							unset( $_REQUEST['discount_exclude_addons'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						}
					}
				} else {
					$product_list[ $product_id ]                      = [];
					$product_list_available_variations[ $product_id ] = '';
				}

				$__min_value = $quantity_min;
				$__max_value = $quantity_max;

				if ( '' !== $__min_value ) {
					$__min_value = floatval( $__min_value );
				} else {
					$__min_value = 0;
				}
				if ( '' === $__min_value ) {
					$__min_value = 0;
				}
				if ( '' !== $__max_value ) {
					$__max_value = floatval( $__max_value );
				}

				if ( is_numeric( $__min_value ) && is_numeric( $__max_value ) ) {
					if ( $__min_value > $__max_value ) {
						$__max_value = $__min_value + 1;
					}
				}

				$template = 'template-item';

				$args = [
					'tm_element_settings'               => [
						'uniqid'      => $uniqid,
						'disable_epo' => $disable_epo,
					],
					'option'                            => [
						'_default_value_counter' => $counter,
						'counter'                => $counter,
					],
					'mode'                              => $mode,
					'template'                          => $template,
					'quantity_min'                      => $__min_value,
					'quantity_max'                      => $__max_value,
					'priced_individually'               => $priced_individually,
					'discount'                          => $discount,
					'discount_type'                     => $discount_type,
					'discount_exclude_addons'           => $discount_exclude_addons,
					'name'                              => $name,
					'product_id'                        => $product_id,
					'parent_id'                         => $parent_id,
					'product_list'                      => $product_list,
					'product_list_available_variations' => $product_list_available_variations,
					'show_image'                        => $show_image,
					'show_title'                        => $show_title,
					'show_price'                        => $show_price,
					'show_description'                  => $show_description,
					'show_meta'                         => $show_meta,
				];
				ob_start();
				wc_get_template(
					'products/template-container-ajax.php',
					$args,
					THEMECOMPLETE_EPO_DISPLAY()->get_template_path(),
					THEMECOMPLETE_EPO_DISPLAY()->get_default_path()
				);
				$json_result['html']   = ob_get_clean();
				$json_result['result'] = 200;

			}
		}

		wp_send_json( $json_result );
		die();

	}

	/**
	 * Modify option prices for discounts
	 *
	 * @param mixed $tmcp_static_prices The option static prices.
	 * @param array $cart_item The cart item.
	 * @since 5.0.12.4
	 */
	public function associated_tmcp_static_prices( $tmcp_static_prices = '', $cart_item = [] ) {

		if ( empty( $cart_item['associated_discount_exclude_addons'] ) && ! empty( $cart_item['associated_discount'] ) && isset( $cart_item['associated_parent'] ) && ! empty( $cart_item['associated_parent'] ) ) {
			$tmcp_static_prices = $this->get_discounted_price( $tmcp_static_prices, $cart_item['associated_discount'], $cart_item['associated_discount_type'] );
		}

		return $tmcp_static_prices;

	}

	/**
	 * Apply discount to the option price
	 *
	 * @param mixed $price The current product price.
	 * @param mixed $original_price The original product price.
	 * @since 5.0.8
	 */
	public function wc_epo_apply_discount( $price = '', $original_price = '' ) {
		if ( empty( $this->discount_exclude_addons ) ) {
			if ( ! is_array( $price ) ) {
				return $this->get_discounted_price( $price, $this->discount, $this->discount_type );
			} else {
				foreach ( $price as $key => $value ) {
					$price[ $key ] = $this->get_discounted_price( $value, $this->discount, $this->discount_type );
				}
				return $price;
			}
		}

		return $price;

	}

	/**
	 * Hook for displaying extra options
	 *
	 * @param object  $product The product object.
	 * @param string  $uniqid The id to use for the epo container.
	 * @param boolean $disable_epo If the addons are disabled, true or false.
	 * @param boolean $per_product_pricing If the product has pricing, true or false.
	 * @param string  $discount The product discount.
	 * @param string  $discount_type The product discount type.
	 * @param string  $discount_exclude_addons If the addons should be excluded fro mthe discount.
	 * @param mixed   $counter The option counter.
	 * @param string  $element_uniqid The product element unique id.
	 * @since 5.0
	 */
	public function wc_epo_associated_product_display( $product, $uniqid, $disable_epo = false, $per_product_pricing = false, $discount = '', $discount_type = '', $discount_exclude_addons = '', $counter = '', $element_uniqid = '' ) {

		if ( $product ) {
			global $associated_product;
			$associated_product = $product;
			$product_id         = themecomplete_get_id( $product );
			if ( ! $per_product_pricing ) {
				$per_product_pricing = 0;
			} else {
				$per_product_pricing = 1;
			}

			$uniqid = $uniqid . '.' . $counter;
			$uniqid = str_replace( [ '.', ' ', '[' ], '', $uniqid );
			$uniqid = THEMECOMPLETE_EPO_HELPER()->normalize_data( $uniqid );
			$epo_id = $uniqid;
			?>
			<div class="tc-extra-product-options-inline" data-epo-id="<?php echo esc_attr( $epo_id ); ?>" data-product-id="<?php echo esc_attr( $product_id ); ?>">
			<?php
			if ( empty( $discount_exclude_addons ) && $discount ) {
				$this->discount                = $discount;
				$this->discount_type           = $discount_type;
				$this->discount_exclude_addons = $discount_exclude_addons;
				add_filter( 'wc_epo_apply_discount', [ $this, 'wc_epo_apply_discount' ], 10, 2 );
			}

			$not_isset_global_post = false;
			$isset_global_post     = false;
			if ( ! isset( $GLOBALS['post'] ) ) {
				$not_isset_global_post = true;
				$GLOBALS['post']       = $product_id; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
			} elseif ( isset( $GLOBALS['post'] ) ) {
				$isset_global_post = $GLOBALS['post'];
				$GLOBALS['post']   = $product_id; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
			}

			THEMECOMPLETE_EPO()->associated_type                = $product->get_type();
			THEMECOMPLETE_EPO()->is_associated                  = true;
			THEMECOMPLETE_EPO()->associated_per_product_pricing = $per_product_pricing;
			THEMECOMPLETE_EPO()->associated_element_uniqid      = $element_uniqid;
			THEMECOMPLETE_EPO()->associated_product_counter     = $counter;
			THEMECOMPLETE_EPO()->set_inline_epo( true );
			THEMECOMPLETE_EPO_DISPLAY()->set_discount( $discount, $discount_type, $discount_exclude_addons );
			THEMECOMPLETE_EPO_DISPLAY()->set_epo_internal_counter( $epo_id );
			if ( ! $disable_epo ) {
				THEMECOMPLETE_EPO_DISPLAY()->tm_epo_fields( $product, $uniqid );
			}
			THEMECOMPLETE_EPO_DISPLAY()->tm_epo_totals( $product, $uniqid );
			THEMECOMPLETE_EPO_DISPLAY()->tm_add_inline_style();
			THEMECOMPLETE_EPO()->set_inline_epo( false );
			THEMECOMPLETE_EPO_DISPLAY()->set_discount( '', '' );
			THEMECOMPLETE_EPO_DISPLAY()->restore_epo_internal_counter();
			THEMECOMPLETE_EPO()->is_associated                  = false;
			THEMECOMPLETE_EPO()->associated_per_product_pricing = null;
			THEMECOMPLETE_EPO()->associated_type                = false;
			THEMECOMPLETE_EPO()->associated_element_uniqid      = false;
			THEMECOMPLETE_EPO()->associated_product_counter     = false;
			if ( empty( $discount_exclude_addons ) && $discount ) {
				$this->discount                = '';
				$this->discount_type           = '';
				$this->discount_exclude_addons = '';
				remove_filter( 'wc_epo_apply_discount', [ $this, 'wc_epo_apply_discount' ], 10 );
			}

			if ( $not_isset_global_post ) {
				unset( $GLOBALS['post'] );
			}
			if ( $isset_global_post ) {
				$GLOBALS['post'] = $isset_global_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
			}

			?>
			</div>
			<?php
		}
	}

	/**
	 * Validates in-cart component quantity changes.
	 *
	 * @param boolean $passed The current passed status.
	 * @param string  $cart_item_key The cart item key.
	 * @param array   $cart_item The cart item.
	 * @param integer $quantity The product quantity.
	 * @since 5.0
	 */
	public function woocommerce_update_cart_validation( $passed, $cart_item_key, $cart_item, $quantity ) {

		if ( isset( $cart_item['associated_parent'] ) && ! empty( $cart_item['associated_parent'] ) && isset( WC()->cart->cart_contents[ $cart_item['associated_parent'] ] ) ) {

			$parent = WC()->cart->cart_contents[ $cart_item['associated_parent'] ];

			$associated_id = array_search( $cart_item_key, $parent['associated_products'], true );

			if ( false === $associated_id ) {
				return false;
			}

			if ( isset( WC()->cart->cart_contents[ $cart_item_key ]['did_set_quantity'] ) ) {
				$quantity = WC()->cart->cart_contents[ $cart_item_key ]['did_set_quantity'];
			}

			$parent_key      = $cart_item['associated_parent'];
			$parent_quantity = 1;
			if ( THEMECOMPLETE_EPO()->tm_epo_global_product_element_quantity_sync === 'yes' ) {
				$parent_quantity = floatval( $parent['quantity'] );
			}
			$min_quantity = floatval( $cart_item['tmproducts'][ $associated_id ]['quantity_min'] );
			$max_quantity = $cart_item['tmproducts'][ $associated_id ]['quantity_max'] ? floatval( $parent_quantity * $cart_item['tmproducts'][ $associated_id ]['quantity_max'] ) : '';

			if ( $quantity < $min_quantity ) {
				/* translators: %1 Product title %2 Minimum quantity. */
				wc_add_notice( sprintf( __( 'The quantity of &quot;%1$s&quot; cannot be lower than %2$d.', 'woocommerce-tm-extra-product-options' ), $cart_item['data']->get_title(), $min_quantity ), 'error' );

				return false;

			} elseif ( $max_quantity && $quantity > $max_quantity ) {
				/* translators: %1 Product title %2 Maximum  quantity. */
				wc_add_notice( sprintf( __( 'The quantity of &quot;%1$s&quot; cannot be higher than %2$d.', 'woocommerce-tm-extra-product-options' ), $cart_item['data']->get_title(), $max_quantity ), 'error' );

				return false;

			} elseif ( 0 != $quantity % $parent_quantity ) { // phpcs:ignore WordPress.PHP.StrictComparisons
				/* translators: %1 Product title %2 Parent product quantity. */
				wc_add_notice( sprintf( __( 'The quantity of &quot;%1$s&quot; must be entered in multiples of %2$d.', 'woocommerce-tm-extra-product-options' ), $cart_item['data']->get_title(), $parent_quantity ), 'error' );

				return false;

			} else {

				WC()->cart->cart_contents[ $parent_key ]['tmproducts'][ $associated_id ]['quantity'] = $quantity / $parent_quantity;

				$associated_cart_keys = $this->get_associated_cart_keys( $parent, WC()->cart->cart_contents );
				foreach ( $associated_cart_keys as $associated_key_id => $associated_key ) {
					WC()->cart->cart_contents[ $associated_key ]['tmproducts'][ $associated_key_id ]['quantity'] = $quantity / $parent_quantity;
				}
			}
		}

		return $passed;
	}

	/**
	 * Filter order item count removing associated products
	 *
	 * @param integer $count The count or order items that are of $type.
	 * @param string  $type The item type.
	 * @param object  $order The order object.
	 * @since 5.0
	 */
	public function woocommerce_get_item_count( $count, $type, $order ) {

		$remove = 0;

		if ( function_exists( 'is_account_page' ) && is_account_page() ) {
			foreach ( $order->get_items() as $item ) {
				if ( isset( $item['_associated_key'] ) && '' !== $item['_associated_key'][0] ) {
					$remove += $item->get_quantity();
				}
			}
		}

		return (int) $count - (int) $remove;
	}

	/**
	 * Delete associated product quantity from order-details template
	 *
	 * Quantity is inserted into the product name by 'woocommerce_order_item_name'.
	 *
	 * @param string $content The content html.
	 * @param array  $item The order item.
	 * @since 5.0
	 */
	public function woocommerce_order_item_quantity_html( $content, $item ) {

		if ( isset( $item['_associated_key'] ) && '' !== $item['_associated_key'][0] ) {
			$content = '';
		}

		return $content;
	}

	/**
	 * Add the label name to associated products at the order-details template.
	 *
	 * @param string $class The item class.
	 * @param array  $item The order item.
	 * @param object $order The order object.
	 * @since 5.0
	 */
	public function woocommerce_order_item_class( $class, $item, $order ) {

		if ( isset( $item['_associated_key'] ) && '' !== $item['_associated_key'][0] ) {
			$class .= ' tc-associated-table-product';
		} elseif ( isset( $item['_tmproducts'] ) && '' !== $item['_tmproducts'][0] ) {
			$class .= ' tc-container-table-product';
		}

		return $class;

	}

	/**
	 * Add the label name to associated products at the order-details template.
	 *
	 * @param string $content The content html.
	 * @param array  $item The order item.
	 * @since 5.0
	 */
	public function woocommerce_order_item_name( $content, $item ) {

		if ( isset( $item['_associated_key'] ) && '' !== $item['_associated_key'][0] ) {

			$qty = '';

			if ( did_action( 'woocommerce_view_order' ) ||
				did_action( 'woocommerce_thankyou' ) ||
				did_action( 'before_woocommerce_pay' ) ||
				did_action( 'woocommerce_account_view-subscription_endpoint' ) ) {

				$qty = '<strong class="associated-product-quantity"> &times; '
					. $item['qty']
					. '</strong>';

			}

			if ( isset( $item['_associated_name'] ) && '' !== $item['_associated_name'][0] ) {
				$content = '<div class="tc-associated-table-product-name">' . $item['_associated_name'][0] . '</div>' . $content;
			}

			$content = '<div class="tc-associated-table-product-indent">' . $content . $qty . '</div>';
		}

		return $content;

	}

	/**
	 * Adds meta data to the order - WC >= 2.7 (crud)
	 *
	 * @param object $item The item object.
	 * @param string $cart_item_key The cart item key.
	 * @param array  $values Cart item values.
	 * @since 5.0
	 */
	public function woocommerce_checkout_create_order_line_item( $item, $cart_item_key, $values ) {

		if ( isset( $values['associated_parent'] ) && ! empty( $values['associated_parent'] ) ) {

			$item->add_meta_data( '_associated_name', [ $values['tmproducts'][ $values['associated_key'] ]['name'] ] );
			$item->add_meta_data( '_associated_key', [ $values['associated_key'] ] );
			$item->add_meta_data( '_priced_individually', [ $values['associated_priced_individually'] ] );
			$item->add_meta_data( '_required', [ $values['associated_required'] ] );

			if ( isset( $values['hiddenin'] ) && is_array( $values['hiddenin'] ) && in_array( 'order', $values['hiddenin'], true ) ) {
				$item->add_meta_data( '_associated_hidden', [ '1' ] );
			}
		}

		if ( ! empty( $values['associated_products'] ) ) {
			if ( is_array( $values['tmproducts'] ) ) {
				$item->add_meta_data(
					'_tmproducts',
					[
						array_map(
							function ( $v ) {
								return $v['product_id'];
							},
							$values['tmproducts']
						),
					]
				);
			}
		}

	}

	/**
	 * Wrap associated products in cart
	 *
	 * @param string $subtotal The subtotal html.
	 * @param array  $item The order item.
	 * @param object $order The order object.
	 * @since 5.0
	 */
	public function associated_woocommerce_order_formatted_line_subtotal( $subtotal, $item, $order ) {

		if ( isset( $item['_associated_key'] ) && '' !== $item['_associated_key'][0] ) {
			$priced_individually = isset( $item['_priced_individually'] ) ? $item['_priced_individually'][0] : '';

			if ( empty( $priced_individually ) && empty( $item->get_subtotal( 'edit' ) ) ) {
				$subtotal = '';
			} elseif ( $subtotal ) {
				$subtotal = '<span class="tc-associated-table-product-price">' . $subtotal . '</span>';
			}
		}

		return $subtotal;

	}

	/**
	 * Modifies the cart item
	 * Add associated product weights to the main product
	 *
	 * @param array $cart_object The cart object.
	 *
	 * @since 6.1
	 */
	public function woocommerce_before_calculate_totals( $cart_object ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}
		if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) {
			return;
		}

		if ( method_exists( $cart_object, 'get_cart' ) ) {
			$cart_contents = $cart_object->get_cart();
		} else {
			$cart_contents = $cart_object->cart_contents;
		}

		foreach ( $cart_contents as $cart_key => $cart_item ) {
			if ( ! isset( $cart_item['associated_parent'] ) && isset( $cart_item['associated_products'] ) ) {
				$associated_weight = 0.0;
				foreach ( $cart_item['associated_products'] as $associated_cart_key ) {
					if ( is_array( WC()->cart->cart_contents ) && isset( WC()->cart->cart_contents[ $associated_cart_key ] ) ) {
						$associated_product_weight = isset( WC()->cart->cart_contents[ $associated_cart_key ]['associated_weight'] ) ? WC()->cart->cart_contents[ $associated_cart_key ]['associated_weight'] : 0.0;
						if ( $associated_product_weight ) {
							$associated_product_qty = WC()->cart->cart_contents[ $associated_cart_key ]['quantity'];
							$associated_weight     += (float) $associated_product_weight * (float) $associated_product_qty;
						}
						if ( $associated_weight > 0 ) {
							$main_product_weight = $cart_item['data']->get_weight();
							$main_product_qty    = $cart_item['quantity'];
							$assoc_weight        = (float) $main_product_weight + $associated_weight / $main_product_qty;
							$cart_item['data']->set_weight( $assoc_weight );
							WC()->cart->cart_contents[ $cart_key ]['data']->set_weight( $assoc_weight, 'edit' );
						}
					}
				}
			}
			$cart_contents[ $cart_key ] = $cart_item;
		}

		if ( method_exists( $cart_object, 'set_cart_contents' ) ) {
			$cart_object->set_cart_contents( $cart_contents );
		} else {
			$cart_object->cart_contents = $cart_contents;
		}

	}

	/**
	 * Make sure products marked as associated have a parent
	 *
	 * @param object $cart The cart object.
	 * @since 5.0
	 */
	public function associated_woocommerce_cart_loaded_from_session( $cart ) {

		$cart_contents = $cart->cart_contents;

		if ( ! empty( $cart_contents ) ) {

			foreach ( $cart_contents as $key => $value ) {

				if ( isset( $value['associated_parent'] ) && ! empty( $value['associated_parent'] ) ) {

					$parent = [];
					if ( isset( $cart_contents[ $value['associated_parent'] ] ) ) {
						$parent = $cart_contents[ $value['associated_parent'] ];
					}

					if ( ! $parent ||
						! isset( $parent['associated_products'] ) ||
						! is_array( $parent['associated_products'] ) ||
						! in_array( $key, $parent['associated_products'], true ) ) {
						unset( WC()->cart->cart_contents[ $key ] );
					}
				}
			}
		}

	}

	/**
	 * Make cart item count not count associated products
	 *
	 * @param integer $count Number of items in the cart.
	 * @since 5.0
	 */
	public function associated_woocommerce_cart_contents_count( $count ) {
		$cart                      = WC()->cart->get_cart();
		$associated_products_count = 0;

		foreach ( $cart as $key => $value ) {

			if ( isset( $value['associated_parent'] ) && ! empty( $value['associated_parent'] ) ) {
				$associated_products_count += $value['quantity'];
			}
		}

		return absint( $count ) - absint( $associated_products_count );
	}

	/**
	 * Wrap associated products in mini cart
	 *
	 * @param string $html The string HTML.
	 * @param array  $cart_item The cart item.
	 * @param string $cart_item_key The cart item key.
	 * @since 5.0
	 */
	public function associated_woocommerce_widget_cart_item_quantity( $html, $cart_item, $cart_item_key ) {

		remove_filter( 'woocommerce_cart_item_price', [ $this, 'associated_woocommerce_cart_item_price' ], 99999 );

		if ( isset( $cart_item['associated_parent'] ) && ! empty( $cart_item['associated_parent'] ) ) {
			if ( empty( $cart_item['associated_priced_individually'] ) && empty( $cart_item['line_subtotal'] ) ) {
				$html = '';
			} elseif ( $html ) {
				$html = '<span class="tc-associated-table-product-price">' . $html . '</span>';
			}
		}

		return $html;

	}

	/**
	 * Wrap associated products in cart
	 *
	 * @param string $price The price HTML.
	 * @param array  $cart_item The cart item.
	 * @param string $cart_item_key The cart item key.
	 * @since 5.0
	 */
	public function associated_woocommerce_cart_item_price( $price, $cart_item, $cart_item_key ) {

		if ( isset( $cart_item['associated_parent'] ) && ! empty( $cart_item['associated_parent'] ) ) {

			if ( empty( $cart_item['associated_priced_individually'] ) && empty( $cart_item['line_subtotal'] ) ) {
				$price = '';
			} elseif ( $price ) {
				$price = '<span class="tc-associated-table-product-price">' . $price . '</span>';
			}
		}

		return $price;

	}

	/**
	 * Wrap associated products in cart
	 *
	 * @param string $title The title HTML.
	 * @param array  $cart_item The cart item.
	 * @param string $cart_item_key The cart item key.
	 * @since 5.0
	 */
	public function associated_woocommerce_cart_item_name( $title = '', $cart_item = [], $cart_item_key = '' ) {

		if ( isset( $cart_item['associated_parent'] ) && ! empty( $cart_item['associated_parent'] ) ) {

			if ( isset( $cart_item['associated_label'] ) && '' !== $cart_item['associated_label'] ) {
				$title = '<div class="tc-associated-table-product-name">' . $cart_item['associated_label'] . '</div>' . $title;
			}

			$title = '<div class="tc-associated-table-product-indent">' . $title . '</div>';

		}

		return $title;

	}

	/**
	 * Associated product cart remove link
	 *
	 * @param string $link The link HTML.
	 * @param string $cart_item_key The cart item key.
	 * @since 5.0
	 */
	public function associated_woocommerce_cart_item_remove_link( $link, $cart_item_key ) {

		if ( isset( WC()->cart->cart_contents[ $cart_item_key ]['associated_parent'] ) && ! empty( WC()->cart->cart_contents[ $cart_item_key ]['associated_parent'] ) && isset( WC()->cart->cart_contents[ $cart_item_key ]['associated_required'] ) && ! empty( WC()->cart->cart_contents[ $cart_item_key ]['associated_required'] ) ) {

			$parent_key = WC()->cart->cart_contents[ $cart_item_key ]['associated_parent'];

			if ( isset( WC()->cart->cart_contents[ $parent_key ] ) ) {
				return '';
			}
		}

		return $link;
	}

	/**
	 * Fix internal associated data
	 *
	 * @param boolean $cart_updated The current cart_updated status.
	 * @since 5.1
	 */
	public function woocommerce_update_cart_action_cart_updated( $cart_updated = false ) {

		if ( apply_filters( 'wc_epo_update_cart_action_cart_updated', false, $cart_updated ) ) {
			return $cart_updated;
		}

		$cart_contents = WC()->cart->cart_contents;
		if ( is_array( $cart_contents ) ) {
			foreach ( $cart_contents as $cart_item_key => $cart_item ) {
				unset( WC()->cart->cart_contents[ $cart_item_key ]['did_set_quantity'] );
			}
		}

		return $cart_updated;

	}

	/**
	 * Sync associated products quantity with main product
	 *
	 * @param string  $cart_item_key The cart item key.
	 * @param integer $quantity The product quantity.
	 * @since 5.0
	 */
	public function woocommerce_after_cart_item_quantity_update( $cart_item_key, $quantity = 0 ) {

		if ( THEMECOMPLETE_EPO()->tm_epo_global_product_element_quantity_sync === 'no' ) {
			return;
		}

		$cart_item = WC()->cart->cart_contents[ $cart_item_key ];

		if ( ! empty( $cart_item ) ) {

			$associated_cart_keys = $this->get_associated_cart_keys( $cart_item, WC()->cart->cart_contents );

			if ( ! empty( $associated_cart_keys ) && is_array( $associated_cart_keys ) ) {

				if ( (float) 0 === (float) $quantity || $quantity < 0 ) {
					$quantity = 0;
				} else {
					$quantity = floatval( $cart_item['quantity'] );
				}

				foreach ( $associated_cart_keys as $associated_key_id => $associated_key ) {

					$associated_data = WC()->cart->cart_contents[ $associated_key ];

					if ( ! isset( $associated_data['data'] ) || ! $associated_data['data'] ) {
						continue;
					}
					if ( $associated_data['data']->is_sold_individually() && $quantity > 0 ) {

						WC()->cart->cart_contents[ $associated_key ]['quantity']         = 1;
						WC()->cart->cart_contents[ $associated_key ]['did_set_quantity'] = 1;

					} else {

						$associated_quantity = floatval( $associated_data['tmproducts'][ $associated_key_id ]['quantity'] );
						$no_change_quantity  = floatval( $associated_data['tmproducts'][ $associated_key_id ]['no_change_quantity'] );
						$initial_quantity    = floatval( $associated_data['tmproducts'][ $associated_key_id ]['initial_quantity'] );

						if ( $no_change_quantity ) {
							$associated_quantity = $initial_quantity;
						}
						WC()->cart->cart_contents[ $associated_key ]['quantity']         = $associated_quantity * $quantity;
						WC()->cart->cart_contents[ $associated_key ]['did_set_quantity'] = $associated_quantity * $quantity;

					}
				}
			} elseif ( isset( $cart_item['associated_parent'] ) && ! empty( $cart_item['associated_parent'] ) ) {
				if ( isset( WC()->cart->cart_contents[ $cart_item_key ]['did_set_quantity'] ) ) {
					WC()->cart->cart_contents[ $cart_item_key ]['quantity'] = WC()->cart->cart_contents[ $cart_item_key ]['did_set_quantity'];
					unset( WC()->cart->cart_contents[ $cart_item_key ]['did_set_quantity'] );
					WC()->cart->calculate_totals();
				}
			}
		}

	}

	/**
	 * Sync associated products quantity input
	 *
	 * @param integer $quantity The product quantity.
	 * @param string  $cart_item_key The cart item key.
	 * @since 5.0
	 */
	public function associated_woocommerce_cart_item_quantity( $quantity, $cart_item_key ) {

		$cart_item = WC()->cart->cart_contents[ $cart_item_key ];

		if ( isset( $cart_item['associated_parent'] ) && ! empty( $cart_item['associated_parent'] ) ) {

			$parent        = WC()->cart->cart_contents[ $cart_item['associated_parent'] ];
			$associated_id = array_search( $cart_item_key, $parent['associated_products'], true );

			if ( false === $associated_id ) {
				return $quantity;
			}

			if ( $cart_item['tmproducts'][ $associated_id ]['quantity_min'] === $cart_item['tmproducts'][ $associated_id ]['quantity_max'] ) {

				$quantity = $cart_item['quantity'];

			} else {

				$parent_quantity = 1;
				if ( THEMECOMPLETE_EPO()->tm_epo_global_product_element_quantity_sync === 'yes' ) {
					$parent_quantity = $parent['quantity'];
				}
				$max_stock = $cart_item['data']->managing_stock() && ! $cart_item['data']->backorders_allowed() ? $cart_item['data']->get_stock_quantity() : '';
				$max_stock = null === $max_stock ? '' : $max_stock;

				if ( '' !== $max_stock ) {
					$max_qty = '' !== $cart_item['tmproducts'][ $associated_id ]['quantity_max'] ? min( $max_stock, $parent_quantity * $cart_item['tmproducts'][ $associated_id ]['quantity_max'] ) : $max_stock;
				} else {
					$max_qty = '' !== $cart_item['tmproducts'][ $associated_id ]['quantity_max'] ? $parent_quantity * $cart_item['tmproducts'][ $associated_id ]['quantity_max'] : '';
				}

				$min_qty = floatval( $parent_quantity ) * floatval( $cart_item['tmproducts'][ $associated_id ]['quantity_min'] );

				if ( ( $max_qty > $min_qty || '' === $max_qty ) && ! $cart_item['data']->is_sold_individually() ) {

					$quantity = woocommerce_quantity_input(
						[
							'input_name'  => 'cart[' . $cart_item_key . '][qty]',
							'input_value' => $cart_item['quantity'],
							'min_value'   => $min_qty,
							'max_value'   => $max_qty,
							'step'        => $parent_quantity,
						],
						$cart_item['data'],
						false
					);

				} else {
					$quantity = $cart_item['quantity'];
				}
			}
		}

		return $quantity;
	}

	/**
	 * Associated product table item classes
	 *
	 * @param string $class The item class.
	 * @param array  $cart_item The cart item.
	 * @since 5.0
	 */
	public function associated_woocommerce_cart_item_class( $class, $cart_item ) {

		if ( isset( $cart_item['associated_parent'] ) && ! empty( $cart_item['associated_parent'] ) ) {
			$class .= ' tc-associated-table-product';
		} elseif ( isset( $cart_item['associated_products'] ) && ! empty( $cart_item['associated_products'] ) ) {
			$class .= ' tc-container-table-product';
		}

		return $class;
	}

	/**
	 * Clear notices
	 *
	 * @since 5.0
	 */
	public function associated_clear_removed_notice() {

		if ( is_admin() || ! function_exists( 'WC' ) ) {
			return;
		}

		$notices = isset( WC()->session ) ? WC()->session->get( 'wc_notices', [] ) : [];

		if ( isset( $notices['EPO_REMOVED_REQUIRED_ASSOCIATED_PRODUCT'] ) ) {
			if ( isset( $notices['success'] ) && is_array( $notices['success'] ) ) {
				$last = $notices['success'][ count( $notices['success'] ) - 1 ];
				if ( is_array( $last ) && isset( $last['notice'] ) ) {
					$last['notice'] = $notices['EPO_REMOVED_REQUIRED_ASSOCIATED_PRODUCT'][0]['notice'];
					$notices['success'][ count( $notices['success'] ) - 1 ] = $last;

				}
			}

			unset( $notices['EPO_REMOVED_REQUIRED_ASSOCIATED_PRODUCT'] );

			WC()->session->set( 'wc_notices', $notices );
		}

	}

	/**
	 * Fetch associated product cart keys
	 *
	 * @param array $cart_item The cart item.
	 * @param array $cart_contents The cart contents.
	 * @since 5.0
	 */
	public function get_associated_cart_keys( $cart_item, $cart_contents = false ) {
		if ( ! $cart_contents ) {
			$cart_contents = WC()->cart->cart_contents;
		}

		$associated_cart_keys = [];

		if ( isset( $cart_item['associated_products'] ) && isset( $cart_item['tmproducts'] ) && ! empty( $cart_item['tmproducts'] ) && is_array( $cart_item['tmproducts'] ) ) {

			$associated_products = $cart_item['associated_products'];

			if ( ! empty( $cart_contents ) && ! empty( $associated_products ) && is_array( $associated_products ) ) {

				foreach ( $associated_products as $key ) {
					if ( isset( $cart_contents[ $key ] ) ) {
						$associated_cart_keys[ $key ] = $cart_contents[ $key ];
					}
				}
			}
		}

		return array_keys( $associated_cart_keys );
	}

	/**
	 * Remove associated products when the parent gets removed.
	 *
	 * @param string $cart_item_key The cart item key.
	 * @param object $cart The cart object.
	 * @since 5.0
	 */
	public function associated_woocommerce_remove_cart_item( $cart_item_key, $cart ) {

		// This is an associated product.
		if ( isset( $cart->cart_contents[ $cart_item_key ]['associated_parent'] ) ) {

			// If it is required remove all other associated products and the parent product.
			if ( isset( $cart->cart_contents[ $cart_item_key ]['associated_required'] ) && ! empty( $cart->cart_contents[ $cart_item_key ]['associated_required'] ) ) {

				$associated_parent_key = $cart->cart_contents[ $cart_item_key ]['associated_parent'];

				// Remove all other associated products.
				if ( isset( $cart->cart_contents[ $associated_parent_key ] ) ) {
					$cart_keys = $this->get_associated_cart_keys( $cart->cart_contents[ $associated_parent_key ], $cart->cart_contents );

					foreach ( $cart_keys as $associated_cart_key ) {
						if ( ! isset( $cart->cart_contents[ $associated_cart_key ] ) ) {
							continue;
						}

						unset( WC()->cart->cart_contents[ $associated_cart_key ] );
					}
				}

				// Remove parent product.
				if ( isset( $cart->cart_contents[ $associated_parent_key ] ) ) {

					$product = wc_get_product( $cart->cart_contents[ $associated_parent_key ]['product_id'] );

					/* translators: %s: Item name. */
					$item_removed_title = $product ? $product->get_name() : '';

					/* Translators: %s Product title. */
					$removed_notice = sprintf( __( '%s removed along with all of its associated products!', 'woocommerce-tm-extra-product-options' ), $item_removed_title );

					wc_add_notice( $removed_notice, 'EPO_REMOVED_REQUIRED_ASSOCIATED_PRODUCT' );

					unset( WC()->cart->cart_contents[ $associated_parent_key ] );
				}

				unset( WC()->cart->removed_cart_contents[ $cart_item_key ] );

			}

			// This is a parent product.
		} elseif ( isset( $cart->removed_cart_contents[ $cart_item_key ]['associated_products'] ) && ! empty( $cart->removed_cart_contents[ $cart_item_key ]['associated_products'] ) && is_array( $cart->removed_cart_contents[ $cart_item_key ]['associated_products'] ) ) {

			$cart_totals = isset( $_POST['cart'] ) ? wp_unslash( $_POST['cart'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification
			$quantity    = -1;
			if ( $cart_totals ) {
				$quantity = apply_filters( 'woocommerce_stock_amount_cart_item', wc_stock_amount( preg_replace( '/[^0-9\.]/', '', $cart_totals[ $cart_item_key ]['qty'] ) ), $cart_item_key );
			}

			$associated_cart_keys = $this->get_associated_cart_keys( $cart->removed_cart_contents[ $cart_item_key ], $cart->cart_contents );

			// Remove all other associated products.
			foreach ( $associated_cart_keys as $associated_cart_key ) {
				if ( ! isset( $cart->cart_contents[ $associated_cart_key ] ) ) {
					continue;
				}
				$remove = $cart->cart_contents[ $associated_cart_key ];
				WC()->cart->removed_cart_contents[ $associated_cart_key ] = $remove;

				// Required to check if parent product was set to zero from cart.
				if ( '' !== $quantity && (float) 0 !== (float) $quantity ) {
					unset( WC()->cart->cart_contents[ $associated_cart_key ] );
				}
			}
		}
	}

	/**
	 * Restore associated products when the parent gets restored.
	 *
	 * @param string $cart_item_key The cart item key.
	 * @param object $cart The cart object.
	 * @since 5.0
	 */
	public function associated_woocommerce_restore_cart_item( $cart_item_key, $cart ) {

		if ( isset( $cart->cart_contents[ $cart_item_key ]['associated_parent'] ) && ! empty( $cart->cart_contents[ $cart_item_key ]['associated_parent'] ) ) {

			$cart_item_data = $cart->cart_contents[ $cart_item_key ];

			$position       = array_search( $cart_item_data['associated_parent'], array_keys( $cart->cart_contents ), true );
			$position       = (int) $position + (int) $cart_item_data['associated_key'] + 1;
			$array          = $cart->cart_contents;
			$previous_items = array_slice( $array, 0, $position, true );
			$next_items     = array_slice( $array, $position, null, true );

			$item = [ $cart_item_key => $cart_item_data ];

			WC()->cart->cart_contents = $previous_items + $item + $next_items;

		} elseif ( isset( $cart->cart_contents[ $cart_item_key ]['associated_products'] ) && ! empty( $cart->cart_contents[ $cart_item_key ]['associated_products'] ) && is_array( $cart->cart_contents[ $cart_item_key ]['associated_products'] ) ) {

			$cart_keys = $this->get_associated_cart_keys( $cart->cart_contents[ $cart_item_key ], $cart->removed_cart_contents );

			foreach ( $cart_keys as $associated_cart_key ) {

				$remove                                      = $cart->removed_cart_contents[ $associated_cart_key ];
				$cart->cart_contents[ $associated_cart_key ] = $remove;

				do_action( 'woocommerce_restore_cart_item', $associated_cart_key, $cart );

				unset( WC()->cart->removed_cart_contents[ $associated_cart_key ] );
			}
		}
	}

	/**
	 * Add associated products (from elements) to the cart.
	 *
	 * @param string  $parent_cart_key The parent cart key.
	 * @param integer $parent_id The parent product id.
	 * @param integer $parent_quantity Contains the quantity of the parent product to add.
	 * @param integer $variation_id ID of the parent variation being added to the cart.
	 * @param array   $variation Attribute values.
	 * @param array   $cart_item_data Extra cart item data we want to pass into the item.
	 * @since 5.0
	 */
	public function associated_woocommerce_add_to_cart( $parent_cart_key, $parent_id, $parent_quantity, $variation_id, $variation, $cart_item_data ) {

		if ( ! did_action( 'woocommerce_cart_loaded_from_session' ) ) {
			return;
		}

		if ( ! defined( 'TM_ASSOC_WC_ADD_TO_CART' ) ) {
			define( 'TM_ASSOC_WC_ADD_TO_CART', true );
		}

		// Check to see if there are associated product to add.
		if ( isset( $cart_item_data['tmproducts'] ) && ! empty( $cart_item_data['tmproducts'] ) && is_array( $cart_item_data['tmproducts'] ) ) {

			// Prevent adding the same associated product for the same parent product.
			foreach ( WC()->cart->cart_contents as $cart_key => $cart_value ) {
				if ( isset( $cart_value['tmproducts'] ) && isset( $cart_value['associated_parent'] ) && $cart_value['associated_parent'] === $parent_cart_key ) {
					return;
				}
			}

			// Required to allow a different version of the same product to be added to the cart.
			$associated_cart_data = [
				'associated_parent' => $parent_cart_key,
				'tmproducts'        => $cart_item_data['tmproducts'],
			];

			foreach ( $cart_item_data['tmproducts'] as $key => $associated_data ) {

				$associated_item_cart_data = $associated_cart_data;

				$associated_data_form_prefix = isset( $associated_data['form_prefix'] ) ? $associated_data['form_prefix'] : '';

				if ( is_array( $associated_data_form_prefix ) ) {

					$form_prefix_counter = $key; // failsafe.
					if ( isset( $associated_data['form_prefix_counter'] ) ) {
						$form_prefix_counter = $associated_data['form_prefix_counter'];
					};

					if ( '' !== $form_prefix_counter ) {
						if ( isset( $associated_data_form_prefix[ $form_prefix_counter ] ) ) {
							$associated_data_form_prefix = $associated_data_form_prefix[ $form_prefix_counter ];
						}
					} else {
						$associated_data_form_prefix = array_values( $associated_data_form_prefix );
						$associated_data_form_prefix = $associated_data_form_prefix[0];
					}
				}

				if ( is_array( $associated_data_form_prefix ) ) {
					continue; // something went wrong.
				}

				$associated_item_cart_data['associated_key']                     = $key;
				$associated_item_cart_data['associated_required']                = isset( $associated_data['required'] ) ? $associated_data['required'] : '';
				$associated_item_cart_data['associated_shipped_individually']    = isset( $associated_data['shipped_individually'] ) ? $associated_data['shipped_individually'] : '';
				$associated_item_cart_data['associated_priced_individually']     = isset( $associated_data['priced_individually'] ) ? $associated_data['priced_individually'] : '';
				$associated_item_cart_data['associated_maintain_weight']         = isset( $associated_data['maintain_weight'] ) ? $associated_data['maintain_weight'] : '';
				$associated_item_cart_data['associated_uniqid']                  = isset( $associated_data['section'] ) ? $associated_data['section'] : '';
				$associated_item_cart_data['associated_label']                   = isset( $associated_data['section_label'] ) ? $associated_data['section_label'] : '';
				$associated_item_cart_data['associated_discount']                = isset( $associated_data['discount'] ) ? $associated_data['discount'] : '';
				$associated_item_cart_data['associated_discount_type']           = isset( $associated_data['discount_type'] ) ? $associated_data['discount_type'] : '';
				$associated_item_cart_data['associated_discount_exclude_addons'] = isset( $associated_data['discount_exclude_addons'] ) ? $associated_data['discount_exclude_addons'] : '';
				$associated_item_cart_data['associated_element_name']            = $associated_data['element_name'];
				$associated_item_cart_data['associated_formprefix']              = str_replace( [ '.', ' ', '[' ], '', $associated_data_form_prefix );
				$associated_item_cart_data['hiddenin']                           = $associated_data['hiddenin'];
				$associated_product_id = $associated_data['product_id'];
				$variation_id          = '';
				$variations            = [];

				if ( '' === $associated_product_id ) {
					continue;
				}

				$associated_product = wc_get_product( $associated_product_id );

				if ( ! $associated_product ) {
					continue;
				}

				// Only allow simple or variable products.
				if ( apply_filters( 'wc_epo_associated_add_to_cart', true, $associated_product, $associated_item_cart_data ) && ( ! ( $associated_product->is_type( 'simple' ) || $associated_product->is_type( 'variable' ) || $associated_product->is_type( 'variation' ) ) ) ) {
					continue;
				}

				$item_quantity = $associated_data['quantity'];

				if ( $associated_product->is_sold_individually() ) {
					$quantity = 1;
				} else {
					$quantity = $item_quantity;
					if ( THEMECOMPLETE_EPO()->tm_epo_global_product_element_quantity_sync === 'yes' ) {
						$quantity = floatval( $quantity ) * floatval( $parent_quantity );
					}
				}

				if ( (float) 0 === (float) $quantity ) {
					continue;
				}

				if ( $associated_product->is_type( 'variable' ) || $associated_product->is_type( 'variation' ) ) {

					if ( $associated_product->is_type( 'variation' ) ) {
						$variation_id = (int) $associated_data['product_id'];
						$associated_item_cart_data['associated_variation_id'] = $variation_id;
						$variations           = [];
						$parent_data          = wc_get_product( $associated_product->get_parent_id() );
						$variation_attributes = $associated_product->get_variation_attributes();
						// Filter out 'any' variations, which are empty, as they need to be explicitly specified while adding to cart.
						$variation_attributes = array_filter( $variation_attributes );
					} else {
						$variation_id = (int) $associated_data['variation_id'];
						$associated_item_cart_data['associated_variation_id'] = $variation_id;
						$variations           = $associated_data['attributes'];
						$parent_data          = $associated_product;
						$variation_attributes = [];
					}

					if ( $variation_id ) {
						$variation_data = wc_get_product_variation_attributes( $variation_id );

						foreach ( $parent_data->get_attributes() as $attribute ) {
							if ( ! $attribute['is_variation'] ) {
								continue;
							}

							// Get valid value from variation data.
							$attribute_key = 'attribute_' . sanitize_title( $attribute['name'] );
							$valid_value   = isset( $variation_data[ $attribute_key ] ) ? $variation_data[ $attribute_key ] : '';
							$value         = false;
							/**
							 * If the attribute value was posted, check if it's valid.
							 *
							 * If no attribute was posted, only error if the variation has an 'any' attribute which requires a value.
							 */
							if ( $associated_product->is_type( 'variation' ) ) {
								if ( isset( $variation_attributes[ $attribute_key ] ) ) {
									$value = $variation_attributes[ $attribute_key ];
								}
							} else {
								if ( isset( $cart_item_data['tmpost_data'][ $associated_data['element_name'] . '_' . $attribute_key ] ) ) {
									$value = $cart_item_data['tmpost_data'][ $associated_data['element_name'] . '_' . $attribute_key ];
								}
							}
							if ( false === $value ) {
								continue;
							}
							// Allow if valid or show error.
							if ( $valid_value === $value ) {
								$variations[ $attribute_key ] = $value;
							} elseif ( '' === $valid_value && in_array( $value, $attribute->get_slugs(), true ) ) {
								// If valid values are empty, this is an 'any' variation so get all possible values.
								$variations[ $attribute_key ] = $value;
							}
						}
					} else {
						continue;
					}
				}

				THEMECOMPLETE_EPO()->associated_element_uniqid     = $associated_item_cart_data['associated_uniqid'];
				THEMECOMPLETE_EPO()->associated_product_counter    = $key;
				THEMECOMPLETE_EPO()->associated_product_formprefix = $associated_item_cart_data['associated_formprefix'];
				$associated_item_cart_key                          = $this->add_associated_to_cart( $parent_id, $associated_product, $quantity, $variation_id, $variations, $associated_item_cart_data );
				THEMECOMPLETE_EPO()->associated_element_uniqid     = false;
				THEMECOMPLETE_EPO()->associated_product_counter    = false;
				THEMECOMPLETE_EPO()->associated_product_formprefix = false;

				if ( ! isset( WC()->cart->cart_contents[ $parent_cart_key ]['associated_products'] ) ) {
					WC()->cart->cart_contents[ $parent_cart_key ]['associated_products'] = [];
				}
				if ( $associated_item_cart_key && ! in_array( $associated_item_cart_key, WC()->cart->cart_contents[ $parent_cart_key ]['associated_products'], true ) ) {
					WC()->cart->cart_contents[ $parent_cart_key ]['associated_products'][] = $associated_item_cart_key;
				}
			}
		}

	}

	/**
	 * Add an associated product to the cart.
	 *
	 * @param integer $parend_id The parent product id.
	 * @param object  $product The associated product object.
	 * @param integer $quantity Contains the quantity of the associated product to add.
	 * @param integer $variation_id ID of the associated variation being added to the cart.
	 * @param array   $variation Attribute values.
	 * @param array   $cart_item_data Extra cart item data we want to pass into the item.
	 * @since 5.0
	 */
	private function add_associated_to_cart( $parend_id, $product, $quantity = 1, $variation_id = '', $variation = '', $cart_item_data = [] ) {

		if ( $quantity <= 0 ) {
			return false;
		}

		// Get the product / ID.
		if ( is_a( $product, 'WC_Product' ) ) {

			$product_id   = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();
			$variation_id = $product->is_type( 'variation' ) ? $product->get_id() : $variation_id;
			$product_data = $product->is_type( 'variation' ) ? $product : wc_get_product( $variation_id ? $variation_id : $product_id );

		} else {

			$product_id   = absint( $product );
			$product_data = wc_get_product( $product_id );

			if ( $product_data->is_type( 'variation' ) ) {
				$product_id   = $product_data->get_parent_id();
				$variation_id = $product_data->get_id();
			} else {
				$product_data = wc_get_product( $variation_id ? $variation_id : $product_id );
			}
		}

		if ( ! $product_data ) {
			return false;
		}

		if ( ! $product_data->is_in_stock() ) {
			return false;
		}

		// Load cart item data when adding to cart (WC core filter).
		$cart_item_data = (array) apply_filters( 'woocommerce_add_cart_item_data', $cart_item_data, $product_id, $variation_id, $quantity );

		// Generate a ID based on product ID, variation ID, variation data, and other cart item data.
		$cart_id = WC()->cart->generate_cart_id( $product_id, $variation_id, $variation, $cart_item_data );

		// See if this product and its options is already in the cart.
		$cart_item_key = WC()->cart->find_product_in_cart( $cart_id );

		// If cart_item_key is set, the item is already in the cart and its quantity will be handled by update_quantity_in_cart.
		if ( ! $cart_item_key ) {

			$cart_item_key = $cart_id;

			$array    = WC()->cart->cart_contents;
			$position = array_search( $cart_item_data['associated_parent'], array_keys( $array ), true );
			$position = (int) $position + (int) $cart_item_data['associated_key'] + 1;

			$previous_items = array_slice( $array, 0, $position, true );
			$next_items     = array_slice( $array, $position, null, true );

			$item = [
				$cart_item_key => apply_filters(
					'woocommerce_add_cart_item',
					array_merge(
						$cart_item_data,
						[
							'key'          => $cart_item_key,
							'product_id'   => absint( $product_id ),
							'variation_id' => absint( $variation_id ),
							'variation'    => $variation,
							'quantity'     => $quantity,
							'data'         => $product_data,
						]
					),
					$cart_item_key
				),
			];

			WC()->cart->cart_contents = $previous_items + $item + $next_items;

		}

		return $cart_item_key;
	}

	/**
	 * Get discounted price
	 *
	 * @param mixed  $current_price The current prodcut price.
	 * @param string $discount The product discount.
	 * @param string $discount_type The product discount type.
	 * @since 5.0.8
	 */
	public function get_discounted_price( $current_price = 0, $discount = '', $discount_type = '' ) {

		$discount = wc_format_decimal( (float) $discount, wc_get_price_decimals() );

		if ( $current_price && $discount ) {
			if ( is_numeric( $current_price ) ) {
				$price = wc_format_decimal( (float) $current_price, wc_get_price_decimals() );
				if ( 'fixed' === $discount_type ) {
					$current_price = max( $price - $discount, 0 );
				} else {
					$current_price = max( $price * ( ( 100 - $discount ) / 100 ), 0 );
				}
			} else { // math formula.
				$price = '(' . $current_price . ')';
				if ( 'fixed' === $discount_type ) {
					$current_price = $price . ' - ' . $discount;
				} else {
					$current_price = $price . ' * ( ( 100 - ' . $discount . ' ) / 100 )';
				}
			}
		}

		return $current_price;

	}

	/**
	 * Modify cart item
	 *
	 * @param array $cart_item The cart item.
	 * @since 5.0
	 */
	public function modify_cart_item( $cart_item = [] ) {

		if ( isset( $cart_item['associated_parent'] ) && ! empty( $cart_item['associated_parent'] ) ) {

			if ( empty( $cart_item['associated_priced_individually'] ) ) {
				$cart_item['data']->set_regular_price( 0 );
				$cart_item['data']->set_sale_price( '' );
				$cart_item['data']->set_price( 0 );
			}

			if ( $cart_item['associated_discount'] ) {

				$discounted_price = $this->get_discounted_price( $cart_item['data']->get_price( 'edit' ), $cart_item['associated_discount'], $cart_item['associated_discount_type'] );

				$cart_item['data']->set_price( $discounted_price );
				$cart_item['data']->set_sale_price( $discounted_price );

			}

			if ( $cart_item['data']->needs_shipping() ) {

				if ( empty( $cart_item['associated_shipped_individually'] ) ) {

					if ( '1' === $cart_item['associated_maintain_weight'] ) {

						$cart_item_weight = $cart_item['data']->get_weight( 'edit' );

						if ( $cart_item['data']->is_type( 'variation' ) && '' === $cart_item_weight ) {
							$parent_data      = $cart_item['data']->get_parent_data();
							$cart_item_weight = $parent_data['weight'];
						}

						$cart_item['associated_weight'] = $cart_item_weight;

					}

					$cart_item['data']->associated_value = $cart_item['data']->get_price( 'edit' );

					$cart_item['data']->set_virtual( 'yes' );
					$cart_item['data']->set_weight( '' );

				}
			}
		}

		return $cart_item;

	}

	/**
	 * Modify cart
	 *
	 * @param array $cart_item The cart item.
	 * @since 5.0
	 */
	public function woocommerce_add_cart_item( $cart_item = [] ) {

		$cart_item = $this->modify_cart_item( $cart_item );

		return $cart_item;

	}

	/**
	 * Gets the cart from session.
	 *
	 * @param array  $cart_item The cart item.
	 * @param array  $values Cart item values.
	 * @param string $cart_item_key The cart item key.
	 * @since 5.0
	 */
	public function woocommerce_get_cart_item_from_session( $cart_item = [], $values = [], $cart_item_key = '' ) {

		if ( isset( $values['tmproducts'] ) ) {
			$cart_item['tmproducts'] = $values['tmproducts'];
		}
		if ( isset( $values['associated_products'] ) ) {
			$cart_item['associated_products'] = $values['associated_products'];
		}
		if ( isset( $values['associated_parent'] ) ) {
			$cart_item['associated_parent'] = $values['associated_parent'];
		}
		if ( isset( $values['associated_key'] ) ) {
			$cart_item['associated_key'] = $values['associated_key'];
		}
		if ( isset( $values['associated_required'] ) ) {
			$cart_item['associated_required'] = $values['associated_required'];
		}
		if ( isset( $values['associated_shipped_individually'] ) ) {
			$cart_item['associated_shipped_individually'] = $values['associated_shipped_individually'];
		}
		if ( isset( $values['associated_priced_individually'] ) ) {
			$cart_item['associated_priced_individually'] = $values['associated_priced_individually'];
		}
		if ( isset( $values['associated_maintain_weight'] ) ) {
			$cart_item['associated_maintain_weight'] = $values['associated_maintain_weight'];
		}
		if ( isset( $values['associated_uniqid'] ) ) {
			$cart_item['associated_uniqid'] = $values['associated_uniqid'];
		}
		if ( isset( $values['associated_label'] ) ) {
			$cart_item['associated_label'] = $values['associated_label'];
		}
		if ( isset( $values['associated_discount'] ) ) {
			$cart_item['associated_discount'] = $values['associated_discount'];
		}
		if ( isset( $values['associated_discount_type'] ) ) {
			$cart_item['associated_discount_type'] = $values['associated_discount_type'];
		}
		if ( isset( $values['associated_discount_exclude_addons'] ) ) {
			$cart_item['associated_discount_exclude_addons'] = $values['associated_discount_exclude_addons'];
		}
		$cart_item = $this->modify_cart_item( $cart_item );

		return $cart_item;

	}


	/**
	 * Edit cart functionality
	 * This serves as edit cart regardless if the product has associated products.
	 *
	 * @param string  $cart_item_key The cart item key.
	 * @param integer $product_id Contains the id of the product to add to the cart.
	 * @param integer $quantity Contains the quantity of the item to add.
	 * @param integer $variation_id ID of the variation being added to the cart.
	 * @param array   $variation Attribute values.
	 * @param array   $cart_item_data Extra cart item data we want to pass into the item.
	 * @since 5.0
	 */
	public function woocommerce_add_to_cart( $cart_item_key = '', $product_id = '', $quantity = '', $variation_id = '', $variation = [], $cart_item_data = '' ) {

		if ( THEMECOMPLETE_EPO()->cart_edit_key ) {

			$original_key = THEMECOMPLETE_EPO()->cart_edit_key;

			// Check if there isn't any data change.
			if ( $original_key === $cart_item_key ) {
				WC()->cart->cart_contents[ $cart_item_key ]['quantity'] = $quantity;
				$this->woocommerce_after_cart_item_quantity_update( $cart_item_key, $quantity );
				return;
			}

			// Remove old associated products.
			if ( isset( WC()->cart->cart_contents[ $original_key ]['associated_products'] ) && is_array( WC()->cart->cart_contents[ $original_key ]['associated_products'] ) ) {
				foreach ( WC()->cart->cart_contents[ $original_key ]['associated_products'] as $key ) {
					unset( WC()->cart->cart_contents[ $key ] );
				}
			}

			// Replace original key entry with the new key entry.
			if ( $original_key !== $cart_item_key ) {
				$array   = WC()->cart->cart_contents;
				$old_key = $original_key;
				$new_key = $cart_item_key;

				if ( array_key_exists( $old_key, $array ) ) {
					$keys = array_keys( $array );
					$keys[ array_search( $old_key, $keys, true ) ] = $new_key;
					$array                    = array_combine( $keys, $array );
					WC()->cart->cart_contents = $array;
				}
			}

			// Reposition new associated product to be below the edited product.
			if ( isset( WC()->cart->cart_contents[ $cart_item_key ]['associated_products'] ) && is_array( WC()->cart->cart_contents[ $cart_item_key ]['associated_products'] ) ) {

				$associated_products = [];
				foreach ( WC()->cart->cart_contents[ $cart_item_key ]['associated_products'] as $key ) {
					if ( isset( WC()->cart->cart_contents[ $key ] ) ) {
						$associated_products[ $key ] = WC()->cart->cart_contents[ $key ];
					}
				}

				$start_position = array_search( $cart_item_key, array_keys( WC()->cart->cart_contents ), true );
				foreach ( $associated_products as $key => $item ) {
					$position       = (int) $start_position + (int) $item['associated_key'] + 1;
					$array          = WC()->cart->cart_contents;
					$previous_items = array_slice( $array, 0, $position, true );
					$next_items     = array_slice( $array, $position, null, true );

					$item = [ $key => $item ];

					WC()->cart->cart_contents = $previous_items + $item + $next_items;

				}
			}
		}

	}

}
