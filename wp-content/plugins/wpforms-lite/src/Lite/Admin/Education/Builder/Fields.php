<?php

namespace WPForms\Lite\Admin\Education\Builder;

use \WPForms\Admin\Education;

/**
 * Builder/Fields Education for Lite.
 *
 * @since 1.6.6
 */
class Fields extends Education\Builder\Fields {

	/**
	 * Hooks.
	 *
	 * @since 1.6.6
	 */
	public function hooks() {

		add_filter( 'wpforms_builder_fields_buttons', [ $this, 'add_fields' ], 500 );
		add_filter( 'wpforms_builder_field_button_attributes', [ $this, 'fields_attributes' ], 100, 2 );
		add_action( 'wpforms_field_options_after_advanced-options', [ $this, 'field_conditional_logic' ] );
	}

	/**
	 * Add fields.
	 *
	 * @since 1.6.6
	 *
	 * @param array $fields Form fields.
	 *
	 * @return array
	 */
	public function add_fields( $fields ) {

		foreach ( $fields as $group => $group_data ) {
			$edu_fields = $this->fields->get_by_group( $group );
			$edu_fields = $this->fields->set_values( $edu_fields, 'class', 'education-modal', 'empty' );

			$fields[ $group ]['fields'] = array_merge( $group_data['fields'], $edu_fields );
		}

		return $fields;
	}

	/**
	 * Display conditional logic settings section for fields inside the form builder.
	 *
	 * @since 1.6.6
	 *
	 * @param array $field Field data.
	 */
	public function field_conditional_logic( $field ) {

		// Certain fields don't support conditional logic.
		if ( in_array( $field['type'], [ 'pagebreak', 'divider', 'hidden' ], true ) ) {
			return;
		}
		?>

		<div class="wpforms-field-option-group">
			<a href="#" class="wpforms-field-option-group-toggle education-modal" data-name="<?php esc_attr_e( 'Conditional Logic', 'wpforms-lite' ); ?>">
				<?php esc_html_e( 'Conditionals', 'wpforms-lite' ); ?> <i class="fa fa-angle-right"></i>
			</a>
		</div>
		<?php
	}

	/**
	 * Adjust attributes on field buttons.
	 *
	 * @since 1.6.6
	 *
	 * @param array $atts  Button attributes.
	 * @param array $field Button properties.
	 *
	 * @return array Attributes array.
	 */
	public function fields_attributes( $atts, $field ) {

		if ( empty( $field['addon'] ) ) {
			return $atts;
		}

		$addon = $this->addons->get_addon( $field['addon'] );

		if ( empty( $addon ) ) {
			return $atts;
		}

		if ( ! empty( $addon['video'] ) ) {
			$atts['data']['video'] = $addon['video'];
		}

		return $atts;
	}
}
