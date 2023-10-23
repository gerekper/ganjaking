<?php
/**
 * YITH WooCommerce Wishlist Compatibility Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 3.0.0
 */

defined( 'YITH_WCMAP' ) || exit;

if ( ! class_exists( 'YITH_WCMAP_Wishlist_Compatibility' ) ) {
	/**
	 * Class YITH_WCMAP_Wishlist_Compatibility
	 *
	 * @since 2.3.0
	 */
	class YITH_WCMAP_Wishlist_Compatibility extends YITH_WCMAP_Compatibility {

		/**
		 * Constructor
		 *
		 * @since 2.3.0
		 */
		public function __construct() {
			$this->endpoint_key = 'my-wishlist';
			$this->endpoint     = array(
				'slug'    => 'my-wishlist',
				'label'   => __( 'My Wishlist', 'yith-woocommerce-customize-myaccount-page' ),
				'icon'    => 'heart',
				'content' => '[yith_wcwl_wishlist]',
			);
			$this->register_endpoint();

			add_filter( 'yith_wcwl_wishlist_page_url', array( $this, 'change_wishlist_base_url' ), 10, 2 );
			add_action( 'template_redirect', array( $this, 'init_compatibility' ) );

			// Banner options.
			add_filter( 'yith_wcmap_banner_counter_type_options', array( $this, 'add_counter_type' ), 10 );
			add_filter( 'yith_wcmap_banner_wishlist_counter_value', array( $this, 'count_products_in_wishlist' ), 10, 2 );
		}

		/**
		 * Init compatibility once we're sure we're in My Account page
		 *
		 * @return void
		 */
		public function init_compatibility() {
			if ( YITH_WCMAP()->frontend->is_my_account() ) {
				add_filter( 'yith_wcwl_current_wishlist_view_params', array( $this, 'change_wishlist_view_params' ), 10, 1 );
				add_action( 'yith_wcwl_after_wishlist', array( $this, 'add_redirect_to_hidden' ) );
				add_action( 'yith_wcwl_after_wishlist_manage', array( $this, 'add_redirect_to_hidden' ) );
				add_action( 'yith_wcwl_after_wishlist_create', array( $this, 'add_redirect_to_hidden' ) );
				add_action( 'yith_wcwl_wishlist_delete_url', array( $this, 'add_redirect_to_param' ) );
			}
		}

		/**
		 * Change view params for wishlist shortcode
		 *
		 * @since  1.0.6
		 * @param string $params Wishlist view param.
		 * @return mixed
		 */
		public function change_wishlist_view_params( $params ) {
			global $wp;

			if ( isset( $wp->query_vars[ $this->endpoint_key ] ) ) {
				/**
				 * APPLY_FILTERS: yith_wcmap_change_wishlist_view_params
				 *
				 * Filters the params to view the wishlist page.
				 *
				 * @param string $query_var_results Query var results.
				 * @param array  $params            Array of parameters.
				 *
				 * @return array
				 */
				$params = apply_filters( 'yith_wcmap_change_wishlist_view_params', $wp->query_vars[ $this->endpoint_key ], $params );
			}

			return $params;
		}

		/**
		 * Get wishlist endpoint url
		 *
		 * @since  1.0.0
		 * @return string
		 */
		protected function get_endpoint_url() {
			$url    = wc_get_endpoint_url( $this->endpoint['slug'] );
			$url    = trailingslashit( $url );
			$params = get_query_var( $this->endpoint['slug'], false );

			if ( $params ) {
				$url .= trailingslashit( $params );
			}

			return esc_url_raw( $url );
		}

		/**
		 * Change wishlist base url
		 *
		 * @since  1.0.0
		 * @param string $url The wishlist base url.
		 * @param string $action The action.
		 * @return string
		 */
		public function change_wishlist_base_url( $url, $action ) {
			/**
			 * APPLY_FILTERS: yith_wcmap_filter_wishlist_url
			 *
			 * Filters whether to change the URL to view the wishlist, to view it on the My Account page.
			 *
			 * @param bool $filter_wishlist_url Whether to change the URL to view the wishlist or not.
			 *
			 * @return bool
			 */
			if ( ( ! is_null( YITH_WCMAP()->frontend ) && YITH_WCMAP()->frontend->is_my_account() ) || apply_filters( 'yith_wcmap_filter_wishlist_url', true ) ) {
				$my_account = wc_get_page_id( 'myaccount' );
				$url        = wc_get_endpoint_url( $this->endpoint['slug'], $action, get_permalink( $my_account ) );
			}

			return $url;
		}

		/**
		 * Add hidden for redirect to value
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function add_redirect_to_hidden() {
			$url = $this->get_endpoint_url();
			echo '<input type="hidden" name="redirect_to" value="' . esc_attr( $url ) . '">';
		}

		/**
		 * Add redirect to param to an url
		 *
		 * @since  1.0.0
		 * @param string $url The redirect url.
		 * @return string
		 */
		public function add_redirect_to_param( $url ) {
			$redirect_to = $this->get_endpoint_url();
			return add_query_arg( 'redirect_to', $redirect_to, $url );
		}

		/**
		 * Add wishlist count option to available counter types
		 *
		 * @since 3.0.0
		 * @param array $options The counter banner options.
		 * @return array
		 */
		public function add_counter_type( $options ) {
			$options['wishlist'] = _x( 'Products in wishlist', 'Banner counter option', 'yith-woocommerce-customize-myaccount-page' );
			return $options;
		}

		/**
		 * Return the number of products in wishlist
		 *
		 * @since 3.0.0
		 * @param integer $value The products in wishlist value.
		 * @param integer $customer_id The customer ID.
		 * @return integer
		 */
		public function count_products_in_wishlist( $value, $customer_id = 0 ) {
			return yith_wcwl_count_all_products();
		}
	}
}
