<?php
/**
 * YITH WooCommerce Request a Quote Compatibility Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 3.0.0
 */

defined( 'YITH_WCMAP' ) || exit;

if ( ! class_exists( 'YITH_WCMAP_Request_Quote_Compatibility' ) ) {
	/**
	 * Class YITH_WCMAP_Request_Quote_Compatibility
	 *
	 * @since 3.0.0
	 */
	class YITH_WCMAP_Request_Quote_Compatibility extends YITH_WCMAP_Compatibility {

		/**
		 * Constructor
		 *
		 * @since 3.0.0
		 */
		public function __construct() {

			if ( 1 === version_compare( '3.0', YITH_YWRAQ_VERSION ) ) {
				$this->endpoint_key = 'view-quote';
				$this->endpoint     = array(
					'slug'    => 'view-quote',
					'label'   => __( 'My Quotes', 'yith-woocommerce-customize-myaccount-page' ),
					'icon'    => 'pencil',
					'content' => '[yith_ywraq_myaccount_quote]',
				);

				$this->register_endpoint();

				add_action( 'template_redirect', array( $this, 'hooks' ), 5 );
			} else { // Otherwise call YITH_Request_Quote_My_Account instance.
				if ( ! class_exists( 'YITH_Request_Quote_My_Account' ) ) {
					require_once YITH_YWRAQ_INC . 'class.yith-request-quote-my-account.php';
				}
				YITH_Request_Quote_My_Account();

				$this->endpoint_key = 'quotes';

				add_filter( 'ywraq_endpoint', array( $this, 'filter_quote_endpoint_slug' ), 10, 1 );
				add_filter( 'yith_wcmap_get_default_endpoint_options', array( $this, 'filter_default_endpoint_values' ), 10, 2 );
				add_filter( 'yith_wcmap_endpoint_menu_class', array( $this, 'quote_menu_item_active' ), 10, 2 );
			}

			add_filter( 'yith_wcmap_account_page_title', array( $this, 'account_page_title' ), 10, 2 );
			// Banner options.
			add_filter( 'yith_wcmap_banner_counter_type_options', array( $this, 'add_counter_type' ), 10 );
			add_filter( 'yith_wcmap_banner_request_a_quote_counter_value', array( $this, 'count_quotes' ), 10, 2 );

			add_filter( 'yith_wcmap_counter_banner_orders', array( $this, 'filter_counter_orders' ) );
		}

		/**
		 * Compatibility hooks and filter
		 *
		 * @since 3.0.0
		 */
		public function hooks() {
			if ( class_exists( 'YITH_YWRAQ_Order_Request' ) ) {
				// Remove content in my account.
				remove_action( 'woocommerce_before_my_account', array( YITH_YWRAQ_Order_Request(), 'my_account_my_quotes' ) );
				remove_action( 'template_redirect', array( YITH_YWRAQ_Order_Request(), 'load_view_quote_page' ) );
			}
		}

		/**
		 * Filter default endpoint value for quote
		 *
		 * @since 3.0.5
		 * @param array  $data The endpoint data.
		 * @param string $endpoint The endpoint.
		 * @return array
		 */
		public function filter_default_endpoint_values( $data, $endpoint ) {
			if ( $this->endpoint_key === $endpoint ) {
				$data = array_merge(
					$data,
					array(
						'icon'             => 'pencil',
						'content_position' => 'before',
					)
				);
			}

			return $data;
		}

		/**
		 * Set main endpoint active when a sub is the current one
		 *
		 * @since 3.0.5
		 * @param array  $classes An array of item classes.
		 * @param string $item The current menu item.
		 * @return mixed
		 */
		public function quote_menu_item_active( $classes, $item ) {
			// Check if endpoint is active.
			$current = yith_wcmap_get_current_endpoint();
			if ( $item === $this->endpoint_key && YITH_Request_Quote()->view_endpoint === $current ) {
				$classes[] = 'active';
			}

			return $classes;
		}

		/**
		 * Filter quote endpoint slug
		 *
		 * @since 3.0.5
		 * @param string $slug Current endpoint slug.
		 * @return string
		 */
		public function filter_quote_endpoint_slug( $slug ) {
			$option = get_option( 'yith_wcmap_endpoint_' . $this->endpoint_key, array() );
			if ( ! empty( $option['slug'] ) ) {
				return $option['slug'];
			}

			return $slug;
		}

		/**
		 * Change my account page title on quote section
		 *
		 * @since  3.0.0
		 * @param string $title Current page title.
		 * @param array  $endpoint The current endpoint.
		 * @return string
		 */
		public function account_page_title( $title, $endpoint ) {

			global $wp;

			// Search for active endpoints.
			$active = yith_wcmap_get_current_endpoint();

			if ( isset( $endpoint[ $this->endpoint_key ] ) && ! empty( $wp->query_vars[ $active ] ) ) {
				// translators: %s stand for the ID of the quote.
				$title = sprintf( __( 'Quote #%s', 'yith-woocommerce-request-a-quote' ), $wp->query_vars[ $active ] );
			}

			return $title;
		}

		/**
		 * Add request a qupte count option to available counter types
		 *
		 * @since 3.0.4
		 * @param array $options Banner counter options.
		 * @return array
		 */
		public function add_counter_type( $options ) {
			$options['request_a_quote'] = _x( 'Quotes', 'Banner counter option', 'yith-woocommerce-customize-myaccount-page' );

			return $options;
		}

		/**
		 * Return the number of quotes associated to the customer
		 *
		 * @since 3.0.4
		 * @param integer $value Current counter value.
		 * @param integer $customer_id The customer ID.
		 * @return integer
		 */
		public function count_quotes( $value, $customer_id = 0 ) {
			/**
			 * APPLY_FILTERS: ywraq_my_account_my_quotes_query
			 *
			 * Filters the array with the arguments to get the user quotes.
			 *
			 * @param array $attrs Array of attributes.
			 *
			 * @return array
			 */
			$quotes = wc_get_orders(
				apply_filters(
					'ywraq_my_account_my_quotes_query',
					array(
						'limit'     => 15,
						'ywraq_raq' => 'yes',
						'customer'  => get_current_user_id(),
						'status'    => array_merge( YITH_YWRAQ_Order_Request()->raq_order_status, array_keys( wc_get_order_statuses() ) ),
					)
				)
			);

			return count( $quotes );
		}


		/**
		 * Filter counter orders to prevent include quotes in orders count
		 *
		 * @param integer $counter Current counter value.
		 * @return integer
		 */
		public function filter_counter_orders( $counter ) {
			$order_statuses = array_keys( wc_get_order_statuses() );
			foreach ( $order_statuses as $key => $status ) {

				if ( in_array( $status, YITH_YWRAQ_Order_Request()->raq_order_status ) ) { // phpcs:ignore
					unset( $order_statuses[ $key ] );
				}
			}
			$orders  = wc_get_orders(
				array(
					'numberposts' => - 1,
					'customer_id' => get_current_user_id(),
					'post_status' => $order_statuses,
					'return'      => 'ids',
				)
			);
			$counter = count( $orders );

			return $counter;
		}
	}
}
