<?php

namespace WPMailSMTP\Pro\ConditionalLogic;

/**
 * Trait CanProcessConditionalLogicTrait.
 *
 * @since 3.7.0
 */
trait CanProcessConditionalLogicTrait {

	/**
	 * Process conditional rules.
	 *
	 * @since 3.7.0
	 *
	 * @param array $conditionals List of conditionals.
	 * @param array $values       List of values.
	 *
	 * @return bool
	 */
	public function process_conditionals( $conditionals, $values ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded, Generic.Metrics.NestingLevel.MaxExceeded

		if ( empty( $conditionals ) ) {
			return true;
		}

		$pass = false;

		foreach ( $conditionals as $group ) {
			$pass_group = true;

			if ( ! empty( $group ) ) {
				foreach ( $group as $rule ) {
					if ( empty( $rule['property'] ) || empty( $rule['operator'] ) ) {
						continue;
					}

					$rule_property = $rule['property'];
					$rule_operator = $rule['operator'];
					$rule_value    = isset( $rule['value'] ) ? strtolower( trim( $rule['value'] ) ) : '';

					$actual_values = [];

					if ( isset( $values[ $rule_property ] ) ) {
						$actual_values = array_map( 'strtolower', array_map( 'trim', (array) $values[ $rule_property ] ) );
					}

					$pass_rule = false;

					foreach ( $actual_values as $actual_value ) {
						if ( $this->process_rule( $actual_value, $rule_value, $rule_operator ) ) {
							$pass_rule = true;

							break;
						}
					}

					if ( ! $pass_rule ) {
						$pass_group = false;

						break;
					}
				}
			}

			if ( $pass_group ) {
				$pass = true;
			}
		}

		return $pass;
	}

	/**
	 * Process conditional rule.
	 *
	 * @since 3.7.0
	 *
	 * @param string $left     Actual value.
	 * @param string $right    Rule value.
	 * @param string $operator Rule operator.
	 *
	 * @return bool
	 */
	private function process_rule( $left, $right, $operator ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		switch ( $operator ) {
			case '==':
				$pass_rule = $left === $right;
				break;

			case '!=':
				$pass_rule = $left !== $right;
				break;

			case 'c':
				$pass_rule = ( strpos( $left, $right ) !== false );
				break;

			case '!c':
				$pass_rule = ( strpos( $left, $right ) === false );
				break;

			case '^':
				$pass_rule = ( strrpos( $left, $right, - strlen( $left ) ) !== false );
				break;

			case '~':
				// phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
				$pass_rule = ( ( $temp = strlen( $left ) - strlen( $right ) ) >= 0 && strpos( $left, $right, $temp ) !== false );
				break;

			default:
				$pass_rule = false;
				break;
		}

		return $pass_rule;
	}
}
