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

if ( ! class_exists( 'YITH_Frontend_Manager_Section_Coupons_Premium' ) ) {

	class YITH_Frontend_Manager_Section_Coupons_Premium extends YITH_Frontend_Manager_Section_Coupons {

        /**
         * Constructor method
         *
         * @return \YITH_Frontend_Manager_Section_Coupons_Premium
         * @since 1.0.0
         */
        public function __construct() {
            add_filter( 'yith_wcfm_coupons_args', array( $this, 'premium_coupons_type' ) );

            parent::__construct();
        }

        /**
         * Add premium coupons
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         *
         * @param  array $atts template args
         *
         * @return array $atts template args
         * @since  1.0
         */
        public function premium_coupons_type( $atts ){
            $atts['coupon_types'] = wc_get_coupon_types();
            return $atts;
        }
	}
}
