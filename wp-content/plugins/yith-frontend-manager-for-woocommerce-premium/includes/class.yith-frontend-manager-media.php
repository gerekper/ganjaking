<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'Direct access forbidden.' );
}

if ( ! class_exists( 'YITH_Frontend_Manager_Media' ) ) {

    class YITH_Frontend_Manager_Media {

        function __construct() {
            add_action( 'init', array( $this, 'init' ) );
        }

        function init() {
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
            add_filter( 'ajax_query_attachments_args', array( $this, 'filter_media' ) );
        }

        function enqueue_scripts() {
        	if( ! empty( YITH_Frontend_Manager()->gui ) && YITH_Frontend_Manager()->gui->is_main_page() ){
		        $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) || ! empty( $_GET['yith_debug'] ) ? '' : '.min';
		        wp_enqueue_media();
		        wp_enqueue_script( 'yith-frontend-manager-media-js', YITH_WCFM_URL . "assets/js/yith-frontend-manager-media{$suffix}.js", array( 'jquery' ) );
		        $wc_placeholder_img = wc_placeholder_img( array( 70, 70 ) );
		        wp_localize_script( 'yith-frontend-manager-media-js', 'yith_wcfm_media', array(
			        'wc_placeholder_img' => $wc_placeholder_img
		        ) );
	        }
        }

        function filter_media( $query ) {
            if ( ! current_user_can( 'manage_woocommerce' ) ) { $query['author'] = get_current_user_id(); }
            return $query;
        }

        public static function upload_image_input( $type, $item_id = 0 ) {

            if ( current_user_can( 'upload_files' ) ) {

                $btn1 = __( 'Upload/add image', 'yith-frontend-manager-for-woocommerce' );
                $btn2 = __( 'Remove image', 'yith-frontend-manager-for-woocommerce' );

                $thumbnail = $thumbnail_id = '';
                if ( $item_id > 0 ) {
                    if ( $type == 'post' && get_the_post_thumbnail_url( $item_id ) != '' ) {
                        $thumbnail = get_the_post_thumbnail_url( $item_id, 'thumbnail' );
                        $thumbnail_id = get_post_thumbnail_id( $item_id );
                    } else if ( $type == 'term' && get_term_meta( $item_id, 'thumbnail_id', true ) > 0 ) {
                        $thumbnail_id = get_term_meta( $item_id, 'thumbnail_id', true );
                        $thumbnail = wp_get_attachment_url( $thumbnail_id );
                    }
                }

                $input = '<input type="hidden" id="image_url" name="image_url" value="' . $thumbnail . '">';
                $input_id = '<input type="hidden" id="attach_id" name="attach_id" value="' . $thumbnail_id . '">';
                $button_1 = '<input id="upload_image_button" type="button" value="' . $btn1 . '" class="button" style="position: relative; z-index: 1;">';

                if ( $thumbnail != '' ) {

                    $thumbnail = '<img src="' . $thumbnail . '" width="65"  height="65" class="thumbnail">';
                    $button_2 = '<button id="upload_image_remove_button">' . $btn2 . '</button>';

                } else {

                    $thumbnail = wc_placeholder_img( array( 70, 70 ) );
                    $button_2 = '<button id="upload_image_remove_button" style="display: none;">' . $btn2 . '</button>';

                }

                return $input . $input_id . $thumbnail . $button_1 . $button_2;

            }

            return __( 'Please log in to upload', 'yith-frontend-manager-for-woocommerce' );

        }

        public static function product_gallery( $item_id = 0 ) {

            if ( current_user_can( 'upload_files' ) ) {

                $button = '<input id="add_product_gallery_image" type="button" value="' . __( 'Add product gallery image', 'yith-frontend-manager-for-woocommerce' ) . '" class="button" style="">';

                return $button;

            }

            return __( 'Please log in to upload', 'yith-frontend-manager-for-woocommerce' );

        }

    }

}

new YITH_Frontend_Manager_Media();
