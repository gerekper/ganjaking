<?php

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( !class_exists( 'YITH_WCET_WP_Email' ) ) :

    class YITH_WCET_WP_Email extends WC_Email {

        /**
         * Constructor
         */
        function __construct() {

            $this->id             = 'yith_wcet_wp_email';
            $this->customer_email = true;
            $this->title          = __( 'WordPress Emails', 'yith-woocommerce-email-templates' );
            $this->customer_email = false;
            $this->email_type     = 'html';

            // Call parent constructor
            parent::__construct();
        }
    }

endif;