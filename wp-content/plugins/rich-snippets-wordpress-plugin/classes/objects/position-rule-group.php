<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Class Position_Rule_Group.
 *
 * Exists for backwards compatibility.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.0.0
 */
class Position_Rule_Group {
	/**
	 * @var Position_Rule[]
	 */
	protected $rules = [];


	/**
	 * Transforms from Position_Rule_Group to \wpbuddy\rich_snippets\pro\Position_Rule_Group
	 * @return boolean|pro\Position_Rule_Group
	 * @since 2.19.0
	 */
	public function transform() {
		if ( ! class_exists( '\wpbuddy\rich_snippets\pro\Position_Rule_Group' ) ) {
			return false;
		}

		$new_rule_group = new \wpbuddy\rich_snippets\pro\Position_Rule_Group();

		foreach ( $this->rules as $rule ) {
			$new_rule = $rule->transform();

			if ( false === $new_rule ) {
				continue;
			}

			$new_rule_group->add_rule( $new_rule );
		}

		return $new_rule_group;
	}
}