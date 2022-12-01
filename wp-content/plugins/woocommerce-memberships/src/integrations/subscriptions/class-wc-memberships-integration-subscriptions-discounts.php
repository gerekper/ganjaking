<?php
/**
 * WooCommerce Memberships
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Discounts integration class for WooCommerce Subscriptions.
 *
 * @since 1.6.0
 */
class WC_Memberships_Integration_Subscriptions_Discounts {


	/** @var bool whether to apply discounts to sign up fees (user setting) */
	private $apply_member_discounts_to_sign_up_fees = false;

	/** @var array applied sign up fee discount cache (associative array) */
	private $discounted_sign_up_fee = [];

	/** @var array memoized subscription products HTML prices (associative array) */
	private $subscription_product_price_html = [];

	/** @var array memoized subscription products HTML subtotals (associative array) */
	private $subscription_product_subtotal_html = [];


	/**
	 * Hooks into Memberships Discounts to handle Subscription products.
	 *
	 * @since 1.6.0
	 */
	public function __construct() {

		// process member discounts for Subscriptions after standard discounts
		add_action( 'wp_loaded', [ $this, 'init' ], 20 );

		// create an option in settings to enable sign up fees discounts
		add_filter( 'wc_memberships_products_settings', [ $this, 'enable_discounts_to_sign_up_fees' ] );

		// ensure we filter member prices before Subscriptions calculates cart totals
		add_filter( 'wc_memberships_price_adjustments_filter_priority', [ $this, 'adjust_price_filters_priority' ] );

		// if using cart discounts, ensure renewals don't get double discounted
		add_filter( 'wc_memberships_exclude_product_from_member_discounts', [ $this, 'disable_cart_discounts_for_renewals' ], 999, 2 );
	}


	/**
	 * Initializes member discounts for subscription products.
	 *
	 * @see \WC_Memberships_Member_Discounts::init()
	 * @internal
	 *
	 * @since 1.8.3
	 */
	public function init() {

		// process discounts only if there's a member logged in
		if ( wc_memberships()->get_member_discounts_instance()->applying_discounts() ) {

			$this->apply_member_discounts_to_sign_up_fees = 'yes' === get_option( 'wc_memberships_enable_subscriptions_sign_up_fees_discounts', 'no' );

			// make sure the price of subscription renewal cart items is honored (i.e. not discounted again)
			add_action( 'woocommerce_before_calculate_totals',                    [ $this, 'disable_price_adjustments_for_renewal' ], 11 );
			add_action( 'wc_memberships_discounts_enable_price_adjustments',      [ $this, 'disable_price_adjustments_for_renewal' ], 11 );
			add_action( 'wc_memberships_discounts_enable_price_html_adjustments', [ $this, 'disable_price_adjustments_for_renewal' ], 11 );

			// make sure the subscription product HTML price is right when discounted
			add_filter( 'woocommerce_subscriptions_product_price_string', [ $this, 'get_subscription_product_price_html' ], 999, 2 );
			add_filter( 'wc_memberships_get_price_html_after_discount',   [ $this, 'handle_subscription_product_discounted_price_html'], 10, 3 );
			add_filter( 'wc_memberships_get_price_html_before_discount',  [ $this, 'handle_subscription_product_discounted_price_html'], 10, 3 );
			add_filter( 'woocommerce_cart_item_subtotal',                 [ $this, 'handle_subscription_product_discounted_subtotal_html' ], 10, 2 );

			// make sure that product sign ups are handled according to discount setting
			add_filter( 'woocommerce_subscriptions_product_sign_up_fee', [ $this, 'maybe_adjust_product_sign_up_fee' ], 1, 2 );
		}
	}


	/**
	 * Adjust the filter priority for member discounts when filtering product price.
	 *
	 * This ensures that member discounts are added *before* Subscriptions calculates recurring cart totals,
	 *  keeping the initial and recurring prices correct.
	 *
	 * @since 1.9.1
	 *
	 * @param int $priority the default filter priority, 999
	 * @return int the adjusted priority
	 */
	public function adjust_price_filters_priority( $priority ) {
		return 99;
	}


