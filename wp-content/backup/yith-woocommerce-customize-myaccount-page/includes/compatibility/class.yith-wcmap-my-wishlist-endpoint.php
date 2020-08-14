<?php
/**
 * YITH WooCommerce Wishlist Compatibility Class
 *
 * @author  YITH
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */


if ( ! defined( 'YITH_WCMAP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMAP_My_Wishlist_Endpoint' ) ) {
	/**
	 * Class YITH_WCMAP_My_Wishlist_Endpoint
	 *
	 * @since 2.3.0
	 */
	class YITH_WCMAP_My_Wishlist_Endpoint {

		/**
		 * The endpoint key
		 * @var string
		 */
		public $endpoint_key = '';

		/**
		 * The endpoint
		 * @var array
		 */
		public $endpoint = [];

		/**
		 * Constructor
		 *
		 * @since 2.3.0
		 */
		public function __construct() {
			$this->endpoint_key = 'my-wishlist';
			$this->endpoint     = yith_wcmap_get_endpoint_by( $this->endpoint_key, 'key' );
			empty( $this->endpoint ) || $this->endpoint = array_shift( $this->endpoint );

			if ( ! empty( $this->endpoint ) ) {

				add_filter( 'yith_wcwl_wishlist_page_url', array( $this, 'change_wishlist_base_url' ), 10, 2 );

				if ( YITH_WCMAP()->frontend->is_my_account() ) {
					add_filter( 'yith_wcwl_current_wishlist_view_params', array( $this, 'change_wishlist_view_params' ), 10, 1 );
					add_action( 'yith_wcwl_after_wishlist', array( $this, 'add_redirect_to_hidden' ) );
					add_action( 'yith_wcwl_after_wishlist_manage', array( $this, 'add_redirect_to_hidden' ) );
					add_action( 'yith_wcwl_after_wishlist_create', array( $this, 'add_redirect_to_hidden' ) );
					add_action( 'yith_wcwl_wishlist_delete_url', array( $this, 'add_redirect_to_param' ) );
				}
			}
		}

		/**
		 * Change view params for wishlist shortcode
		 *
		 * @since  1.0.6
		 * @author Francesco Licandro
		 * @param $params
		 * @return mixed
		 */
		public function change_wishlist_view_params( $params ) {
			$params = apply_filters( 'yith_wcmap_change_wishlist_view_params', get_query_var( $this->endpoint['slug'], false ), $params );
			return $params;
		}

		/**
		 * Get wishlist endpoint url
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @return string
		 */
		protected function get_endpoint_url() {
			$url    = wc_get_endpoint_url( $this->endpoint['slug'] );
			$url    = trailingslashit( $url );
			$params = get_query_var( $this->endpoint['slug'], false );

			$params && $url .= trailingslashit( $params );

			return esc_url_raw( $url );
		}

		/**
		 * Change wishlist base url
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @param string $url
		 * @param string $action
		 * @return string
		 */
		public function change_wishlist_base_url( $url, $action ) {
			if ( YITH_WCMAP()->frontend->is_my_account() || apply_filters( 'yith_wcmap_filter_wishlist_url', true ) ) {
				$my_account = wc_get_page_id( 'myaccount' );
				$url        = wc_get_endpoint_url( $this->endpoint['slug'], $action, get_permalink( $my_account ) );
			}

			return $url;
		}

		/**
		 * Add hidden for redirect to value
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @return void
		 */
		public function add_redirect_to_hidden() {
			$url = $this->get_endpoint_url();
			echo '<input type="hidden" name="redirect_to" value="'. esc_attr( $url ) . '">';
		}

		/**
		 * Add redirect to param to an url
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @param string $url
		 * @return string
		 */
		public function add_redirect_to_param( $url ) {
			$redirect_to = $this->get_endpoint_url();
			return add_query_arg( 'redirect_to', $redirect_to, $url );
		}
	}
}

new YITH_WCMAP_My_Wishlist_Endpoint();