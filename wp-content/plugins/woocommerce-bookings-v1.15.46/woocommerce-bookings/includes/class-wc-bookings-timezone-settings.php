<?php
/**
 * Class WC_Booking_Timezone_Settings
 *
 * Defines timezone related settings.
 *
 * @package Woocommerce/Bookings
 */

/**
 * Class WC_Booking_Timezone_Settings
 *
 * Singleton class for Bookings timezone settings.
 *
 * @package Woocommerce/Bookings
 */
class WC_Bookings_Timezone_Settings extends WC_Settings_API {
	/**
	 * The single instance of the class.
	 *
	 * @var $_instance
	 * @since 1.13.0
	 */
	protected static $_instance = null;

	/**
	 * Name for nonce to update timezone settings.
	 *
	 * @since 1.13.0
	 * @var string self::NONCE_NAME
	 */
	const NONCE_NAME   = 'bookings_timezone_settings_nonce';

	/**
	 * Action name for nonce to update timezone settings.
	 *
	 * @since 1.13.0
	 * @var string self::NONCE_ACTION
	 */
	const NONCE_ACTION = 'submit_bookings_timezone_settings';

	/**
	 * Constructor.
	 *
	 * @since 1.13.0
	 */
	private function __construct() {
		$this->plugin_id = "wc_bookings_";
		$this->id = "timezone";

		// Initialize settings and form data.
		add_action( 'init', array( $this, 'init_timezone_settings' ) );
		add_action( 'admin_init', array( $this, 'maybe_save_settings' ) );
	}

	/**
	 * Initialize settings and form data.
	 *
	 * @since 1.13.0
	 * @return void
	 */
	public function init_timezone_settings() {
		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();
	}

	/**
	 * Update settings values from form.
	 *
	 * @since 1.13.0
	 * @return void
	 */
	public function maybe_save_settings() {
		if ( isset( $_POST['Submit'] )
			&& isset( $_POST[ self::NONCE_NAME ] )
			&& wp_verify_nonce( wc_clean( wp_unslash( $_POST[ self::NONCE_NAME ] ) ), self::NONCE_ACTION ) ) {
				$this->process_admin_options();

			echo '<div class="updated"><p>' . esc_html__( 'Settings saved', 'woocommerce-bookings' ) . '</p></div>';

			do_action( 'wc_bookings_timezone_settings_on_save', $this );
		}
	}

	/**
	 * Defines settings fields.
	 *
	 * @since 1.13.0
	 * @return void
	 */
	public function init_form_fields() {
		global $wp_locale;

		$this->form_fields = array(
			'use_server_timezone_for_actions' => array(
				'title'   => __( 'Enable Bookings Timezone Calculation', 'woocommerce-bookings' ),
				'desc'    => __( 'Schedule Bookings events, such as reminder emails and auto-completions of bookings, using your site’s configured timezone.', 'woocommerce-bookings' ),
				'default' => 'no',
				'type'    => 'checkbox',
			),
			'use_client_timezone'             => array(
				'title'   => __( 'Timezone', 'woocommerce-bookings' ),
				'default' => 'no',
				'type'    => 'radio_custom_label',
				'options' => array(
					'yes' => __( 'Display visitor\'s local time', 'woocommerce-bookings' ),
					/* translators: 1: URL to Timezone String settings 2: server timezone */
					'no'  => sprintf( __( 'Display your local time from <a href="%1$s">WordPress settings</a> (%2$s)', 'woocommerce-bookings' ), admin_url( 'options-general.php#timezone_string' ), wc_booking_get_timezone_string() ),
				),
				'notice'  => array(
					'display' => ! wc_booking_has_location_timezone_set(),
					/* translators: 1: URL to Timezone String settings */
					'text'    => sprintf( __( '<div>Due to the sites current UTC offset selection the timezone above is displaying an estimation.</div><div>To ensure accurate timezone setups, select a location based timezone in the <a href="%1$s">WordPress settings</a>.</div>', 'woocommerce-bookings' ), admin_url( 'options-general.php#timezone_string' ) ),
				),
			),
			'display_timezone' => array(
				'title'   => __( 'Display a timezone', 'woocommerce-bookings' ),
				'desc'    => __( 'Whether to display the current timezone on the calendar frontend', 'woocommerce-bookings' ),
				'default' => 'yes',
				'type'    => 'checkbox',
			),
			'use_client_firstday'             => array(
				'title'   => __( 'Calendar start day', 'woocommerce-bookings' ),
				'default' => 'no',
				'type'    => 'radio_custom_label',
				'options' => array(
					'yes' => __( 'Set start day from visitor\'s locale', 'woocommerce-bookings' ),
					/* translators: 1: URL to Firstday settings 2: first day of week */
					'no'  => sprintf( __( 'Set start day from <a href="%1$s">WordPress settings</a> (%2$s)', 'woocommerce-bookings' ), admin_url( 'options-general.php#start_of_week' ), $wp_locale->get_weekday( get_option( 'start_of_week' ) ) ),
				),
			),
		);
	}

	/**
	 * Render a custom radio button field.
	 *
	 * Renders a radio button field that does not escape the label HTML, so hyperlinks
	 * can be included the content.
	 *
	 * @since 1.13.0
	 * @param string $key Setting key name.
	 * @param array  $field Attributes for setting field.
	 * @return string Input HTML string.
	 */
	protected function generate_radio_custom_label_html( $key, $field ) {
		$field_key = $this->get_field_key( $key );
		$current_value = $this->get_option( $key );

		ob_start();
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo esc_html( $field['title'] ); ?></label>
			</th>
			<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $field['type'] ) ); ?>">
				<fieldset>
					<ul>
						<?php
						foreach ( $field['options'] as $option_value => $option_description ) {
							?>
							<li>
								<label><input
										name="<?php echo esc_attr( $field_key ); ?>"
										value="<?php echo esc_attr( $option_value ); ?>"
										type="radio"
										<?php checked( $option_value, $current_value ); ?>
									/> <?php echo wp_kses_post( $option_description ); ?></label>
							</li>
							<?php
						}
						?>
					</ul>
					<?php
						if ( array_key_exists( 'notice', $field ) && $field['notice']['display'] ) {
					?>
						<label class="wc_bookings_radio_custom_label_notice"><?php echo wp_kses_post( $field['notice']['text'] ); ?></label>
					<?php
						}
					?>
				</fieldset>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}

	/**
	 * Returns true if settings exist in database.
	 *
	 * @since 1.13.0
	 * @return bool
	 */
	public static function exists_in_db() {
		$maybe_settings = get_option( self::instance()->get_option_key(), null );
		return is_array( $maybe_settings );
	}

	/**
	 * Generates full HTML form for the instance settings.
	 *
	 * @since 1.13.0
	 * @return void
	 */
	public static function generate_form_html() {
		?>
			<form method="post" action="" id="bookings_settings">
				<?php self::instance()->admin_options(); ?>
				<p class="submit">
					<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'woocommerce-bookings' ); ?>" />
					<?php wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME ); ?>
				</p>
			</form>
		<?php
	}

	/**
	 * Returns WC_Bookings_Timezone_Settings singleton
	 *
	 * Ensures only one instance of WC_Bookings_Timezone_Settings is created.
	 *
	 * @since 1.13.0
	 * @return WC_Bookings_Timezone_Settings - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Retrieves value for the provided option key.
	 *
	 * @since 1.13.0
	 * @param string $key Option key.
	 * @return mixed Option value.
	 */
	public static function get( $key ) {
		return self::instance()->get_option( $key );
	}
}
