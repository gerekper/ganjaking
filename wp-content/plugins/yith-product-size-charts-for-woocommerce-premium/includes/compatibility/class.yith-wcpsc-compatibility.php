<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Compatibility Class
 *
 * @class   YITH_WCPSC_Compatibility
 * @package Yithemes
 * @since   1.0.0
 * @author  Yithemes
 *
 */
class YITH_WCPSC_Compatibility {

    /**
     * Single instance of the class
     *
     * @var \YITH_WCPSC_Compatibility
     * @since 1.0.0
     */
    private static $_instance;

    /**
     * Returns single instance of the class
     *
     * @return \YITH_WCPSC_Compatibility
     * @since 1.0.0
     */
    public static function get_instance() {
        return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();

    }

    /**
     * Constructor
     *
     * @access public
     * @since  1.0.0
     */
    private function __construct() {
        $this->include_compatibility_files();
    }

    /**
     * Include compatibility files
     *
     * @access public
     * @since  1.0.0
     */
    private function include_compatibility_files() {
        $compatibility_dir = YITH_WCPSC_DIR . 'includes/compatibility/';

        $files = array(
            $compatibility_dir . 'class.yith-wcpsc-multivendor-compatibility.php',
            $compatibility_dir . 'class.yith-wcpsc-quick-view-compatibility.php',
        );

        foreach ( $files as $file ) {
            file_exists( $file ) && require_once( $file );
        }

    }


    /**
     * Check if user has YITH Multivendor Premium plugin
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     * @deprecated since 1.1.1 use YITH_WCPSC_Compatibility::has_plugin($slug) instead
     * @since  1.0
     * @return bool
     */
    static function has_multivendor_plugin() {
        return defined( 'YITH_WPV_PREMIUM' ) && YITH_WPV_PREMIUM && defined( 'YITH_WPV_VERSION' ) && version_compare( YITH_WPV_VERSION, apply_filters( 'yith_wcpsc_multivendor_min_version', '1.7.1' ), '>=' );
    }

    /**
     * check if has a plugin active
     *
     * @param $slug
     *
     * @return bool
     */
    static function has_plugin( $slug ) {
        $value = false;

        switch ( $slug ) {
            case 'multi-vendor':
                $value = defined( 'YITH_WPV_PREMIUM' ) && YITH_WPV_PREMIUM && defined( 'YITH_WPV_VERSION' ) && version_compare( YITH_WPV_VERSION, apply_filters( 'yith_wcpsc_multivendor_min_version', '1.7.1' ), '>=' );
                break;

            case 'quick-view':
                $has_premium = defined( 'YITH_WCQV_PREMIUM' ) && YITH_WCQV_PREMIUM && defined( 'YITH_WCQV_VERSION' ) && version_compare( YITH_WCQV_VERSION, apply_filters( 'yith_wcpsc_quick_view_min_version', '1.1.5' ), '>=' );
                $has_free    = !defined( 'YITH_WCQV_PREMIUM' ) && defined( 'YITH_WCQV_VERSION' ) && version_compare( YITH_WCQV_VERSION, apply_filters( 'yith_wcpsc_quick_view_free_min_version', '1.1.4' ), '>=' );
                $value       = $has_premium || $has_free;
                break;
        }

        return $value;
    }
}

/**
 * Unique access to instance of YITH_WCPSC_Compatibility class
 *
 * @return YITH_WCPSC_Compatibility
 * @since 1.0.0
 */
function YITH_WCPSC_Compatibility() {
    return YITH_WCPSC_Compatibility::get_instance();
}