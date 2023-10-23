<?php
/**
 * YITH WooCommerce Gift Cards Compatibility Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

defined( 'YITH_WCMAP' ) || exit;

if ( ! class_exists( 'YITH_WCMAP_Gift_Cards_Compatibility' ) ) {
	/**
	 * Class YITH_WCMAP_Gift_Cards_Compatibility
	 *
	 * @since 3.0.0
	 */
	class YITH_WCMAP_Gift_Cards_Compatibility extends YITH_WCMAP_Compatibility {

		/**
		 * Constructor
		 *
		 * @since 3.0.0
		 */
		public function __construct() {
			$this->endpoint_key = 'gift-cards';
			$this->endpoint     = array(
				'slug'    => 'gift-cards',
				'label'   => __( 'Gift Cards', 'yith-woocommerce-customize-myaccount-page' ),
				'icon'    => 'gift',
				'content' => '[yith_wcgc_show_gift_card_list]',
			);

			// Register endpoint.
			$this->register_endpoint();

			// Banner options.
			if ( class_exists( 'YITH_WooCommerce_Gift_Cards_Premium' ) ) {
				add_filter( 'yith_wcmap_banner_counter_type_options', array( $this, 'add_counter_type' ), 10 );
				add_filter( 'yith_wcmap_banner_gift_cards_counter_value', array( $this, 'count_customer_gift_cards' ), 10, 2 );
			}
		}

		/**
		 * Add gift card count option to available counter types
		 *
		 * @since 3.0.0
		 * @param array $options Banner counter options.
		 * @return array
		 */
		public function add_counter_type( $options ) {
			$options['gift-cards'] = _x( 'Gift cards', 'Banner counter option', 'yith-woocommerce-customize-myaccount-page' );

			return $options;
		}

		/**
		 * Return the number of customer gift cards
		 *
		 * @since 3.0.0
		 * @param integer $value Banner counter value.
		 * @param integer $customer_id Customer ID.
		 * @return integer
		 */
		public function count_customer_gift_cards( $value, $customer_id = 0 ) {
			if ( ! $customer_id ) {
				$customer_id = get_current_user_id();
			}

			$gift_card_premium_class = YITH_WooCommerce_Gift_Cards_Premium::get_instance();
			return method_exists( $gift_card_premium_class, 'ywgc_count_user_gift_cards' ) ? $gift_card_premium_class->ywgc_count_user_gift_cards( $customer_id ) : 0;
		}
	}
}
