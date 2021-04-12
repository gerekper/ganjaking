<?php

class GPLS_Shortcode {
	protected $debugMode   = false;
	protected $debugOutput = '';

	public function __construct() {
		$this->add();
	}

	public function add() {
		add_shortcode( 'gpls', array( $this, 'handler' ) );
	}

	public function handler( $atts ) {

		$atts = shortcode_atts( array(
			'form'   => 0,
			'feed'   => 0,
			'action' => 'remaining',
			'before' => '',
			'after'  => '',
			'debug'  => false,
		), $atts, 'gpls' );

		if ( empty( $atts ) || ! array_key_exists( 'form', $atts ) ) {
			return __( 'No Form ID specified in GPLS shortcode [gpls]', 'gp-limit-submissions' );
		}

		if ( $atts['debug'] == 1 || $atts['debug'] == 'true' ) {
			$this->debugMode = true;
		}

		$content = '';

		/**
		 * @var $form
		 * @var $feed
		 * @var $action
		 * @var $before
		 * @var $after
		 */
		extract( $atts );
		switch ( $atts['action'] ) {
			case 'remaining':
				$content = $this->remainder( $form, $feed, $before, $after );
				break;
			case 'count':
				$content = $this->count( $form, $feed, $before, $after );
				break;
			case 'limit':
				$content = $this->limit( $form, $feed, $before, $after );
				break;
		}

		return $content;
	}

	public function remainder( $form_id, $feed_id, $before, $after ) {

		$test = $this->get_applicable_test( $form_id, $feed_id );
		if ( empty( $test ) ) {
			return '';
		}

		$remainder = $test->limit - $test->count;

		return $this->render( $remainder, $before, $after );
	}

	public function count( $form_id, $feed_id, $before, $after ) {

		$test = $this->get_applicable_test( $form_id, $feed_id );
		if ( empty( $test ) ) {
			return '';
		}

		return $this->render( $test->count, $before, $after );
	}

	public function limit( $form_id, $feed_id, $before, $after ) {

		$test = $this->get_applicable_test( $form_id, $feed_id );
		if ( empty( $test ) ) {
			return '';
		}

		return $this->render( $test->limit, $before, $after );
	}

	public function render( $value, $before, $after ) {
		$output = $before . $value . $after;
		if ( $this->debugMode ) {
			$output .= $this->debug_render();
		}
		return $output;
	}

	/**
	 * Return the test with the least remaining submissions.
	 */
	public function get_applicable_test( $form_id, $feed_id ) {

		$enforce = new GPLS_Enforce;
		$enforce->set_form_id( $form_id );

		if ( $feed_id == 0 ) {
			$feeds = GPLS_RuleGroup::load_by_form( $form_id );
			$enforce->set_rule_groups( $feeds );
		} else {
			$feed = GPLS_RuleGroup::load_by_id( $feed_id );
			$enforce->set_rule_groups( array( $feed ) ); // set_rule_groups expects array
		}

		$test_results = $enforce->test();
		if ( empty( $test_results->tests ) ) {
			return '';
		}

		$ordered_rule_groups = array();

		foreach ( $test_results->tests as $test ) {

			// Tested rule group was not in context.
			if ( $test->context == false ) {
				continue;
			}

			// Test failed which means limit reached and zero remainder.
			if ( $test->fail ) {
				return $test;
			}

			$ordered_rule_groups[ $test->limit - $test->count ] = $test;

		}

		ksort( $ordered_rule_groups );

		// Return the first feed after ordering by minimum remaining.
		return reset( $ordered_rule_groups );
	}

	public function rule_group_count( $form_id, $feed ) {
		$count = 0;
		foreach ( $feed->get_rulesets() as $ruleset ) {

			$test              = new GPLS_RuleTest;
			$test->rules       = $ruleset;
			$test->limit       = $feed->get_limit();
			$test->form_id     = $form_id;
			$test->time_period = $feed->get_time_period();
			$test->run();
			// add to total count
			$count = $count + $test->count;
		}

		return $count;
	}

	public function debug_variable( $var ) {
		ob_start();
		var_dump( $var );
		$this->debugOutput .= ob_get_clean();
	}

	public function debug_render() {
		return '<pre>' . $this->debugOutput . '</pre>';
	}
}
