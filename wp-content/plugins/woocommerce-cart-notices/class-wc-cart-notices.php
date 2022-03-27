<?php
/**
 * WooCommerce Cart Notices
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Cart Notices to newer
 * versions in the future. If you wish to customize WooCommerce Cart Notices for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-cart-notices/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * WooCommerce Cart Notices main class.
 *
 * @since 1.0.0
 */
class WC_Cart_Notices extends Framework\SV_WC_Plugin {


	const VERSION = '1.13.3';

	/** @var WC_Cart_Notices single instance of this plugin */
	protected static $instance;

	/** string the plugin id */
	const PLUGIN_ID = 'cart_notices';

	/** @var string The plugin's id, used for various slugs and such */
	public $id = 'wc-cart-notices';

	/** @var array notices objects @see WC_Cart_Notices::get_notices() */
	private $notices = array();

	/** @var \WC_Cart_Notices_Admin the admin class */
	protected $admin;


	/**
	 * Initialize the main plugin class
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			array(
				'text_domain'        => 'woocommerce-cart-notices',
				'display_php_notice' => true,
			)
		);

		// store the client's referer, if needed
		add_action( 'woocommerce_init', array( $this, 'store_referer' ) );

		// add the notices to the top of the cart/checkout pages
		add_action( 'woocommerce_before_cart_contents', array( $this, 'add_cart_notice' ) );
		add_action( 'woocommerce_before_checkout_form', array( $this, 'add_cart_notice' ) );

		// add the notices shortcodes
		add_shortcode( 'woocommerce_cart_notice', array( $this, 'woocommerce_cart_notices_shortcode' ) );

		// allow shortcodes within notice text
		add_filter( 'woocommerce_cart_notice_minimum_amount_notice', 'do_shortcode' );
		add_filter( 'woocommerce_cart_notice_deadline_notice',       'do_shortcode' );
		add_filter( 'woocommerce_cart_notice_referer_notice',        'do_shortcode' );
		add_filter( 'woocommerce_cart_notice_products_notice',       'do_shortcode' );
		add_filter( 'woocommerce_cart_notice_categories_notice',     'do_shortcode' );

		// ajax search categories handler
		add_action( 'wp_ajax_wc_cart_notices_json_search_product_categories', array( $this, 'woocommerce_json_search_product_categories' ) );
	}


	/**
	 * Initializes the plugin.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 */
	public function init_plugin() {

		$this->includes();
	}


	/**
	 * Includes required files.
	 *
	 * @since 1.0.7
	 */
	private function includes() {

		if ( is_admin() && ! wp_doing_ajax() ) {
			$this->admin_includes();
		}
	}


	/**
	 * Include required admin files.
	 *
	 * @since 1.0.7
	 */
	private function admin_includes() {

		// load admin
		$this->admin = $this->load_class( '/src/admin/class-wc-cart-notices-admin.php', 'WC_Cart_Notices_Admin' );

		// add message handler
		$this->admin->message_handler = $this->get_message_handler();
	}


	/**
	 * Builds the lifecycle handler instance.
	 *
	 * @since 1.9.0
	 */
	protected function init_lifecycle_handler() {

		require_once( $this->get_plugin_path() . '/src/Lifecycle.php' );

		$this->lifecycle_handler = new SkyVerge\WooCommerce\Cart_Notices\Lifecycle( $this );
	}


	/**
	 * Invoked after woocommerce has finished loading,
	 * so we know sessions have been started.
	 */
	public function store_referer() {

		// If the referer notice is enabled...
		if ( isset( $_SERVER['HTTP_REFERER'] ) && $this->has_referer_notice() ) {

			$referer_host = parse_url( $_SERVER['HTTP_REFERER'], PHP_URL_HOST );

			// ...and the referer host does not match the site...
			if ( $referer_host && $referer_host !== parse_url( site_url(), PHP_URL_HOST ) ) {

				// ...record it in the session
				WC()->session->wc_cart_notice_referer = $referer_host;
			}
		}
	}


	/** Frontend ************************************************************/


