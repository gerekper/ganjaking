<?php

defined( 'ABSPATH' ) or exit;

/*
 *  YITH WooCommerce Customer History Admin
 */

if ( ! class_exists( 'YITH_WCCH_Frontend' ) ) {

    class YITH_WCCH_Frontend {

        /*
         *  Constructor
         */

        function __construct() {

            /*
             *  Hooks
             */

            add_action( 'wp_enqueue_scripts', array( &$this, 'wp_enqueue_scripts' ), 999 );

        }

        /*
         *  Admin Enqueue Scripts
         */

        function wp_enqueue_scripts() {

            /*
             *  Js
             */

            wp_enqueue_script( 'jquery' );

            /*
             *  Css
             */
            wp_enqueue_style( 'yith-wcch-style', YITH_WCCH_URL . 'assets/css/yith-wcch.css', false, '1.0.0' );

        }

    }

}