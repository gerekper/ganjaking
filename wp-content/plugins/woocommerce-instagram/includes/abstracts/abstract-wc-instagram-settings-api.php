<?php
/**
 * Abstract Settings API class
 *
 * @package WC_Instagram/Abstracts
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Settings_API', false ) ) {
	include_once dirname( WC_PLUGIN_FILE ) . '/includes/abstracts/abstract-wc-settings-api.php';
}

if ( class_exists( 'WC_Instagram_Settings_API', false ) ) {
	return;
}

/**
 * WC_Instagram_Settings_API class.
 */
abstract class WC_Instagram_Settings_API extends WC_Settings_API {

	/**
	 * The plugin ID. Used for option names.
	 *
	 * @var string
	 */
	public $plugin_id = 'wc_instagram_';

	/**
	 * Form title.
	 *
	 * @var string
	 */
	public $form_title = '';

	/**
	 * Form description.
	 *
	 * @var string
	 */
	public $form_description = '';

	/**
	 * The keys of the 'subset' fields.
	 *
	 * @var array
	 */
	protected $subset_keys = array();

	/**
	 * Gets the name of the option in the WP DB.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function get_option_key() {
		return $this->plugin_id . $this->id;
	}

	/**
	 * Gets the keys of the 'subset' fields.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function get_subset_keys() {
		return $this->subset_keys;
	}

	/**
	 * Gets the settings from the option stored in the WP DB.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function get_option_settings() {
		$settings = get_option( $this->get_option_key(), array() );

		return ( is_array( $settings ) ? $settings : array() );
	}

	/**
	 * Prefix key for settings.
	 *
	 * @since 3.0.0
	 *
	 * @param  string $key Field key.
	 * @return string
	 */
	public function get_field_key( $key ) {
		return $key;
	}

	/**
	 * Gets the form fields after they are initialized.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	public function get_form_fields() {
		/** This filter is documented in woocommerce/includes/abstracts/abstract-wc-settings-api.php */
		return apply_filters( 'woocommerce_settings_api_form_fields_' . $this->plugin_id . $this->id, array_map( array( $this, 'set_defaults' ), $this->form_fields ) ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingSinceComment
	}

	/**
	 * Gets a form field by key.
	 *
	 * @since 3.0.0
	 *
	 * @param string $key The field key.
	 * @return array|false An array with the form field data. False otherwise.
	 */
	public function get_form_field( $key ) {
		if ( empty( $this->form_fields ) ) {
			$this->init_form_fields();
		}

		$fields = $this->get_form_fields();

		return ( ! empty( $fields[ $key ] ) ? $fields[ $key ] : false );
	}

	/**
	 * Gets a field's posted and validated value.
	 *
	 * @since 4.0.0
	 *
	 * @param string $key Field key.
	 * @param array  $field Field array.
	 * @param array  $post_data Posted data.
	 * @return string
	 */
	public function get_field_value( $key, $field, $post_data = array() ) {
		// Disabled fields are not submitted.
		if ( isset( $field['disabled'] ) && $field['disabled'] ) {
			return ( isset( $this->settings[ $key ] ) ? $this->settings[ $key ] : '' );
		}

		return parent::get_field_value( $key, $field, $post_data );
	}

	/**
	 * Gets if the field value should be included in the settings.
	 *
	 * @since 3.0.0
	 *
	 * @param array $field The form field.
	 * @return bool
	 */
	public function is_setting_field( $field ) {
		return ( 'title' !== $this->get_field_type( $field ) && ( ! isset( $field['no_validate'] ) || ! $field['no_validate'] ) );
	}

	/**
	 * Gets the default values for the form fields.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function get_form_fields_defaults() {
		$form_fields = $this->get_form_fields();
		$form_fields = array_filter( $form_fields, array( $this, 'is_setting_field' ) );

		return array_merge( array_fill_keys( array_keys( $form_fields ), '' ), wp_list_pluck( $form_fields, 'default' ) );
	}

	/**
	 * Enqueues the settings scripts.
	 *
	 * @since 3.0.0
	 */
	public function enqueue_scripts() {}