	/**
	 * Adds any available cart notices.
	 *
	 * @internal
	 *
	 * @since 1.0
	 */
	public function add_cart_notice() {

		$messages = [];

		foreach ( $this->get_notices() as $notice ) {

			$args = [];

			if ( 'minimum_amount' === $notice->type ) {
				$args['cart_contents_total']        = $this->get_cart_total();
				$args['cart_free_shipping_minimum'] = $this->get_cart_free_shipping_minimum();
			}

			if ( $notice->enabled && method_exists( $this, 'get_' . $notice->type . '_notice' ) && ( $message = $this->{ 'get_' . $notice->type . '_notice' }( $notice, $args ) ) ) {
				$messages[] = $message;
			}
		}

		echo implode( "\n", $messages );
	}


	/** Shortcode ************************************************************/


	/**
	 * Handles the WooCommerce Cart Notices shortcode.
	 *
	 * @internal
	 *
	 * @since 1.0
	 *
	 * @param $atts array associative array of shortcode parameters
	 * @return string shortcode content
	 */
	public function woocommerce_cart_notices_shortcode( $atts ) {

		$a = shortcode_atts( array(
			'type' => '',
			'name' => '',
		), $atts );

		$type = $a['type'];
		$name = $a['name'];

		if ( ! $type && ! $name ) {
			$type = 'all';
		}

		$messages = array();

		foreach ( $this->get_notices() as $notice ) {

			/**
			 * Fires before processing a notice.
			 *
			 * @since 1.1
			 *
			 * @param \stdClass $notice the notice
			 */
			do_action( 'wc_cart_notices_process_notice_before', $notice );

			if ( 'all' === $type || $type === $notice->type || 0 === strcasecmp( $name, $notice->name ) ) {

				$args = [];

				if ( 'minimum_amount' === $notice->type ) {
					$args['cart_contents_total']        = $this->get_cart_total();
					$args['cart_free_shipping_minimum'] = $this->get_cart_free_shipping_minimum();
				}

				if ( $notice->enabled && method_exists( $this, 'get_' . $notice->type . '_notice' ) && ( $message = $this->{'get_' . $notice->type . '_notice'}( $notice, $args ) ) ) {
					$messages[] = $message;
				}
			}
		}

		return implode( "\n", $messages );
	}


	/** Helper methods ******************************************************/


	/**
	 * Returns the main Cart Notices instance.
	 *
	 * Ensures only one instance is/can be loaded.
	 *
	 * @since 1.3.0
	 *
	 * @return \WC_Cart_Notices
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Gets the Admin instance.
	 *
	 * @since 1.6.0
	 *
	 * @return \WC_Cart_Notices_Admin
	 */
	public function get_admin_instance() {

		return $this->admin;
	}


	/**
	 * Gets the cart contents total (after calculation).
	 *
	 * @since 1.0
	 *
	 * @return int|float price amount
	 */
	private function get_cart_total() {

		if ( 'incl' !== get_option( 'woocommerce_tax_display_cart' ) ) {
			// if prices don't include tax, just return the subtotal excluding tax
			$cart_contents_total = $this->is_cart_available( [ 'cart_contents_total' ] ) ? WC()->cart->cart_contents_total : 0;
		} else {
			// if prices do include tax, add the tax amount back in
			$cart_contents_total = $this->is_cart_available( [ 'cart_contents_total', 'tax_total' ] ) ? WC()->cart->cart_contents_total + WC()->cart->tax_total : 0;
		}

		/**
		 * Filters the cart total used to determine if the minimum amount notice should appear.
		 *
		 * @since 1.8.3
		 *
		 * @param float $cart_contents_total the cart total
		 */
		return apply_filters( 'wc_cart_notices_get_cart_total', $cart_contents_total );
	}


