<?php

namespace ACA\BP\Settings;

use AC;

class Date extends AC\Settings\Column\Date {

	public function __construct( $column ) {
		parent::__construct( $column );

		$this->set_default( 'buddypress' );
	}

	protected function get_date_options() {
		$label = __( 'BuddyPress Difference', 'codepress-admin-columns' );

		$options = parent::get_date_options();
		$options['diff'] = $this->get_html_label( $label, '', __( 'The difference is returned in a human readable format.', 'codepress-admin-columns' ) . ' <br/>' . sprintf( __( 'For example: %s.', 'codepress-admin-columns' ), '"' . bp_core_get_last_activity( strtotime( '-1 hour -30 minutes' ) ) . '" ' . __( 'or' ) . ' "' . bp_core_get_last_activity( strtotime( '-2 days -2 hours' ) ) . '"' ) );

		return $options;
	}

	public function format( $value, $original_value ) {
		if ( 'diff' === $this->get_date_format() ) {
			return bp_core_get_last_activity( $value );
		}

		if ( ! $value ) {
			return false;
		}

		return parent::format( $value, $original_value );
	}

}