	/**
	 * Outputs the settings notices.
	 *
	 * @since 3.0.0
	 * @deprecated 4.1.2
	 */
	public function output_notices() {
		wc_deprecated_function( __FUNCTION__, '4.1.2' );
	}

	/**
	 * Outputs the settings screen heading.
	 *
	 * @since 3.0.0
	 */
	public function output_heading() {
		echo '<h2>' . esc_html( $this->form_title );
		if ( 'settings' !== $this->id ) :
			wc_back_link( _x( 'Return to the Instagram settings', 'settings back link label', 'woocommerce-instagram' ), wc_instagram_get_settings_url() );
		endif;
		echo '</h2>';

		echo wp_kses_post( wpautop( $this->form_description ) );
		echo '<input type="hidden" name="section" value="instagram" />';
	}

	/**
	 * Initialise Settings.
	 *
	 * @since 3.0.0
	 */
	public function init_settings() {
		$settings = $this->get_option_settings();
		$settings = $this->init_subset_fields_settings( $settings );

		$this->settings = array_merge( $this->get_form_fields_defaults(), $settings );
	}

	/**
	 * Initializes the settings of the 'subset' fields.
	 *
	 * @since 3.0.0
	 *
	 * @param array $settings The settings.
	 * @return array
	 */
	public function init_subset_fields_settings( $settings ) {
		$subset_keys = $this->get_subset_keys();

		// Process 'subset' fields settings.
		foreach ( $subset_keys as $key ) {
			// Rename the setting key from '$key' to '{subset_option_key}_{$key}'.
			if ( ! empty( $settings[ $key ] ) && ! empty( $settings[ "{$key}_option" ] ) ) {
				$option_key = $settings[ "{$key}_option" ] . '_' . $key;

				$settings[ $option_key ] = $settings[ $key ];

			}

			unset( $settings[ $key ] );
		}

		return $settings;
	}

	/**
	 * Output the settings screen.
	 *
	 * @since 3.0.0
	 */
	public function admin_options() {
		if ( empty( $this->form_fields ) ) {
			$this->init_form_fields();
		}

		if ( empty( $this->settings ) ) {
			$this->init_settings();
		}

		$this->enqueue_scripts();
		$this->output_heading();

		parent::admin_options();
	}

	/**
	 * Processes and saves options.
	 *
	 * @since 3.0.0
	 *
	 * @return bool was anything saved?
	 */
	public function process_admin_options() {
		$this->init_form_fields();
		$this->init_settings();

		$fields    = $this->get_form_fields();
		$post_data = $this->get_post_data();
		$settings  = array();

		foreach ( $fields as $key => $field ) {
			if ( $this->is_setting_field( $field ) ) {
				try {
					$settings[ $key ] = $this->get_field_value( $key, $field, $post_data );
				} catch ( Exception $e ) {
					$this->add_error( $e->getMessage() );
				}
			}
		}

		$this->settings = array_merge( $this->settings, $this->validate_fields( $settings ) );

		$this->before_save();

		$saved = $this->save();

		$this->after_save( $saved );

		return $saved;
	}

	/**
	 * Validates the settings.
	 *
	 * The non-returned settings won't be updated.
	 *
	 * @since 4.0.0
	 *
	 * @param array $settings The settings to validate.
	 * @return array
	 */
	public function validate_fields( $settings ) {
		return $settings;
	}

	/**
	 * Saves the settings.
	 *
	 * @since 4.0.0
	 *
	 * @return bool was anything saved?
	 */
	public function save() {
		$settings = $this->sanitized_fields( $this->settings );

		return update_option( $this->get_option_key(), $settings, 'yes' );
	}

	/**
	 * Add an error message for display in admin on save.
	 *
	 * @since 3.0.0
	 *
	 * @param string $error Error message.
	 */
	public function add_error( $error ) {
		parent::add_error( $error );

		// Prevent displaying the success notice.
		WC_Admin_Settings::add_error( $error );
	}

	/**
	 * Before saving the form.
	 *
	 * @since 3.0.0
	 */
	public function before_save() {}

	/**
	 * After saving the form.
	 *
	 * @since 3.0.0
	 *
	 * @param bool $saved Was anything saved?.
	 */
	public function after_save( $saved ) {}

