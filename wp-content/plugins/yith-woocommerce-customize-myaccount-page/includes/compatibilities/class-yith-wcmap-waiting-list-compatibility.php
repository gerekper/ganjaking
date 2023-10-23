<?php
/**
 * YITH WooCommerce Waiting List Compatibility Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

defined( 'YITH_WCMAP' ) || exit;

if ( ! class_exists( 'YITH_WCMAP_Waiting_List_Compatibility' ) ) {
	/**
	 * Class YITH_WCMAP_Waiting_List_Compatibility
	 *
	 * @since 3.0.0
	 */
	class YITH_WCMAP_Waiting_List_Compatibility extends YITH_WCMAP_Compatibility {

		/**
		 * Constructor
		 *
		 * @since 3.0.0
		 */
		public function __construct() {
			$this->endpoint_key = 'waiting-list';
			$this->endpoint     = array(
				'slug'    => get_option( 'woocommerce_myaccount_waiting_list_endpoint', 'waiting-list' ),
				'label'   => __( 'My Waiting List', 'yith-woocommerce-customize-myaccount-page' ),
				'icon'    => 'clock-o',
				'content' => '[ywcwtl_waitlist_table]',
			);

			// Register endpoint.
			$this->register_endpoint();

			// handle compatibility.
			add_action( 'template_redirect', array( $this, 'hooks' ), 5 );

			// Banner options.
			add_filter( 'yith_wcmap_banner_counter_type_options', array( $this, 'add_counter_type' ), 10 );
			add_filter( 'yith_wcmap_banner_waiting_list_counter_value', array( $this, 'count_products_in_waiting_list' ), 10, 2 );
		}

		/**
		 * Compatibility hooks and filter
		 *
		 * @since 3.0.0
		 */
		public function hooks() {
			if ( class_exists( 'YITH_WCWTL_Frontend' ) ) {
				// Remove content in my account.
				remove_action( 'woocommerce_before_my_account', array( YITH_WCWTL_Frontend(), 'add_waitlist_my_account' ) );
			}
		}


		/**
		 * Add waiting list count option to available counter types
		 *
		 * @since 3.0.4
		 * @param array $options The banner counter options.
		 * @return array
		 */
		public function add_counter_type( $options ) {
			$options['waiting_list'] = _x( 'Products in waiting list', 'Banner counter option', 'yith-woocommerce-customize-myaccount-page' );
			return $options;
		}

		/**
		 * Return the number of products in waiting list
		 *
		 * @since 3.0.4
		 * @param integer $value The count value.
		 * @param integer $customer_id The customer ID.
		 * @return integer
		 */
		public function count_products_in_waiting_list( $value, $customer_id = 0 ) {
			$products     = yith_get_user_waitlists( $customer_id );
			$num_products = 0;

			if ( $products && is_array( $products ) ) {
				$num_products = count( $products );
			}

			return $num_products;
		}
	}
}
