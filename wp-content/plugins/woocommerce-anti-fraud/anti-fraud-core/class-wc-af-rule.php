<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! interface_exists( 'WC_AF_iRule' ) ) {
	interface WC_AF_iRule {

		/**
		 * Check if the current rule finds a risk in order. The method must return a boolean.
		 *
		 * @param WC_Order $order
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @return bool
		 */
		public function is_risk( WC_Order $order );
	}
}

if ( ! class_exists( 'WC_AF_Rule' ) ) {
	abstract class WC_AF_Rule implements WC_AF_iRule {

		private $id;
		private $label;
		private $risk_points;

		/**
		 * Constructor
		 *
		 * @param $id
		 * @param $label
		 * @param $risk_points
		 *
		 * @since  1.0.0
		 * @access public
		 */
		function __construct( $id, $label, $risk_points ) {
			$this->id          = $id;
			$this->label       = $label;
			$this->risk_points = $risk_points;
		}

		/**
		 * @since  1.0.0
		 * @access public
		 *
		 * @return string
		 */
		public function get_id() {
			return $this->id;
		}

		/**
		 * @param $id
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 */
		public function set_id( $id ) {
			$this->id = $id;
		}

		/**
		 * @since  1.0.0
		 * @access public
		 *
		 * @return string
		 */
		public function get_label() {
			return $this->label;
		}

		/**
		 * @param $label
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 */
		public function set_label( $label ) {
			$this->label = $label;
		}

		/**
		 * @since  1.0.0
		 * @access public
		 *
		 * @return int
		 */
		public function get_risk_points() {
			return $this->risk_points;
		}

		/**
		 * @param $risk_points
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 */
		public function set_risk_points( $risk_points ) {
			$this->risk_points = $risk_points;
		}

		/**
		 * Create JSON object containing rule data
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 */
		public function to_json() {
			return json_encode( array( 'id' => $this->get_id(), 'label' => $this->get_label() ) );
		}

	}
}

