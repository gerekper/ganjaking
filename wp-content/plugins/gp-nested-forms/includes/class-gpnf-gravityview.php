<?php

class GPNF_GravityView {

	private static $instance = null;

	private static $form_has_gv_buttons = array();

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	private function __construct() {

		add_action( 'gpnf_pre_nested_forms_markup', array( $this, 'remove_gravityview_edit_hooks' ) );
		add_action( 'gpnf_nested_forms_markup', array( $this, 'add_gravityview_edit_hooks' ) );
		add_action( 'gravityview/view/query', array( $this, 'filter_unsubmitted_child_entries'), 10, 3 );

	}

	/**
	 * Prevent child entries of unsubmitted parent forms from displaying in GravityView views.
	 *
	 * @param $query GF_Query
	 * @param $view
	 * @param $request
	 */
	public function filter_unsubmitted_child_entries( &$query, $view, $request ) {
		$query_parts = $query->_introspect();

		$condition = new GF_Query_Condition(
			new GF_Query_Column( '_gpnf_expiration' ),
			GF_Query_Condition::EQ,
			new GF_Query_Literal( '' )
		);

		$query->where( \GF_Query_Condition::_and( $query_parts['where'], $condition ) );
	}

	public function gravityview_edit_render_instance() {

		if ( ! method_exists( 'GravityView_Edit_Entry', 'getInstance' ) ) {
			return null;
		}

		$edit_entry_instance = GravityView_Edit_Entry::getInstance();
		$render_instance     = $edit_entry_instance->instances['render'];

		return $render_instance;

	}

	/**
	 * GravityView adds a few hooks such as changing the submit buttons and changing the field value.
	 * These don't work well with the Nested Form so we need to temporarily unhook the filters/actions and re-add them.
	 */
	public function remove_gravityview_edit_hooks( $form ) {

		if ( $render_instance = $this->gravityview_edit_render_instance() ) {
			self::$form_has_gv_buttons[ $form['id'] ] =
				has_filter( 'gform_submit_button', array( $render_instance, 'render_form_buttons' ) )
				|| has_filter( 'gform_submit_button', array( $render_instance, 'modify_edit_field_input' ) );

			remove_filter( 'gform_submit_button', array( $render_instance, 'render_form_buttons' ) );
			remove_filter( 'gform_field_input', array( $render_instance, 'modify_edit_field_input' ) );
		}

	}

	public function add_gravityview_edit_hooks( $form ) {

		if ( ! rgar( self::$form_has_gv_buttons, $form['id'] ) ) {
			return;
		}

		if ( $render_instance = $this->gravityview_edit_render_instance() ) {
			add_filter( 'gform_submit_button', array( $render_instance, 'render_form_buttons' ) );
			add_filter( 'gform_field_input', array( $render_instance, 'modify_edit_field_input' ), 10, 5 );
		}

	}

}

function gpnf_gravityview() {
	return GPNF_GravityView::get_instance();
}
