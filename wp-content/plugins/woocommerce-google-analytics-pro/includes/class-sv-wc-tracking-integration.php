<?php
/**
 * WooCommerce Google Analytics Pro
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Google Analytics Pro to newer
 * versions in the future. If you wish to customize WooCommerce Google Analytics Pro for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-google-analytics-pro/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2020, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\PluginFramework\v5_10_2;

defined( 'ABSPATH' ) or exit;

/**
 * Base tracking integration class.
 *
 * Provides basic setup, form fields and hooks for tracking plugins.
 *
 * The subclass should at least provide a constructor that sets the
 * integration ID, `method_title` and `method_description`, and implement a
 * `get_plugin()` method, which should return the integration plugin instance.
 *
 * @since 1.0.0
 */
class SV_WC_Tracking_Integration extends \WC_Integration {


	/** @var SV_WC_API_Base the API instance */
	protected $api;

	/** @var array of event names */
	public $event_name = array();

	/** @var array of property names */
	public $property_name = array();


	/**
	 * Constructs the class.
	 *
	 * Should be overridden and called from within subclasses, so that they can
	 * set up the integration ID, method title & description.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id the integration ID
	 * @param string $title the integration title
	 * @param string $description the integration description
	 */
	public function __construct( $id, $title, $description ) {

		// setup integration
		$this->id                 = $id;
		$this->method_title       = $title;
		$this->method_description = $description;

		// load admin form
		$this->init_form_fields();

		// load settings, events and properties
		$this->load_events_and_properties();

		// add hooks to record events - only add hook if event name is populated

		// pageviews
		add_action( 'wp_head', array( $this, 'pageview' ) );

		// viewed homepage
		if ( $this->has_event( 'viewed_homepage' ) ) {
			add_action( 'wp_head', array( $this, 'viewed_homepage' ) );
		}

		// signed in
		if ( $this->has_event( 'signed_in' ) ) {
			add_action( 'wp_login', array( $this, 'signed_in' ), 10, 2 );
		}

		// signed out
		if ( $this->has_event( 'signed_out' ) ) {
			add_action( 'wp_logout', array( $this, 'signed_out' ) );
		}

		// viewed Signup page (on my account page, if enabled)
		if ( $this->has_event( 'viewed_signup' ) ) {
			add_action( 'register_form', array( $this, 'viewed_signup' ) );
		}

		// signed up for new account (on my account page if enabled OR during checkout)
		if ( $this->has_event( 'signed_up' ) ) {
			add_action( 'user_register', array( $this, 'signed_up' ) );
		}

		// viewed product (properties: Name)
		if ( $this->has_event( 'viewed_product' ) ) {
			add_action( 'woocommerce_after_single_product_summary', array( $this, 'viewed_product' ), 1 );
		}

		// clicked product in listing
		if ( $this->has_event( 'clicked_product' ) ) {
			add_action( 'woocommerce_before_shop_loop_item', array( $this, 'clicked_product' ) );
		}

		// added product to cart (properties: Product Name, Quantity)
		if ( $this->has_event( 'added_to_cart' ) ) {

			// single product add to cart button
			add_action( 'woocommerce_add_to_cart', array( $this, 'added_to_cart' ), 10, 6 );

			// AJAX add to cart
			if ( is_ajax() ) {
				add_action( 'woocommerce_ajax_added_to_cart', array( $this, 'ajax_added_to_cart' ) );
			}
		}

		// removed product from cart (Properties: Product Name)
		if ( $this->has_event( 'removed_from_cart' ) ) {

			if ( SV_WC_Plugin_Compatibility::is_wc_version_lt( '3.7' ) ) {
				add_action( 'woocommerce_before_cart_item_quantity_zero', [ $this, 'removed_from_cart' ] );
			}

			add_action( 'woocommerce_remove_cart_item', [ $this, 'removed_from_cart' ] );
		}

		// changed quantity of product in cart (properties: Product Name, Quantity )
		if ( $this->has_event( 'changed_cart_quantity' ) ) {
			add_action( 'woocommerce_after_cart_item_quantity_update', array( $this, 'changed_cart_quantity' ), 10, 2 );
		}

		// viewed cart
		if ( $this->has_event( 'viewed_cart' ) ) {
			add_action( 'woocommerce_after_cart_contents', array( $this, 'viewed_cart' ) );
			add_action( 'woocommerce_cart_is_empty', array( $this, 'viewed_cart' ) );
		}

		// started checkout
		if ( $this->has_event( 'started_checkout' ) ) {
			add_action( 'woocommerce_after_checkout_form', array( $this, 'started_checkout' ) );
		}

		// selected payment method
		if ( isset( $this->event_name['provided_billing_email'] ) && $this->event_name['provided_billing_email'] ) {
			add_action( 'woocommerce_after_checkout_form', array( $this, 'provided_billing_email' ) );
		}

		// selected payment method
		if ( $this->has_event( 'selected_payment_method' ) ) {
			add_action( 'woocommerce_after_checkout_form', array( $this, 'selected_payment_method' ) );
		}

		// placed order
		if ( $this->has_event( 'placed_order' ) ) {
			add_action( 'woocommerce_checkout_order_processed', array( $this, 'placed_order' ) );
		}

		// started payment (for gateways that direct post from payment page, eg: Braintree TR, Authorize.net AIM, etc
		if ( $this->has_event( 'started_payment' ) ) {
			add_action( 'after_woocommerce_pay', array( $this, 'started_payment' ) );
		}

		// completed purchase
		if ( $this->has_event( 'completed_purchase' ) ) {

			add_action( 'woocommerce_order_status_on-hold',    array( $this, 'purchase_on_hold' ) );

			add_action( 'woocommerce_payment_complete',        array( $this, 'completed_purchase' ) );
			add_action( 'woocommerce_order_status_processing', array( $this, 'completed_purchase' ) );
			add_action( 'woocommerce_order_status_completed',  array( $this, 'completed_purchase' ) );

			// catch orders processed through payment gateways such as COD
			add_action( 'woocommerce_thankyou', array( $this, 'completed_purchase' ) );
		}

		// wrote review or commented (properties: Product Name if review, Post Title if blog post)
		if ( $this->has_event( 'wrote_review' ) || $this->has_event( 'commented' ) ) {
			add_action( 'comment_post', array( $this, 'wrote_review_or_commented' ) );
		}

		// viewed account
		if ( $this->has_event( 'viewed_account' ) ) {
			add_action( 'woocommerce_after_my_account', array( $this, 'viewed_account' ) );
		}

		// viewed order
		if ( $this->has_event( 'viewed_order' ) ) {
			add_action( 'woocommerce_view_order', array( $this, 'viewed_order' ) );
		}

		// updated address
		if ( $this->has_event( 'updated_address' ) ) {
			add_action( 'woocommerce_customer_save_address', array( $this, 'updated_address' ) );
		}

		// changed password
		if ( $this->has_event( 'changed_password' ) && ! empty( $_POST['password_1'] ) ) {
			add_action( 'woocommerce_save_account_details', array( $this, 'changed_password' ) );
		}

		// applied coupon
		if ( $this->has_event( 'applied_coupon' ) ) {
			add_action( 'woocommerce_applied_coupon', array( $this, 'applied_coupon' ) );
		}

		// removed coupon
		if ( $this->has_event( 'removed_coupon' ) && ! empty( $_GET['remove_coupon'] ) ) {
			add_action( 'woocommerce_init', array( $this, 'removed_coupon' ) );
		}

		// tracked order
		if ( $this->has_event( 'tracked_order' ) ) {
			add_action( 'woocommerce_track_order', array( $this, 'tracked_order' ) );
		}

		// estimated shipping
		if ( $this->has_event( 'estimated_shipping' ) ) {
			add_action( 'woocommerce_calculated_shipping', array( $this, 'estimated_shipping' ) );
		}

		// cancelled order
		if ( $this->has_event( 'cancelled_order' ) ) {
			add_action( 'woocommerce_cancelled_order', array( $this, 'cancelled_order' ) );
		}

		// order refunded
		if ( $this->has_event( 'order_refunded' ) ) {
			add_action( 'woocommerce_order_partially_refunded', array( $this, 'order_refunded' ), 10, 2 );
			add_action( 'woocommerce_order_fully_refunded',     array( $this, 'order_refunded' ), 10, 2 );
		}

		// reordered previous order
		if ( $this->has_event('reordered' ) ) {
			add_action( 'woocommerce_ordered_again', array( $this, 'reordered' ) );
		}

		// save admin options
		if ( is_admin() ) {
			add_action( 'woocommerce_update_options_integration_' . $this->id, array( $this, 'process_admin_options' ) );
		}

		// ensure Google AdWords doesn't mistake offsite gateways as the referrer
		add_filter( 'woocommerce_get_return_url', array( $this, 'adwords_referrer_remove_gateways' ) );
	}


