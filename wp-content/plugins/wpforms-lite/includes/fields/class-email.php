<?php

/**
 * Email text field.
 *
 * @since 1.0.0
 */
class WPForms_Field_Email extends WPForms_Field {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		// Define field type information.
		$this->name  = esc_html__( 'Email', 'wpforms-lite' );
		$this->type  = 'email';
		$this->icon  = 'fa-envelope-o';
		$this->order = 170;

		// Define additional field properties.
		add_filter( 'wpforms_field_properties_email', array( $this, 'field_properties' ), 5, 3 );

		// Set field to default to required.
		add_filter( 'wpforms_field_new_required', array( $this, 'default_required' ), 10, 2 );

		// Set confirmation status to option wrapper class.
		add_filter( 'wpforms_builder_field_option_class', array( $this, 'field_option_class' ), 10, 2 );

		add_action( 'wp_ajax_wpforms_restricted_email', [ $this, 'ajax_check_restricted_email' ] );
		add_action( 'wp_ajax_nopriv_wpforms_restricted_email', [ $this, 'ajax_check_restricted_email' ] );

		add_action( 'wp_ajax_wpforms_sanitize_restricted_rules', [ $this, 'ajax_sanitize_restricted_rules' ] );
	}

	/**
	 * Define additional field properties.
	 *
	 * @since 1.3.7
	 *
	 * @param array $properties List field properties.
	 * @param array $field      Field data and settings.
	 * @param array $form_data  Form data and settings.
	 *
	 * @return array
	 */
	public function field_properties( $properties, $field, $form_data ) {

		if ( ! empty( $field['confirmation'] ) ) {
			$properties = $this->confirmation_field_properties( $properties, $field, $form_data );
		}
		if ( ! empty( $field['filter_type'] ) ) {
			$properties = $this->filter_type_field_properties( $properties, $field, $form_data );
		}

		return $properties;
	}

	/**
	 * Define the confirmation field properties.
	 *
	 * @since 1.6.3
	 *
	 * @param array $properties List field properties.
	 * @param array $field      Field data and settings.
	 * @param array $form_data  Form data and settings.
	 *
	 * @return array
	 */
	public function confirmation_field_properties( $properties, $field, $form_data ) {
		$form_id  = absint( $form_data['id'] );
		$field_id = absint( $field['id'] );

		// Email confirmation setting enabled.
		$props = array(
			'inputs' => array(
				'primary'   => array(
					'block'    => array(
						'wpforms-field-row-block',
						'wpforms-one-half',
						'wpforms-first',
					),
					'class'    => array(
						'wpforms-field-email-primary',
					),
					'sublabel' => array(
						'hidden' => ! empty( $field['sublabel_hide'] ),
						'value'  => esc_html__( 'Email', 'wpforms-lite' ),
					),
				),
				'secondary' => array(
					'attr'     => array(
						'name'        => "wpforms[fields][{$field_id}][secondary]",
						'value'       => '',
						'placeholder' => ! empty( $field['confirmation_placeholder'] ) ? $field['confirmation_placeholder'] : '',
					),
					'block'    => array(
						'wpforms-field-row-block',
						'wpforms-one-half',
					),
					'class'    => array(
						'wpforms-field-email-secondary',
					),
					'data'     => array(
						'rule-confirm' => '#' . $properties['inputs']['primary']['id'],
					),
					'id'       => "wpforms-{$form_id}-field_{$field_id}-secondary",
					'required' => ! empty( $field['required'] ) ? 'required' : '',
					'sublabel' => array(
						'hidden' => ! empty( $field['sublabel_hide'] ),
						'value'  => esc_html__( 'Confirm Email', 'wpforms-lite' ),
					),
					'value'    => '',
				),
			),
		);

		$properties = array_merge_recursive( $properties, $props );

		// Input Primary: adjust name.
		$properties['inputs']['primary']['attr']['name'] = "wpforms[fields][{$field_id}][primary]";

		// Input Primary: remove size and error classes.
		$properties['inputs']['primary']['class'] = array_diff(
			$properties['inputs']['primary']['class'],
			array(
				'wpforms-field-' . sanitize_html_class( $field['size'] ),
				'wpforms-error',
			)
		);

		// Input Primary: add error class if needed.
		if ( ! empty( $properties['error']['value']['primary'] ) ) {
			$properties['inputs']['primary']['class'][] = 'wpforms-error';
		}

		// Input Secondary: add error class if needed.
		if ( ! empty( $properties['error']['value']['secondary'] ) ) {
			$properties['inputs']['secondary']['class'][] = 'wpforms-error';
		}

		// Input Secondary: add required class if needed.
		if ( ! empty( $field['required'] ) ) {
			$properties['inputs']['secondary']['class'][] = 'wpforms-field-required';
		}

		return $properties;
	}

	/**
	 * Define the filter field properties.
	 *
	 * @since 1.6.3
	 *
	 * @param array $properties List field properties.
	 * @param array $field      Field data and settings.
	 * @param array $form_data  Form data and settings.
	 *
	 * @return array
	 */
	public function filter_type_field_properties( $properties, $field, $form_data ) {

		if ( ! empty( $field['filter_type'] ) && ! empty( $field[ $field['filter_type'] ] ) ) {
			$properties['inputs']['primary']['data']['rule-restricted-email'] = true;
		}

		return $properties;
	}

	/**
	 * Field should default to being required.
	 *
	 * @since 1.0.9
	 * @param bool $required
	 * @param array $field
	 * @return bool
	 */
	public function default_required( $required, $field ) {

		if ( 'email' === $field['type'] ) {
			return true;
		}
		return $required;
	}

	/**
	 * Add class to field options wrapper to indicate if field confirmation is
	 * enabled.
	 *
	 * @since 1.3.0
	 *
	 * @param string $class Class strings.
	 * @param array  $field Current field.
	 *
	 * @return string
	 */
	public function field_option_class( $class, $field ) {

		if ( 'email' !== $field['type'] ) {
			return $class;
		}

		$class .= isset( $field['confirmation'] ) ? ' wpforms-confirm-enabled' : ' wpforms-confirm-disabled';
		if ( ! empty( $field['filter_type'] ) ) {
			$class .= ' wpforms-filter-' . $field['filter_type'];
		}

		return $class;
	}

	/**
	 * Field options panel inside the builder.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field
	 */
	public function field_options( $field ) {
		/*
		 * Basic field options.
		 */

		// Options open markup.
		$args = array(
			'markup' => 'open',
		);
		$this->field_option( 'basic-options', $field, $args );

		// Label.
		$this->field_option( 'label', $field );

		// Description.
		$this->field_option( 'description', $field );

		// Required toggle.
		$this->field_option( 'required', $field );

		// Confirmation toggle.
		$fld = $this->field_element(
			'checkbox',
			$field,
			array(
				'slug'    => 'confirmation',
				'value'   => isset( $field['confirmation'] ) ? '1' : '0',
				'desc'    => esc_html__( 'Enable Email Confirmation', 'wpforms-lite' ),
				'tooltip' => esc_html__( 'Check this option to ask users to provide an email address twice.', 'wpforms-lite' ),
			),
			false
		);
		$args = array(
			'slug'    => 'confirmation',
			'content' => $fld,
		);
		$this->field_element( 'row', $field, $args );

		// Options close markup.
		$args = array(
			'markup' => 'close',
		);
		$this->field_option( 'basic-options', $field, $args );

		/*
		 * Advanced field options.
		 */

		// Options open markup.
		$args = array(
			'markup' => 'open',
		);
		$this->field_option( 'advanced-options', $field, $args );

		// Size.
		$this->field_option( 'size', $field );

		// Placeholder.
		$this->field_option( 'placeholder', $field );

		// Confirmation Placeholder.
		$lbl = $this->field_element(
			'label',
			$field,
			array(
				'slug'    => 'confirmation_placeholder',
				'value'   => esc_html__( 'Confirmation Placeholder Text', 'wpforms-lite' ),
				'tooltip' => esc_html__( 'Enter text for the confirmation field placeholder.', 'wpforms-lite' ),
			),
			false
		);
		$fld  = $this->field_element(
			'text',
			$field,
			array(
				'slug'  => 'confirmation_placeholder',
				'value' => ! empty( $field['confirmation_placeholder'] ) ? esc_attr( $field['confirmation_placeholder'] ) : '',
			),
			false
		);
		$args = array(
			'slug'    => 'confirmation_placeholder',
			'content' => $lbl . $fld,
		);
		$this->field_element( 'row', $field, $args );

		// Hide Label.
		$this->field_option( 'label_hide', $field );

		// Hide sub-labels.
		$this->field_option( 'sublabel_hide', $field );

		// Default value.
		$this->field_option( 'default_value', $field );

		// Custom CSS classes.
		$this->field_option( 'css', $field );

		$filter_type_label = $this->field_element(
			'label',
			$field,
			[
				'slug'    => 'filter_type',
				'value'   => esc_html__( 'Allowlist / Denylist', 'wpforms-lite' ),
				'tooltip' => esc_html__( 'Restrict which email addresses are allowed. Be sure to separate each email address with a comma.', 'wpforms-lite' ),
			],
			false
		);
		$filter_type_field = $this->field_element(
			'select',
			$field,
			[
				'slug'    => 'filter_type',
				'value'   => ! empty( $field['filter_type'] ) ? esc_attr( $field['filter_type'] ) : '',
				'options' => [
					''          => esc_html__( 'None', 'wpforms-lite' ),
					'allowlist' => esc_html__( 'Allowlist', 'wpforms-lite' ),
					'denylist'  => esc_html__( 'Denylist', 'wpforms-lite' ),
				],
			],
			false
		);
		$this->field_element(
			'row',
			$field,
			[
				'slug'    => 'filter_type',
				'content' => $filter_type_label . $filter_type_field,
			]
		);

		$this->field_element(
			'row',
			$field,
			[
				'slug'    => 'allowlist',
				'content' => $this->field_element(
					'textarea',
					$field,
					[
						'slug'  => 'allowlist',
						'value' => ! empty( $field['allowlist'] ) ? esc_attr( $field['allowlist'] ) : '',
					],
					false
				),
			]
		);

		$this->field_element(
			'row',
			$field,
			[
				'slug'    => 'denylist',
				'content' => $this->field_element(
					'textarea',
					$field,
					[
						'slug'  => 'denylist',
						'value' => ! empty( $field['denylist'] ) ? esc_attr( $field['denylist'] ) : '',
					],
					false
				),
			]
		);

		// Options close markup.
		$args = array(
			'markup' => 'close',
		);
		$this->field_option( 'advanced-options', $field, $args );
	}

	/**
	 * Field preview inside the builder.
	 *
	 * @since 1.0.0
	 * @param array $field
	 */
	public function field_preview( $field ) {

		// Define data.
		$placeholder         = ! empty( $field['placeholder'] ) ? esc_attr( $field['placeholder'] ) : '';
		$confirm_placeholder = ! empty( $field['confirmation_placeholder'] ) ? esc_attr( $field['confirmation_placeholder'] ) : '';
		$confirm             = ! empty( $field['confirmation'] ) ? 'enabled' : 'disabled';

		// Label.
		$this->field_preview_option( 'label', $field );
		?>

		<div class="wpforms-confirm wpforms-confirm-<?php echo $confirm; ?>">

			<div class="wpforms-confirm-primary">
				<input type="email" placeholder="<?php echo $placeholder; ?>" class="primary-input" disabled>
				<label class="wpforms-sub-label"><?php esc_html_e( 'Email', 'wpforms-lite' ); ?></label>
			</div>

			<div class="wpforms-confirm-confirmation">
				<input type="email" placeholder="<?php echo $confirm_placeholder; ?>" class="secondary-input" disabled>
				<label class="wpforms-sub-label"><?php esc_html_e( 'Confirm Email', 'wpforms-lite' ); ?></label>
			</div>

		</div>

		<?php
		// Description.
		$this->field_preview_option( 'description', $field );
	}

	/**
	 * Field display on the form front-end.
	 *
	 * @since 1.0.0
	 * @param array $field
	 * @param array $deprecated
	 * @param array $form_data
	 */
	public function field_display( $field, $deprecated, $form_data ) {

		// Define data.
		$form_id      = absint( $form_data['id'] );
		$confirmation = ! empty( $field['confirmation'] );
		$primary      = $field['properties']['inputs']['primary'];
		$secondary    = ! empty( $field['properties']['inputs']['secondary'] ) ? $field['properties']['inputs']['secondary'] : '';

		// Standard email field.
		if ( ! $confirmation ) {

			// Primary field.
			printf(
				'<input type="email" %s %s>',
				wpforms_html_attributes( $primary['id'], $primary['class'], $primary['data'], $primary['attr'] ),
				esc_attr( $primary['required'] )
			);
			$this->field_display_error( 'primary', $field );

		// Confirmation email field configuration.
		} else {

			// Row wrapper.
			echo '<div class="wpforms-field-row wpforms-field-' . sanitize_html_class( $field['size'] ) . '">';

				// Primary field.
				echo '<div ' . wpforms_html_attributes( false, $primary['block'] ) . '>';
					$this->field_display_sublabel( 'primary', 'before', $field );
					printf(
						'<input type="email" %s %s>',
						wpforms_html_attributes( $primary['id'], $primary['class'], $primary['data'], $primary['attr'] ),
						$primary['required']
					);
					$this->field_display_sublabel( 'primary', 'after', $field );
					$this->field_display_error( 'primary', $field );
				echo '</div>';

				// Secondary field.
				echo '<div ' . wpforms_html_attributes( false, $secondary['block'] ) . '>';
					$this->field_display_sublabel( 'secondary', 'before', $field );
					printf(
						'<input type="email" %s %s>',
						wpforms_html_attributes( $secondary['id'], $secondary['class'], $secondary['data'], $secondary['attr'] ),
						$secondary['required']
					);
					$this->field_display_sublabel( 'secondary', 'after', $field );
					$this->field_display_error( 'secondary', $field );
				echo '</div>';

			echo '</div>';

		} // End if().
	}

	/**
	 * Format and sanitize field.
	 *
	 * @since 1.3.0
	 * @param int   $field_id     Field ID.
	 * @param mixed $field_submit Field value that was submitted.
	 * @param array $form_data    Form data and settings.
	 */
	public function format( $field_id, $field_submit, $form_data ) {

		// Define data.
		if ( is_array( $field_submit ) ) {
			$value = ! empty( $field_submit['primary'] ) ? $field_submit['primary'] : '';
		} else {
			$value = ! empty( $field_submit ) ? $field_submit : '';
		}

		$name  = ! empty( $form_data['fields'][ $field_id ] ['label'] ) ? $form_data['fields'][ $field_id ]['label'] : '';

		// Set final field details.
		wpforms()->process->fields[ $field_id ] = array(
			'name'  => sanitize_text_field( $name ),
			'value' => sanitize_text_field( $value ),
			'id'    => absint( $field_id ),
			'type'  => $this->type,
		);
	}

	/**
	 * Validate field on form submit.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $field_id     Field ID.
	 * @param mixed $field_submit Field value that was submitted.
	 * @param array $form_data    Form data and settings.
	 */
	public function validate( $field_id, $field_submit, $form_data ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		$form_id = (int) $form_data['id'];

		parent::validate( $field_id, $field_submit, $form_data );

		if ( ! is_array( $field_submit ) && ! empty( $field_submit ) ) {
			$field_submit = [
				'primary' => $field_submit,
			];
		}

		// Validate email field with confirmation.
		if ( isset( $form_data['fields'][ $field_id ]['confirmation'] ) && ! empty( $field_submit['primary'] ) && ! empty( $field_submit['secondary'] ) ) {

			if ( ! is_email( $field_submit['primary'] ) ) {
				wpforms()->process->errors[ $form_id ][ $field_id ] = esc_html__( 'The provided email is not valid.', 'wpforms-lite' );

			} elseif ( $field_submit['primary'] !== $field_submit['secondary'] ) {
				wpforms()->process->errors[ $form_id ][ $field_id ] = esc_html__( 'The provided emails do not match.', 'wpforms-lite' );

			} elseif ( ! $this->is_restricted_email( $field_submit['primary'], $form_data['fields'][ $field_id ] ) ) {
				wpforms()->process->errors[ $form_id ][ $field_id ] = wpforms_setting( 'validation-email-restricted', esc_html__( 'This email address is not allowed.', 'wpforms-lite' ) );
			}
		}

		// Validate regular email field, without confirmation.
		if ( ! isset( $form_data['fields'][ $field_id ]['confirmation'] ) && ! empty( $field_submit['primary'] ) ) {

			if ( ! is_email( $field_submit['primary'] ) ) {
				wpforms()->process->errors[ $form_id ][ $field_id ] = esc_html__( 'The provided email is not valid.', 'wpforms-lite' );

			} elseif ( ! $this->is_restricted_email( $field_submit['primary'], $form_data['fields'][ $field_id ] ) ) {
				wpforms()->process->errors[ $form_id ][ $field_id ] = wpforms_setting( 'validation-email-restricted', esc_html__( 'This email address is not allowed.', 'wpforms-lite' ) );
			}
		}
	}

	/**
	 * Ajax handler to detect restricted email.
	 *
	 * @since 1.6.3
	 */
	public function ajax_check_restricted_email() {

		$token = wpforms()->get( 'token' );
		if ( ! $token || ! $token->verify( filter_input( INPUT_POST, 'token', FILTER_SANITIZE_STRING ) ) ) {
			wp_send_json_error();
		}
		$form_id  = filter_input( INPUT_POST, 'form_id', FILTER_SANITIZE_NUMBER_INT );
		$field_id = filter_input( INPUT_POST, 'field_id', FILTER_SANITIZE_NUMBER_INT );
		$email    = filter_input( INPUT_POST, 'email', FILTER_SANITIZE_EMAIL );
		if ( ! $form_id || ! $field_id || ! $email ) {
			wp_send_json_error();
		}
		$form_data = wpforms()->form->get(
			$form_id,
			array( 'content_only' => true )
		);
		if ( empty( $form_data['fields'][ $field_id ] ) ) {
			wp_send_json_error();
		}
		wp_send_json_success(
			$this->is_restricted_email( $email, $form_data['fields'][ $field_id ] )
		);
	}

	/**
	 * Sanitize restricted rules.
	 *
	 * @since 1.6.3
	 */
	public function ajax_sanitize_restricted_rules() {

		// Run a security check.
		check_ajax_referer( 'wpforms-builder', 'nonce' );
		$content = filter_input( INPUT_GET, 'content', FILTER_SANITIZE_STRING );
		if ( ! $content ) {
			wp_send_json_error();
		}
		$rules = $this->sanitize_restricted_rules( $content );

		wp_send_json_success(
			implode( PHP_EOL, $rules )
		);
	}

	/**
	 * Sanitize restricted rules.
	 *
	 * @since 1.6.3
	 *
	 * @param string $content Content.
	 *
	 * @return array
	 */
	private function sanitize_restricted_rules( $content ) {

		$patterns = array_filter( preg_split( '/\r\n|\r|\n|,/', $content ) );

		foreach ( $patterns as $key => $pattern ) {
			$pattern = trim( $pattern );
			if ( ! $pattern ) {
				unset( $patterns[ $key ] );
			}
			// Strip all deny symbols for prevent double convert.
			$pattern = strtolower( $pattern );
			// Symbol '*' allow using in field settings. Symbol '-' need to email with domain in punycode.
			$patterns[ $key ] = preg_replace( '/[^a-z0-9@.*-]/', '', $pattern );
		}

		return ! empty( $patterns ) ? array_filter( $patterns ) : [];
	}

	/**
	 * The check is a restricted email.
	 *
	 * @since 1.6.3
	 *
	 * @param string $email Email string.
	 * @param array  $field Field data.
	 *
	 * @return bool
	 */
	private function is_restricted_email( $email, $field ) {

		if ( empty( $field['filter_type'] ) || empty( $field[ $field['filter_type'] ] ) ) {
			return true;
		}
		$email    = strtolower( $email );
		$patterns = $this->sanitize_restricted_rules( $field[ $field['filter_type'] ] );
		$patterns = array_unique( array_map( [ $this, 'sanitize_email_pattern' ], $patterns ) );

		$check = 'allowlist' === $field['filter_type'];
		foreach ( $patterns as $pattern ) {
			if ( true === (bool) preg_match( '/' . $pattern . '/', $email ) ) {
				return $check;
			}
		}
		return ! $check;
	}

	/**
	 * Sanitize from email patter a REGEX pattern.
	 *
	 * @since 1.6.3
	 *
	 * @param string $pattern Pattern line.
	 *
	 * @return string
	 */
	private function sanitize_email_pattern( $pattern ) {

		// Create regex pattern from a string.
		return '^' . str_replace( [ '.', '*' ], [ '\.', '.*' ], $pattern ) . '$';
	}
}
