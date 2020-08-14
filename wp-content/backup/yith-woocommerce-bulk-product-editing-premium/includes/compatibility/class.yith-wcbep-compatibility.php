<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Compatibility Class
 *
 * @class   YITH_WCBEP_Compatibility
 * @package Yithemes
 * @since   1.1.2
 * @author  Yithemes
 *
 */
class YITH_WCBEP_Compatibility {

    /**
     * Single instance of the class
     *
     * @var \YITH_WCBEP_Compatibility
     */
    protected static $_instance;

    /**
     * Returns single instance of the class
     *
     * @return \YITH_WCBEP_Compatibility
     */
    public static function get_instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    /**
     * @var YITH_WCBEP_Badge_Management_Compatibility
     */
    public $badge_management;
    /**
     * @var YITH_WCBEP_Brands_Add_On_Compatibility
     */
    public $brands;
    /**
     * @var YITH_WCBEP_Deposits_Compatibility
     */
    public $deposits;
    /**
     * @var YITH_WCBEP_Multivendor_Compatibility
     */
    public $multivendor;

    /**
     * Constructor
     */
    protected function __construct() {
        $this->include_files();

        // Instances compatibility classes
        if ( self::has_plugin( 'badge_management' ) ) {
            $this->badge_management = YITH_WCBEP_Badge_Management_Compatibility();
        }

        if ( self::has_plugin( 'brands_add_on' ) ) {
            $this->brands = YITH_WCBEP_Brands_Add_On_Compatibility();
        }

        if ( self::has_plugin( 'deposits' ) ) {
            $this->deposits = YITH_WCBEP_Deposits_Compatibility();
        }

        if ( self::has_plugin( 'multivendor' ) ) {
            $this->multivendor = YITH_WCBEP_Multivendor_Compatibility();
        }
    }

    public function include_files() {
        $dir = YITH_WCBEP_INCLUDES_PATH . '/compatibility/';

        $files = array(
            $dir . 'class.yith-wcbep-badge-management-compatibility.php',
            $dir . 'class.yith-wcbep-brands-add-on-compatibility.php',
            $dir . 'class.yith-wcbep-deposits-compatibility.php',
            $dir . 'class.yith-wcbep-multivendor-compatibility.php',
        );

        foreach ( $files as $file ) {
            if ( file_exists( $file ) ) {
                require_once( $file );
            }
        }

    }

    /**
     * Check if user has plugin
     *
     * @param string $plugin_name
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     * @since  1.1.2
     * @return bool
     */
    static function has_plugin( $plugin_name ) {

        switch ( $plugin_name ) {
            case 'badge_management':
                return defined( 'YITH_WCBM_PREMIUM' ) && YITH_WCBM_PREMIUM && defined( 'YITH_WCBM_VERSION' ) && version_compare( YITH_WCBM_VERSION, '1.3.14', '>=' );
            case 'brands_add_on':
                return defined( 'YITH_WCBR_PREMIUM_INIT' ) && YITH_WCBR_PREMIUM_INIT;
            case 'deposits':
                return defined( 'YITH_WCDP_PREMIUM_INIT' ) && YITH_WCDP_PREMIUM_INIT;
            case 'multivendor':
                return defined( 'YITH_WPV_PREMIUM' ) && YITH_WPV_PREMIUM;

            default:
                return false;
        }
    }
}

/**
 * Unique access to instance of YITH_WCBEP_Compatibility class
 *
 * @return YITH_WCBEP_Compatibility
 * @since 1.0.0
 */
function YITH_WCBEP_Compatibility() {
    return YITH_WCBEP_Compatibility::get_instance();
}