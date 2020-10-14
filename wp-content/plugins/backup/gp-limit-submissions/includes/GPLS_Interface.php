<?php

class GPLS_Interface {
	protected $gfaddon;

	public function __construct( $gfaddon ) {
		$this->gfaddon = $gfaddon;
	}

	public function render_data_storage_field() {
		$this->gfaddon->settings_hidden(
			array(
				'label' => __( 'Rule Set', 'gp-limit-submissions' ),
				'name'  => 'limit_rules_data',
				'class' => 'limit_rules_data'
			)
		);
	}

	public function render_rule_groups( $existing_rules ) {

		foreach ( $existing_rules as $index => $rule_group ) {
			?>
			<?php if ( $index >= 1 ) { ?>
				<h4 class="gpls-or-header" id="rule_group_<?php print $index; ?>_header">&mdash; OR &mdash;</h4>
			<?php } ?>

			<div id="rule_group_<?php print $index; ?>" class="rule_group">
				<div class="row">

					<div class="rule_group_buttons">
						{buttons}
					</div>

					<?php
					// rule type field
					$this->render_rule_type_field();
					// rule options fields
					$this->render_rule_option_fields();
					?>

				</div>
			</div><!-- / end rule_group -->
		<?php }
	}

	public function render_rule_type_field() {
		$this->gfaddon->settings_select(
			array(
				'label'   => 'Rule Type',
				'name'    => 'rule_type_{i}',
				'class'   => 'rule_type_selector rule_type_{i}',
				'choices' => array(
					array(
						'label' => __( 'IP', 'gp-limit-submissions' ),
						'value' => 'ip',
					),
					array(
						'label' => __( 'User', 'gp-limit-submissions' ),
						'value' => 'user',
					),
					array(
						'label' => __( 'Embed Url', 'gp-limit-submissions' ),
						'value' => 'embed_url',
					),
					array(
						'label' => __( 'Role', 'gp-limit-submissions' ),
						'value' => 'role',
					),
					array(
						'label' => __( 'Field Value', 'gp-limit-submissions' ),
						'value' => 'field',
					),
				),
			)
		);
	}

	/*
	 * Instantiate each rule type class and output it's defined option field(s)
	 * Called during output of rule definition repeating rows
	 */
	public function render_rule_option_fields() {

		$rule_types = $this->ruleTypes();
		foreach ( $rule_types as $key => $class ) {
			$rule_type = new $class( $this->gfaddon );
			$rule_type->render_option_fields( $this->gfaddon );
		}
	}

	public function default_rule() {
		return array(
			'rule_type'                 => 'ip',
			'rule_ip'                   => '',
			'rule_ip_specific'          => '',
			'rule_user'                 => '',
			'rule_embed_url'            => '',
			'rule_role'                 => '',
			'rule_field'                => '',
			'rule_embed_url_value_full' => '',
			'rule_embed_url_value_post' => '',
		);
	}

	public function existing_rules( $rules_data ) {

		$rules = array();
		// default if empty
		if ( empty( $rules_data ) ) {

			$rules[] = array( $this->default_rule() );

			return $rules;
		}
		$ruleGroupIndex = (int) 0;
		foreach ( $rules_data as $rule_group ) {

			$rules[ $ruleGroupIndex ] = array();
			foreach ( $rule_group as $ruleData ) {

				// stash this rule
				$rules[ $ruleGroupIndex ][] = $ruleData;
			}
			// increment counter
			$ruleGroupIndex ++;
		}

		return $rules;
	}

	public function ruleTypes() {
		return array(
			'ip'        => 'GPLS_Rule_Ip',
			'user'      => 'GPLS_Rule_User',
			'embed_url' => 'GPLS_Rule_Embed_Url',
			'role'      => 'GPLS_Rule_Role',
			'field'     => 'GPLS_Rule_Field',
		);
	}
}