	/**
	 * Gets the free shipping minimum for the current cart and available zone.
	 *
	 * @since 1.6.1
	 *
	 * @return int|bool $minimum_amount free shipping minimum based on available zones
	 */
	private function get_cart_free_shipping_minimum() {

		$minimum = false;

		// we only need this check if zones are available and the cart is shipped
		// as a configured notice amount would have been check already when this is called
		if ( ! $this->is_cart_available() || ! WC()->cart->needs_shipping() ) {
			return $minimum;
		}

		$packages      = WC()->cart->get_shipping_packages();
		$free_minimums = array();

		// check all packages for available methods on each
		foreach ( $packages as $i => $package ) {

			if ( empty( $package['contents'] ) ) {
				continue;
			}

			// hold the lowest free shipping minimum for this package;
			$free_minimums[ $i ] = '';

			$shipping = WC()->shipping()->load_shipping_methods( $package );

			// loop all package methods to get the min amount for any free shipping rate
			// there could be more than one free shipping rate available
			foreach ( $shipping as $method ) {

				// ensure we're looking at a free shipping rate assigned to a zone (instance ID can't be 0)
				if ( 'yes' === $method->enabled && 'free_shipping' === $method->id && $method->instance_id > 0 ) {

					// sanity check -- ensure that the min amount actually is in effect
					if ( ! in_array( $method->requires, array( 'min_amount', 'either', 'both' ) ) ) {
						continue;
					}

					// set the value for our first loop
					if ( empty( $free_minimums[ $i ] ) ) {
						$free_minimums[ $i ] = $method->min_amount;
					}

					// if we've already pushed a value, only push our new minimum if it's lower
					elseif ( $method->min_amount < $free_minimums[ $i ] ) {
						$free_minimums[ $i ] = $method->min_amount;
					}

				}
			}
		}

		// now we have the lowest min_amount for each package, pull the absolute lowest for the notice
		$minimum = ! empty( $free_minimums ) ? (float) min( $free_minimums ) : false;

		return $minimum;
	}


	/**
	 * Returns the minimum amount cart notice HTML snippet
	 *
	 * @since 1.0
	 *
	 * TODO: Perhaps I should be checking the cart total excluding taxes.  Though, is this impossible if prices include taxes?
	 *
	 * @param stdClass $notice the notice settings object
	 * @param array $args associative array of parameters, 'cart_contents_total' is required
	 * @return string minimum amount cart notice
	 */
	public function get_minimum_amount_notice( $notice, $args ) {

		// get the target amount
		$minimum_order_amount = $this->get_minimum_order_amount( $notice );

		// if we don't have a configured amount from settings, get one from the available zones
		if ( ! $minimum_order_amount ) {
			$minimum_order_amount = isset( $args['cart_free_shipping_minimum'] ) ? $args['cart_free_shipping_minimum'] : false;
		}

		$threshold_order_amount   = isset( $notice->data['threshold_order_amount'] ) ? $notice->data['threshold_order_amount'] : null;

		$order_thresholds = array(
			'minimum_order_amount'   => $minimum_order_amount,
			'threshold_order_amount' => $threshold_order_amount,
		);

		/**
		 * Filters the notice order thresholds.
		 *
		 * @since 1.1
		 *
		 * @param array $order_thresholds notice thresholds
		 * @param \stdClass $notice the notice object
		 * @param array $args notice arguments
		 */
		$order_thresholds = apply_filters( 'wc_cart_notices_order_thresholds', $order_thresholds, $notice, $args );

		$minimum_order_amount   = $order_thresholds['minimum_order_amount'];
		$threshold_order_amount = $order_thresholds['threshold_order_amount'];

		// no configured target amount, none from legacy settings, none from the cart zones
		if ( ! $minimum_order_amount ) {
			return false;
		}

		// misconfigured?
		if ( ! $notice->message ) {
			return false;
		}

		// they already meet the target amount
		if ( is_numeric( $minimum_order_amount ) && $args['cart_contents_total'] >= $minimum_order_amount ) {
			return false;
		}

		// if they're below the thereshold order amount, bail with no notice
		if ( is_numeric( $threshold_order_amount ) && $args['cart_contents_total'] < $threshold_order_amount ) {
			return false;
		}

		$message = $this->get_notice_message( $notice );

		// get the minimum amount notice message, with the amount required, if needed
		$amount_under = wc_price( $minimum_order_amount - $args['cart_contents_total'] );
		if ( false !== strpos( $message, '{amount_under}' ) ) {
			$message = str_replace( '{amount_under}', $amount_under,  $message );
		}

		// add the call to action button/text if used
		$action = '';
		if ( $notice->action && $notice->action_url ) {
			$action = ' <a class="button" href="' . esc_url( $notice->action_url ) . '">' . esc_html__( $notice->action, 'woocommerce-cart-notices' ) . '</a>';
		}

		// add the message variables for the benefit of the filter
		$args['amount_under'] = $amount_under;

		/**
		 * Filters the minimum cart notice.
		 *
		 * @since 1.0.6
		 *
		 * @param string $notice_html the notice content
		 * @param \stdClass $notice the notice object
		 * @param array $args notice arguments
		 */
		return apply_filters( 'woocommerce_cart_notice_minimum_amount_notice', '<div id="woocommerce-cart-notice-' . sanitize_title( $notice->name ) . '" class="woocommerce-cart-notice woocommerce-cart-notice-minimum-amount woocommerce-info">' . wp_kses_post( $message ) . $action . '</div>', $notice, $args );
	}


