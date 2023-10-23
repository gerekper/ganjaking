<?php
/**
 * Manage install, and performs all post update operations
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes
 * @version 4.0.0
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Install_Premium' ) ) {
	/**
	 * Filter Presets Handling
	 *
	 * @since 4.0.0
	 */
	class YITH_WCAN_Install_Premium extends YITH_WCAN_Install_Extended {

		/**
		 * Hooks methods required to install/update plugin
		 *
		 * @return void
		 */
		public static function init() {
			parent::init();

			// premium specific upgrades.
			add_action( 'yith_wcan_did_410_upgrade', array( __CLASS__, 'maybe_update_filters' ) );
		}

		/**
		 * Update review filters, to set a default design
		 *
		 * @return void.
		 */
		public static function maybe_update_filters() {
			$presets = YITH_WCAN_Preset_Factory::get_presets();

			foreach ( $presets as $preset ) {
				if ( ! $preset->has_filters() ) {
					continue;
				}

				$fuzzy           = false;
				$filters_to_save = array();

				foreach ( $preset->get_filters() as $filter_id => $filter ) {
					$type = $filter->get_type();

					if ( 'review' === $type ) {
						$filter->set_filter_design( 'select' );
						$fuzzy = true;
					} elseif ( 'price_range' === $type ) {
						$filter->set_filter_design( 'text' );
						$fuzzy = true;
					} elseif ( 'tax' === $type && 'term_order' === $filter->get_order_by() ) {
						$filter->set_order_by( 'include' );
						$fuzzy = true;
					}

					$filters_to_save[] = $filter->get_data();
				}

				if ( ! $fuzzy ) {
					continue;
				}

				$preset->set_filters( $filters_to_save );
				$preset->save();
			}
		}

		/**
		 * Generates default filters for the preset created on first installation of the plugin
		 *
		 * @return array Array of filters.
		 */
		protected static function get_default_filters() {
			$filters = parent::get_default_filters();

			// set additional filters.
			$filters[] = self::get_price_filter();
			$filters[] = self::get_review_filter();
			$filters[] = self::get_sale_stock_filter();
			$filters[] = self::get_orederby_filter();

			return apply_filters( 'yith_wcan_default_filters', $filters );
		}

		/**
		 * Generates default Price filter for the preset created on first installation of the plugin
		 *
		 * @return array Filter options.
		 */
		protected static function get_price_filter() {
			global $wpdb;

			$filter = new YITH_WCAN_Filter_Price_Slider();

			// lookup for max product price.
			$max_price = $wpdb->get_var( "SELECT MAX(max_price) FROM {$wpdb->prefix}wc_product_meta_lookup" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$step      = max( (int) $max_price / 10, 1 );

			$filter->set_title( _x( 'Filter by price', '[ADMIN] Name of default price filter created by plugin', 'yith-woocommerce-ajax-navigation' ) );
			$filter->set_show_toggle( 'no' );
			$filter->set_price_slider_min( 0 );
			$filter->set_price_slider_max( ceil( $max_price ) );
			$filter->set_price_slider_step( floor( $step ) );

			return $filter->get_data();
		}

		/**
		 * Generates default Review filter for the preset created on first installation of the plugin
		 *
		 * @return array Filter options.
		 */
		protected static function get_review_filter() {
			$filter = new YITH_WCAN_Filter_Review();

			$filter->set_title( _x( 'Filter by review', '[ADMIN] Name of default review filter created by plugin', 'yith-woocommerce-ajax-navigation' ) );
			$filter->set_show_toggle( 'no' );
			$filter->set_show_count( 'no' );
			$filter->set_adoptive( 'hide' );

			return $filter->get_data();
		}

		/**
		 * Generates default Stock/Sale filter for the preset created on first installation of the plugin
		 *
		 * @return array Filter options.
		 */
		protected static function get_sale_stock_filter() {
			$filter = new YITH_WCAN_Filter_Stock_Sale();

			$filter->set_title( _x( 'Additional filters', '[ADMIN] Name of default stock/sale filter created by plugin', 'yith-woocommerce-ajax-navigation' ) );
			$filter->set_show_toggle( 'no' );
			$filter->set_show_count( 'no' );
			$filter->set_adoptive( 'hide' );

			return $filter->get_data();
		}

		/**
		 * Generates default Orderby filter for the preset created on first installation of the plugin
		 *
		 * @return array Filter options.
		 */
		protected static function get_orederby_filter() {
			$filter = new YITH_WCAN_Filter_Orderby();

			$filter->set_title( _x( 'Order by', '[ADMIN] Name of default order by filter created by plugin', 'yith-woocommerce-ajax-navigation' ) );
			$filter->set_show_toggle( 'no' );
			$filter->set_order_options( array_keys( YITH_WCAN_Filter_Factory::get_supported_orders() ) );

			return $filter->get_data();
		}

	}
}
