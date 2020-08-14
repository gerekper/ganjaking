<?php
/**
 * Functions
 *
 * @author Yithemes
 * @package YITH WooCommerce Bulk Product Editing
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCBEP' ) ) { exit; } // Exit if accessed directly

if ( ! function_exists( 'yith_wcbep_get_template' ) ) {
    function yith_wcbep_get_template( $template , $args = array() ){
        extract( $args );
        include(YITH_WCBEP_TEMPLATE_PATH . '/' . $template);
    }
}



?>