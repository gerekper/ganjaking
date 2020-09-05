<?php

namespace wpbuddy\rich_snippets\pro;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Class Position_Rule_Group
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.0.0
 */
class Position_Rule_Group extends \wpbuddy\rich_snippets\Position_Rule_Group {

	/**
	 * Adds a rule to the array of ruels.
	 *
	 * @param Position_Rule $rule
	 *
	 * @since 2.0.0
	 *
	 */
	public function add_rule( Position_Rule $rule ) {

		$this->rules[] = $rule;
	}


	/**
	 * Get all rules.
	 *
	 * @return Position_Rule[]
	 * @since 2.0.0
	 *
	 */
	public function get_rules(): array {

		return $this->rules;
	}


	/**
	 * Returns the number of rules.
	 *
	 * @return int
	 * @since 2.0.0
	 *
	 */
	public function count(): int {

		return count( $this->rules );
	}


	/**
	 * Checks if all rules matches.
	 *
	 * @return bool
	 * @since 2.0.0
	 *
	 */
	public function match(): bool {

		/**
		 * Position Rulegroup match filter.
		 *
		 * Allows to bail early for a position rulegroup.
		 *
		 * @hook  wpbuddy/rich_snippets/rulegroup/match
		 *
		 * @param {bool|null} $bail_early If and how to bail early.
		 * @param {Position_Rule_Group} $rulegroup The current rule group object.
		 *
		 * @returns {bool|null} NULL if default behaviour; true|false if bail early.
		 *
		 * @since 2.0.0
		 */
		$bail_early = apply_filters( 'wpbuddy/rich_snippets/rulegroup/match', null, $this );

		if ( is_bool( $bail_early ) ) {
			return $bail_early;
		}

		/**
		 * All rules are connected with AND.
		 * This means we don't need to run through every rule.
		 * We can return false immediately if one rule returns false.
		 */
		foreach ( $this->get_rules() as $rule ) {
			if ( ! $rule->match() ) {
				return false;
			}
		}

		return true;
	}
}