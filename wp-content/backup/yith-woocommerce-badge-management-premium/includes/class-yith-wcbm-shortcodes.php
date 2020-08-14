<?php
/**
 * Shortcodes Class
 *
 * @author  Yithemes
 * @package YITH WooCommerce Badge Management
 * @version 1.0.0
 */


if ( !defined( 'YITH_WCBM' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCBM_Shortcodes' ) ) {
    /**
     * YITH_WCBM_Shortcodes
     *
     * @since 1.2.31
     */
    class YITH_WCBM_Shortcodes {

        public static function init() {
            $shortcodes = array(
                'yith_badge_container' => __CLASS__ . '::badge_container', // print badge container
            );

            foreach ( $shortcodes as $shortcode => $function ) {
                add_shortcode( $shortcode, $function );
            }
        }

        /**
         * print badge container
         *
         * @access   public
         *
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         *
         * @param array       $atts the attributes of shortcode
         * @param string|null $content
         *
         * @return string
         */
        public static function badge_container( $atts, $content = null ) {
            if ( !$content )
                return '';

            global $post;

            $default_atts = array(
                'product_id' => !!$post && !empty( $post->ID ) ? $post->ID : 0,
                'class'      => '',
            );

            $atts       = wp_parse_args( $atts, $default_atts );
            $product_id = absint( $atts[ 'product_id' ] );
            $class      = $atts[ 'class' ];

            $r = "<div class='yith-wcbm-shortcode-badge-container $class'>";
            $r .= apply_filters( 'yith_wcbm_product_thumbnail_container', do_shortcode( $content ), $product_id );
            $r .= "</div>";

            return $r;
        }

    }
}
?>