<?php
/**
 * Abstract Settings API Class
 *
 * @package WC_Instagram/Admin/Settings
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Settings_API', false ) ) {
	include_once dirname( WC_PLUGIN_FILE ) . '/includes/abstracts/abstract-wc-settings-api.php';
}

if ( ! class_exists( 'WC_Instagram_Settings_API', false ) ) {
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
		 * Constructor.
		 *
		 * @since 3.0.0
		 */
		public function __construct() {
			add_filter( 'woocommerce_settings_api_sanitized_fields_' . $this->id, array( $this, 'sanitized_fields' ) );
		}

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
		 */
		public function output_notices() {}

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
			$this->init_form_fields();
			$this->init_settings();

			$this->enqueue_scripts();
			$this->output_notices();
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

			$post_data = $this->get_post_data();

			foreach ( $this->get_form_fields() as $key => $field ) {
				if ( $this->is_setting_field( $field ) ) {
					try {
						$this->settings[ $key ] = $this->get_field_value( $key, $field, $post_data );
					} catch ( Exception $e ) {
						$this->add_error( $e->getMessage() );
					}
				}
			}

			$this->before_save();

			$saved = update_option( $this->get_option_key(), apply_filters( 'woocommerce_settings_api_sanitized_fields_' . $this->id, $this->settings ), 'yes' );

			$this->after_save( $saved );

			return $saved;
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
			return $this->sanitize_subset_fields( $settings );
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
		 * @return array An array with the tags.
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
			if ( ! $value && ! is_int( $value ) ) {
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

			$data      = wp_parse_args( $data, $defaults );
			$field_key = $this->get_field_key( $key );
			$value     = $this->get_option( $field_key );

			ob_start();
			$this->output_field_start( $key, $data );

			echo '<ul>';

			foreach ( $data['options'] as $key => $label ) :
				$input = sprintf(
					'<input type="radio" id="%1$s" name="%1$s" value="%2$s" class="%3$s" style="%4$s"%5$s%6$s />',
					esc_attr( $field_key ),
					esc_attr( $key ),
					esc_attr( $data['class'] ),
					esc_attr( $data['css'] ),
					checked( $key, $value, false ),
					wp_kses_post( $this->get_custom_attribute_html( $data ) )
				);

				echo '<li><label>' . $input . ' ' . wp_kses_post( $label ) . '</label></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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

			$field_key    = $this->get_field_key( $key );
			$tip_in_label = version_compare( WC_VERSION, '3.4', '>=' );
			$tip_html     = $this->get_tooltip_html( $data );
			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<?php
					if ( ! $tip_in_label ) :
						echo $tip_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					endif;

					printf(
						'<label for="%1$s">%2$s%3$s</label>',
						esc_attr( $field_key ),
						wp_kses_post( $data['title'] ),
						( $tip_in_label ? " {$tip_html}" : '' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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
}