	/** Helper methods ********************************************************/


	/**
	 * Helper to determine if an event is set and is being triggered.
	 *
	 * @since 1.3.2
	 *
	 * @param string $name the event name
	 * @return bool true if the event is set and being triggered
	 */
	public function has_event( $name = null ) {
		return isset( $this->event_name[ $name ] ) && $this->event_name[ $name ];
	}


	/**
	 * Determines if the integration is enabled.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_enabled() {

		return 'yes' === $this->get_option( 'enabled' );
	}


	/**
	 * Gets the category hierarchy up to 5 levels deep for the passed product.
	 *
	 * @since 1.1.1
	 *
	 * @param \WC_Product $product the product object
	 * @return string the category hierarchy or an empty string
	 */
	public function get_category_hierarchy( $product ) {

		if ( $parent_id = $product->get_parent_id() ) {
			$product_id = $parent_id;
		} else {
			$product_id = $product->get_id();
		}

		$categories = wc_get_product_terms( $product_id, 'product_cat', array( 'orderby' => 'parent', 'order' => 'DESC' ) );

		if ( ! is_array( $categories ) || empty( $categories ) ) {
			return '';
		}

		$child_term = $categories[0];

		return trim( $this->get_category_parents( $child_term->term_id ), '/' );
	}


	/**
	 * Builds the category hierarchy recursively.
	 *
	 * Inspired by `get_category_parents()` in WordPress core.
	 *
	 * @since 1.1.1
	 * @param int $term_id the category term ID
	 * @param string $separator the term separator
	 * @param array $visited the visited term IDs
	 * @return string the category hierarchy
	 */
	private function get_category_parents( $term_id, $separator = '/', $visited = array() ) {

		$chain  = '';
		$parent = get_term( $term_id, 'product_cat' );

		if ( is_wp_error( $parent ) ) {
			return $parent;
		}

		$name = $parent->name;

		if ( $parent->parent && ( $parent->parent !== $parent->term_id ) && ! in_array( $parent->parent, $visited, true ) && count( $visited ) < 4 ) {

			$visited[] = $parent->parent;

			$chain .= $this->get_category_parents( $parent->parent, $separator, $visited );
		}

		$chain .= $name . $separator;

		return $chain;
	}


