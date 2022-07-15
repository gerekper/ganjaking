<?php
/**
 * DB lifecycle management of mix and match products, child items and their meta.
 *
 * @package  WooCommerce Mix and Match Products
 * @since    2.0.0
 * @version  2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_MNM_DB_Sync class
 */
class WC_MNM_DB_Sync {

	/**
	 * Attach hooks and filters.
	 */
	public static function init() {

		// Duplicate child items when duplicating a container.
		add_action( 'woocommerce_product_duplicate_before_save', array( __CLASS__, 'duplicate_product_before_save' ), 10, 2 );

		// Delete bundled item DB entries when: i) the container bundle is deleted, or ii) the associated product is deleted.
		add_action( 'delete_post', array( __CLASS__, 'delete_post' ), 11 );
		add_action( 'woocommerce_delete_product', array( __CLASS__, 'delete_product' ), 11 );

	}

	/**
	 * Duplicates child items when duplicating a container.
	 *
	 * @param  WC_Product  $duplicated_product
	 * @param  WC_Product  $product
	 */
	public static function duplicate_product_before_save( $duplicated_product, $product ) {

		if ( $product->is_type( 'mix-and-match' ) ) {

			$child_items      = $product->get_child_items( 'edit' );
			$duplicated_items = array();

			if ( ! empty( $child_items ) ) {
				foreach ( $child_items as $child_item ) {
					$child_item->set_id( 0 );
					$child_item->set_container_id( $duplicated_product->get_id() );
					$duplicated_items[] = $child_item;
				}

				$duplicated_product->set_child_items( $duplicated_items );
			}
		}
	}

	/**
	 * Deletes bundled item DB entries when their container product is deleted.
	 *
	 * @param  mixed  $id  ID of post being deleted.
	 */
	public static function delete_post( $id ) {

		if ( ! current_user_can( 'delete_posts' ) ) {
			return;
		}

		if ( $id > 0 ) {

			$post_type = get_post_type( $id );

			if ( 'product' === $post_type ) {
				self::delete_product( $id );
			}
		}
	}

	/**
	 * Deletes child item DB entries when their container product is deleted
	 *
	 * @param  mixed  $id  ID of product being deleted.
	 */
	public static function delete_product( $id ) {

		$container = wc_get_product( $id );

		if ( $container &&  $container->is_type( 'mix-and-match' ) ) {

			try {

				wc_transaction_query();

				$child_items = $container->get_child_items( 'edit' );

				foreach ( $child_items as $child_item ) {
					$child_item->delete();
				}

			} catch ( Exception $e ) {
				wc_get_logger()->error(
					esc_html__( 'Error deleting Mix and Match product child items.', 'woocommerce-mix-and-match-products' ),
					array(
						'source' => 'wc-mix-and-match-product-delete',
						'product' => $this,
						'error' => $e,
					)
				);
				wc_transaction_query( 'rollback' );
			}

		}

		return $id;

	}

}
WC_MNM_DB_Sync::init();
