<?php
/**
 * Shortcode tab options
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Recently Viewed Products
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WRVP' ) ) {
    exit;
} // Exit if accessed directly

$options = array(
    'shortcode' => array(
        'tab' => array(
            'type' => 'custom_tab',
            'action' => 'yith_wrvp_shortcode_tab'
        )
    )
);

return $options;
