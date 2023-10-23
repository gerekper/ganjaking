<?php
/**
 * Review filter class
 *
 * Offers method specific to Review filter
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes\Filters
 * @version 4.16.0
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Filter_Review' ) ) {
	/**
	 * OrderBy Filter Handling
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAN_Filter_Review extends YITH_WCAN_Filter {

		/**
		 * Filter type
		 *
		 * @var string
		 */
		protected $type = 'review';

		/**
		 * List of formatted review ratings for current view
		 *
		 * @var array
		 */
		protected $formatted_rates;

		/**
		 * Checks if filter is relevant to current product selection
		 *
		 * @return bool Whether filter is relevant or not.
		 */
		public function is_relevant() {
			return apply_filters( 'yith_wcan_is_filter_relevant', $this->is_enabled() && $this->has_relevant_rates(), $this );
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

			return yith_wcan_get_template( 'filters/filter-review/filter-start.php', $atts, false );
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

			return yith_wcan_get_template( 'filters/filter-review/filter-end.php', $atts, false );
		}

		/**
		 * Render every single item in the list
		 *
		 * @param array $rate Array of formatted rate.
		 *
		 * @return string Item template.
		 */
		public function render_item( $rate ) {
			$design = $this->get_filter_design();

			$atts = array(
				'filter'             => $this,
				'preset'             => $this->get_preset(),
				'rate'               => $rate,
				'additional_classes' => implode( ' ', apply_filters( 'yith_wcan_filter_review_additional_item_classes', array() ) ),
				'show_count'         => $this->show_count(),
				'allow_multiple'     => 'yes' === $this->get_multiple(),
				'adoptive'           => $this->get_adoptive(),
				'item_id'            => "filter_{$this->get_preset()->get_id()}_{$this->get_id()}_rating_{$rate['rate']}",
				'item_name'          => "filter[{$this->get_preset()->get_id()}][{$this->get_id()}]",
			);

			return yith_wcan_get_template( "filters/filter-review/items/{$design}.php", $atts, false );
		}

		/**
		 * Render count for them each review rating
		 *
		 * @param int $rate Review rating to count.
		 * @return string Count template
		 */
		public function render_review_rate_count( $rate ) {
			$count = is_int( $rate ) ? $this->get_review_rate_count( $rate ) : $rate['count'];

			return $this->render_count( $count );
		}

		/**
		 * Checks whether there are review rates relevant to current query
		 *
		 * @return bool Result of the test.
		 */
		public function has_relevant_rates() {
			return ! ! $this->get_formatted_review_rates();
		}

		/**
		 * Retrieve formatted rates
		 *
		 * @return array Array of formatted ranges.
		 */
		public function get_formatted_review_rates() {
			if ( ! empty( $this->formatted_rates ) ) {
				return $this->formatted_rates;
			}

			$result = array();

			for ( $i = 5; $i > 0; $i-- ) {
				$rating = array(
					'rate' => $i,
				);

				$rating['count'] = $this->get_review_rate_count( $i );

				// hidden item.
				if ( ! $rating['count'] && 'hide' === $this->get_adoptive() ) {
					continue;
				}

				// set additional classes.
				$rating['additional_classes'] = array();

				if ( $this->is_review_rate_active( $i ) ) {
					$rating['additional_classes'][] = 'active';
				}

				if ( ! $rating['count'] ) {
					$rating['additional_classes'][] = 'disabled';
				}

				$rating['additional_classes'] = implode( ' ', $rating['additional_classes'] );

				$result[] = $rating;
			}

			$this->formatted_rates = $result;

			return $result;
		}

		/**
		 * Retrieves url to filter by the passed review rate
		 *
		 * @param int $rate Review rate to check.
		 * @return string Url to filter by specified parameter.
		 */
		public function get_filter_url( $rate ) {
			$param = array( 'rating_filter' => (array) $rate );

			if ( $this->is_review_rate_active( $rate ) ) {
				$url = YITH_WCAN_Query()->get_filter_url( array(), $param );
			} else {
				$url = YITH_WCAN_Query()->get_filter_url( $param, array(), 'or' );
			}

			return $url;
		}

		/**
		 * Checks if we're filtering by a specific review rate
		 *
		 * @param int $rate Review rate to check.
		 * @return bool Whether that rate is active or not
		 */
		public function is_review_rate_active( $rate ) {
			return YITH_WCAN_Query()->is_review_rate( $rate );
		}

		/**
		 * Returns count of products with a specific review rating for current query
		 *
		 * @param int $rate Review rating to test.
		 *
		 * @return int Items count
		 */
		public function get_review_rate_count( $rate ) {
			return YITH_WCAN_Query()->count_query_relevant_rated_products( $rate );
		}
	}
}
