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

/**
 *
 *
 * @class      YITH_GeoDirectory_Support
 * @package    Yithemes
 * @since      Version 1.7
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_GeoDirectory_Support' ) ) {

    /**
     * YITH_Vendor_Vacation Class
     */
    class YITH_GeoDirectory_Support {

        /**
         * Main instance
         */
        private static $_instance = null;

        /**
         * Construct
         */
        public function __construct(){
            $this->prevent_admin_access();
        }

        /**
         * Disable GeoDirectory "Prevent admin access" for vendor
         *
         * @return void
         *
         * @since  1.9.4
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function prevent_admin_access() {
            if ( function_exists( 'geodir_allow_wpadmin' ) && is_admin() ) {
                $vendor = yith_get_vendor( 'current', 'user' );
                if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
                    remove_action( 'admin_init', 'geodir_allow_wpadmin' );
                }
            }
        }

        /**
         * Main plugin Instance
         *
         * @static
         * @return YITH_Vendor_Vacation Main instance
         *
         * @since  1.7
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }
    }
}

/**
 * Main instance of plugin
 *
 * @return /YITH_GeoDirectory_Support
 * @since  1.7
 * @author Andrea Grillo <andrea.grillo@yithemes.com>
 */
if ( ! function_exists( 'YITH_GeoDirectory_Support' ) ) {
    function YITH_GeoDirectory_Support() {
        return YITH_GeoDirectory_Support::instance();
    }
}

YITH_GeoDirectory_Support();
