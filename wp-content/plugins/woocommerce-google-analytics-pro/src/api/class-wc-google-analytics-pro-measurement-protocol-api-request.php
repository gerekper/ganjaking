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
 * Do not edit or add to this file if you wish to upgrade WooCommerce PayPal Express to newer
 * versions in the future. If you wish to customize WooCommerce PayPal Express for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-PayPal Express/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2015-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_2 as Framework;

/**
 * The Measurement Protocol API request class.
 *
 * Generates a query string required by API specs to perform an API request.
 *
 * @since 1.0.0
 */
class WC_Google_Analytics_Pro_Measurement_Protocol_API_Request implements Framework\SV_WC_API_Request {


	/** @var array the request parameters */
	private $parameters = array();

	/** @var string Google Analytics tracking ID */
	private $tracking_id;


	/**
	 * Constructs the class.
	 *
	 * @since 1.0.0
	 * @param string $tracking_id the Google Analytics tracking ID
	 */
	public function __construct( $tracking_id ) {

		$this->tracking_id = $tracking_id;
		$host_name         = $this->get_host_name();
		$default_params    = array(
			'v'   => '1',                // API version
			'tid' => $this->tracking_id, // tracking ID
			'z'   => time(),             // request time
		);

		if ( ! empty( $host_name ) ) {
			$default_params['dh'] = $host_name; // document host name
		}

		// set default params for all requests
		$this->add_parameters( $default_params );
	}


	/**
	 * Gets the document host name.
	 *
	 * @since 1.6.5
	 *
	 * @return string
	 */
	private function get_host_name() {

		$host_name = str_replace( array( 'http://', 'https://', 'www.', '//' ), '', get_site_url() );
		$paths     = ! empty( $host_name ) ? explode ( '/', $host_name ) : array();

		return isset( $paths[0] ) ? $paths[0] : '';
	}


	/**
	 * Gets the request parameters.
	 *
	 * @see \WC_Google_Analytics_Pro_Measurement_Protocol_API_Request::get_parameters() for normalized results used in this object
	 *
	 * @since 1.6.0
	 *
	 * @return array implements interface method
	 */
	public function get_params() {

		return $this->parameters;
	}


	/**
	 * Gets the request data.
	 *
	 * @since 1.6.0
	 *
	 * @return array implements interface method
	 */
	public function get_data() {

		return [];
	}


	/**
	 * Adds identity params to the request.
	 *
	 * In 1.3.0 removed $client_id and $user_id params, added $args param.
	 *
	 * @since 1.0.0
	 * @param string[] $args an array of arguments {
	 * 		 @type string $client_id the anonymous GA client ID, usually from GA cookie (cid)
	 *     @type string $user_id (optional) identified user ID (uid)
	 *     @type string $ip (optional) the visitor's IP
	 *     @type string $ip (optional) the visitor's User-Agent (browser)
	 * }
	 */
	public function identify( $args ) {

		$this->add_parameter( 'cid', $args['cid'] );

		if ( ! empty( $args['uid'] ) ) {
			$this->add_parameter( 'uid', $args['uid'] );
		}

		if ( ! empty( $args['uip'] ) ) {
			$this->add_parameter( 'uip', $args['uip'] );
		}

		if ( ! empty( $args['ua'] ) ) {
			$this->add_parameter( 'ua', $args['ua'] );
		}
	}

	/**
	 * Adds parameters to track an event.
	 *
	 * @since 1.0.0
	 * @param string $event_name the event name
	 * @param array $properties the event properties
	 */
	public function track_event( $event_name, $properties ) {

		/**
		 * Filters the event parameters
		 *
		 * @since 1.1.1
		 * @param array $parameters An associative array of event parameters
		 * @param string $event_name The event name
		 */
		$this->add_parameters( apply_filters( 'wc_google_analytics_pro_api_event_parameters', array(
			't'   => 'event',
			'ec'  => isset( $properties['eventCategory'] )  ? $properties['eventCategory']  : 'general',
			'ea'  => isset( $properties['eventAction'] )    ? $properties['eventAction']    : $event_name,
			'el'  => isset( $properties['eventLabel'] )     ? $properties['eventLabel']     : null,
			'ev'  => isset( $properties['eventValue'] )     ? $properties['eventValue']     : null,
			'ni'  => isset( $properties['nonInteraction'] ) ? $properties['nonInteraction'] : null,
		), $event_name ) );
	}