	/**
	 * Filters the subscription product price string.
	 *
	 * Also used to filter the subscription product subtotal in cart.
	 * @see \WC_Memberships_Integration_Subscriptions_Discounts::handle_subscription_product_discounted_price_html()
	 * @see \WC_Memberships_Integration_Subscriptions_Discounts::handle_subscription_product_discounted_subtotal_html()
	 *
	 * @internal
	 *
	 * @since 1.8.0
	 *
	 * @param string $html_price the price HTML
	 * @param \WC_Product_Subscription|\WC_Product_Variable_Subscription $product a subscription product
	 * @param int|float $quantity optional quantity (defaults to 1), only used for cart subtotal handling
	 * @return string HTML
	 */
	public function get_subscription_product_price_html( $html_price, $product, $quantity = 1 ) {

		$memoize    = 'woocommerce_cart_item_subtotal' !== current_filter();
		$product_id = $product->get_id();

		if ( $memoize && isset( $this->subscription_product_price_html[ $product_id ] ) && $product->is_type( 'variable-subscription' ) ) {

			$html_price = $this->subscription_product_price_html[ $product_id ];

		} elseif ( \WC_Subscriptions_Product::is_subscription( $product ) && wc_memberships()->get_rules_instance()->product_has_purchasing_discount_rules( $product->get_id() ) ) {

			do_action( 'wc_memberships_discounts_disable_price_adjustments' );

			$price_before_discount = wc_get_price_to_display( $product, $quantity > 1 ? [ 'qty' => $quantity ] : [] );

			do_action( 'wc_memberships_discounts_enable_price_adjustments' );

			$price_after_discount = wc_get_price_to_display( $product, $quantity > 1 ? [ 'qty' => $quantity ] : [] );

			if ( $price_before_discount !== $price_after_discount ) {

				// prevent infinite filter loop
				remove_filter( 'woocommerce_subscriptions_product_price_string', [ $this, 'get_subscription_product_price_html' ], 999 );

				// this is already going to be discounted if needed in self::maybe_adjust_product_sign_up_fee()
				$sign_up_fee = \WC_Subscriptions_Product::get_sign_up_fee( $product );

				if ( 'variable-subscription' === $product->get_type() ) {

					// with variable subscription product we need to insert the before price after the "From:" string
					$from_text = wc_get_price_html_from_text( $product );

					if ( Framework\SV_WC_Helper::str_starts_with( $html_price, $from_text ) || ( is_rtl() && Framework\SV_WC_Helper::str_ends_with( $html_price, $from_text ) ) ) {
						$html_price = $from_text . ' <del>' . wc_price( $price_before_discount ) . '</del> ' . wc_price( $price_after_discount ) . ' ';
					} else {
						// may happen in rare chances such as when all variations are identically priced or if the above check fails for unforeseen circumstances
						$html_price = '<del>' . wc_price( $price_before_discount ) . '</del> ' . wc_price( $price_after_discount ) . ' ';
					}

				} else {

					// simple subscriptions and individual variations, just show the before/after price info
					$html_price = '<del>' . wc_price( $price_before_discount ) . '</del> ' . wc_price( $price_after_discount ) . ' ';
				}

				// rebuild the HTML price string using Subscriptions helper
				$html_price = (string) \WC_Subscriptions_Product::get_price_string( $product, [
					'price'       => $html_price,
					'sign_up_fee' => $sign_up_fee,
				] );

				if ( $memoize ) {
					$this->subscription_product_price_html[ $product_id ] = $html_price;
				}

				// restore the current HTML price string filter
				add_filter( 'woocommerce_subscriptions_product_price_string', [ $this, 'get_subscription_product_price_html' ], 999, 2 );
			}
		}

		return $html_price;
	}


	/**
	 * Ensures there's no repeated string in subscription products that have discounts applied.
	 *
	 * @internal
	 *
	 * @since 1.8.0
	 *
	 * @param string $price_html the price HTML (before or after memberships discounts)
	 * @param \WC_Product $product the product, which might be a subscription product
	 * @param string $original_price_html the original price HTML
	 * @return string HTML
	 */
	public function handle_subscription_product_discounted_price_html( $price_html, $product, $original_price_html ) {

		return \WC_Subscriptions_Product::is_subscription( $product ) && ! $product->is_type( 'variable-subscription' ) ? $original_price_html : $price_html;
	}


	/**
	 * Filters a subscription product subtotal when the customer signs up for more than 1 quantity.
	 *
	 * @internal
	 *
	 * @since 1.22.2
	 *
	 * @param string $subtotal_html the cart subtotal
	 * @param array $cart_item cart item data
	 * @return string HTML
	 */
	public function handle_subscription_product_discounted_subtotal_html( $subtotal_html, $cart_item ) {

		if ( isset( $cart_item['data'], $cart_item['quantity'] ) && $cart_item['quantity'] > 1 ) {

			/** @var \WC_Product_Subscription|\WC_Product $product */
			$product = $cart_item['data'] ?? null;

			if ( $product && WC_Subscriptions_Product::is_subscription( $product ) && wc_memberships()->get_rules_instance()->product_has_purchasing_discount_rules( $product->get_id() ) ) {

				if ( isset( $this->subscription_product_subtotal_html[ $product->get_id() ] ) ) {

					$subtotal_html = $this->subscription_product_subtotal_html[ $product->get_id() ];

				} else {

					$subtotal_html = $this->get_subscription_product_price_html( $subtotal_html, $cart_item['data'], $cart_item['quantity'] );

					$this->subscription_product_subtotal_html[ $product->get_id() ] = $subtotal_html;
				}
			}
		}

		return $subtotal_html;
	}


