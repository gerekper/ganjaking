<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCPO_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

if ( ! class_exists( 'YITH_Pre_Order_Download_Links' ) ) {
	/**
	 * Class YITH_Pre_Order_Download_Links
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 * @since 1.3.0
	 */
	class YITH_Pre_Order_Download_Links {

		/**
		 * Construct
		 *
		 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
		 * @since 1.3.0
		 */
		public function __construct(){
			add_filter( 'woocommerce_get_item_downloads', array( $this, 'hide_pre_order_download_link_from_single_order_page' ), 10, 3 );
			add_filter( 'woocommerce_customer_get_downloadable_products', array( $this, 'delete_pre_order_download_from_array' ) );
		}

		public function hide_pre_order_download_link_from_single_order_page( $files, $item, $order ) {

			if ( ( isset( $item['ywpo_item_for_sale_date'] ) && $item['ywpo_item_for_sale_date'] > time() ) ) {
				foreach ( $files as $download_id => $file ) {
					unset( $files[ $download_id ] );
				}
			}

			return $files;
		}

		public function delete_pre_order_download_from_array( $downloads ) {

			foreach ( $downloads as $key => &$download ) {
				$order        = wc_get_order( $download['order_id'] );

				if ( ( 'yes' == yit_get_prop( $order, '_order_has_preorder', true ) ) ) {
					foreach ( $order->get_items() as $item ) {
						if ( ( isset( $item['ywpo_item_for_sale_date'] ) && $item['ywpo_item_for_sale_date'] > time() ) ) {
							if ( $item['product_id'] == $download['product_id'] ) {
								unset( $downloads[ $key ] );
								break;
							}
						}
					}
				}
			}

			return $downloads;
		}

	}
}