<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'WC_AF_Rules' ) ) {
	class WC_AF_Rules {

		private static $instance = null;

		private $rules;

		private function __construct() {

		}

		/**
		 * Returns the only possible instance of WC_AF_Rules
		 * @return WC_AF_Rules
		 */
		public static function get() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * No clones
		 */
		private function __clone() {
		}

		/**
		 * Add a WC_AF_Rule object to the rules map
		 *
		 * @param WC_AF_Rule $rule
		 */
		public function add_rule( WC_AF_Rule $rule ) {
			$this->rules[] = $rule;
		}

		/**
		 * Get the rules
		 *
		 * @return array<WC_AF_Rule>
		 */
		public function get_rules() {
			return apply_filters( 'wc_anti_fraud_rules', $this->rules );
		}

		/**
		 * Get a Rule object from JSON
		 *
		 * @param $json
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @return WC_AF_Rule
		 */
		public function get_rule_from_json( $json ) {

			// The Rule this method returns
			$rule = null;

			// Create the JSON object
			$generic_object = json_decode( $json );

			// Version 1.0.1 encoded id 'proxy' for 'detect proxy' rule violations when it should
			// have encoded 'detect_proxy'.  Unfortunately, this caused viewing orders with this
			// rule violation in wp-admin to be mostly broken.  This was fixed in 1.0.2, but to
			// support backwards compatibility with 1.0.1 orders meta, we replace 'proxy' with
			// 'detect_proxy' here
			if ( 'proxy' === $generic_object->id ) {
				$generic_object->id = 'detect_proxy';
			}

			// Create the class name
			$class_name = 'WC_AF_Rule_' . implode( '_', array_map( 'ucfirst', explode( '_', $generic_object->id ) ) );

			// Check if class exists
			if ( class_exists( $class_name ) ) {

				// Create Rule object
				$rule = new $class_name;

				// Check if need to set a label
				if ( isset( $generic_object->label ) ) {
					$rule->set_label( $generic_object->label );
				}

			}

			// Return the Rule object
			return $rule;
		}

	}
}