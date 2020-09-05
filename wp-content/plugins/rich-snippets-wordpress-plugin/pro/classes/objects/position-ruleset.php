<?php

namespace wpbuddy\rich_snippets\pro;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Class Position_Ruleset
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.0.0
 */
class Position_Ruleset extends \wpbuddy\rich_snippets\Position_Ruleset {

	/**
	 * Adds a new rulegroup.
	 *
	 * @param Position_Rule_Group $group
	 *
	 * @since 2.0.0
	 *
	 */
	public function add_rulegroup( Position_Rule_Group $group ) {

		$this->rulegroups[] = $group;
	}


	/**
	 * Checks if the Ruleset has roulegroups.
	 *
	 * @return bool
	 * @since 2.0.0
	 *
	 */
	public function has_rulegroups(): bool {

		return $this->count() > 0;
	}


	/**
	 * Get the rulegroup count.
	 *
	 * @return int
	 * @since 2.0.0
	 *
	 */
	public function count(): int {

		return count( $this->rulegroups );
	}


	/**
	 * Returns rule groups.
	 *
	 * @return Position_Rule_Group[]
	 */
	public function get_rulegroups(): array {

		return $this->rulegroups;
	}


	/**
	 * Checks if the rule groups matches.
	 *
	 * @return bool
	 * @since 2.0.0
	 *
	 */
	public function match(): bool {

		/**
		 * Ruleset match filter.
		 *
		 * Bail early on ruleset match.
		 *
		 * @hook  wpbuddy/rich_snippets/ruleset/match
		 *
		 * @param {bool|null} $bail_early If and how to bail early.
		 * @param {Position_Ruleset} $ruleset The current ruleset object.
		 *
		 * @returns {bool|null} If NULL, default behaviour is turned ON. Otherwise bail early with true or false.
		 *
		 * @since 2.0.0
		 */
		$bail_early = apply_filters( 'wpbuddy/rich_snippets/ruleset/match', null, $this );

		if ( is_bool( $bail_early ) ) {
			return $bail_early;
		}

		if ( ! $this->has_rulegroups() ) {
			return false;
		}

		foreach ( $this->get_rulegroups() as $rule_group ) {

			/**
			 * Every rule group is connected with OR.
			 * This means we can return true immaterially if one rule group returns true.
			 */
			if ( $rule_group->match() ) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Returns JSON representation of the Ruleset.
	 *
	 * @return string
	 * @since 2.14.0
	 *
	 */
	public function __toString() {
		$data = [];

		$i = - 1;
		foreach ( $this->get_rulegroups() as $rule_group ) {
			$i ++;
			foreach ( $rule_group->get_rules() as $rule ) {
				$data[ $i ][] = [
					'param'    => $rule->param,
					'operator' => $rule->operator,
					'value'    => $rule->value,
				];
			}
		}

		return json_encode( $data );
	}
}