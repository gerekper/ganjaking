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

if ( ! class_exists( 'YITH_Frontend_Manager_Section_Reports_Premium' ) ) {

	class YITH_Frontend_Manager_Section_Reports_Premium extends YITH_Frontend_Manager_Section_Reports {

        /**
         * Constructor method
         *
         * @since 1.0.0
         */
		public function __construct() {
            add_filter( 'yith_wcfm_reports_subsections', array( $this, 'add_premium_subsections' ) );

			/*
			 *  Parent Construct
			 */
			parent::__construct();
		}

		/**
         * Add premium reports
         *
         * @since 1.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @return array all reports subsections
         */
		public function add_premium_subsections( $subsections ){
            $premium_report_subsections = array();
            $premium_report_subsections['orders-report'] = array(
                'slug' => $this->get_option( 'slug', $this->id . '_orders-report', 'orders-report' ),
                'name' => __( 'Orders', 'yith-frontend-manager-for-woocommerce' )
            );

            return array_merge( $premium_report_subsections, $subsections );
        }
		
	}

}
