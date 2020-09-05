<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Class Position_Ruleset.
 *
 * Exists for backwards compatibility.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.0.0
 */
class Position_Ruleset {
	/**
	 * @var Position_Rule_Group[]
	 */
	protected $rulegroups = [];

	/**
	 * Transforms from Position_Ruleset to pro\Position_Ruleset
	 * @return false|pro\Position_Ruleset
	 * @since 2.19.0
	 */
	public function transform() {
		if ( ! class_exists( '\wpbuddy\rich_snippets\pro\Position_Ruleset' ) ) {
			return false;
		}

		$new_ruleset = new \wpbuddy\rich_snippets\pro\Position_Ruleset();

		foreach ( $this->rulegroups as $rulegroup ) {
			$new_rulegroup = $rulegroup->transform();
			if ( false === $new_rulegroup ) {
				continue;
			}
			$new_ruleset->add_rulegroup( $new_rulegroup );
		}

		return $new_ruleset;
	}
}