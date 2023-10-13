<?php
/**
 * Popular users products table class
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Classes\Tables
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Users_Popular_Table_Premium' ) ) {
	/**
	 * Admin view class. Create and populate "users that added product to wishlist" table
	 *
	 * @since 2.0.6
	 */
	class YITH_WCWL_Users_Popular_Table_Premium extends YITH_WCWL_Users_Popular_Table {

		/**
		 * Returns a set of valid actions for current item
		 *
		 * @param array $item Single record.
		 * @return array Array of available actions
		 */
		protected function get_item_actions( $item ) {
			$send_promotion = esc_url(
				wp_nonce_url(
					add_query_arg(
						array(
							'page'       => 'yith_wcwl_panel',
							'tab'        => 'dashboard-popular',
							'action'     => 'send_promotional_email',
							'user_id'    => $item['id'],
							'product_id' => $this->product_id,
						),
						admin_url( 'admin.php' )
					),
					'send_promotional_email'
				)
			);

			$target = array(
				'product_id' => (array) $this->product_id,
				'user_id'    => (array) $item['id'],
			);
			$hash   = md5( http_build_query( $target ) );
			$drafts = get_option( 'yith_wcwl_promotion_drafts', array() );

			// retrieve draft for current item.
			$actions = array(
				'send-promotion' => array(
					'action'     => 'send-promotion',
					'title'      => __( 'Create promotion', 'yith-woocommerce-wishlist' ),
					'url'        => $send_promotion,
					'icon'       => 'mail-out',
					'class'      => 'send create-promotion',
					'attributes' => array(
						'data-product_id' => $this->product_id,
						'data-user_id'    => $item['id'],
						'data-draft'      => isset( $drafts[ $hash ] ) ? wp_json_encode( $drafts[ $hash ] ) : false,
					),
				),
			);

			return $actions;
		}
	}
}
