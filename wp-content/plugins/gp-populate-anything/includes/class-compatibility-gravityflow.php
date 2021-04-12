<?php
class GPPA_Compatibility_GravityFlow {

	private static $instance = null;

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function __construct() {
		/* Source form is hydrated below. Target form is hydrated via "gform_form_pre_update_entry" in GPPA proper. */
		add_filter( 'gravityflowformconnector_update_entry_form', array( $this, 'hydrate_form' ), 10, 2 );

		// Added these at the request of Gravity Flow support. They have not been properly tested. Will revisit in the future.
		// See: https://secure.helpscout.net/conversation/1104725512/16351/
		add_filter( 'gravityflowformconnector_new_entry_form', array( $this, 'hydrate_form' ), 10, 2 );
		add_filter( 'gravityflowformconnector_update_field_values_form', array( $this, 'hydrate_form' ), 10, 2 );
	}

	public function hydrate_form( $form, $entry ) {
		/**
		 * Do not send through hydrate_form if the form doesn't use dynamic population or have any Live Merge Tags.
		 */
		if ( ! gp_populate_anything()->should_enqueue_frontend_scripts( $form ) ) {
			return $form;
		}

		return gp_populate_anything()->hydrate_form( $form, $entry );
	}

}

function gppa_compatibility_gravityflow() {
	return GPPA_Compatibility_GravityFlow::get_instance();
}