	/**
	 * Adds parameters to track enhanced ecommerce product impression.
	 *
	 * @since 1.0.0
	 * @param \WC_Product $product the product object
	 */
	public function track_ec_impression( $product ) {

		$product_identifier = wc_google_analytics_pro()->get_integration()->get_product_identifier( $product );
		$category_hierarchy = wc_google_analytics_pro()->get_integration()->get_category_hierarchy( $product );
		$product_variant    = wc_google_analytics_pro()->get_integration()->get_product_variation_attributes( $product );

		/**
		 * Filters the enhanced ecommerce product impression parameters
		 *
		 * @since 1.1.1
		 * @param array $parameters An associative array of enhanced ecommerce product impression parameters
		 * @param \WC_Product $product The product
		 */
		$this->add_parameters( apply_filters( 'wc_google_analytics_pro_api_ec_impression_parameters', array(
			'il1nm'     => '',                                                  // impression list 1, required
			'il1pi1id'  => $product_identifier,                                 // product impression 1 ID, either ID or name must be set
			'il1pi1nm'  => $product->get_title(),                               // product impression 1 name, either ID or name must be set
			'il1pi1ca'  => $category_hierarchy,                                 // product impression 1 category
			'il1pi1pr'  => $product->get_price(),                               // product impression 1 price
			'il1pi1br'  => '',                                                  // product impression 1 brand
			'il1pi1va'  => $product_variant,                                    // product impression 1 variant
			'il1pi1ps'  => '',                                                  // product impression 1 position
			'il1pi1cd1' => '',                                                  // custom dimension
		), $product ) );
	}


	/**
	 * Adds parameters to track the enhanced ecommerce add-to-cart event.
	 *
	 * @since 1.0.0
	 *
	 * @param \WC_Product $product the product object
	 * @param int|float $quantity the product cart quantity
	 * @param string $cart_item_key the item key of the product added to cart
	 */
	public function track_ec_add_to_cart( $product, $quantity, $cart_item_key = '' ) {

		$product_identifier = wc_google_analytics_pro()->get_integration()->get_product_identifier( $product );
		$category_hierarchy = wc_google_analytics_pro()->get_integration()->get_category_hierarchy( $product );
		$product_variant    = wc_google_analytics_pro()->get_integration()->get_product_variation_attributes( $product );

		if ( $parent_id = $product->get_parent_id() ) {
			$product_id = $parent_id;
		} else {
			$product_id = $product->get_id();
		}

		// if this is a single product page and the event is for the main product, we don't specify a list
		if ( is_single() && $product_id === (int) get_the_ID() ) {
			$product_list = '';
		} else {
			$product_list = wc_google_analytics_pro()->get_integration()->get_list_type();
		}

		/**
		 * Filters the enhanced ecommerce add to cart event parameters.
		 *
		 * @since 1.1.1
		 *
		 * @param array $parameters an associative array of enhanced ecommerce add to cart event parameters
		 * @param \WC_Product $product the product
		 * @param int|float $quantity the item quantity
		 * @param string $cart_item_key the item key of the product added to cart
		 */
		$this->add_parameters( apply_filters( 'wc_google_analytics_pro_api_ec_add_to_cart_parameters', [
			'pa'    => 'add',                 // product action
			'pal'   => $product_list,         // product list
			'pr1id' => $product_identifier,   // product id
			'pr1nm' => $product->get_title(), // product name
			'pr1ca' => $category_hierarchy,   // product category
			'pr1pr' => $product->get_price(), // product price
			'pr1qt' => $quantity,             // product quantity
			'pr1va' => $product_variant,      // product variant
		], $product, $quantity, $cart_item_key ) );
	}


	/**
	 * Adds parameters to track the enhanced ecommerce remove-from-cart event.
	 *
	 * @since 1.0.0
	 *
	 * @param \WC_Product $product the product object
	 * @param array $cart_item data from the product just removed from cart
	 */
	public function track_ec_remove_from_cart( $product, $cart_item = [] ) {

		$product_identifier = wc_google_analytics_pro()->get_integration()->get_product_identifier( $product );
		$category_hierarchy = wc_google_analytics_pro()->get_integration()->get_category_hierarchy( $product );
		$product_variant    = wc_google_analytics_pro()->get_integration()->get_product_variation_attributes( $product );

		/**
		 * Filters the enhanced ecommerce remove from cart event parameters.
		 *
		 * @since 1.1.1
		 *
		 * @param array $parameters an associative array of enhanced ecommerce remove from cart event parameters
		 * @param \WC_Product $product the product removed from cart
		 * @param array $cart_item the cart data of the product removed
		 */
		$this->add_parameters( apply_filters( 'wc_google_analytics_pro_api_ec_remove_from_cart_parameters', [
			'pa'    => 'remove',              // product action
			'pal'   => '',                    // product list
			'pr1id' => $product_identifier,   // product id
			'pr1nm' => $product->get_title(), // product name
			'pr1ca' => $category_hierarchy,   // product category
			'pr1pr' => $product->get_price(), // product price
			'pr1qt' => '1',                   // product quantity
			'pr1va' => $product_variant,      // product variant
		], $product, $cart_item ) );
	}


