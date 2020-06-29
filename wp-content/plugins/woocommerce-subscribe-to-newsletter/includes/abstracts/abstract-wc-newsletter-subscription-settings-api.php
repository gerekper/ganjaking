<?php
/**
 * Abstract Settings API class
 *
 * @package WC_Newsletter_Subscription/Abstracts
 * @since   2.8.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Settings_API', false ) ) {
	include_once dirname( WC_PLUGIN_FILE ) . '/includes/abstracts/abstract-wc-settings-api.php';
}

if ( class_exists( 'WC_Newsletter_Subscription_Settings_API', false ) ) {
	return;
}

/**
 * Class WC_Newsletter_Subscription_Settings_API
 */
abstract class WC_Newsletter_Subscription_Settings_API extends WC_Settings_API {

	/**
	 * The plugin ID. Used for option names.
	 *
	 * @var string
	 */
	public $plugin_id = 'wc_newsletter_subscription_';

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
	 * Save the settings individually or grouped in a single option.
	 *
	 * @var bool
	 */
	public $save_individually = true;

	/**
	 * Constructor.
	 *
	 * @since 2.8.0
	 */
	public function __construct() {
		add_filter( 'woocommerce_settings_api_sanitized_fields_' . $this->id, array( $this, 'sanitized_fields' ) );
	}

	/**
	 * Gets the name of the option in the WP DB.
	 *
	 * @since 2.8.0
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
	 * @since 2.8.0
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
	 * @since 2.8.0
	 *
	 * @param string $key The field key.
	 * @return array|false An array with the form field data. False otherwise.
	 */
	public function get_form_field( $key ) {
		if ( ! empty( $this->form_fields ) ) {
			$this->init_form_fields();
		}

		$fields = $this->get_form_fields();

		return ( ! empty( $fields[ $key ] ) ? $fields[ $key ] : false );
	}

	/**
	 * Gets a field's posted and validated value.
	 *
	 * @since 2.8.0
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
	 * @since 2.8.0
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
	 * @since 2.8.0
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
	 * @since 2.8.0
	 */
	public function enqueue_scripts() {}

	/**
	 * Outputs the settings notices.
	 *
	 * @since 2.8.0
	 */
	public function output_notices() {}

	/**
	 * Outputs the settings screen heading.
	 *
	 * @since 2.8.0
	 */
	public function output_heading() {
		echo '<h2>' . esc_html( $this->form_title );
		if ( 'settings' !== $this->id ) :
			wc_back_link( _x( 'Return to the Extension settings', 'settings back link label', 'woocommerce-subscribe-to-newsletter' ), wc_newsletter_subscription_get_settings_url() );
		endif;
		echo '</h2>';

		echo wp_kses_post( wpautop( $this->form_description ) );
	}

	/**
	 * Initialise settings.
	 *
	 * @since 2.8.0
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
	 * Output the settings screen.
	 *
	 * @since 2.8.0
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
	 * @since 2.8.0
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

		return $this->save();
	}

	/**
	 * Add an error message for display in admin on save.
	 *
	 * @since 2.8.0
	 *
	 * @param string $error Error message.
	 */
	public function add_error( $error ) {
		parent::add_error( $error );

		// Prevent displaying the success notice.
		WC_Admin_Settings::add_error( $error );
	}

	/**
	 * Saves the settings.
	 *
	 * @since 2.8.0
	 *
	 * @return bool was anything saved?
	 */
	public function save() {
		/** This filter is documented in woocommerce/includes/abstracts/abstract-wc-settings-api.php */
		$settings = apply_filters( 'woocommerce_settings_api_sanitized_fields_' . $this->id, $this->settings );

		if ( $this->save_individually ) {
			$saved = false;

			foreach ( $settings as $key => $value ) {
				$updated = update_option( $this->get_option_key( $key ), $value );
				$saved   = ( $saved || $updated );
			}
		} else {
			$saved = update_option( $this->get_option_key(), $settings, 'yes' );
		}

		return $saved;
	}

	/**
	 * Gets if there are error messages to display or not.
	 *
	 * @since 2.8.0
	 *
	 * @return bool
	 */
	public function has_errors() {
		return ( ! empty( $this->errors ) );
	}

	/**
	 * Sanitize the settings before save the option.
	 *
	 * @since 2.8.0
	 *
	 * @param array $settings The settings to sanitize.
	 * @return array
	 */
	public function sanitized_fields( $settings ) {
		return $settings;
	}

	/**
	 * Validates a field with an array as value.
	 *
	 * @since 2.8.0
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
	 * @since 2.8.0
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
	 * @since 3.0.0
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

		$field_key = $this->get_field_key( $key );
		$value     = $this->get_option( $field_key );

		ob_start();

		$this->output_field_start( $key, $data );

		echo '<ul>';

		foreach ( $data['options'] as $key => $label ) :
			$input = sprintf(
				'<input type="radio" id="%1$s" name="%1$s" value="%2$s" class="%3$s" style="%4$s"%5$s%6$s /> %7$s',
				esc_attr( $field_key ),
				esc_attr( $key ),
				esc_attr( $data['class'] ),
				esc_attr( $data['css'] ),
				checked( $key, $value, false ),
				wp_kses_post( $this->get_custom_attribute_html( $data ) ),
				esc_html( $label )
			);

			echo '<li><label>' . $input . '</label></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		endforeach;

		echo '</ul>';

		$this->output_field_end( $key, $data );

		return ob_get_clean();
	}

	/**
	 * Outputs the HTML at the start of a field.
	 *
	 * @since 2.8.0
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
				<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<?php echo wp_kses_post( $this->get_tooltip_html( $data ) ); ?>
			</th>
			<td class="forminp forminp-<?php echo esc_attr( $data['type'] ); ?>">
				<fieldset>
					<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
		<?php
	}

	/**
	 * Outputs the HTML at the end of a field.
	 *
	 * @since 2.8.0
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
