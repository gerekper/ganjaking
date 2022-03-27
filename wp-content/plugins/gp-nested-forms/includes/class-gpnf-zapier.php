<?php
/**
 * Zapier is a feed-based add-on that does not use the Feed Add-on Framework. Special cases get special treatment, I guess.
 */
class GPNF_Zapier {

	private static $instance = null;

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	private function __construct() {

		// Must fire *before* the Zapier's gform_after_submission filter.
		add_action( 'gform_after_submission', array( $this, 'maybe_process_feeds' ), 9, 2 );

	}

	public function maybe_process_feeds( $entry, $form ) {

		if ( ! is_callable( array( 'GFZapier', 'send_form_data_to_zapier' ) ) ) {
			return;
		}

		if ( gp_nested_forms()->is_nested_form_submission() ) {
			remove_action( 'gform_after_submission', array( 'GFZapier', 'send_form_data_to_zapier' ), 10 );
		} elseif ( gp_nested_forms()->has_nested_form_field( $form ) ) {
			add_action( 'gform_after_submission', array( $this, 'process_feeds' ), 11, 2 );
		}

	}

	public function process_feeds( $entry, $form ) {

		if ( gp_nested_forms()->is_nested_form_submission() || ! gp_nested_forms()->has_nested_form_field( $form ) ) {
			return;
		}

		add_filter( 'gform_zapier_use_stored_body', array( $this, 'disable_stored_body' ), 99 );

		$_entry        = new GPNF_Entry( $entry );
		$child_entries = $_entry->get_child_entries();
		foreach ( $child_entries as $child_entry ) {
			$form = gp_nested_forms()->get_nested_form( $child_entry['form_id'] );
			GFZapier::send_form_data_to_zapier( $child_entry, $form );
		}

		remove_filter( 'gform_zapier_use_stored_body', array( $this, 'disable_stored_body' ), 99 );

	}

	public function disable_stored_body() {
		return false;
	}

}

function gpnf_zapier() {
	return GPNF_Zapier::get_instance();
}
