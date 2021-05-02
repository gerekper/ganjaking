<?php

namespace Gravity_Forms\Gravity_Forms\Settings\Fields;

use Gravity_Forms\Gravity_Forms\Settings\Fields;

defined( 'ABSPATH' ) || die();

class Textarea extends Base {

	/**
	 * Field type.
	 *
	 * @since 2.5
	 *
	 * @var string
	 */
	public $type = 'textarea';

	/**
	 * Allow HTML in field value.
	 *
	 * @since 2.5
	 *
	 * @var bool
	 */
	public $allow_html = false;

	/**
	 * Initialize as Rich Text Editor.
	 *
	 * @since 2.5
	 *
	 * @var bool
	 */
	public $use_editor = false;

	/**
	 * Initialize Save field.
	 *
	 * @since 2.5
	 *
	 * @param array                                $props    Field properties.
	 * @param \Gravity_Forms\Gravity_Forms\Settings\Settings $settings Settings instance.
	 */
	public function __construct( $props, $settings ) {

		parent::__construct( $props, $settings );

		// Set default row count.
		if ( ! isset( $this->rows ) ) {
			$this->rows = 4;
		}

		// Set default editor height.
		if ( ! isset( $this->editor_height ) ) {
			$this->editor_height = 200;
		}

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

		// Get value.
		$value = $this->get_value();

		// Initialize rich text editor.
		if ( $this->use_editor ) {

			// Create editor container.
			$html = sprintf(
				'<span class="mt-gaddon-editor mt-%s_%s"></span>',
				esc_attr( $this->settings->get_input_name_prefix() ),
				esc_attr( $this->name )
			);

			// Display description.
			$html .= $this->get_description();

			$html .= '<span class="' . esc_attr( $this->get_container_classes() ) . '">';

			// Insert editor.
			ob_start();
			wp_editor(
				$value,
				esc_attr( $this->settings->get_input_name_prefix() ) . '_' . esc_attr( $this->name ),
				array(
					'autop'         => false,
					'editor_class'  => 'merge-tag-support mt-wp_editor mt-manual_position mt-position-right',
					'editor_height' => $this->editor_height,
				)
			);
			$html .= ob_get_contents();
			ob_end_clean();

			// If field failed validation, add error icon.
			$html .= $this->get_error_icon();

			$html .= '</span>';

		} else {

			// Prepare markup.
			// Display description.
			$html = $this->get_description();

			$html .= sprintf(
				'<span class="%s"><textarea name="%s_%s" %s %s>%s</textarea>%s</span>',
				esc_attr( $this->get_container_classes() ),
				esc_attr( $this->settings->get_input_name_prefix() ),
				esc_attr( $this->name ),
				$this->get_describer() ? sprintf( 'aria-describedby="%s"', $this->get_describer() ) : '',
				implode( ' ', $this->get_attributes() ),
				esc_textarea( $value ),
				// If field failed validation, add error icon.
				$this->get_error_icon()
			);

		}

		return $html;

	}





	// # VALIDATION METHODS --------------------------------------------------------------------------------------------

	/**
	 * Validate posted field value.
	 *
	 * @since 2.5
	 *
	 * @param string $value Posted field value.
	 */
	public function do_validation( $value ) {

		// If field is required and value is missing, set field error.
		if ( $this->required && rgblank( $value ) ) {
			$this->set_error( rgobj( $this, 'error_message' ) );
		}

		if ( $this->use_editor || $this->allow_html ) {
			return;
		}

		// Sanitize posted value.
		$sanitized_value = sanitize_textarea_field( $value );

		// If posted and sanitized values do not match, add field error.
		if ( $value !== $sanitized_value ) {

			// Prepare correction script.
			$double_encoded_safe_value = htmlspecialchars( htmlspecialchars( $sanitized_value, ENT_QUOTES ), ENT_QUOTES );
			$script                    = sprintf(
				'jQuery("textarea[name=\"%s_%s\"]").val(jQuery(this).data("safe"));',
				$this->settings->get_input_name_prefix(),
				$this->name
			);

			// Prepare message.
			$message = sprintf(
				"%s <a href='javascript:void(0);' onclick='%s' data-safe='%s'>%s</a>",
				esc_html__( 'The text you have entered is not valid. For security reasons, some characters are not allowed. ', 'gravityforms' ),
				htmlspecialchars( $script, ENT_QUOTES ),
				$double_encoded_safe_value,
				esc_html__( 'Fix it', 'gravityforms' )
			);

			// Set field error.
			$this->set_error( $message );

		}

	}

}

Fields::register( 'textarea', '\Gravity_Forms\Gravity_Forms\Settings\Fields\Textarea' );
