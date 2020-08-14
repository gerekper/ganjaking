<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCGPF_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_WCGPF_Brands_Compatibility
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_WCGPF_Brands_Compatibility' ) ) {

    class YITH_WCGPF_Brands_Compatibility
    {

        public function __construct()
        {
            add_filter('yith_wcgpf_values_in_feed', array($this,'product_brands_feed'),10,4);
        }

        public function product_brands_feed( $value,$field,$product_fields, $product ) {

            if( 'brand' == $field && apply_filters('yith_wcgpf_show_brand_yith_plugin',true)) {
                    $product_id = $product->get_id();
                    $id = ($post_parent = wp_get_post_parent_id($product_id)) ? $post_parent : $product_id ;
                    $brand = wp_get_post_terms( $id, YITH_WCBR::$brands_taxonomy, array("fields" => "names" ));

                if(!is_wp_error($brand) && !empty($brand)) {
                    $value = $brand[0];
                }
            }

            return $value;
        }

    }
}

return new YITH_WCGPF_Brands_Compatibility();