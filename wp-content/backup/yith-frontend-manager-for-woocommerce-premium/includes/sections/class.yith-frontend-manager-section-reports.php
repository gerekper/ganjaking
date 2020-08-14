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

if ( ! class_exists( 'YITH_Frontend_Manager_Section_Reports' ) ) {

	class YITH_Frontend_Manager_Section_Reports extends YITH_WCFM_Section {

		public $default_subsection = 'orders-report';

		/**
		 * Constructor method
		 *
		 * @return \YITH_Frontend_Manager_Section
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->id = 'reports';
			$this->_default_section_name = _x( 'Reports', '[Frontend]: Reports menu item', 'yith-frontend-manager-for-woocommerce' );

			$this->_subsections = apply_filters( 'yith_wcfm_reports_subsections', array(

                    'customers-report' => array(
                        'slug' => $this->get_option( 'slug', $this->id . '_customers_report', 'customers-report' ),
                        'name' => __( 'Customers', 'yith-frontend-manager-for-woocommerce' )
                    ),

                    'stock-report' => array(
                        'slug' => $this->get_option( 'slug', $this->id . '_stock-report', 'stock-report' ),
                        'name' => __( 'Stock', 'yith-frontend-manager-for-woocommerce' )
                    ),

				), $this
			);


			/*
			 *  Enqueue Scripts
			 */

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			add_filter( 'woocommerce_admin_stock_report_product_actions', 'YITH_Frontend_Manager_Section_Reports::stock_report_product_actions', 10, 2 );
			add_filter( 'yith_wcfm_section_url', array( $this, 'set_default_report' ), 10, 4 );
			/*
			 *  Construct
			 */

			parent::__construct();

		}

		/* === SECTION METHODS === */

		/**
		 * Print shortcode function
		 *
		 * @author Andrea Grillo    <andrea.grillo@yithemes.com>
		 * @return void
		 * @since  1.0.0
		 */
		public function print_shortcode( $atts = array(), $content = '', $tag ) {
			$section = $this->id;
			$subsection_prefix = $this->get_shortcodes_prefix() . $section;
			$subsection = $tag != $subsection_prefix ? str_replace( $subsection_prefix . '_', '', $tag ) : $section;

			if( $subsection == $this->id ){
				$subsection = $this->default_subsection;
			}

			$this->print_section( $subsection, $section, $atts );
		}

		/**
		 * WP Enqueue Scripts
		 *
		 * @author Corrado Porzio <corradoporzio@gmail.com>
		 * @return void
		 * @since  1.0.0
		 */
		public function enqueue_section_scripts() {

			// CSS
			wp_enqueue_style( 'yith-wcfm-reports', YITH_WCFM_URL . 'assets/css/reports.css', array(), YITH_WCFM_VERSION );
			wp_enqueue_style( 'woocommerce_admin_styles' );
			wp_enqueue_style( 'woocommerce_admin_print_reports_styles' );
			wp_enqueue_style( 'jquery-ui-style' );
			wp_enqueue_style( 'wp-color-picker' );

			// JS
			wp_enqueue_script( 'woocommerce_admin' );
			wp_enqueue_script( 'wc-reports' );
			wp_enqueue_script( 'flot' );
			wp_enqueue_script( 'flot-resize' );
			wp_enqueue_script( 'flot-time' );
			wp_enqueue_script( 'flot-pie' );
			wp_enqueue_script( 'flot-stack' );

		}

        /**
         * Require the WooCommerce Admin Classes
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @return void
         * @since  1.0.12
         */
        public static function require_reports_core_files(){
            $pre_required = array(
                'WC_Admin_Reports'  => array(
                    'section'   => 'includes/admin',
                    'file_name' => 'class-wc-admin-reports.php'
                    ),

                'WC_Admin_Report'   => array(
                    'section'   => 'includes/admin/reports',
                    'filename'  => 'class-wc-admin-report.php'
                ),
            );

            foreach( $pre_required as $class_name => $file_info ){
                if( ! class_exists( $class_name ) ){
                    yith_wcfm_include_woocommerce_core_file( $file_info['filename'], $file_info['section'] );
                }
            }

            $reports_core_path = WC()->plugin_path() . '/includes/admin/reports/';
            foreach ( new DirectoryIterator( $reports_core_path ) as $fileInfo ) {
                if( ! $fileInfo->isDot() && $fileInfo->isFile() ){
                    require_once $fileInfo->getPathname();
                }
            }
        }

		/**
		 * Require the WooCommerce Admin Classes
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 * @since  1.0.12
		 */
		public static function stock_report_product_actions( $actions, $product ){
			if( isset( $actions['edit']['url'] ) && ! empty( YITH_Frontend_Manager()->gui ) && class_exists( 'YITH_Frontend_Manager_Section_Products' ) && YITH_Frontend_Manager()->gui->is_main_page() ){
				$actions['edit']['url'] = YITH_Frontend_Manager_Section_Products::get_edit_product_link( $product->get_id() );
			}

			return $actions;
		}

		/**
		 * Add default query arg value for stock report to prevent 404 issue on pagination
		 *
		 * @param $url string report url
		 * @param $slug string slug section
		 * @param $subsection string slug of subsection. If empty the current url is for main section
		 * @param $id string current section ID
		 *
		 * @return string the filtered url
		 */
		public function set_default_report( $url, $slug, $subsection, $id ){
			if( $this->id == $id && 'stock-report' == $subsection ){
				$url = add_query_arg( array( 'reports' => 'stock-report', 'report'  => 'low_in_stock' ), $url );
			}
			return $url;
		}
	}
}
