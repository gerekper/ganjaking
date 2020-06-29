<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WC_AF_Score' ) ) {

	class WC_AF_Score {

		private $order_id;
		private $order = null;

		private $score = 0;

		private $passed_rules = array();
		private $failed_rules = array();
		private $max_weight   = 20;

		/**
		 * Constructor
		 *
		 * @param $order_id
		 */
		public function __construct( $order_id ) {
			$this->order_id = $order_id;
		}

		/**
		 * Load the order into class property.
		 * We're not doing this on construct because of performance reasons.
		 */
		private function load_order() {
			if ( null == $this->order ) {
				$this->order = wc_get_order( $this->order_id );
			}
		}

		/**
		 * Calculates the risk score the order has.
		 */
		public function calculate() {
			// Load the order
			$this->load_order();

			// Get the rules
			$rules = WC_AF_Rules::get()->get_rules();

			// Set points
			$score = 100;
			$risk_points = 0;
			// Count the rules
			if ( count( $rules ) > 0 ) {

				// Loop through the rules
				foreach ( $rules as $rule ) {

					// Check if the rule reported a risk
					if ( true === $rule->is_enabled() && true === $rule->is_risk( $this->order ) ) {
						//$risk_points += $rule->get_risk_points();
						$score -= $rule->get_risk_points();
						$this->failed_rules[] = $rule;
					} else {
						$this->passed_rules[] = $rule;
					}

				}

			}
			//$score = ($this->max_weight*100)/$risk_points;
			// Calculate score
			//$this->score = absint( $score );
			$this->score = $score ;
		}

		/**
		 * Get the score
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @return int
		 */
		public function get_score() {
			return $this->score;
		}


		/**
		 * Get passed rules
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @return array
		 */
		public function get_passed_rules() {
			return $this->passed_rules;
		}

		/**
		 * Get failed rules
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @return array
		 */
		public function get_failed_rules() {
			return $this->failed_rules;
		}

	}

}
