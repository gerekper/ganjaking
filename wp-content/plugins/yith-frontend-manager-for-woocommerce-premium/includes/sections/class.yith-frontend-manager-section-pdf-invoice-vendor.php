<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined ( 'ABSPATH' ) ) {
	exit( 'Direct access forbidden.' );
}

if( ! class_exists( 'YITH_Frontend_Manager_Section_PDF_Invoice_Vendor' ) && class_exists( 'YITH_Vendors_Premium' ) && function_exists( 'YITH_PDF_Invoice' ) ) {

	class YITH_Frontend_Manager_Section_PDF_Invoice_Vendor extends YITH_WCFM_Section {

        /**
         * Current Vendor Object
         */
        public $vendor = null;

		/**
		 * PDF Invoice for Vendor object
		 */
		public $pdf_invoice_mv_loader = null;

		/**
		 * Constructor method
		 *
		 * @return \YITH_Frontend_Manager_Section_PDF_Invoice_Vendor
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->id                    = 'pdf-invoice-vendor-panel';
			$this->_default_section_name = _x( 'PDF Invoice', '[Frontend]: Vendor settings menu item', 'yith-frontend-manager-for-woocommerce' );
			$this->vendor                = function_exists('yith_wcmv_get_vendor') ? yith_wcmv_get_vendor('current', 'user') : yith_get_vendor( 'current', 'user' );

            if( $this->vendor->is_valid() && $this->vendor->has_limited_access() ){

	            add_filter( 'yith_plugin_fw_panel_url', array( $this, 'change_panel_url' ), 10, 5 );
	            add_filter( 'yith_wcfm_section_url', array( $this, 'vendor_settings_uri' ), 10, 4 );

	            $this->set_allowed_query_string( array( 'page' => 'yith-plugins_page_pdf_invoice_for_multivendor' ) );

				add_filter('yit_framework_show_float_save_button', array( $this, 'remove_save_option_float_button' ));

                parent::__construct();
            }

            else{
                if( is_admin() ){
                    add_filter( 'yith_wcfm_endpoints_options', array( $this, 'add_endpoints_settings' ) );
                    add_filter( 'yith_wcfm_sections_options', array( $this, 'sections_options' ) );
                }

				add_filter( 'yith_wcmf_section_allowed_only_for_vendors', array( $this, 'allow_only_for_vendors' ) );
                add_filter( 'yith_wcfm_get_sections_before_print_navigation', array( $this, 'remove_vendor_menu_item_for_admin' ) );
            }
		}

		/* === SECTION METHODS === */

		/**
		 * Print shortcode function
		 *
		 * @author YITH <plugins@yithemes.com>
		 * @return void
		 * @since 1.0.0
		 */
		public function print_shortcode( $atts = array(), $content = '', $tag = '') {

			if( class_exists( 'YITH_Vendors_Admin_Premium' ) ){

				add_filter( 'yith_wc_plugin_panel_current_tab', array( $this, 'admin_tab_params' ) );

				if( ! empty( YITH_Frontend_Manager()->gui ) ){
					add_filter( 'yit_panel_hide_sidebar', '__return_true' );
				}

				if( ! class_exists( 'YITH_YWPI_Multivendor_Loader' ) ){
					require_once( YITH_YWPI_LIB_DIR . 'class.yith-ywpi-multivendor-loader.php' );
				}

				if( ! function_exists( 'woocommerce_admin_fields' ) ){
					require_once WC()->plugin_path() . '/includes/admin/wc-admin-functions.php';
				}

				$pdf_invoice_mv_loader = YITH_YWPI_Multivendor_Loader::get_instance();
				$pdf_invoice_mv_loader->register_panel();
				$vendor_panel = $pdf_invoice_mv_loader->get_vendor_panel();
				$vendor_panel->woocommerce_update_options();
				$vendor_panel->yit_panel();
			}
		}

        /**
         * Remove vendor panel menu item for adminstrator
         *
         * @param $sections
         * @return mixed
         */
		public function remove_vendor_menu_item_for_admin( $sections ){
		    if( isset( $sections[ $this->id ] ) ){
		        unset( $sections[ $this->id ] );
            }
		    return $sections;
        }

        public function admin_tab_params( $tab ){
			if( ! empty( $_GET['page'] ) && 'pdf-invoice-vendor-panel' ===  $_GET['page'] );{
		        $tab = ! empty( $_GET['tab'] ) ? esc_html( $_GET['tab'] ) : $tab;
	        }
			return $tab;
        }

		/**
		 * Change the panle uri from admin area to FM dashboard
		 *
		 * @param $url
		 * @param $page
		 * @param $tab
		 * @param $sub_tab
		 * @param $parent_page
		 *
		 * @return string the new uri
		 *
		 */
		public function change_panel_url( $url, $page, $tab, $sub_tab, $parent_page ){
			$pdf_invoice_mv_loader = YITH_YWPI_Multivendor_Loader::get_instance();
			if( $pdf_invoice_mv_loader->get_vendor_panel_page_name() == $page && ! empty( YITH_Frontend_Manager()->gui ) && YITH_Frontend_Manager()->gui->is_main_page() ){
				$old_url = add_query_arg( array( 'page' => $page ), admin_url( 'admin.php' ) );
				$url = str_replace( $old_url, $this->get_url(), $url );
			}
			return $url;
		}

		/**
		 * Get correct vendor panel endpoint uri
		 *
		 * @param $endpoint_uri
		 * @param $slug
		 * @param $subsection
		 * @return mixed
		 */
		public function vendor_settings_uri( $endpoint_uri, $slug, $subsection, $id ){
			if( $id == $this->get_id() && class_exists( 'YITH_YWPI_Multivendor_Loader' ) ){
				$pdf_invoice_mv_loader = YITH_YWPI_Multivendor_Loader::get_instance();
				$endpoint_uri = add_query_arg( array( 'page' => $pdf_invoice_mv_loader->get_vendor_panel_page_name() ), $endpoint_uri );
			}
			return $endpoint_uri;
		}

		/**
		 * Allow section only for vendors
		 *
		 * @param $args array section ids array
		 * @return array section ids
		 *
		 * @since  1.9
		 */
		public function allow_only_for_vendors( $section_ids ){
			$section_ids[] = 'pdf-invoice-vendor-panel';
			return $section_ids;
		}
		/**
		 * Remove floating save option on panel for vendors
		 *
		 * @param $show bool display button
		 * @return bool display button
		 *
		 * @since  1.18.0
		 */
		public function remove_save_option_float_button( $show ) {
			if(  ! empty( YITH_Frontend_Manager()->gui ) && YITH_Frontend_Manager()->gui->is_main_page() ) {
				$show = false;
			}

			return $show;
		}
	}
}