	/**
	 * Returns the deadline notice based on the current time and configuration.
	 *
	 * @since 1.0
	 *
	 * @param stdClass $notice the notice settings object
	 * @return string deadline notice snippet, or false if there is no deadline notice at this time
	 */
	public function get_deadline_notice( $notice ) {

		// misconfigured?
		if ( ! $notice->message || ( false !== strpos( $notice->message, '{time}' ) && ! $notice->data['deadline_hour'] ) ) {
			return false;
		}

		$current_time = current_time( 'timestamp' );

		// enabled for today?
		$day_of_week = date( 'w', $current_time );

		if ( ! isset( $notice->data['deadline_days'][ $day_of_week ] ) || ! $notice->data['deadline_days'][ $day_of_week ] ) {
			return false;
		}

		$message = $this->get_notice_message( $notice );

		// get the deadline notice message, with the time remaining
		$minutes_of_day = (int) date( 'G', $current_time ) * 60 + (int) date( 'i', $current_time );
		$deadline_minutes = $notice->data['deadline_hour'] * 60;

		// already past the deadline?
		if ( $minutes_of_day > $deadline_minutes ) {
			return false;
		}

		$minutes_remaining = $deadline_minutes - $minutes_of_day;
		$hours = floor( $minutes_remaining / 60 );
		$minutes = $minutes_remaining % 60;

		// format the string
		$deadline_amount = '';

		if ( $hours ) {
			$deadline_amount .= sprintf( _n( '%d hour', '%d hours', $hours, 'woocommerce-cart-notices' ), $hours );
		}
		if ( $minutes ) {
			$deadline_amount .= ( $deadline_amount ? ' ' : '' ) . sprintf( _n( '%d minute', '%d minutes', $minutes, 'woocommerce-cart-notices' ), $minutes );
		}

		// add the time remaining, if required
		if ( false !== strpos( $message, '{time}' ) ) {
			$message = str_replace( '{time}', $deadline_amount,  $message );
		}

		// add the call to action button/text if used
		$action = '';
		if ( $notice->action && $notice->action_url ) {
			$action = ' <a class="button" href="' . esc_url( $notice->action_url ) . '">' . esc_html__( $notice->action, 'woocommerce-cart-notices' ) . '</a>';
		}

		// add the message variables for the benefit of the filter
		$args['time']              = $deadline_amount;   // the formatted string
		$args['minutes_remaining'] = $minutes_remaining; // the number of minutes, for more advanced usage

		/**
		 * Filters the deadline notice.
		 *
		 * @since 1.0.6
		 *
		 * @param string $notice_html the notice content
		 * @param \stdClass $notice the notice object
		 * @param array $args notice arguments
		 */
		return apply_filters( 'woocommerce_cart_notice_deadline_notice', '<div id="woocommerce-cart-notice-' . sanitize_title( $notice->name ) . '" class="woocommerce-cart-notice woocommerce-cart-notice-deadline woocommerce-info">' . wp_kses_post( $message ) . $action . '</div>', $notice, $args );
	}


