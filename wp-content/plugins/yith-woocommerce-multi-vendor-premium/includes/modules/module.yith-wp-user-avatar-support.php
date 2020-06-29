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
 * @class      YITH_WPUserAvatar_Support
 * @package    Yithemes
 * @since      Version 1.7
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_WPUserAvatar_Support' ) ) {

    /**
     * YITH_WPUserAvatar_Support Class
     */
    class YITH_WPUserAvatar_Support {

        /**
         * Main instance
         */
        private static $_instance = null;

        /**
         * Construct
         */
        public function __construct(){
            add_filter( 'wpua_subscriber_offlimits', array( $this, 'enable_vendor_to_manage_dashboard' ) );
        }

        /**
         * Enabled vendor to manage dashboard
         *
         * @return array The offlimits pages
         *
         * @since  1.9.4
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function enable_vendor_to_manage_dashboard( $offlimits ){
            $vendor = yith_get_vendor( 'current', 'user' );
            if( $vendor->is_valid() && $vendor->has_limited_access() ){
                $offlimits = array();
            }
            return $offlimits;
        }

        /**
         * Main plugin Instance
         *
         * @static
         * @return YITH_WPUserAvatar_Support Main instance
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
 * @return /YITH_WPUserAvatar_Support
 * @since  1.7
 * @author Andrea Grillo <andrea.grillo@yithemes.com>
 */
if ( ! function_exists( 'YITH_WPUserAvatar_Support' ) ) {
    function YITH_WPUserAvatar_Support() {
        return YITH_WPUserAvatar_Support::instance();
    }
}

YITH_WPUserAvatar_Support();
