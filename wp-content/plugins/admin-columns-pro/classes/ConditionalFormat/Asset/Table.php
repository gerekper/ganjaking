<?php

namespace ACP\ConditionalFormat\Asset;

use AC\Asset\Location\Absolute;
use AC\Asset\Script;
use AC\Type\Url\Documentation;
use ACP\ConditionalFormat\Operators;
use ACP\ConditionalFormat\RuleCollection;

final class Table extends Script {

	/**
	 * @var Operators
	 */
	private $operators;

	/**
	 * @var RuleCollection
	 */
	private $rules;

	/**
	 * @var array
	 */
	private $columns;

	public function __construct( Absolute $location, Operators $operators, RuleCollection $rules, array $columns ) {
		parent::__construct( 'acp-cf-table', $location );

		$this->operators = $operators;
		$this->rules = $rules;
		$this->columns = $columns;
	}

	public function register(): void {
		parent::register();

		$rules = [];

		foreach ( $this->rules as $rule ) {
			$rules[] = [
				'column_name' => $rule->get_column_name(),
				'format'      => (string) $rule->get_format(),
				'operator'    => $rule->get_operator(),
				'fact'        => $rule->has_fact() ? $rule->get_fact() : null,
			];
		}

		$this->add_inline_variable( 'acp_cf_settings', [
			'operators' => array_values( $this->operators->get_operators() ),
			'rules'     => $rules,
			'columns'   => $this->columns,
		] );

		wp_localize_script( $this->get_handle(), 'acp_cf_settings_i18n', [
			'between_and'            => _x( 'and', 'between_operator', 'codepress-admin-columns' ),
			'save_apply'             => __( 'Save &amp; Apply', 'codepress-admin-columns' ),
			'cancel'                 => __( 'Cancel', 'codepress-admin-columns' ),
			'add_rule'               => __( 'Add Rule', 'codepress-admin-columns' ),
			'add_another_condition'  => __( 'Add condition', 'codepress-admin-columns' ),
			'conditional_formatting' => __( 'Conditional Formatting', 'codepress-admin-columns' ),
			'formatting'             => __( 'Formatting', 'codepress-admin-columns' ),
			'formatting_style'       => __( 'Formatting Style', 'codepress-admin-columns' ),
			'documentation_link'     => $this->get_documentation_link(),
		] );
	}

	private function get_documentation_link(): string {
		$url = Documentation::create_with_path( Documentation::ARTICLE_CONDITIONAL_FORMATTING );

		return sprintf(
			'<a href="%s" class="ac-external" target="_blank">%s</a><span class="dashicons dashicons-external"></span>',
			$url->get_url(),
			__( 'documentation', 'codepress-admin-columns' )
		);
	}

}