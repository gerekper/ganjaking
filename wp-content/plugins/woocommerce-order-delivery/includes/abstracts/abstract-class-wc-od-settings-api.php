<?php
/**
 * Abstract Settings API Class
 *
 * @package WC_OD/Abstracts
 * @since   1.5.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Settings_API', false ) ) {
	include_once dirname( WC_PLUGIN_FILE ) . '/includes/abstracts/abstract-wc-settings-api.php';
}

if ( class_exists( 'WC_OD_Settings_API', false ) ) {
	return;
}

/**
 * Class WC_OD_Settings_API
 */
abstract class WC_OD_Settings_API extends WC_Settings_API {

	/**
	 * The plugin ID. Used for option names.
	 *
	 * @since 1.5.0
	 *
	 * @var string
	 */
	public $plugin_id = 'wc_od_';

	/**
	 * Save the settings individually or grouped in a single option.
	 *
	 * @since 1.7.0
	 *
	 * @var bool
	 */
	public $save_individually = true;

	/**
	 * Constructor.
	 *
	 * @since 1.5.0
	 */
	public function __construct() {}

	/**
	 * Return the name of the option in the WP DB.
	 *
	 * @since 1.5.0
	 * @since 1.7.0 Added `$setting` parameter.
	 *
	 * @param string $setting Optional. Setting key.
	 * @return string
	 */
	public function get_option_key( $setting = '' ) {
		$option_key = $this->plugin_id . $this->id;

		// The setting key when saving them individually.
		if ( $setting ) {
			$option_key .= "_{$setting}";
		}

		return $option_key;
	}

	/**
	 * Prefix key for settings.
	 *
	 * @since 1.5.0
	 *
	 * @param  string $key Field key.
	 * @return string
	 */
	public function get_field_key( $key ) {
		return $key;
	}

	/**
	 * Gets a form field by key.
	 *
	 * @since 1.7.0
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
	 * @since 1.7.0
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
	 * @since 1.7.0
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
	 * @since 1.5.0
	 *
	 * @return array
	 */
	public function get_form_fields_defaults() {
		$form_fields = $this->get_form_fields();
		$form_fields = array_filter( $form_fields, array( $this, 'is_setting_field' ) );

		return array_merge( array_fill_keys( array_keys( $form_fields ), '' ), wp_list_pluck( $form_fields, 'default' ) );
	}

	/**
	 * Initialise Settings.
	 *
	 * @since 1.5.0
	 */
	public function init_settings() {
		$defaults = $this->get_form_fields_defaults();

		if ( $this->save_individually ) {
			$settings = array();

			foreach ( $defaults as $key => $default ) {
				$settings[ $key ] = get_option( $this->get_option_key( $key ), $default );
			}
		} else {
			$values   = get_option( $this->get_option_key(), array() );
			$settings = array_merge( $defaults, ( is_array( $values ) ? $values : array() ) );
		}

		$this->settings = $settings;
	}

	/**
	 * Enqueues the settings scripts.
	 *
	 * @since 1.6.0
	 */
	public function enqueue_scripts() {}

	/**
	 * Outputs the settings notices.
	 *
	 * @since 1.7.0
	 */
	public function output_notices() {}

	/**
	 * Outputs the settings screen heading.
	 *
	 * @since 1.6.0
	 */
	public function output_heading() {
		echo '<h2>' . esc_html( $this->get_form_title() );
		$this->output_heading_backlink();
		echo '</h2>';

		$description = $this->get_form_description();

		if ( $description ) :
			echo wp_kses_post( wpautop( $description ) );
		endif;
	}

	/**
	 * Gets the form title.
	 *
	 * @since 1.7.0
	 *
	 * @return string
	 */
	public function get_form_title() {
		return '';
	}

	/**
	 * Gets the form description.
	 *
	 * @since 1.7.0
	 *
	 * @return string
	 */
	public function get_form_description() {
		return '';
	}

	/**
	 * Outputs the backlink in the heading.
	 *
	 * @since 1.7.0
	 */
	public function output_heading_backlink() {}

	/**
	 * Output the settings screen.
	 *
	 * @since 1.6.0
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
	 * @since 1.6.0
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

		return $this->save();
	}

	/**
	 * Validates the settings.
	 *
	 * The non-returned settings won't be updated.
	 *
	 * @since 1.7.0
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
	 * @since 1.7.0
	 *
	 * @return bool was anything saved?
	 */
	public function save() {
		$settings = $this->sanitized_fields( $this->settings );

		if ( $this->save_individually ) {
			$saved = false;

			foreach ( $settings as $key => $value ) {
				$updated = update_option( $this->get_option_key( $key ), $value );
				$saved   = ( $saved || $updated );
			}
		} else {
			$saved = update_option( $this->get_option_key(), $settings, 'yes' );
		}

		// Backward compatibility.
		$this->after_save( $saved );

		return $saved;
	}

	/**
	 * After saving the form.
	 *
	 * @since 1.6.0
	 *
	 * @param bool $saved Was anything saved?.
	 */
	public function after_save( $saved ) {}

	/**
	 * Redirects to a different page after saving the settings if necessary.
	 *
	 * @since 1.7.0
	 */
	public function maybe_redirect() {}

	/**
	 * Gets if there are error messages to display or not.
	 *
	 * @since 1.6.0
	 *
	 * @return bool
	 */
	public function has_errors() {
		return ( ! empty( $this->errors ) );
	}

	/**
	 * Adds an error message for display in admin on save.
	 *
	 * @since 1.7.0
	 *
	 * @param string $error Error message.
	 */
	public function add_error( $error ) {
		parent::add_error( $error );

		// Prevent displaying the success notice.
		WC_Admin_Settings::add_error( $error );
	}