	/**
	 * Returns the referer cart notice HTML snippet
	 *
	 * @since 1.0
	 *
	 * @param stdClass $notice the notice settings object
	 * @return string|bool referer cart notice or false on error
	 */
	public function get_referer_notice( $notice ) {

		// get the referer
		if ( ! isset( WC()->session->wc_cart_notice_referer ) || ! WC()->session->wc_cart_notice_referer ) {
			return false;
		}

		$client_referer_host = WC()->session->wc_cart_notice_referer;

		// misconfigured?
		if ( ! $notice->message || ! $notice->data['referer'] ) {
			return false;
		}

		$referer      = strpos( $notice->data['referer'], '://' ) === false ? 'http://' . $notice->data['referer'] : $notice->data['referer'];
		$referer_host = parse_url( $referer, PHP_URL_HOST );

		// referer matches?
		if ( $client_referer_host !== $referer_host ) {
			return false;
		}

		$message = $this->get_notice_message( $notice );

		// add the call to action button/text if used
		$action = '';
		if ( $notice->action && $notice->action_url ) {
			$action = ' <a class="button" href="' . esc_url( $notice->action_url ) . '">' . esc_html__( $notice->action, 'woocommerce-cart-notices' ) . '</a>';
		}

		/**
		 * Filters the referer notice.
		 *
		 * @since 1.0.6
		 *
		 * @param string $notice_html the notice content
		 * @param \stdClass $notice the notice object
		 */
		return apply_filters( 'woocommerce_cart_notice_referer_notice', '<div id="woocommerce-cart-notice-' . sanitize_title( $notice->name ) . '" class="woocommerce-cart-notice woocommerce-cart-notice-referer woocommerce-info">' . wp_kses_post( $message ) . $action . '</div>', $notice );
	}