	/**
	 * Gets if there are error messages to display or not.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public function has_errors() {
		return ( ! empty( $this->errors ) );
	}

	/**
	 * Sanitize the settings before save the option.
	 *
	 * @since 3.0.0
	 *
	 * @param array $settings The settings to sanitize.
	 * @return array
	 */
	public function sanitized_fields( $settings ) {
		$settings = $this->sanitize_subset_fields( $settings );

		/** This filter is documented in woocommerce/includes/abstracts/abstract-wc-settings-api.php */
		return apply_filters( 'woocommerce_settings_api_sanitized_fields_' . $this->plugin_id . $this->id, $settings ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingSinceComment
	}

	/**
	 * Sanitizes the 'subset' fields.
	 *
	 * @since 3.0.0
	 *
	 * @param array $settings The settings to sanitize.
	 * @return array
	 */
	protected function sanitize_subset_fields( $settings ) {
		$subset_keys = $this->get_subset_keys();

		// Sanitizes 'subset' fields settings.
		foreach ( $subset_keys as $key ) {
			$settings[ $key ] = array();

			$option_field_key = "{$key}_option";
			$option_field     = $this->get_form_field( $option_field_key );

			// Rename the setting key from '{subset_option_key}_{$key}' to '$key'.
			if ( ! empty( $settings[ $option_field_key ] ) ) {
				$option_key = $settings[ $option_field_key ] . '_' . $key;

				$settings[ $key ] = ( ! empty( $settings[ $option_key ] ) ? $settings[ $option_key ] : array() );

				// Reset the option field when the subset is empty.
				if ( empty( $settings[ $key ] ) ) {
					$settings[ $option_field_key ] = '';
				}
			}

			// Remove 'subset' options settings.
			foreach ( $option_field['options'] as $option_key => $option_label ) {
				if ( ! empty( $option_key ) ) {
					unset( $settings[ "{$option_key}_{$key}" ] );
				}
			}
		}

		return $settings;
	}

	/**
	 * Validates a field with an array as value.
	 *
	 * @since 3.0.0
	 *
	 * @param string $key   Field key.
	 * @param mixed  $value Posted Value.
	 * @return array
	 */
	public function validate_array_field( $key, $value ) {
		if ( $value && ! is_array( $value ) ) {
			$value = array_map( 'trim', explode( ',', $value ) );
		} elseif ( empty( $value ) ) {
			$value = array();
		}

		return array_map( 'wc_clean', array_map( 'wp_unslash', $value ) );
	}

	/**
	 * Validates a numeric field.
	 *
	 * @since 3.0.0
	 *
	 * @param string $key   The setting key.
	 * @param mixed  $value The setting value.
	 * @return int
	 */
	public function validate_number_field( $key, $value ) {
		$field = $this->get_form_field( $key );

		if ( ! $field ) {
			return $value;
		}

		// Set up to the default value.
		if ( ! $value && ! is_numeric( $value ) ) {
			$default = $this->get_field_default( $field );

			if ( '' !== $default ) {
				$value = $default;
			}
		}

		$value = intval( $value );

		// Check range.
		if ( ! empty( $field['custom_attributes'] ) ) {
			if ( ! empty( $field['custom_attributes']['min'] ) && $field['custom_attributes']['min'] > $value ) {
				$value = $field['custom_attributes']['min'];
			} elseif ( ! empty( $field['custom_attributes']['max'] ) && $field['custom_attributes']['max'] < $value ) {
				$value = $field['custom_attributes']['max'];
			}
		}

		return $value;
	}

	/**
	 * Generates the HTML for a radio field.
	 *
	 * @since 3.5.0
	 *
	 * @param string $key  The field key.
	 * @param mixed  $data The field data.
	 * @return string
	 */
	public function generate_radio_html( $key, $data ) {
		$defaults = array(
			'title'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array(),
			'options'           => array(),
		);

		$data = wp_parse_args( $data, $defaults );

		$field_key   = $this->get_field_key( $key );
		$field_value = $this->get_option( $field_key );

		ob_start();

		$this->output_field_start( $key, $data );

		echo '<ul>';

		foreach ( $data['options'] as $value => $label ) :
			echo '<li><label>';
			printf(
				'<input type="radio" id="%1$s" name="%1$s" value="%2$s" class="%3$s" style="%4$s"%5$s%6$s /> ',
				esc_attr( $field_key ),
				esc_attr( $value ),
				esc_attr( $data['class'] ),
				esc_attr( $data['css'] ),
				checked( $value, $field_value, false ),
				wp_kses_post( $this->get_custom_attribute_html( $data ) )
			);
			echo wp_kses_post( $label ) . '</label></li>';
		endforeach;

		echo '</ul>';

		$this->output_field_end( $key, $data );

		return ob_get_clean();
	}

	/**
	 * Generates the 'subset' fields.
	 *
	 * @since 3.0.0
	 *
	 * @param string $subset_key   The subset key.
	 * @param array  $option_field The option field data.
	 * @param mixed  $subset_field The subset field data or a string with the field type.
	 * @return array
	 */
	public function generate_subset_fields( $subset_key, $option_field, $subset_field ) {
		if ( empty( $option_field['options'] ) ) {
			return array();
		}

		$option_field = wp_parse_args(
			$option_field,
			array(
				'type'  => 'select',
				'class' => '',
			)
		);

		// Force the subset class.
		$option_field['class'] = trim( 'wc-instagram-field-subset-option ' . $option_field['class'] );

		$form_fields = array(
			"{$subset_key}_option" => $option_field,
		);

		if ( is_string( $subset_field ) ) {
			$subset_field = array(
				'type' => $subset_field,
			);
		}

		foreach ( $option_field['options'] as $option_key => $option_label ) {
			if ( ! empty( $option_key ) ) {
				$form_fields[ "{$option_key}_{$subset_key}" ] = wp_parse_args(
					array( 'title' => $option_label ),
					$subset_field
				);
			}
		}

		// Register the subset key.
		$this->subset_keys[] = $subset_key;

		return $form_fields;
	}

	/**
	 * Generate Hidden Input HTML.
	 *
	 * @param string $key Field key.
	 * @param array  $data Field data.
	 * @since  1.0.0
	 * @return string
	 */
	public function generate_hidden_html( $key, $data ) {
		$field_key = $this->get_field_key( $key );
		$defaults  = array(
			'disabled'          => false,
			'class'             => '',
			'type'              => 'hidden',
			'custom_attributes' => array(),
		);

		$data = wp_parse_args( $data, $defaults );

		return sprintf(
			'<input class="input-text regular-input %1$s" type="%2$s" name="%3$s" id="%3$s" value="%4$s" %5$s %6$s />',
			esc_attr( $data['class'] ),
			esc_attr( $data['type'] ),
			esc_attr( $field_key ),
			esc_attr( $this->get_option( $key ) ),
			disabled( $data['disabled'], true, false ),
			$this->get_custom_attribute_html( $data )
		);
	}

	/**
	 * Outputs the HTML at the start of a field.
	 *
	 * @since 3.0.0
	 *
	 * @param string $key  The field key.
	 * @param mixed  $data The field data.
	 */
	protected function output_field_start( $key, $data ) {
		$data = wp_parse_args(
			$data,
			array(
				'title'       => '',
				'desc_tip'    => false,
				'description' => '',
			)
		);

		$field_key = $this->get_field_key( $key );
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<?php
				printf(
					'<label for="%1$s">%2$s %3$s</label>',
					esc_attr( $field_key ),
					wp_kses_post( $data['title'] ),
					wp_kses_post( $this->get_tooltip_html( $data ) )
				);
				?>
			</th>
			<td class="forminp forminp-<?php echo esc_attr( $data['type'] ); ?>">
				<fieldset>
					<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
		<?php
	}

	/**
	 * Outputs the HTML at the end of a field.
	 *
	 * @since 3.0.0
	 *
	 * @param string $key  The field key.
	 * @param mixed  $data The field data.
	 */
	protected function output_field_end( $key, $data ) {
		$data = wp_parse_args(
			$data,
			array(
				'desc_tip'    => false,
				'description' => '',
			)
		);
		?>
					<?php echo wp_kses_post( $this->get_description_html( $data ) ); ?>
				</fieldset>
			</td>
		</tr>
		<?php
	}
}
