<?php
/**
 * Price Slider filter class
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

if ( ! class_exists( 'YITH_WCAN_Filter_Price_Slider' ) ) {
	/**
	 * Price Slider Filter Handling
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAN_Filter_Price_Slider extends YITH_WCAN_Filter {

		/**
		 * Filter type
		 *
		 * @var string
		 */
		protected $type = 'price_slider';

		/**
		 * Stores minimum value for current slider.
		 *
		 * @var float
		 */
		protected $real_min;

		/**
		 * Stores maximum value for currents slider.
		 *
		 * @var float
		 */
		protected $real_max;

		/**
		 * Checks if filter is relevant to current product selection
		 *
		 * @return bool Whether filter is relevant or not.
		 */
		public function is_relevant() {
			return apply_filters( 'yith_wcan_is_filter_relevant', $this->is_enabled() && $this->get_price_slider_max(), $this );
		}

		/**
		 * Returns slider minimum, using value set, or product minimum price, if "adaptive" limits were enabled
		 *
		 * @return float Minimum value to use for the slider, independent from current filtering.
		 */
		public function get_real_min() {
			if ( ! is_null( $this->real_min ) ) {
				return $this->real_min;
			}

			if ( ! $this->use_price_slider_adaptive_limits() ) {
				$this->real_min = $this->get_price_slider_min();
			} else {
				$this->real_min = (float) YITH_WCAN_Query()->get_query_relevant_min_price();
			}

			return $this->real_min;
		}

		/**
		 * Returns slider maximum, using value set, or product maximum price, if "adaptive" limits were enabled
		 *
		 * @return float Minimum value to use for the slider, independent from current filtering.
		 */
		public function get_real_max() {
			if ( ! is_null( $this->real_max ) ) {
				return $this->real_max;
			}

			if ( ! $this->use_price_slider_adaptive_limits() ) {
				$this->real_max = $this->get_price_slider_max();
			} else {
				$this->real_max = (float) YITH_WCAN_Query()->get_query_relevant_max_price();
			}

			return $this->real_max;
		}

		/**
		 * Returns current minimum value of the price range
		 *
		 * @return float Current minimum value of the price range.
		 */
		public function get_current_min() {
			return (float) YITH_WCAN_Query()->get( 'min_price', $this->get_real_min() );
		}

		/**
		 * Returns current maximum value of the price range
		 *
		 * @return float Current maximum value of the price range.
		 */
		public function get_current_max() {
			return (float) YITH_WCAN_Query()->get( 'max_price', $this->get_real_max() );
		}
	}
}