	/**
	 * Returns the products cart notice HTML snippet.
	 *
	 * @since 1.0
	 *
	 * @param stdClass $notice the notice settings object
	 * @return string products cart notice
	 */
	public function get_products_notice( $notice ) {

		// anything in the cart?
		if ( ! $this->is_cart_available( [ 'cart_contents' ] ) || empty( WC()->cart->cart_contents ) ) {
			return false;
		}

		/**
		 * Filters whether the products notice should apply to all products.
		 *
		 * @since 1.2.1
		 *
		 * @param bool $all_products default false
		 * @param \stdClass $notice the notice object
		 */
		$all_products = (bool) apply_filters( 'wc_cart_notices_products_notice_all_products', false, $notice );

		// mis-configured?
		if ( ! $notice->message || ( empty( $notice->data['product_ids'] ) && ! $all_products ) ) {
			return false;
		}

		// are any of the selected products in the cart?
		$found_product_titles = array();
		$the_products         = array();
		$product_quantity     = 0;

		foreach ( WC()->cart->cart_contents as $cart_item ) {

			// check by main product id as well as variation id (if available).  That way
			//  a message can be set for a whole set of variable products, or for one individually
			$_product_id    = $cart_item['product_id'];
			$_variation_id  = isset( $cart_item['variation_id'] ) && $cart_item['variation_id'] ? $cart_item['variation_id'] : null;

			// if we've found a product that should hide the notice, we can completely bail
			if ( is_array( $notice->data['hide_product_ids'] ) ) {

				if ( in_array( $_product_id, $notice->data['hide_product_ids'], false ) || ( $_variation_id && in_array( $_variation_id, $notice->data['hide_product_ids'] ) ) ) {
					return false;
				}
			}


			if ( $all_products || in_array( $_product_id, $notice->data['product_ids'], false ) || ( $_variation_id && in_array( $_variation_id, $notice->data['product_ids'] ) ) ) {
				$found_product_titles[ $_product_id ]  = $cart_item['data']->get_title();
				$the_products[]                        = $cart_item['data'];
				$product_quantity                     += $cart_item['quantity'];
			}
		}

		if ( empty( $found_product_titles ) ) {
			return false;
		}

		// any minimum/maximum quantity rules?
		$quantity_met = true;

		if ( isset( $notice->data['minimum_quantity'] ) && is_numeric( $notice->data['minimum_quantity'] ) && $product_quantity < $notice->data['minimum_quantity'] ) {
			$quantity_met = false;
		}
		if ( isset( $notice->data['maximum_quantity'] ) && is_numeric( $notice->data['maximum_quantity'] ) && $product_quantity > $notice->data['maximum_quantity'] ) {
			$quantity_met = false;
		}
		if ( ! $quantity_met ) {
			return false;
		}

		$shipping_country_code = '';
		$shipping_country_name = '';

		if (    isset( $notice->data['shipping_countries'] ) && $notice->data['shipping_countries']
			&& isset( WC()->customer ) && WC()->customer && WC()->customer->get_shipping_country() ) {

			if ( ! in_array( WC()->customer->get_shipping_country(), $notice->data['shipping_countries'], false ) ) {

				return false;

			} else {

				// grab the matching country code/name
				$shipping_country_code = WC()->customer->get_shipping_country();
				$shipping_country_name = isset( WC()->countries->countries[ WC()->customer->get_shipping_country() ] ) ? WC()->countries->countries[ WC()->customer->get_shipping_country() ] : $shipping_country_code;
			}
		}

		$message = $this->get_notice_message( $notice );

		// get the products notice message, with the list of products, if needed
		$products = implode( ', ', $found_product_titles );
		if ( false !== strpos( $message, '{products}' ) ) {
			$message = str_replace( '{products}', $products,  $message );
		}
		if ( false !== strpos( $message, '{shipping_country_code}' ) ) {
			$message = str_replace( '{shipping_country_code}', $shipping_country_code,  $message );
		}
		if ( false !== strpos( $message, '{shipping_country_name}' ) ) {
			$message = str_replace( '{shipping_country_name}', $shipping_country_name,  $message );
		}
		if ( false !== strpos( $message, '{quantity}' ) ) {
			$message = str_replace( '{quantity}', $product_quantity,  $message );
		}
		if ( false !== strpos( $message, '{quantity_under}' ) ) {
			$quantity_under = isset( $notice->data['maximum_quantity'] ) && '' !== $notice->data['maximum_quantity'] ? $notice->data['maximum_quantity'] - $product_quantity + 1 : '';
			if ( $quantity_under < 0 ) {
				$quantity_under = '';
			}
			$message = str_replace( '{quantity_under}', $quantity_under,  $message );
		}
		if ( false !== strpos( $message, '{quantity_over}' ) ) {
			$quantity_over = isset( $notice->data['minimum_quantity'] ) && '' !== $notice->data['minimum_quantity'] ? $product_quantity - $notice->data['minimum_quantity'] + 1 : '';
			if ( $quantity_over < 0 ) {
				$quantity_over = '';
			}
			$message = str_replace( '{quantity_over}', $quantity_over,  $message );
		}

		// add the call to action button/text if used
		$action = '';
		if ( $notice->action && $notice->action_url ) {
			$action = ' <a class="button" href="' . esc_url( $notice->action_url ) . '">' . esc_html__( $notice->action, 'woocommerce-cart-notices' ) . '</a>';
		}

		// add the message variables for the benefit of the filter
		$args['products']     = $products;     // the formatted string
		$args['the_products'] = $the_products; // the product objects, for more advanced usage
		$args['shipping_country_code'] = $shipping_country_code;
		$args['shipping_country_name'] = $shipping_country_name;

		/**
		 * Filters the products notice.
		 *
		 * @since 1.0.6
		 *
		 * @param string $notice_html the notice content
		 * @param \stdClass $notice the notice object
		 * @param array $args notice args
		 */
		return apply_filters( 'woocommerce_cart_notice_products_notice', '<div id="woocommerce-cart-notice-' . sanitize_title( $notice->name ) . '" class="woocommerce-cart-notice woocommerce-cart-notice-products woocommerce-info">' . wp_kses_post( $message ) . $action . '</div>', $notice, $args );
	}


