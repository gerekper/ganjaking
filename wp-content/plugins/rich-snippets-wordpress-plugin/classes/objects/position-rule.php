<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Class Position_Rule.
 *
 * Exists for backwards compatibility.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.0.0
 */
class Position_Rule {

	/**
	 * The param.
	 *
	 * @since 2.0.0
	 *
	 * @var null|string
	 */
	public $param = null;


	/**
	 * The operator.
	 *
	 * @since 2.0.0
	 *
	 * @var null|string
	 */
	public $operator = null;


	/**
	 * The value.
	 *
	 * @since 2.0.0
	 *
	 * @var mixed
	 */
	public $value = null;


	/**
	 * Transforms from Position_Rule to \wpbuddy\rich_snippets\pro\Position_Rule
	 * @return boolean|pro\Position_Rule
	 * @since 2.19.0
	 */
	public function transform() {
		if ( ! class_exists( '\wpbuddy\rich_snippets\pro\Position_Rule' ) ) {
			return false;
		}

		$new_rule           = new \wpbuddy\rich_snippets\pro\Position_Rule();
		$new_rule->param    = $this->param;
		$new_rule->operator = $this->operator;
		$new_rule->value    = $this->value;

		return $new_rule;
	}
}