	/**
	 * Returns the identifier for a given product.
	 *
	 * In 1.3.0 moved from \WC_Google_Analytics_Pro
	 *
	 * @since 1.0.3
	 *
	 * @param \WC_Product|int $product the product object or ID
	 * @return string the product identifier, either its SKU or `#<id>`
	 */
	public function get_product_identifier( $product ) {

		if ( ! $product instanceof \WC_Product ) {
			$product = wc_get_product( $product );
		}

		if ( ! $product ) {
			return '';
		}

		if ( $product->get_sku() ) {

			$identifier = $product->get_sku();

		} else {

			if ( $parent_id = $product->get_parent_id() ) {
				$product_id = $parent_id;
			} else {
				$product_id = $product->get_id();
			}

			$identifier = '#' . $product_id;
		}

		return $identifier;
	}


	/**
	 * Returns a comma separated list of variation attributes for a given variation or variable product.
	 *
	 * For a variable prouct, the default variation attributes ar returned.
	 *
	 * @since 1.3.0
	 * @param \WC_Product|int $product the product object or ID
	 * @return string comma-separated list of variation attributes
	 */
	public function get_product_variation_attributes( $product) {

		if ( ! $product instanceof \WC_Product ) {
			$product = wc_get_product( $product );
		}

		if ( ! $product ) {
			return '';
		}

		$variant = '';

		if ( $product->is_type( 'variation' ) ) {

			$variant = implode( ',', array_values( $product->get_variation_attributes() ) );

		} elseif ( $product->is_type( 'variable' ) ) {

			$variant = implode( ', ', array_values( $product->get_default_attributes() ) );
		}

		return $variant;
	}


	/** General methods *******************************************************/


	/**
	 * Ensures Google Adwords doesn't mistake the offsite gateway as the
	 * referrer by adding the `utm_nooverride` parameter.
	 *
	 * @param string $return_url WooCommerce return URL
	 * @return string the return URL
	 */
	public function adwords_referrer_remove_gateways( $return_url ) {

		$return_url = remove_query_arg( 'utm_nooverride', $return_url );

		$return_url = add_query_arg( 'utm_nooverride', '1', $return_url );

		return $return_url;
	}