	/**
	 * Returns the categories cart notice HTML snippet.
	 *
	 * @since 1.0
	 *
	 * @param stdClass $notice the notice settings object
	 * @return string categories cart notice
	 */
	public function get_categories_notice( $notice ) {

		// anything in the cart?
		if ( ! $this->is_cart_available( [ 'cart_contents' ] ) || empty( WC()->cart->cart_contents ) ) {
			return false;
		}

		// misconfigured?
		if ( ! $notice->message || empty( $notice->data['category_ids'] ) ) {
			return false;
		}

		// are any of the selected categories in the cart?
		$found_category_ids = array();
		$product_names      = array();
		$the_products       = array();

		// check all cart items for hide / show categories
		foreach ( WC()->cart->cart_contents as $cart_item ) {

			if ( is_array( $notice->data['hide_category_ids'] ) ) {

				// first, see if we should be hiding the notice and bail
				foreach ( $notice->data['hide_category_ids'] as $hide_id ) {

					if ( has_term( $hide_id, 'product_cat', $cart_item['product_id'] ) ) {
						return false;
					}
				}
			}

			// now, see if we should be showing the notice
			foreach ( $notice->data['category_ids'] as $category_id ) {

				if ( has_term( $category_id, 'product_cat', $cart_item['product_id'] ) ) {

					if ( ! in_array( $category_id, $found_category_ids ) ) {
						$found_category_ids[] = $category_id;
					}

					$product_names[ $cart_item['product_id'] ] = $cart_item['data']->get_title();
					$the_products[]                            = $cart_item['data'];
				}
			}
		}

		if ( empty( $found_category_ids ) ) {
			return false;
		}

		$message = $this->get_notice_message( $notice );

		// get the categories notice message, with the list of products, if needed
		$products = implode( ', ', $product_names );

		if ( false !== strpos( $message, '{products}' ) ) {
			$message = str_replace( '{products}', $products,  $message );
		}

		// get the categories notice message, with the list of categories, if needed
		$category_names = array();
		$the_categories = array();

		foreach ( $found_category_ids as $category_id ) {

			$category = get_term( $category_id, 'product_cat' );
			$category_names[] = $category->name;
			$the_categories[] = $category;

		}

		$category_names = array_unique( $category_names );
		$categories = implode( ', ', $category_names );

		if ( strpos( $message, '{categories}' ) !== false ) {
			$message = str_replace( '{categories}', $categories,  $message );
		}

		// add the call to action button/text if used
		$action = '';

		if ( $notice->action && $notice->action_url ) {
			$action = ' <a class="button" href="' . esc_url( $notice->action_url ) . '">' . esc_html__( $notice->action, 'woocommerce-cart-notices' ) . '</a>';
		}

		// add the message variables for the benefit of the filter
		$args['products']       = $products;       // the formatted string
		$args['the_products']   = $the_products;   // the product objects, for more advanced usage
		$args['categories']     = $categories;     // the formatted string
		$args['the_categories'] = $the_categories; // the category objects, for more advanced usage

		/**
		 * Filters the categories notice.
		 *
		 * @since 1.0.6
		 *
		 * @param string $notice_html the notice content
		 * @param \stdClass $notice the notice object
		 * @param array $args notice arguments
		 */
		return apply_filters( 'woocommerce_cart_notice_categories_notice', '<div id="woocommerce-cart-notice-' . sanitize_title( $notice->name ) . '" class="woocommerce-cart-notice woocommerce-cart-notice-categories woocommerce-info">' . wp_kses_post( $message ) . $action . '</div>', $notice, $args );
	}


	/**
	 * Gets the target amount.
	 *
	 * This is returned from the Cart Notices plugin settings, if set, otherwise it is returned from the Free Shipping gateway if enabled and configured.
	 *
	 * @since 1.0
	 *
	 * @param StdClass $notice the notice settings object
	 * @return false|float target amount configured, for free shipping, or false otherwise
	 */
	public function get_minimum_order_amount( $notice ) {

		// configured target amount?
		if ( $notice->data['minimum_order_amount'] ) {
			return $notice->data['minimum_order_amount'];
		}

		// load the shipping methods if not already available
		if ( 0 === count( $shipping_methods = WC()->shipping()->get_shipping_methods() ) ) {
			$shipping_methods = WC()->shipping()->load_shipping_methods();
		}

		// minimum order amount set for free shipping method?
		foreach ( $shipping_methods as $method ) {

			if ( 'legacy_free_shipping' === $method->id && 'yes' === $method->enabled && isset( $method->min_amount ) && $method->min_amount ) {
				return $method->min_amount;
			}
		}

		// no minimum amount configured, return false
		return false;
	}


	/**
	 * Loads any notices from the database table and into the notices member.
	 *
	 * @since 1.0
	 *
	 * @return array of notice objects
	 */
	public function get_notices() {
		global $wpdb;

		// Avoid database table not found errors when plugin is first installed
		// by checking if the plugin option exists
		if ( empty( $this->notices ) && get_option( $this->get_plugin_version_name() ) ) {

			$wpdb->hide_errors();
			$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cart_notices ORDER BY name ASC" );

			if ( ! empty( $results ) ) {

				foreach ( $results as $key => $result ) {
					$results[ $key ]->data = maybe_unserialize( $results[ $key ]->data );
				}

				$this->notices = $results;
			}
		}

		return $this->notices;
	}


