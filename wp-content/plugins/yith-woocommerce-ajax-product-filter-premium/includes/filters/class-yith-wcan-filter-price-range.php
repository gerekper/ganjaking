<?php
/**
 * Price Range filter class
 *
 * Offers method specific to Price Range filter
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes\Filters
 * @version 4.16.0
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Filter_Price_Range' ) ) {
	/**
	 * Price Range Filter Handling
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAN_Filter_Price_Range extends YITH_WCAN_Filter {

		/**
		 * Filter type
		 *
		 * @var string
		 */
		protected $type = 'price_range';

		/**
		 * List of formatted ranges for current view
		 *
		 * @var array
		 */
		protected $formatted_ranges;

		/**
		 * Checks if filter is relevant to current product selection
		 *
		 * @return bool Whether filter is relevant or not.
		 */
		public function is_relevant() {
			return apply_filters( 'yith_wcan_is_filter_relevant', $this->is_enabled() && $this->has_relevant_ranges(), $this );
		}

		/**
		 * Render start for the filter section
		 *
		 * @return string Header template.
		 */
		public function render_start() {
			$atts = array(
				'filter' => $this,
				'preset' => $this->get_preset(),
			);

			return yith_wcan_get_template( 'filters/filter-price-range/filter-start.php', $atts, false );
		}

		/**
		 * Render end for the filter section
		 *
		 * @return string Footer template.
		 */
		public function render_end() {
			$atts = array(
				'filter' => $this,
				'preset' => $this->get_preset(),
			);

			return yith_wcan_get_template( 'filters/filter-price-range/filter-end.php', $atts, false );
		}

		/**
		 * Render every single item in the list
		 *
		 * @param array $range Array of formatted range.
		 *
		 * @return string Item template.
		 */
		public function render_item( $range ) {
			$design             = $this->get_filter_design();
			$max                = $range['unlimited'] ? '' : "-{$range['max']}";
			$count              = isset( $range['count'] ) ? $range['count'] : $this->get_range_count( $range );
			$additional_classes = array();

			if ( ! $count && 'or' === $this->get_adoptive() ) {
				$additional_classes[] = 'disabled';
			}

			$atts = array(
				'filter'             => $this,
				'preset'             => $this->get_preset(),
				'range'              => $range,
				'formatted_range'    => "{$range['min']}{$max}",
				'additional_classes' => implode( ' ', apply_filters( 'yith_wcan_filter_price_range_additional_item_classes', $additional_classes ) ),
				'show_count'         => $this->show_count(),
				'item_id'            => "filter_{$this->get_preset()->get_id()}_{$this->get_id()}_price_range_{$range['min']}{$max}",
				'item_name'          => "filter[{$this->get_preset()->get_id()}][{$this->get_id()}]",
			);

			return yith_wcan_get_template( "filters/filter-price-range/items/{$design}.php", $atts, false );
		}

		/**
		 * Render range count
		 * Wrapper for self::render_count method
		 *
		 * @param array $range Current range.
		 * @return string HTML template for the count
		 */
		public function render_range_count( $range ) {
			$count = isset( $range['count'] ) ? $range['count'] : $this->get_range_count( $range );

			return $this->render_count( $count );
		}

		/**
		 * Returns formatted price ranges
		 *
		 * @param array $range Range to format.
		 * @return string Formatted range label.
		 */
		public function render_formatted_range( $range ) {
			if ( $this->is_last_range( $range ) && $range['unlimited'] ) {
				$formatted_range = sprintf(
				// translators: 1. Min price of the range (formatted HTML).
					_x( '%1$s & above', '[FRONTEND] Price range option: 1. Min value.', 'yith-woocommerce-ajax-navigation' ),
					wc_price( $range['min'] )
				);
			} else {
				$formatted_range = sprintf(
				// translators: 1. Min price of the range (formatted HTML). 2. Max price of the range (formatted HTML).
					_x( '%1$s - %2$s', '[FRONTEND] Price range option: 1. Min value. 2. Max value', 'yith-woocommerce-ajax-navigation' ),
					wc_price( $range['min'] ),
					wc_price( $range['max'] )
				);
			}

			return wp_kses_post( apply_filters( 'yith_wcan_formatted_price_range', $formatted_range, $range ) );
		}

		/* === PRICE RANGES METHODS === */

		/**
		 * Checks whther passed range is the last one defined
		 *
		 * @param array $range Range to test.
		 * @return bool Whether range is last in set.
		 */
		public function is_last_range( $range ) {
			$ranges     = $this->get_price_ranges();
			$last_range = array_pop( $ranges );

			return $range['min'] === $last_range['min'] && $range['max'] === $last_range['max'];
		}

		/**
		 * Checks whether there are ranges relevant to current query
		 *
		 * @return bool Result of the test.
		 */
		public function has_relevant_ranges() {
			return ! ! $this->get_formatted_ranges();
		}

		/**
		 * Retrieve formatted ranges
		 *
		 * @return array Array of formatted ranges.
		 */
		public function get_formatted_ranges() {
			if ( ! empty( $this->formatted_ranges ) ) {
				return $this->formatted_ranges;
			}

			$ranges = $this->get_price_ranges();
			$result = array();

			if ( ! empty( $ranges ) ) {
				foreach ( $ranges as $range ) {
					// malformed.
					if ( ! isset( $range['min'] ) || ! isset( $range['max'] ) ) {
						continue;
					}

					$range['count'] = $this->get_range_count( $range );

					// hidden item.
					if ( ! $range['count'] && 'hide' === $this->get_adoptive() ) {
						continue;
					}

					// set additional classes.
					$range['additional_classes'] = array();

					if ( $this->is_range_active( $range ) ) {
						$range['additional_classes'][] = 'active';
					}

					if ( ! $range['count'] ) {
						$range['additional_classes'][] = 'disabled';
					}

					$range['additional_classes'] = implode( ' ', $range['additional_classes'] );

					$result[] = $range;
				}
			}

			$this->formatted_ranges = $result;

			return $result;
		}

		/**
		 * Checks whether we're currently filtering for a specific price range
		 *
		 * @param array $range Expects an array that contains min/max indexes for the range ends.
		 * @return bool Whether that range is active or not
		 */
		public function is_range_active( $range ) {
			return YITH_WCAN_Query()->is_price_range( $range );
		}

		/**
		 * Retrieves url to filter by the passed price range
		 *
		 * @param array $range Price range to check.
		 * @return string Url to filter by specified parameter.
		 */
		public function get_filter_url( $range ) {
			$param = array(
				'min_price' => $range['min'],
			);

			if ( ! $range['unlimited'] ) {
				$param['max_price'] = $range['max'];
			}

			if ( $this->is_range_active( $range ) ) {
				$url = YITH_WCAN_Query()->get_filter_url( array(), $param );
			} else {
				$url = YITH_WCAN_Query()->get_filter_url( $param );
			}

			return $url;
		}

		/**
		 * Returns count of products within a specific price range, for current query
		 *
		 * @param array $range Array containing min and max for the price range.
		 *
		 * @return int Items count
		 */
		public function get_range_count( $range ) {
			return YITH_WCAN_Query()->count_query_relevant_price_range_products( $range );
		}
	}
}
