<?php

namespace wpbuddy\rich_snippets\pro;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Rules.
 *
 * Functions to read and write Rulesets.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.0.0
 */
final class Rules_Model {

	/**
	 * Fetches the Rulset.
	 *
	 * This is an array of groups where each array is connected as OR.
	 * Ex.:
	 * array(
	 *      0 => Rule connected with AND
	 *      1 => array( Ruleset connected with OR
	 *              1 => Rule connected with AND
	 *              2 => Rule connected with AND
	 *      )
	 * )
	 *
	 * @param int $post_id
	 *
	 * @return Position_Ruleset
	 * @since 2.0.0
	 *
	 */
	public static function get_ruleset( int $post_id ): Position_Ruleset {

		/**
		 * Ruleset filter.
		 *
		 * Allows to overwrite a ruleset for a given Global Snippet.
		 *
		 * @hook  wpbuddy/rich_snippets/ruleset/get
		 *
		 * @param {Position_Ruleset|null} $ruleset The new ruleset.
		 * @param {int} $post_id The post ID.
		 * @returns {Position_Ruleset|null} The new ruleset or NULL if default ruleset should be loaded.
		 *
		 * @since 2.0.0
		 */
		$ruleset = apply_filters( 'wpbuddy/rich_snippets/ruleset/get', null, $post_id );

		if ( $ruleset instanceof Position_Ruleset ) {
			return $ruleset;
		}

		$ruleset = get_post_meta( $post_id, '_wpb_rs_position', true );

		if ( $ruleset instanceof \wpbuddy\rich_snippets\Position_Ruleset ) {
			$ruleset = $ruleset->transform();
		}

		if ( $ruleset instanceof Position_Ruleset ) {
			return $ruleset;
		}

		return new Position_Ruleset();
	}


	/**
	 * Updates a ruleset.
	 *
	 * @param int $post_id
	 * @param Position_Ruleset $ruleset
	 *
	 * @return bool
	 * @since 2.0.0
	 *
	 */
	public static function update_ruleset( int $post_id, Position_Ruleset $ruleset ): bool {

		return false !== update_post_meta( $post_id, '_wpb_rs_position', $ruleset );
	}


	/**
	 * Converts an array to a Position_Ruleset object.
	 *
	 * @param array $position_rules
	 *
	 * @return Position_Ruleset
	 *
	 * @since 2.14.0
	 */
	public static function convert_to_ruleset( $position_rules ) {

		# filter unnecessary rule group
		if ( isset( $position_rules['%rule_group%'] ) ) {
			unset( $position_rules['%rule_group%'] );
		}

		$ruleset = new Position_Ruleset();

		foreach ( $position_rules as $rules ) {

			$rule_group = new Position_Rule_Group();

			foreach ( $rules as $rule ) {

				if ( ! isset( $rule['param'] ) ) {
					continue;
				}
				if ( ! isset( $rule['operator'] ) ) {
					continue;
				}
				if ( ! isset( $rule['value'] ) ) {
					continue;
				}

				$new_rule           = new Position_Rule();
				$new_rule->param    = sanitize_text_field( $rule['param'] );
				$new_rule->operator = sanitize_text_field( $rule['operator'] );
				$new_rule->value    = sanitize_text_field( $rule['value'] );

				$rule_group->add_rule( $new_rule );

			}

			$ruleset->add_rulegroup( $rule_group );
		}

		return $ruleset;
	}
}
