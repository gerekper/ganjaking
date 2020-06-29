<?php
if ( !defined( 'ABSPATH' ) || !defined( 'YITH_WCBM_PREMIUM' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Implements features of FREE version of YITH WooCommerce Badge Management
 *
 * @class   YITH_WCBM_Premium
 * @package YITH WooCommerce Badge Management
 * @since   1.0.0
 * @author  Yithemes
 */

if ( !class_exists( 'YITH_WCBM_Premium' ) ) {
    /**
     * YITH WooCommerce Badge Management
     *
     * @since 1.0.0
     * @deprecated since 1.4.0 use YITH_WCBM instead
     */
    class YITH_WCBM_Premium extends YITH_WCBM {

    }
}

/**
 * Unique access to instance of YITH_WCBM_Premium class
 *
 * @return YITH_WCBM_Premium
 * @deprecated since 1.3.0 use YITH_WCBM() instead
 * @since 1.0.0
 */
function YITH_WCBM_Premium() {
    return YITH_WCBM();
}