<?php

class GPNF_Merge_Tags {

	private static $instance = null;

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct() {
		add_filter( 'gform_custom_merge_tags', array( $this, 'add_merge_tags' ), 10, 4 );
	}

	public function add_merge_tags( $merge_tags, $form_id, $fields, $element_id ) {

		foreach ( $fields as $field ) {
			if ( $field->type !== 'form' ) {
				continue;
			}

			$merge_tags[] = array(
				'tag'   => "{{$field->label}:{$field->id}:count}",
				'label' => sprintf( esc_html__( '%s: Count', 'gp-nested-forms' ), $field->label ),
			);

			$merge_tags[] = array(
				'tag'   => "{{$field->label}:{$field->id}:sum=CHILD_FIELD_ID}",
				'label' => sprintf( esc_html__( '%s: Sum', 'gp-nested-forms' ), $field->label ),
			);

			$child_form = GFAPI::get_form( $field->gpnfForm );

			if ( gp_nested_forms()->has_pricing_field( $child_form ) ) {
				$merge_tags[] = array(
					'tag'   => "{{$field->label}:{$field->id}:total}",
					'label' => sprintf( esc_html__( '%s: Total', 'gp-nested-forms' ), $field->label ),
				);
			}
		}

		return $merge_tags;
	}

}

function gpnf_merge_tags() {
	return GPNF_Merge_Tags::get_instance();
}

