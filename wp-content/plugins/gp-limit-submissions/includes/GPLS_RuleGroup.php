<?php

class GPLS_RuleGroup {
	public $feed_id;
	public $form_id;
	public $name             = '';
	public $limit            = '';
	public $time_period      = false;
	public $message          = false;
	public $rulesets         = array();
	public $applicable_forms = array();

	public static function load_by_form( $form_id ) {
		$rule_groups = array();
		$gpls        = gp_limit_submissions();
		// load feeds, if none exit
		$feeds = $gpls->get_active_feeds( $form_id );
		if ( empty( $feeds ) ) {
			return array();
		}
		// turn feed array data into gpls rule objects
		foreach ( $feeds as $feed ) {
			// load GPLS Rules by Feed and store in $rules property
			$rule_groups[] = self::load_by_feed( $feed );
		}

		return $rule_groups;
	}

	public static function load_by_id( $id ) {
		$gpls = gp_limit_submissions();
		$feed = $gpls->get_feed( $id );

		return self::load_by_feed( $feed );
	}

	public static function load_by_feed( $feed ) {

		$rule_group          = new GPLS_RuleGroup();
		$rule_group->feed_id = $feed['id'];
		$rule_group->form_id = $feed['form_id'];
		$rule_group->name    = $feed['meta']['rule_group_name'];
		$rule_group->limit   = GFCommon::replace_variables_prepopulate( $feed['meta']['rule_submission_limit'] );
		// set time period as array with value, unit keys
		if ( $feed['meta']['rule_time_period_type'] != 'forever' ) {
			$rule_group->time_period = $rule_group->load_time_period( $feed );
		}
		$rule_group->message = $feed['meta']['rule_limit_message'];
		$rule_group->load_rulesets( $feed['meta']['limit_rules_data'], $rule_group->form_id );

		return $rule_group;
	}

	private function load_time_period( $feed ) {

		$time_period         = array();
		$type                = $feed['meta']['rule_time_period_type'];
		$time_period['type'] = $type;
		// time period value
		if ( $type == 'time_period' ) {
			$time_period['value'] = $feed['meta']['rule_time_period_value'];
			$time_period['unit']  = $feed['meta']['rule_time_period_unit'];
		}
		// calendar period value
		if ( $type == 'calendar_period' ) {
			$time_period['value'] = $feed['meta']['rule_calendar_period'];
		}

		return $time_period;
	}

	private function load_rulesets( $limit_rules_data, $form_id = false ) {
		foreach ( $limit_rules_data as $rulesetData ) {
			$ruleset = array();
			foreach ( $rulesetData as $ruleData ) {
				$ruleset[] = GPLS_Rule::load( $ruleData, $form_id );
			}
			$this->rulesets[] = $ruleset;
		}
	}

	public function get_rulesets() {
		return $this->rulesets;
	}

	public function get_limit() {
		return $this->limit;
	}

	public function get_time_period() {
		return $this->time_period;
	}

	public function get_message() {
		return $this->message;
	}

	public function get_form_id() {
		return $this->form_id;
	}

	public function get_applicable_forms() {
		return $this->applicable_forms;
	}

	public function populate_applicable_forms( $current_form_id ) {

		// $applicable_forms can be an array or a boolean; if false, it is a global rule group.
		if ( is_array( $this->applicable_forms ) && ! in_array( $current_form_id, $this->applicable_forms ) ) {
			$this->applicable_forms[] = $current_form_id;
		}
	}

	public function is_limit_per_form() {
		/**
		 * When the same limit feed is applied to multiple forms via the gpls_rule_groups filter, specify whether the
		 * limit apply per form or collectively across all forms that share that feed.
		 *
		 * @since 1.0
		 *
		 * @param bool $limit_per_form Defaults to true.
		 */
		return apply_filters( 'gpls_apply_limit_per_form', true );
	}

	public function get_feed_id() {
		return $this->feed_id;
	}
}
