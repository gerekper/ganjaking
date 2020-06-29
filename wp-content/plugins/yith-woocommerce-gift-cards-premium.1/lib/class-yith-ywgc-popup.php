<?php
/**
 * Popup class
 *
 * @author YITH
 * @package YITH Easy Login & Register Popup For WooCommerce
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_YWGC_Popup' ) ) {
	/**
	 * YITH Easy Login & Register Popup For WooCommerce
	 *
	 * @since 1.0.0
	 */
	class YITH_YWGC_Popup {

		/**
		 * Constructor
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function __construct() {

		    // add main popup
            add_action( 'wp_footer', array( $this, 'ywgc_add_popup' ), 10 );
		    // add popup template parts
            add_action ( 'yith_ywgc_gift_card_preview_end', array( $this, 'ywgc_append_design_presets' ) );


        }

		/**
		 * Output the popup
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return void
		 */
		public function ywgc_add_popup(){

		    wc_get_template( 'ywgc-popup.php',
                apply_filters( 'yith_ywgc_popup_template_args', [] ),
                '',
                YITH_YWGC_TEMPLATES_DIR
            );
        }

        /**
         * Append the design preset to the gift card preview (modal)
         *
         * @param WC_Product $product
         */
        public function ywgc_append_design_presets( $product ) {

            if ( get_option ( "ywgc_template_design", 'yes') != 'yes' ) {
                return;
            }

            $args = apply_filters( 'yith_wcgc_design_presets_args',
                array(
                    'hide_empty' => 1
                )
            );
            $categories = get_terms ( YWGC_CATEGORY_TAXONOMY, $args );

            $item_categories = array();
            foreach ( $categories as $item ) {
                $object_ids = get_objects_in_term ( $item->term_id, YWGC_CATEGORY_TAXONOMY );
                foreach ( $object_ids as $object_id ) {
                    $item_categories[ $object_id ] = isset( $item_categories[ $object_id ] ) ? $item_categories[ $object_id ] . ' ywgc-category-' . $item->term_id : 'ywgc-category-' . $item->term_id;
                }
            }


            wc_get_template ( 'yith-gift-cards/gift-card-presets.php',
                array(
                    'categories'      => $categories,
                    'item_categories' => $item_categories,
                    'product'         => $product
                ),
                '',
                trailingslashit ( YITH_YWGC_TEMPLATES_DIR ) );
        }

	}
}
