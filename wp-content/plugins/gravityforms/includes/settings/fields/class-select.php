<?php

namespace Rocketgenius\Gravity_Forms\Settings\Fields;

use Rocketgenius\Gravity_Forms\Settings\Fields;

defined( 'ABSPATH' ) || die();

class Select extends Base {

	/**
	 * Field type.
	 *
	 * @since 2.5
	 *
	 * @var string
	 */
	public $type = 'select';

	/**
	 * Field choices.
	 *
	 * @since 2.5
	 *
	 * @var array
	 */
	public $choices = array();

	/**
	 * Message to display when no choices exist.
	 *
	 * @since 2.5
	 *
	 * @var string
	 */
	public $no_choices;

	/**
	 * Enable enhanced UI with Select2.
	 *
	 * @since 2.5
	 *
	 * @var bool
	 */
	public $enhanced_ui = false;

	/**
	 * Register scripts to enqueue when displaying field.
	 *
	 * @since 2.5
	 *
	 * @return array
	 */
	public function scripts() {

		if ( ! $this->enhanced_ui ) {
			return array();
		}

		return array(
			array(
				'handle'  => 'gform_selectwoo',
			),
		);

	}





	// # RENDER METHODS ------------------------------------------------------------------------------------------------

	/**
	 * Render field.
	 *
	 * @since 2.5
	 *
	 * @return string
	 */
	public function markup() {

		// Display description.
		$html = $this->get_description();

		$html .= '<span class="' . esc_attr( $this->get_container_classes() ) . '">';

		// Get choices.
		$choices = $this->get_choices();

		// If no choices were provided and there is a no choices message, display it.
		if ( ( $choices === false || empty( $choices ) ) && ! empty( $this->no_choices ) ) {

			$html .= $this->no_choices;

		} else {

			$select_input = sprintf(
				'<select name="%s_%s" %s %s>%s</select>%s',
				esc_attr( $this->settings->get_input_name_prefix() ),
				esc_attr( $this->name ),
				$this->get_describer() ? sprintf( 'aria-describedby="%s"', $this->get_describer() ) : '',
				implode( ' ', $this->get_attributes() ),
				self::get_options( $choices, $this->get_value() ),
				rgobj( $this, 'after_select' )
			);

			// Display enhanced select UI.
			if ( $this->enhanced_ui ) {

				// Wrap select input.
				$html .= sprintf( '<span class="gform-settings-field__select--enhanced">%s</span>', $select_input );

				$html .= '<script type="text/javascript">
					jQuery( document ).ready( function () {
						jQuery( "#' . esc_attr( $this->name ) . '" ).select2( {
							minimumResultsForSearch: Infinity,
							dropdownCssClass: "gform-settings-field__select-enhanced-container",
							dropdownParent: jQuery( "#' . esc_attr( $this->name ) . '" ).parent(),
						} );
					} );
				</script>';

			} else {
				$html .= $select_input;
			}

		}

		$html .= '</span>';

		// If field failed validation, add error icon.
		$html .= $this->get_error_icon();

		return $html;

	}

	/**
	 * Prepares an array of choices as an HTML string of options.
	 *
	 * @since 2.5
	 *
	 * @param array  $choices  Array of drop down choices.
	 * @param string $selected Currently selected value.
	 *
	 * @return string
	 */
	public static function get_options( $choices, $selected ) {

		$html = '';

		// Loop through choices, prepare HTML.
		foreach ( $choices as $choice ) {

			// If choice has choices, render as option group.
			if ( rgar( $choice, 'choices') ) {

				$html .= sprintf(
					'<optgroup label="%s">%s</optgroup>',
					esc_attr( $choice['label'] ),
					self::get_options( $choice['choices'], $selected )
				);

			} else {

				// Set value to label if not defined.
				$choice['value'] = isset( $choice['value'] ) ? $choice['value'] : $choice['label'];

				// Prepare selected attribute.
				if ( is_array( $selected ) ) {
					$selected_attr = in_array( $choice['value'], $selected ) ? 'selected="selected"' : '';
				} else {
					$selected_attr = selected( $selected, $choice['value'], false );
				}

				$html .= sprintf(
					'<option value="%s" %s>%s</option>',
					esc_attr( $choice['value'] ),
					$selected_attr,
					esc_attr( $choice['label'] )
				);

			}

		}

		return $html;

	}





	// # VALIDATION METHODS --------------------------------------------------------------------------------------------

	/**
	 * Validate posted field value.
	 *
	 * @since 2.5
	 *
	 * @param string|array $value Posted field value.
	 */
	public function is_valid( $value ) {

		// Determine if field is a multiselect field, required.
		$multiple = rgobj( $this, 'multiple' ) == 'multiple';

		// If field is not multiselect, but is required with no selection, set field error.
		if ( ! $multiple && $this->required && rgblank( $value ) ) {
			$this->set_error( rgobj( $this, 'error_message' ) );
			return;
		}

		// Get choices.
		$choices = $this->get_choices();

		// If no selection was made or no choices exist, exit.
		if ( rgblank( $value ) || $choices === false || empty( $choices ) ) {
			return;
		}

		if ( $multiple ) {

			$selected = 0;

			// Loop through field choices, determine if valid.
			foreach ( $choices as $choice ) {

				// If choice has nested choices, loop through and determine if valid.
				if ( isset( $choice['choices'] ) ) {

					foreach ( $choice['choices'] as $optgroup_choice ) {
						if ( self::is_choice_valid( $optgroup_choice, $value ) ) {
							$selected++;
						}
					}

				} else {

					if ( self::is_choice_valid( $choice, $value ) ) {
						$selected++;
					}

				}

			}

			// If field is required and no choices were selected, set field error.
			if ( $this->required && $selected == 0 ) {
				$this->set_error( rgobj( $this, 'error_message' ) );
				return;
			}

			// If field is not required and the number of valid selected choices does not match posted value, set field error.
			if ( ! $this->required && $selected !== count( $value ) ) {
				$this->set_error( esc_html__( 'Invalid selection.', 'gravityforms' ) );
			}

		} else {

			// Loop through field choices, determine if valid.
			foreach ( $choices as $choice ) {

				// If choice has nested choices, loop through and determine if valid.
				if ( isset( $choice['choices'] ) ) {

					foreach ( $choice['choices'] as $optgroup_choice ) {
						if ( self::is_choice_valid( $optgroup_choice, $value ) ) {
							return;
						}
					}

				} else {

					if ( self::is_choice_valid( $choice, $value ) ) {
						return; // Choice is valid
					}

				}

			}

			// If invalid selection was made, set field error.
			$this->set_error( esc_html__( 'Invalid selection.', 'gravityforms' ) );

		}

	}

}

Fields::register( 'select', '\Rocketgenius\Gravity_Forms\Settings\Fields\Select' );