	/**
	 * Returns true if at least one referer notice is enabled.
	 *
	 * @since 1.0
	 *
	 * @return bool
	 */
	private function has_referer_notice() {

		$notices = $this->get_notices();

		if ( ! empty( $notices ) && is_array( $notices ) ) {

			foreach ( $notices as $notice ) {

				if ( 'referer' === $notice->type && $notice->enabled ) {

					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Returns the notice message.
	 *
	 * @since 1.8.6
	 *
	 * @param stdClass $notice the notice settings object
	 * @return string the notice message
	 */
	private function get_notice_message( $notice ) {

		/**
		 * Filters the notice message.
		 *
		 * @since 1.8.6
		 *
		 * @param string $message the notice message
		 * @param string $type the notice type
		 * @param \stdClass $notice the notice settings object
		 */
		return apply_filters( 'wc_cart_notices_notice_message', $notice->message, $notice->type, $notice );
	}


	/**
	 * Checks if the cart instance is available.
	 *
	 * @since 1.12.2
	 *
	 * @param array $cart_props optional cart props to check if set
	 * @return bool
	 */
	private function is_cart_available( array $cart_props = [] ) {

		$is_available = is_callable( 'WC' ) && ! empty( WC()->cart );

		if ( $is_available && ! empty( $cart_props ) ) {

			foreach ( $cart_props as $cart_prop ) {

				if ( ! isset( WC()->cart->$cart_prop ) ) {

					$is_available = false;
					break;
				}
			}
		}

		return $is_available;
	}


	/** Helper methods ******************************************************/


	/**
	 * Returns the plugin name, localized.
	 *
	 * @since 1.2
	 *
	 * @return string the plugin name
	 */
	public function get_plugin_name() {

		return __( 'WooCommerce Cart Notices', 'woocommerce-cart-notices' );
	}


	/**
	 * Returns __FILE__.
	 *
	 * @since 1.2
	 *
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {

		return __FILE__;
	}


	/**
	 * Gets the plugin documentation URL.
	 *
	 * @since 1.4.0
	 *
	 * @return string
	 */
	public function get_documentation_url() {

		return 'https://docs.woocommerce.com/document/woocommerce-cart-notices/';
	}


	/**
	 * Gets the plugin support URL.
	 *
	 * @since 1.4.0
	 *
	 * @return string
	 */
	public function get_support_url() {

		return 'https://woocommerce.com/my-account/marketplace-ticket-form/';
	}


	/**
	 * Gets the sales page URL.
	 *
	 * @since 1.9.0
	 *
	 * @return string
	 */
	public function get_sales_page_url() {

		return 'https://woocommerce.com/products/cart-notices/';
	}


	/**
	 * Gets the plugin configuration URL.
	 *
	 * @since 1.5.0
	 *
	 * @param string $_ unused
	 * @return string plugin settings URL
	 */
	public function get_settings_url( $_ = null ) {

		return admin_url( 'admin.php?page=wc-cart-notices' );
	}


	/**
	 * Ajax function to return product categories matching the search term $x.
	 *
	 * @since 1.0
	 *
	 * @param string $x search string
	 * @return string json encoded array of matching category names, or nothing
	 */
	public function woocommerce_json_search_product_categories( $x = '' ) {

		check_ajax_referer( 'search-product-categories', 'security' );

		$term = (string) urldecode( stripslashes( strip_tags( $_GET['term'] ) ) );

		if ( empty( $term ) ) {
			die();
		}

		$args = array(
			'search'     => $term,
			'hide_empty' => 0,
		);

		$categories = get_terms( 'product_cat', $args );

		$found_categories = array();

		if ( $categories ) {

			foreach ( $categories as $category ) {

				$found_categories[ $category->term_id ] = $category->name;
			}
		}

		echo json_encode( $found_categories );

		exit();
	}


}


/**
 * Returns the One True Instance of Cart Notices.
 *
 * @since 1.3.0
 *
 * @return \WC_Cart_Notices
 */
function wc_cart_notices() {

	return \WC_Cart_Notices::instance();
}
