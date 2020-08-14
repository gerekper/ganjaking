<?php
// This file is based on wp-includes/js/tinymce/langs/wp-langs.php

if ( ! defined( 'ABSPATH' ) )
    exit;

if ( ! class_exists( '_WP_Editors' ) )
    require( ABSPATH . WPINC . '/class-wp-editor.php' );

function ywcpos_tinymce_plugin_translation() {
    $strings = array(
        'firstname'      => __( 'User\'s first Name', 'yith-woocommerce-pending-order-survey' ),
        'lastname'       => __( 'User\'s last Name', 'yith-woocommerce-pending-order-survey' ),
        'fullname'       => __( 'Full Name', 'yith-woocommerce-pending-order-survey' ),
        'useremail'      => __( 'User\'s email address', 'yith-woocommerce-pending-order-survey' ),
        'ordercontent'    => __( 'Order Content', 'yith-woocommerce-pending-order-survey' ),
        'orderlink'       => __( 'Order Link', 'yith-woocommerce-pending-order-survey' ),
        'order_link_label'  => __('Pending Order','yith-woocommerce-pending-order-survey' ),
        'pending-survey' => __( 'Pending Survey', 'yith-woocommerce-pending-order-survey' ),
        'coupon'         => __( 'Coupon', 'yith-woocommerce-pending-order-survey' ),
    );

    $locale = _WP_Editors::$mce_locale;
    $translated = 'tinyMCE.addI18n("' . $locale . '.ywcpos_shortcode", ' . json_encode( $strings ) . ");\n";

    return $translated;
}

$strings = ywcpos_tinymce_plugin_translation();