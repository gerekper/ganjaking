<?php
/**
 * Popular products table class
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Classes\Tables
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Popular_Table_Premium' ) ) {
	/**
	 * Admin view class. Create and populate "user with wishlists" table
	 *
	 * @since 1.0.0
	 */
	class YITH_WCWL_Popular_Table_Premium extends YITH_WCWL_Popular_Table {

		/**
		 * Show last time campaign was sent for a specific product
		 *
		 * @param array $item Item for the current record.
		 * @return string Column content
		 * @since 2.2.0
		 */
		public function column_last_sent( $item ) {
			$last_sent = get_post_meta( $item['id'], 'last_promotional_email', true );

			if ( ! $last_sent ) {
				$column = __( 'N/A', 'yith-woocommerce-wishlist' );
			} else {
				$column = gmdate( wc_date_format(), $last_sent );
			}

			return $column;
		}

		/**
		 * Returns columns available in table
		 *
		 * @return array Array of columns of the table
		 * @since 2.0.0
		 */
		public function get_columns() {
			$columns = yith_wcwl_merge_in_array(
				parent::get_columns(),
				array(
					'last_sent' => __( 'Last promotional email sent', 'yith-woocommerce-wishlist' ),
				),
				'count'
			);

			return $columns;
		}

		/**
		 * Returns a set of valid actions for current item
		 *
		 * @param array $item Single record.
		 * @return array Array of available actions
		 */
		protected function get_item_actions( $item ) {
			$actions = parent::get_item_actions( $item );

			$export_users_url = esc_url(
				wp_nonce_url(
					add_query_arg(
						array(
							'action'     => 'export_users',
							'product_id' => $item['id'],
						),
						admin_url( 'admin.php' )
					),
					'export_users'
				)
			);
			$send_promotion   = esc_url(
				wp_nonce_url(
					add_query_arg(
						array(
							'page'       => 'yith_wcwl_panel',
							'tab'        => 'dashboard-popular',
							'action'     => 'send_promotional_email',
							'product_id' => $item['id'],
						),
						admin_url( 'admin.php' )
					),
					'send_promotional_email'
				)
			);

			// retrieve draft for current item.
			$target = array(
				'product_id' => (array) $item['id'],
				'user_id'    => array(),
			);
			$hash   = md5( http_build_query( $target ) );
			$drafts = get_option( 'yith_wcwl_promotion_drafts', array() );

			$actions = array_merge(
				$actions,
				array(
					'send-promotion' => array(
						'action'     => 'send-promotion',
						'title'      => __( 'Create promotion', 'yith-woocommerce-wishlist' ),
						'url'        => $send_promotion,
						'icon'       => 'mail-out',
						'class'      => 'send create-promotion',
						'attributes' => array(
							'data-product_id' => $item['id'],
							'data-draft'      => isset( $drafts[ $hash ] ) ? wp_json_encode( $drafts[ $hash ] ) : false,
						),
					),
					'export-users'   => array(
						'action' => 'export-users',
						'title'  => __( 'Export users that have added this product to their wishlist', 'yith-woocommerce-wishlist' ),
						'url'    => $export_users_url,
						'icon'   => 'upload',
					),
				)
			);

			return $actions;
		}
	}
}