	/**
	 * Adds parameters to track the enhanced ecommerce completed purchase event.
	 *
	 * @since 1.0.0
	 *
	 * @param \WC_Order $order the order object
	 */
	public function track_ec_purchase( $order ) {

		$order_currency = $order->get_currency();
		$coupon_codes   = implode( ',', Framework\SV_WC_Plugin_Compatibility::is_wc_version_gte( '3.7' ) ? $order->get_coupon_codes() : $order->get_used_coupons() );

		// set general data about the purchase
		$params = [
			'pa'  => 'purchase',                   // product action
			'ti'  => $order->get_order_number(),   // transaction ID, required
			'tr'  => $order->get_total(),          // revenue
			'tt'  => $order->get_total_tax(),      // tax
			'ts'  => $order->get_shipping_total(), // shipping
			'tcc' => $coupon_codes,                // coupon code
			'cu'  => $order_currency,              // order currency
		];

		$c = 0;

		// add the purchased products
		foreach ( $order->get_items() as $item ) {

			$c++;
			$product = wc_get_product( ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'] );

			$product_identifier = wc_google_analytics_pro()->get_integration()->get_product_identifier( $product );
			$category_hierarchy = wc_google_analytics_pro()->get_integration()->get_category_hierarchy( $product );
			$product_variant    = wc_google_analytics_pro()->get_integration()->get_product_variation_attributes( $product );

			$params["pr{$c}id"] = $product_identifier;             // product ID
			$params["pr{$c}nm"] = $item['name'];                   // product name
			$params["pr{$c}ca"] = $category_hierarchy;             // product category
			$params["pr{$c}br"] = '';                              // product brand
			$params["pr{$c}pr"] = $order->get_item_total( $item ); // product price
			$params["pr{$c}qt"] = $item['qty'];                    // product quantity
			$params["pr{$c}va"] = $product_variant;                // product variant
		}

		/**
		 * Filters the enhanced ecommerce completed purchase event parameters.
		 *
		 * @since 1.1.1
		 *
		 * @param array $parameters an associative array of enhanced ecommerce completed purchase event parameters
		 * @param \WC_Order $order the order
		 */
		$this->add_parameters( apply_filters( 'wc_google_analytics_pro_api_ec_purchase_parameters', $params, $order ) );
	}


	/**
	 * Adds parameters to track the enhanced ecommerce checkout action.
	 *
	 * @since 1.3.0
	 * @param \WC_Order $order the order object
	 * @param int|string $step (optional) checkout step, empty string indicates no checkout step
	 * @param string $option (optional) checkout option
	 */
	public function track_ec_checkout( $order, $step = '', $option = '' ) {

		// Set general data about the purchase
		$params = array(
			'pa'  => 'checkout',                                                // product action
			'cos' => $step,                                                     // checkout step
			'col' => $option,                                                   // checkout option
		);

		$c = 0;

		// add the purchased products
		foreach ( $order->get_items() as $item ) {

			$c++;
			$product = wc_get_product( ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'] );

			$product_identifier = wc_google_analytics_pro()->get_integration()->get_product_identifier( $product );
			$category_hierarchy = wc_google_analytics_pro()->get_integration()->get_category_hierarchy( $product );
			$product_variant    = wc_google_analytics_pro()->get_integration()->get_product_variation_attributes( $product );

			$params["pr{$c}id"] = $product_identifier;                          // product ID
			$params["pr{$c}nm"] = $item['name'];                                // product name
			$params["pr{$c}ca"] = $category_hierarchy;                          // product category
			$params["pr{$c}br"] = '';                                           // product brand
			$params["pr{$c}pr"] = $order->get_item_total( $item );              // product price
			$params["pr{$c}qt"] = $item['qty'];                                 // product quantity
			$params["pr{$c}va"] = $product_variant;                             // product variant
		}

		/**
		 * Filters the enhanced ecommerce checkout action parameters.
		 *
		 * @since 1.3.0
		 * @param array $parameters an associative array of enhanced ecommerce checkout action parameters
		 * @param \WC_Order $order the order
		 */
		$this->add_parameters( apply_filters( 'wc_google_analytics_pro_api_ec_checkout_parameters', $params, $order ) );
	}


	/**
	 * Adds parameters to track the enhanced ecommerce checkout_option action.
	 *
	 * @since 1.3.0
	 * @param int|string $step (optional) checkout step, empty string indicates no checkout step
	 * @param string $option (optional) checkout option
	 */
	public function track_ec_checkout_option( $step = '', $option = '' ) {

		// Set general data about the purchase
		$params = array(
			'pa'  => 'checkout_option',                                         // product action
			'cos' => $step,                                                     // checkout step
			'col' => $option,                                                   // checkout option
		);

		/**
		 * Filters the enhanced ecommerce checkout_option action parameters.
		 *
		 * @since 1.3.0
		 * @param array $parameters an associative array of enhanced ecommerce checkout_option action parameters
		 */
		$this->add_parameters( apply_filters( 'wc_google_analytics_pro_api_ec_checkout_option_parameters', $params ) );
	}


	/**
	 * Adds parameters to track the enhanced ecommerce order refund event.
	 *
	 * @since 1.0.0
	 * @param \WC_Order $order the order object
	 * @param array $items Optional. The refunded items. If not provided, a full refund is tracked.
	 */
	public function track_ec_refund( $order, $items = array() ) {

		$params = array(
			'ni'  => '1',                                                       // non-interaction parameter
			'ti'  => $order->get_order_number(),                                // transaction ID, required
			'pa'  => 'refund',                                                  // product action, required
		);

		// if this is a partial refund, indicate which products were refunded
		if ( ! empty( $items ) ) {

			$c = 0;

			foreach ( $items as $item_id => $item ) {

				$c++;
				/** @var WC_Product_Variable $product because we use get_variation_attributes */
				$product = wc_get_product( ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'] );

				$product_identifier = wc_google_analytics_pro()->get_integration()->get_product_identifier( $product );

				$params["pr{$c}id"] = $product_identifier;                      // product ID
				$params["pr{$c}qt"] = abs( $item['qty'] );                      // product quantity

				$c++;
			}
		}

		/**
		 * Filters the enhanced ecommerce order refund event parameters
		 *
		 * @since 1.1.1
		 * @param array $parameters An associative array of enhanced ecommerce order refund event parameters
		 * @param \WC_Order $order The order
		 * @param array $items Refunded items
		 */
		$this->add_parameters( apply_filters( 'wc_google_analytics_pro_api_ec_refund_parameters', $params, $order, $items ) );
	}


	/** Helper Methods ******************************************************/


	/**
	 * Adds a parameter.
	 *
	 * @since 1.0.0
	 * @param string $key
	 * @param string|int $value
	 */
	private function add_parameter( $key, $value ) {

		$this->parameters[ $key ] = $value;
	}


	/**
	 * Adds multiple parameters.
	 *
	 * @since 1.0.0
	 * @param array $params
	 */
	private function add_parameters( array $params ) {

		foreach ( $params as $key => $value ) {
			$this->add_parameter( $key, $value );
		}
	}


	/**
	 * Gets the string representation of this request.
	 *
	 * @see Framework\SV_WC_API_Request::to_string()
	 *
	 * @since 1.0.0
	 * @return string the request query string
	 */
	public function to_string() {

		return 'payload_data&' . http_build_query( $this->get_parameters(), '', '&' );
	}


	/**
	 * Gets the string representation of this request with any and all sensitive
	 * elements masked or removed.
	 *
	 * @see Framework\SV_WC_API_Request::to_string_safe()
	 *
	 * @since 1.0.0
	 * @return string the request string representation, safe for logging
	 */
	public function to_string_safe() {

		$request = $this->get_parameters();

		$sensitive_fields = array( 'USER', 'PWD', 'SIGNATURE' );

		foreach ( $sensitive_fields as $field ) {

			if ( isset( $request[ $field ] ) ) {

				$request[ $field ] = str_repeat( '*', strlen( $request[ $field ] ) );
			}
		}

		return print_r( $request, true );
	}


	/**
	 * Gets the request parameters.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_parameters() {

		// validate parameters
		foreach ( $this->parameters as $key => $value ) {

			// remove unused params
			if ( null === $value || '' === $value ) {
				unset( $this->parameters[ $key ] );
			}
		}

		return $this->parameters;
	}


	/**
	 * Gets the method for this request.
	 *
	 * @since 1.0.0
	 * @return string|null the request method, one of HEAD, GET, PUT, PATCH, POST, DELETE
	 */
	public function get_method() {

		return null;
	}


	/**
	 * Gets the request path.
	 *
	 * @since 1.0.0
	 * @return string the request path, or '' if none
	 */
	public function get_path() {

		return '';
	}


}
