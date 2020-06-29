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

if ( ! class_exists( 'YITH_Frontend_Manager_Section_SMS_Vendor' ) && class_exists( 'YITH_Vendors_Premium' ) && function_exists( 'YITH_WSN' ) ) {

	class YITH_Frontend_Manager_Section_SMS_Vendor extends YITH_WCFM_Section {

		/**
		 * Current Vendor Object
		 */
		public $vendor = null;

		var $vendor_panel = null;

		/**
		 * Constructor method
		 *
		 * @return \YITH_Frontend_Manager_Section_Vendor
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->id                    = 'sms-vendor-panel';
			$this->_default_section_name = _x( 'SMS Notifications', '[Frontend]: SMS Notifications vendor settings menu item', 'yith-frontend-manager-for-woocommerce' );
			$this->vendor                = yith_get_vendor( 'current', 'user' );

			if ( $this->vendor->is_valid() && $this->vendor->has_limited_access() ) {
				parent::__construct();

			} else {
				if ( is_admin() ) {
					add_filter( 'yith_wcfm_endpoints_options', array( $this, 'add_endpoints_settings' ) );
					add_filter( 'yith_wcfm_sections_options', array( $this, 'sections_options' ) );
				}

				add_filter( 'yith_wcfm_get_sections_before_print_navigation', array( $this, 'remove_vendor_menu_item_for_admin' ) );

			}


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

			$section           = $this->id;
			$subsection_prefix = $this->get_shortcodes_prefix() . $section;
			$subsection        = $tag != $subsection_prefix ? str_replace( $subsection_prefix . '_', '', $tag ) : $section;
			$atts              = array( 'section_obj' => $this, 'section' => $section, 'subsection' => $subsection );

			if ( apply_filters( 'yith_wcfm_print_sms_section', true, $subsection, $section, $atts ) ) {
				$this->print_section( $subsection, $section, $atts );
			} else {
				do_action( 'yith_wcfm_print_section_unauthorized', $this->id );
			}

		}

		public function print_section( $subsection = '', $section = '', $atts = array() ) {

			if ( ! is_user_logged_in() ) {
				return;
			}

			if ( $this->is_enabled() ) {

				if ( ! empty( YITH_Frontend_Manager()->gui ) ) {
					add_filter( 'yit_panel_hide_sidebar', '__return_true' );
				}

				if ( ! function_exists( 'woocommerce_admin_fields' ) ) {
					include_once( WC()->plugin_path() . '/includes/admin/wc-admin-functions.php' );
				}

				YWSN_MultiVendor()->add_ywsn_vendor();
				$this->vendor_panel = YWSN_MultiVendor()->get_vendor_panel();
				$this->vendor_panel->woocommerce_update_options();
				$this->vendor_panel->yit_panel();

			} else {
				do_action( 'yith_wcfm_print_section_unauthorized', $this->id );
			}

		}


		/**
		 * Remove vendor panel menu item for adminstrator
		 *
		 * @param $sections
		 *
		 * @return mixed
		 */
		public function remove_vendor_menu_item_for_admin( $sections ) {
			if ( isset( $sections[ $this->id ] ) ) {
				unset( $sections[ $this->id ] );
			}

			return $sections;
		}


		/**
		 * Section styles and scripts
		 *
		 * Override this method in section class to enqueue
		 * particular styles and scripts only in correct
		 * section
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return false
		 * @since  1.0.0
		 */
		public function enqueue_section_scripts() {
			YITH_WSN()->admin_scripts();
		}

	}
}

