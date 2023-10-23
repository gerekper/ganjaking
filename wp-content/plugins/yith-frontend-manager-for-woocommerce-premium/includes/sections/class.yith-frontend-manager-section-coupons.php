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

if ( ! class_exists( 'YITH_Frontend_Manager_Section_Coupons' ) ) {

	class YITH_Frontend_Manager_Section_Coupons extends YITH_WCFM_Section {

		/**
		 * Constructor method
		 *
		 * @return \YITH_Frontend_Manager_Section
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->id = 'coupons';
			$this->_default_section_name = _x( 'Coupons', '[Frontend]: Coupons menu item', 'yith-frontend-manager-for-woocommerce' );

			$this->_subsections = array(
				'coupons' => array(
					'slug' => $this->get_option( 'slug', $this->id . '_coupons', 'coupons' ),
					'name' => __( 'All Coupons', 'yith-frontend-manager-for-woocommerce' )
				),

				'coupon' => array(
					'slug' => $this->get_option( 'slug', $this->id . '_coupon', 'coupon' ),
					'name' => __( 'Add Coupon', 'yith-frontend-manager-for-woocommerce' )
				),
			);

			add_action( 'yith_wcfm_delete_coupon', 'YITH_Frontend_Manager_Section_Coupons::delete', 10, 1 );

			/*
			 *  Enqueue Scripts
			 */
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			/*
			 *  Construct
			 */
			parent::__construct();
		}

		/* === SECTION METHODS === */

		/**
		 * Print shortcode function
		 *
		 * @author YITH <plugins@yithemes.com>
		 * @return void
		 * @since  1.0.0
		 */
		public function print_shortcode( $atts = array(), $content = '', $tag = '' ) {
			$section = $this->id;
			$subsection_prefix = $this->get_shortcodes_prefix() . $section;
			$subsection = $tag != $subsection_prefix ? str_replace( $subsection_prefix . '_', '', $tag ) : $section;
			/**
			 * APPLY_FILTERS: yith_wcfm_print_coupons_section
			 *
			 * Filters print the coupons section.
			 *
			 * @param bool $display_coupon_section True/false for print the coupon section.
			 * @param string $subsection Subsection name
			 * @param string $section Section name
			 * @param array $atts shortcode attributes
			 * @return bool
			 */
            if( apply_filters( 'yith_wcfm_print_coupons_section', true, $subsection, $section, $atts ) ){
                $allowed_coupon_types = array();
                $allowed_coupon_types[] = YITH_Frontend_Manager()->is_wc_3_0_or_greather ? 'percent' : 'percent_product';

                $wc_coupon_types = wc_get_coupon_types();
                foreach( $wc_coupon_types as $type => $label ){
                    if( ! in_array( $type, $allowed_coupon_types ) ){
                        unset( $wc_coupon_types[ $type ] );
                    }
                }

                $atts['coupon_types'] = $wc_coupon_types;
                $atts['section_obj'] = $this;
				/**
				 * APPLY_FILTERS: yith_wcfm_coupons_args
				 *
				 * Filter the coupons args
				 *
				 * @param array $atts shortcode attributes.
				 * @param string $subsection Subsection name
				 * @param string $section Section name
				 * @return array
				 */
                $atts = apply_filters( 'yith_wcfm_coupons_args', $atts, $subsection, $section );

                $this->print_section( $subsection, $section, $atts );
            }

            else {
				/**
				 * DO_ACTION: yith_wcfm_print_section_unauthorized
				 *
				 * Print anauthorized section.
				 *
				 * @param string $section_id The section id
				 *
				 */
                do_action( 'yith_wcfm_print_section_unauthorized', $this->id );
            }
		}

		/**
		 * WP Enqueue Scripts
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function enqueue_section_scripts() {
			$suffix     = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) || ! empty( $_GET['yith_debug'] ) ? '' : '.min';

			// CSS
			wp_enqueue_style( 'yith-wcfm-coupons', YITH_WCFM_URL . 'assets/css/coupons.css', array(), YITH_WCFM_VERSION );
            wp_enqueue_style( 'select2' );
            wp_enqueue_style( 'woocommerce_admin_styles' );
            wp_enqueue_style( 'jquery-ui-style' );

            // JS
			$tooltip_script_handle = $selectWoo_script_handle = '';

			if ( YITH_Frontend_Manager()->is_wc_3_2_or_greather ) {
				$tooltip_script_handle   = 'jquery-tiptip';
				$selectWoo_script_handle = 'selectWoo';
			}

			else {
				$tooltip_script_handle   = 'wc-tooltip';
				$selectWoo_script_handle = 'select2';
			}

            wp_enqueue_script( $selectWoo_script_handle );
            wp_enqueue_script( 'wc-enhanced-select' );
            wp_enqueue_script( $tooltip_script_handle );
            wp_enqueue_script('wc-admin-meta-boxes');

			wp_enqueue_script( 'yith-frontend-manager-coupons-js', YITH_WCFM_URL . "assets/js/yith-frontend-manager-coupons{$suffix}.js", array( 'jquery', $selectWoo_script_handle ), YITH_WCFM_VERSION, true );
			wp_localize_script( 'yith-frontend-manager-coupons-js', 'yith_wcfm_coupons', array( 'is_wc_3_2_or_greather' => YITH_Frontend_Manager()->is_wc_3_2_or_greather ) );
		}

		/**
         * Delete coupon (set it to trash)
         *
         * @since 1.0
         * @return void
         */
		public static function delete( $code ){
            $code = '';
            if( isset( $_POST['post_title'] ) ){
                $code = $_POST['post_title'];
            }

            elseif( isset( $_GET['code'] ) ) {
                $code = $_GET['code'];
            }

			$coupon        = new WC_Coupon( $code );
			$coupon_id     = wc_get_coupon_id_by_code( $code );
			$coupon_exists = ! empty( $coupon_id );
			$deleted       = false;

            if( $coupon instanceof WC_Coupon && $coupon_exists ){
				/**
				 * APPLY_FILTERS: yith_wcfm_force_delete_coupon
				 *
				 * Filter force delete coupon.
				 *
				 * @param bool $force_delete Force delete coupon.
				 * @return bool
				 */
                $force_delete = apply_filters( 'yith_wcfm_force_delete_coupon', false );
                $deleted = $coupon->delete( $force_delete );
            }

            if( $deleted ) {
                wc_print_notice( __('Coupon deleted.', 'yith-frontend-manager-for-woocommerce'), 'success' );
            }

            elseif( $coupon_exists && ! $deleted ) {
                wc_print_notice( __('Unable to delete the coupon.', 'yith-frontend-manager-for-woocommerce'), 'error' );
            }

            else{
                wc_print_notice( __( "The coupon doesn't exist.", 'yith-frontend-manager-for-woocommerce'), 'error' );
            }
        }

	}

}
