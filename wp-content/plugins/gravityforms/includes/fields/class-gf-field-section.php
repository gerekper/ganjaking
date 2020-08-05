<?php

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

class GF_Field_Section extends GF_Field {

	public $type = 'section';

	public function get_form_editor_field_title() {
		return esc_attr__( 'Section', 'gravityforms' );
	}

	/**
	 * Returns the field's form editor description.
	 *
	 * @since 2.5
	 *
	 * @return string
	 */
	public function get_form_editor_field_description() {
		return esc_attr__( 'Adds a content separator to your form to help organize groups of fields. This is a visual element and does not collect any data.', 'gravityforms' );
	}

	/**
	 * Returns the field's form editor icon.
	 *
	 * This could be an icon url or a dashicons class.
	 *
	 * @since 2.5
	 *
	 * @return string
	 */
	public function get_form_editor_field_icon() {
		return 'dashicons-minus';
	}

	function get_form_editor_field_settings() {
		return array(
			'conditional_logic_field_setting',
			'label_setting',
			'description_setting',
			'visibility_setting',
			'css_class_setting',
		);
	}

	public function get_field_content( $value, $force_frontend_label, $form ) {

		$field_label = $this->get_field_label( $force_frontend_label, $value );

		$admin_buttons = $this->get_admin_buttons();

		$description = $this->get_description( $this->description, 'gsection_description' );
		$tag         = GFCommon::is_legacy_markup_enabled( $form ) ? 'h2' : 'h3';
		/* translators: 1. Admin buttons markup 2. Heading tag 3. The field label 4. The description */
		$field_content = sprintf( '%1$s<%2$s class="gsection_title">%3$s</%2$s>%4$s', $admin_buttons, $tag, esc_html( $field_label ), $description );

		return $field_content;
	}

}

GF_Fields::register( new GF_Field_Section() );