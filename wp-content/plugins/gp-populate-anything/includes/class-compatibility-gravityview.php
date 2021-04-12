<?php

class GPPA_Compatibility_GravityView {

	private static $instance = null;

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct() {

		add_filter( 'gppa_hydrate_initial_load_entry', array( $this, 'hydrate_initial_load_entry' ), 10, 4 );

		add_filter( 'gravityview_widget_search_filters', array( $this, 'hydrate_gravityview_search_filters' ), 10, 4 );
		add_filter( 'gravityview_widget_search_filters', array( $this, 'localize_for_search' ), 10, 4 );
		add_filter( 'gravityview_widget_search_filters', array( $this, 'add_gravityview_id_filter' ), 10, 4 );

		add_filter( 'gravityview-inline-edit/wrapper-attributes', array( $this, 'gravityview_inline_edit_choices' ), 15, 6 );

		add_filter( 'gppa_field_filter_values', array( $this, 'field_filter_values_replace_filter_prefix' ), 10, 6 );
		add_filter( 'gppa_get_batch_field_html', array( $this, 'render_search_field' ), 10, 6 );

	}

	/**
	 * If editing a form with Gravity View's edit screen, then the form should be hydrated with fields from the current
	 * entry.
	 */
	public function hydrate_initial_load_entry( $entry, $form, $ajax, $field_values ) {

		if ( ! class_exists( 'GravityView_frontend' ) ) {
			return $entry;
		}

		if ( $entry = GravityView_frontend::getInstance()->getEntry() ) {
			return $entry;
		}

		return $entry;

	}

	public function hydrate_gravityview_search_filters( $search_fields, $self, $widget_args, $context ) {
		$form_id = rgar( $widget_args, 'form_id' );
		$form    = GFAPI::get_form( $form_id );

		foreach ( $search_fields as $search_field_index => $search_field ) {
			$field = GFFormsModel::get_field( $form, $search_field['key'] );

			$hydrated_field = gp_populate_anything()->hydrate_field( $field, $form, $this->get_gravityview_filter_values() );

			if ( $choices = rgars( $hydrated_field, 'field/choices' ) ) {
				$search_fields[ $search_field_index ]['choices'] = $choices;
			}
		}

		return $search_fields;
	}

	public function get_gravityview_filter_values() {

		$values = array();

		foreach ( $_REQUEST as $key => $value ) {

			if ( strpos( $key, 'filter_' ) !== 0 ) {
				continue;
			}

			$key = str_replace( 'filter_', '', $key );

			$values[ $key ] = $value;

		}

		return $values;

	}

	public function gravityview_inline_edit_choices( $wrapper_attributes, $input_type, $gf_field_id, $entry, $form, $gf_field ) {
		if ( ! rgar( $gf_field, 'gppa-choices-enabled' ) || ! isset( $wrapper_attributes['data-source'] ) || $input_type !== 'select' ) {
			return $wrapper_attributes;
		}

		$choices = wp_list_pluck( gp_populate_anything()->get_input_choices( $gf_field, $entry ), 'text', 'value' );

		$wrapper_attributes['data-source'] = json_encode( $choices );

		return $wrapper_attributes;
	}

	public function localize_for_search( $search_fields, $self, $widget_args, $context ) {
		$form_id = rgar( $widget_args, 'form_id' );
		$form    = GFAPI::get_form( $form_id );

		gp_populate_anything()->field_value_js( $form );
		gp_populate_anything()->field_value_object_js( $form );

		return $search_fields;
	}

	public function add_gravityview_id_filter( $search_fields, $self, $widget_args, $context ) {
		$form_id               = rgar( $widget_args, 'form_id' );
		$form                  = GFAPI::get_form( $form_id );
		$dynamic_search_fields = array();

		foreach ( $search_fields as $search_field ) {
			$field = GFFormsModel::get_field( $form, $search_field['key'] );

			if ( rgar( $field, 'gppa-choices-enabled' ) ) {
				$dynamic_search_fields[] = $search_field;
			}
		}

		if ( ! count( $dynamic_search_fields ) ) {
			return $search_fields;
		}

		wp_localize_script(
			'gp-populate-anything',
			'GPPA_GRAVITYVIEW_META_' . $form_id,
			array(
				'search_fields' => $dynamic_search_fields,
			)
		);

		return $search_fields;
	}

	public function field_filter_values_replace_filter_prefix( $field_values, $field_values_original, $referer_get_params, $form, $fields, $lead_id ) {

		if ( ! rgar( $_REQUEST, 'gravityview-meta' ) ) {
			return $field_values;
		}

		foreach ( $referer_get_params as $param_name => $param_value ) {
			if ( strpos( $param_name, 'filter_' ) !== 0 ) {
				continue;
			}

			$new_param_name = str_replace( 'filter_', '', $param_name );

			unset( $field_values[ $param_name ] );

			if ( ! empty( $field_values_original[ $new_param_name ] ) ) {
				continue;
			}

			$field_values[ $new_param_name ] = $param_value;
		}

		return $field_values;

	}

	public function render_search_field( $html, $field, $form, $fields, $lead_id, $hydrated_field ) {

		$view_id = rgar( $_REQUEST, 'gravityview-meta' );

		if ( ! $view_id ) {
			return $html;
		}

		$search_field = array(
			'key'     => $field['id'],
			'name'    => 'filter_' . $field['id'],
			'label'   => $field['label'],
			'input'   => 'select',
			'value'   => '',
			'type'    => 'select',
			'choices' => array(),
		);

		if ( $choices = rgars( $hydrated_field, 'field/choices' ) ) {
			$search_field['choices'] = $choices;
		}

		if ( $value = rgar( $hydrated_field, 'field_value' ) ) {
			$search_field['value'] = $value;
		}

		\GravityView_View::getInstance()->search_field = $search_field;

		ob_start();
		\GravityView_View::getInstance()->render( 'search-field', $search_field['type'], false );
		$output = ob_get_clean();

		return $output;

	}

}


function gppa_compatibility_gravityview() {
	return GPPA_Compatibility_GravityView::get_instance();
}