	/** Settings **************************************************************/


	/**
	 * Initializes form fields in the format required by \WC_Integration.
	 *
	 * @since 1.0.0
	 */
	public function init_form_fields() {

		// add fields for event names
		$this->form_fields = array(

			'additional_settings_section' => array(
				'title'       => esc_html__( 'Additional Settings', 'woocommerce-google-analytics-pro' ),
				'type'        => 'title',
			),

			'debug_mode' => array(
				'title'       => __( 'Debug Mode', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'This logs API requests/responses to the WooCommerce log. Please only enable this if you are having issues.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'select',
				'default'     => 'off',
				'options'     => array(
					'off' => __( 'Off', 'woocommerce-google-analytics-pro' ),
					'on'  => __( 'On', 'woocommerce-google-analytics-pro' ),
				),
			),

			'event_names_section' => array(
				'title'       => __( 'Event Names', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Customize the event names. Leave a field blank to disable tracking of that event.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'title',
			),

			'signed_in_event_name' => array(
				'title'       => __( 'Signed In', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer signs in.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'signed in'
			),

			'signed_out_event_name' => array(
				'title'       => __( 'Signed Out', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer signs out.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'signed out'
			),

			'viewed_signup_event_name' => array(
				'title'       => __( 'Viewed Signup', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer views the registration form.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'viewed signup'
			),

			'signed_up_event_name' => array(
				'title'       => __( 'Signed Up', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer registers a new account.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'signed up'
			),

			'viewed_homepage_event_name' => array(
				'title'       => __( 'Viewed Homepage', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer views the homepage.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'viewed homepage'
			),

			'viewed_product_event_name' => array(
				'title'       => __( 'Viewed Product', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer views a single product', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'viewed product'
			),

			'clicked_product_event_name' => array(
				'title'       => __( 'Clicked Product', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer clicks a product in listing, such as search results or related products.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'clicked product'
			),

			'added_to_cart_event_name' => array(
				'title'       => __( 'Added to Cart', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer adds an item to the cart.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'added to cart'
			),

			'removed_from_cart_event_name' => array(
				'title'       => __( 'Removed from Cart', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer removes an item from the cart.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'removed from cart'
			),

			'changed_cart_quantity_event_name' => array(
				'title'       => __( 'Changed Cart Quantity', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer changes the quantity of an item in the cart.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'changed cart quantity'
			),

			'viewed_cart_event_name' => array(
				'title'       => __( 'Viewed Cart', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer views the cart.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'viewed cart'
			),

			'applied_coupon_event_name' => array(
				'title'       => __( 'Applied Coupon', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer applies a coupon', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'applied coupon'
			),

			'removed_coupon_event_name' => array(
				'title'       => __( 'Removed Coupon', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer removes a coupon', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'removed coupon'
			),

			'started_checkout_event_name' => array(
				'title'       => __( 'Started Checkout', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer starts the checkout.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'started checkout'
			),

			'provided_billing_email_event_name' => array(
				'title'       => __( 'Provided Billing Email', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer provides a billing email at checkout.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'provided billing email'
			),

			'selected_payment_method_event_name' => array(
				'title'       => __( 'Selected Payment Method', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer selects a payment method at checkout.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'selected payment method'
			),

			'placed_order_event_name' => array(
				'title'       => __( 'Placed Order', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer places an order via checkout.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'placed order'
			),

			'started_payment_event_name' => array(
				'title'       => __( 'Started Payment', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer views the payment page (used with direct post payment gateways)', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'started payment'
			),

			'completed_purchase_event_name' => array(
				'title'       => __( 'Completed Purchase', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer completes a purchase.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'completed purchase'
			),

			'wrote_review_event_name' => array(
				'title'       => __( 'Wrote Review', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer writes a review.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'wrote review'
			),

			'commented_event_name' => array(
				'title'       => __( 'Commented', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer writes a comment.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'commented'
			),

			'viewed_account_event_name' => array(
				'title'       => __( 'Viewed Account', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer views the My Account page.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'viewed account'
			),

			'viewed_order_event_name' => array(
				'title'       => __( 'Viewed Order', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer views an order', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'viewed order'
			),

			'updated_address_event_name' => array(
				'title'       => __( 'Updated Address', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer updates their address.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'updated address'
			),

			'changed_password_event_name' => array(
				'title'       => __( 'Changed Password', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer changes their password.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'changed password'
			),

			'estimated_shipping_event_name' => array(
				'title'       => __( 'Estimated Shipping', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer estimates shipping.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'estimated shipping'
			),

			'tracked_order_event_name' => array(
				'title'       => __( 'Tracked Order', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer tracks an order.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'tracked order'
			),

			'cancelled_order_event_name' => array(
				'title'       => __( 'Cancelled Order', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer cancels an order.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'cancelled order'
			),

			'order_refunded_event_name' => array(
				'title'       => __( 'Order Refunded', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when an order is refunded.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'order refunded'
			),

			'reordered_event_name' => array(
				'title'       => __( 'Reordered', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Triggered when a customer reorders a previous order.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => 'reordered'
			),
		);

		// add fields for property names
		if ( $this->supports_property_names() ) {

			$this->form_fields = array_merge( $this->form_fields, array(
				'property_names_section' => array(
					'title'       => __( 'Property Names', 'woocommerce-google-analytics-pro' ),
					'description' => __( 'Customize the property names. Leave a field blank to disable tracking of that property.', 'woocommerce-google-analytics-pro' ),
					'type'        => 'section',
					'default'     => ''
				),

				'product_name_property_name' => array(
					'title'       => __( 'Product Name', 'woocommerce-google-analytics-pro' ),
					'description' => __( 'Tracked when a customer views a product, adds / removes / changes quantities in the cart, or writes a review.', 'woocommerce-google-analytics-pro' ),
					'type'        => 'text',
					'default'     => 'product name'
				),

				'quantity_property_name' => array(
					'title'       => __( 'Product Quantity', 'woocommerce-google-analytics-pro' ),
					'description' => __( 'Tracked when a customer adds a product to their cart or changes the quantity in their cart.', 'woocommerce-google-analytics-pro' ),
					'type'        => 'text',
					'default'     => 'quantity'
				),

				'category_property_name' => array(
					'title'       => __( 'Product Category', 'woocommerce-google-analytics-pro' ),
					'description' => __( 'Tracked when a customer adds a product to their cart.', 'woocommerce-google-analytics-pro' ),
					'type'        => 'text',
					'default'     => 'category'
				),

				'coupon_code_property_name' => array(
					'title'       => __( 'Coupon Code', 'woocommerce-google-analytics-pro' ),
					'description' => __( 'Tracked when a customer applies a coupon.', 'woocommerce-google-analytics-pro' ),
					'type'        => 'text',
					'default'     => 'coupon code'
				),

				'order_id_property_name' => array(
					'title'       => __( 'Order ID', 'woocommerce-google-analytics-pro' ),
					'description' => __( 'Tracked when a customer completes their purchase.', 'woocommerce-google-analytics-pro' ),
					'type'        => 'text',
					'default'     => 'order id'
				),

				'order_total_property_name' => array(
					'title'       => __( 'Order Total', 'woocommerce-google-analytics-pro' ),
					'description' => __( 'Tracked when a customer completes their purchase.', 'woocommerce-google-analytics-pro' ),
					'type'        => 'text',
					'default'     => 'order total'
				),

				'shipping_total_property_name' => array(
					'title'       => __( 'Shipping Total', 'woocommerce-google-analytics-pro' ),
					'description' => __( 'Tracked when a customer completes their purchase.', 'woocommerce-google-analytics-pro' ),
					'type'        => 'text',
					'default'     => 'shipping total'
				),

				'total_quantity_property_name' => array(
					'title'       => __( 'Total Quantity', 'woocommerce-google-analytics-pro' ),
					'description' => __( 'Tracked when a customer completes their purchase.', 'woocommerce-google-analytics-pro' ),
					'type'        => 'text',
					'default'     => 'total quantity'
				),

				'payment_method_property_name' => array(
					'title'       => __( 'Payment Method', 'woocommerce-google-analytics-pro' ),
					'description' => __( 'Tracked when a customer completes their purchase.', 'woocommerce-google-analytics-pro' ),
					'type'        => 'text',
					'default'     => 'payment method'
				),

				'post_title_property_name' => array(
					'title'       => __( 'Post Title', 'woocommerce-google-analytics-pro' ),
					'description' => __( 'Tracked when a customer leaves a comment on a post.', 'woocommerce-google-analytics-pro' ),
					'type'        => 'text',
					'default'     => 'post title'
				),

				'country_property_name' => array(
					'title'       => __( 'Shipping Country', 'woocommerce-google-analytics-pro' ),
					'description' => __( 'Tracked when a customer estimates shipping.', 'woocommerce-google-analytics-pro' ),
					'type'        => 'text',
					'default'     => 'country'
				),

				'purchased_product_sku_property_name' => array(
					'title'       => __( 'Purchased SKU', 'woocommerce-google-analytics-pro' ),
					'description' => __( 'Tracked when a customer purchases the product.', 'woocommerce-google-analytics-pro' ),
					'type'        => 'text',
					'default'     => 'purchased product sku'
				),

				'purchased_product_name_property_name' => array(
					'title'       => __( 'Purchased Product Name', 'woocommerce-google-analytics-pro' ),
					'description' => __( 'Tracked when a customer purchases the product.', 'woocommerce-google-analytics-pro' ),
					'type'        => 'text',
					'default'     => 'purchased product name'
				),

				'purchased_product_category_property_name' => array(
					'title'       => __( 'Purchased Category', 'woocommerce-google-analytics-pro' ),
					'description' => __( 'Tracked when a customer purchases the product.', 'woocommerce-google-analytics-pro' ),
					'type'        => 'text',
					'default'     => 'purchased product category'
				),

				'purchased_product_price_property_name' => array(
					'title'       => __( 'Purchased Price', 'woocommerce-google-analytics-pro' ),
					'description' => __( 'Tracked when a customer purchases the product.', 'woocommerce-google-analytics-pro' ),
					'type'        => 'text',
					'default'     => 'purchased product price'
				),

				'purchased_product_qty_property_name' => array(
					'title'       => __( 'Purchased Quantity', 'woocommerce-google-analytics-pro' ),
					'description' => __( 'Tracked when a customer purchases the product.', 'woocommerce-google-analytics-pro' ),
					'type'        => 'text',
					'default'     => 'purchased product quantity'
				)

			) );
		}
	}


	/**
	 * Determines if this tracking integration supports property names.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	protected function supports_property_names() {
		return true;
	}


	/**
	 * Determines if debug mode is enabled.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function debug_mode_on() {
		return 'on' === $this->get_option( 'debug_mode', 'off' );
	}


	/**
	 * Returns a list of all the events.
	 *
	 * @since 1.3.0
	 * @return array
	 */
	public function get_events() {
		return array_keys( $this->event_name );
	}


	/**
	 * Returns the customized event name for the given event.
	 *
	 * @since 1.3.0
	 * @param string $event event key
	 * @return string event name or an empty string
	 */
	public function get_event_name( $event ) {
		return isset( $this->event_name[ $event ] ) ? $this->event_name[ $event ] : '';
	}


	/**
	 * Returns the pretty title for the event.
	 *
	 * @since 1.3.0
	 * @param string $event event key
	 * @return string event title or an empty string
	 */
	public function get_event_title( $event ) {
		return isset( $this->form_fields[ $event . '_event_name' ]['title'] ) ? $this->form_fields[ $event . '_event_name' ]['title'] : '';
	}


	/**
	 * Processes admin options
	 *
	 * @see \WC_Settings_API::process_admin_options()
	 *
	 * @since 1.3.0
	 */
	public function process_admin_options() {

		parent::process_admin_options();

		// make sure updated events and properties are loaded after saving settings - otherwise the funnel UI will
		// display outdated event names
		$this->load_events_and_properties();
	}


	/**
	 * Loads events and properties
	 *
	 * @since 1.3.0
	 */
	public function load_events_and_properties() {

		// load settings, to ensure we have access to the latest and greatest values
		$this->init_settings();

		// load event / property names
		foreach ( $this->settings as $key => $value ) {

			if ( SV_WC_Helper::str_ends_with( $key, 'event_name' ) ) {

				// event name setting, remove '_event_name' and use as key
				$key = str_replace( '_event_name', '', $key );

				$this->event_name[ $key ] = $value;

			} elseif ( SV_WC_Helper::str_ends_with( $key, '_property_name' ) ) {

				// property name setting, remove '_property_name' and use as key
				$key = str_replace( '_property_name', '', $key );
				$this->property_name[ $key ] = $value;
			}
		}
	}


}