	/**
	 * Do not discount the price of subscription renewal items in the cart.
	 *
	 * If the cart contains a renewal (which will be the entire contents of the cart,
	 * because it can only contain a renewal), disable the discounts applied
	 * by @see WC_Memberships_Member_Discounts::enable_price_adjustments() because
	 * we want to honour the renewal price.
	 *
	 * However, we also only want to disable prices for the renewal cart items only,
	 * not other products which should be discounted which may be displayed outside
	 * the cart, so we need to be selective about when we disable the price adjustments
	 * by checking a mix of cart/checkout constants and hooks to see if we're in
	 * something relating to the cart or not.
	 *
	 * @internal
	 *
	 * @since 1.6.1
	 */
	public function disable_price_adjustments_for_renewal() {

		if (
			( function_exists( 'wcs_cart_contains_renewal' ) && false !== wcs_cart_contains_renewal() )
		 || ( function_exists( 'wcs_cart_contains_resubscribe' ) && false !== wcs_cart_contains_resubscribe() )
		) {

			$disable_price_adjustments = false;

			if ( defined( 'WOOCOMMERCE_CHECKOUT' ) || is_checkout() || is_cart() ) {
				$disable_price_adjustments = true;
			} elseif ( did_action( 'woocommerce_before_mini_cart' ) > did_action( 'woocommerce_after_mini_cart' ) ) {
				$disable_price_adjustments = true;
			}

			if ( $disable_price_adjustments ) {
				do_action( 'wc_memberships_discounts_disable_price_adjustments' );
				do_action( 'wc_memberships_discounts_disable_price_html_adjustments' );
			}
		}
	}


	/**
	 * Adds option to product settings.
	 *
	 * Filters product settings fields and add a checkbox to let user choose to enable discounts for subscriptions sign up fees
	 *
	 * @internal
	 *
	 * @since 1.6.0
	 *
	 * @param array $product_settings
	 * @return array
	 */
	public function enable_discounts_to_sign_up_fees( $product_settings ) {

		$new_option = array(
			array(
				'type'    => 'checkbox',
				'id'      => 'wc_memberships_enable_subscriptions_sign_up_fees_discounts',
				'name'    => __( 'Discounts apply to subscriptions sign up fees', 'woocommerce-memberships' ),
				'desc'    => __( 'If enabled, membership discounts will also apply to sign up fees of subscription products.', 'woocommerce-memberships' ),
				'default' => 'no',
			),
		);

		array_splice( $product_settings, 2, 0, $new_option );

		return $product_settings;
	}


	/**
	 * Avoids double-discounting when adding a product to cart intended for subscription's renewal.
	 *
	 * @internal
	 *
	 * @since 1.17.6
	 *
	 * @param bool $exclude_from_discounts whether the product should be excluded from discounts
	 * @param null|int|\WC_Product $product the product object or ID (null value is for a legacy version of this callback)
	 * @return bool
	 */
	public function disable_cart_discounts_for_renewals( $exclude_from_discounts, $product = null ) {

		if ( ! $exclude_from_discounts && isset( WC()->cart ) && \WC_Subscriptions_Product::is_subscription( $product ) && is_user_logged_in() ) {

			$product_id = $product instanceof \WC_Product ? $product->get_id() : $product;

			foreach ( WC()->cart->get_cart_contents() as $item ) {

				if ( $product_id && isset( $item['subscription_renewal'] ) && (int) $product_id === (int) $item['product_id'] ) {

					$exclude_from_discounts = true;
					break;
				}
			}
		}

		return $exclude_from_discounts;
	}


	/**
	 * Maybe filters the sign up fee for handling member discounts.
	 *
	 * @internal
	 *
	 * @since 1.6.0
	 *
	 * @param float|int $sign_up_fee the sign up fee which is probably discounted
	 * @param \WC_Product_Subscription $subscription_product the subscription product the sign up fee is for
	 * @return float|int
	 */
	public function maybe_adjust_product_sign_up_fee( $sign_up_fee, $subscription_product ) {

		// don't filter the sign up fee if we shouldn't apply any discounts
		if ( $this->apply_member_discounts_to_sign_up_fees ) {

			$member_discounts = wc_memberships()->get_member_discounts_instance();
			$cache_key        = $subscription_product->get_id() . ':' . $sign_up_fee;

			if ( ! isset( $this->discounted_sign_up_fee[ $cache_key ] ) ) {
				$this->discounted_sign_up_fee[ $cache_key ] = $member_discounts->user_has_member_discount( $subscription_product ) ? $member_discounts->get_discounted_price( $sign_up_fee, $subscription_product ) : $sign_up_fee;
			}

			$sign_up_fee = $this->discounted_sign_up_fee[ $cache_key ];
		}

		return $sign_up_fee;
	}


}
