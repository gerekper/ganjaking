<?php

class GPLS_Rule_Ip extends GPLS_Rule {
	private $type = 'all';
	private $ip   = '';

	public static function load( $ruleData, $form_id = false ) {
		$rule = new self;
		if ( $ruleData['rule_ip'] == 'specific' ) {
			$rule->type = 'specific';
			$rule->ip   = $ruleData['rule_ip_specific'];
		} else {
			$rule->type = 'all';
		}

		return $rule;
	}

	public function context() {

		// if rule has specific IP, we only want to test if this user has the same IP
		if ( $this->type == 'specific' ) {
			if ( $this->ip != GFFormsModel::get_ip() ) {
				return false;
			}
		}

		return true;
	}

	public function query() {
		global $wpdb;
		if ( $this->type == 'specific' ) {
			// with specific IP we check the provided IP, which won't apply the limit to the current user if they have a different IP
			$ip_to_check = $this->ip;
		} else {
			// with "all" IP's we check current user IP because limit should be applied to them
			$ip_to_check = GFFormsModel::get_ip();
		}

		return $wpdb->prepare( 'ip = %s', $ip_to_check );
	}

	public function render_option_fields( $gfaddon ) {
		$gfaddon->settings_select(
			array(
				'label'   => __( 'IP Rule', 'gp-limit-submissions' ),
				'name'    => 'rule_ip_{i}',
				'class'   => 'rule_value_selector rule_ip rule_ip_{i} gpls-secondary-field',
				'choices' => array(
					array(
						'label' => __( 'Each IP', 'gp-limit-submissions' ),
						'value' => 'all',
					),
					array(
						'label' => __( 'Specific IP', 'gp-limit-submissions' ),
						'value' => 'specific',
					),
				),
			)
		);
		$gfaddon->settings_text(
			array(
				'label' => __( 'IP Rule', 'gp-limit-submissions' ),
				'name'  => 'rule_ip_specific_{i}',
				'class' => 'rule_value_selector rule_ip_specific rule_ip_specific_{i} gpls-secondary-field',
			)
		);
	}

	public function get_type() {
		return 'ip';
	}
}
