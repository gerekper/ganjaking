<?php
/**
 * Storefront Powerpack Frontend Composite Product Details Class
 *
 * @author   WooThemes
 * @package  Storefront_Powerpack
 * @since    1.4.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Frontend_Composite_Product_Details' ) ) :

	/**
	 * The Frontend class.
	 */
	class SP_Frontend_Composite_Product_Details extends SP_Frontend {

		/**
		 * Setup class.
		 */
		public function __construct() {

			// Filter component options loop columns.
			add_filter( 'woocommerce_composite_component_loop_columns', 		array( $this, 'cp_component_options_loop_columns' ), 5, 3 );

			// Filter max component options per page.
			add_filter( 'woocommerce_component_options_per_page', 				array( $this, 'cp_component_options_per_page' ), 5 );

			// Filter max component columns in review/summary.
			add_filter( 'woocommerce_composite_component_summary_max_columns', 	array( $this, 'cp_summary_max_columns' ), 5 );

			// Filter toggle-box view.
			add_filter( 'woocommerce_composite_component_toggled', 				array( $this, 'cp_component_toggled' ), 5, 3 );
		}

		/**
		 * Number of component option columns when the Thumbnails setting is active.
		 *
		 * @param  integer     $cols          Product columns.
		 * @param  string      $component_id  Component ID.
		 * @param  WC_Product  $composite     Composite product object.
		 * @return integer
		 */
		public function cp_component_options_loop_columns( $cols, $component_id, $composite ) {
			$sp_cols = get_theme_mod( 'sp_cp_component_options_loop_columns' );
			return $sp_cols ? $sp_cols : $cols;
		}

		/**
		 * Number of component options per page when the Thumbnails setting is active.
		 *
		 * @param  integer $num products per page.
		 * @return integer
		 */
		public function cp_component_options_per_page( $num ) {
			$sp_num = get_theme_mod( 'sp_cp_component_options_per_page' );
			return $sp_num ? $sp_num : $num;
		}

		/**
		 * Max number of Review/Summary columns when a Multi-page layout is active.
		 *
		 * @param  integer $max_cols maximum columns.
		 * @return integer
		 */
		public function cp_summary_max_columns( $max_cols ) {
			$sp_max_cols = get_theme_mod( 'sp_cp_summary_max_columns' );
			return $sp_max_cols ? $sp_max_cols : $max_cols;
		}

		/**
		 * Enable/disable the toggle-box component view when a Single-page layout is active.
		 *
		 * @param boolean $show_toggle toggle boolean.
		 * @param string  $component_id the component id.
		 * @param array   $product the product.
		 * @return boolean
		 */
		public function cp_component_toggled( $show_toggle, $component_id, $product ) {

			$sp_show_toggle = get_theme_mod( 'sp_cp_component_toggled' );
			$style          = $product->get_composite_layout_style();

			if ( $style === $sp_show_toggle || 'both' === $sp_show_toggle ) {
				$show_toggle = true;
			} elseif ( 'none' === $sp_show_toggle ) {
				$show_toggle = false;
			}

			return $show_toggle;
		}
	}

endif;

return new SP_Frontend_Composite_Product_Details();