	/**
	 * Validates a field with an array as value.
	 *
	 * @since 1.6.0
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
	 * @since 1.7.0
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
	 * Validates a 'shipping_methods' field.
	 *
	 * @since 1.6.0
	 *
	 * @param string $key   Field key.
	 * @param mixed  $value Posted Value.
	 * @return array An array with the shipping methods.
	 */
	public function validate_shipping_methods_field( $key, $value ) {
		return $this->validate_array_field( $key, $value );
	}

	/**
	 * Validates a table field.
	 *
	 * @since 1.5.0
	 *
	 * @param string $field_id Field ID.
	 * @param mixed  $value    Field value.
	 * @return mixed
	 */
	public function validate_wc_od_table_field( $field_id, $value ) {
		$field          = $this->form_fields[ $field_id ];
		$field['id']    = $field_id;
		$field['value'] = $this->get_option( $field_id );

		$instance = wc_od_get_table_field( $field );

		if ( $instance ) {
			$value = $instance->sanitize_field( $value );
		}

		return $value;
	}

	/**
	 * Sanitizes the settings.
	 *
	 * @since 1.5.0
	 *
	 * @param array $settings The settings to sanitize.
	 * @return array
	 */
	public function sanitized_fields( $settings ) {
		$settings = $this->sanitize_shipping_methods_fields( $settings );

		/** This filter is documented in woocommerce/includes/abstracts/abstract-wc-settings-api.php */
		return apply_filters( 'woocommerce_settings_api_sanitized_fields_' . $this->id, $settings );
	}

	/**
	 * Sanitizes the 'shipping methods' fields.
	 *
	 * @since 1.6.0
	 *
	 * @param array $settings The settings to sanitize.
	 * @return array
	 */
	protected function sanitize_shipping_methods_fields( $settings ) {
		if ( ! isset( $settings['shipping_methods_option'] ) ) {
			return $settings;
		}

		$settings['shipping_methods'] = array();

		if ( ! empty( $settings['shipping_methods_option'] ) ) {
			$setting_key = "{$settings['shipping_methods_option']}_shipping_methods";

			$settings['shipping_methods'] = ( ! empty( $settings[ $setting_key ] ) ? $settings[ $setting_key ] : array() );
		}

		unset( $settings['specific_shipping_methods'], $settings['all_except_shipping_methods'] );

		return $settings;
	}

	/**
	 * Generate the HTML for a table field.
	 *
	 * @since 1.5.0
	 *
	 * @param string $field_id Field ID.
	 * @param array  $field    Field data.
	 * @return string
	 */
	public function generate_wc_od_table_html( $field_id, $field ) {
		$field['id']    = $field_id;
		$field['value'] = $this->get_option( $field_id );

		ob_start();
		wc_od_field_wrapper( $field );
		return ob_get_clean();
	}

	/**
	 * Generates the HTML for a 'shipping_methods' field.
	 *
	 * @since 1.6.0
	 *
	 * @param string $key  The field key.
	 * @param mixed  $data The field data.
	 * @return string
	 */
	public function generate_shipping_methods_html( $key, $data ) {
		$defaults = array(
			'type'              => 'multiselect',
			'class'             => 'wc-enhanced-select-nostd',
			'css'               => 'width: 400px;',
			'desc_tip'          => true,
			'options'           => wc_od_get_shipping_methods_choices(),
			'custom_attributes' => array(
				'data-placeholder' => __( 'Select shipping methods', 'woocommerce-order-delivery' ),
			),
		);

		$data = wp_parse_args( $data, $defaults );

		return $this->generate_multiselect_html( $key, $data );
	}

	/**
	 * Gets the 'shipping methods' fields ready to be registered in the form fields.
	 *
	 * @since 1.6.0
	 *
	 * @return array
	 */
	protected function get_shipping_methods_fields() {
		return array(
			'shipping_methods_option'     => array(
				'title'    => __( 'Shipping methods', 'woocommerce-order-delivery' ),
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'css'      => 'width: 400px;',
				'desc_tip' => true,
				'options'  => array(
					''           => __( 'All shipping methods', 'woocommerce-order-delivery' ),
					'all_except' => __( 'All shipping methods, except&hellip;', 'woocommerce-order-delivery' ),
					'specific'   => __( 'Only specific shipping methods', 'woocommerce-order-delivery' ),
				),
			),
			'all_except_shipping_methods' => array(
				'title' => __( 'All shipping methods, except&hellip;', 'woocommerce-order-delivery' ),
				'type'  => 'shipping_methods',
			),
			'specific_shipping_methods'   => array(
				'title' => __( 'Only specific shipping methods', 'woocommerce-order-delivery' ),
				'type'  => 'shipping_methods',
			),
		);
	}

	/**
	 * Gets the 'number_of_orders' field ready to be registered in the form fields.
	 *
	 * @since 1.8.0
	 *
	 * @return array
	 */
	protected function get_number_of_orders_field() {
		return array(
			'number_of_orders' => array(
				'title'             => __( 'Number of orders', 'woocommerce-order-delivery' ),
				'description'       => __( '0 means that there is no limit of orders.', 'woocommerce-order-delivery' ),
				'desc_tip'          => __( 'Maximum number of orders that can be delivered on the day.', 'woocommerce-order-delivery' ),
				'type'              => 'number',
				'css'               => 'width: 50px;',
				'custom_attributes' => array(
					'min'  => 0,
					'step' => 1,
				),
			),
		);
	}

	/**
	 * Outputs the HTML at the start of a field.
	 *
	 * @since 1.7.0
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
		$tip_in_label = version_compare( WC()->version, '3.4', '>=' );
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
	 * @since 1.7.0
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
