<?php
/**
 * WooCommerce Local Pickup Plus
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Local Pickup Plus to newer
 * versions in the future. If you wish to customize WooCommerce Local Pickup Plus for your
 * needs please refer to http://docs.woocommerce.com/document/local-pickup-plus/
 *
 * @package     WC-Shipping-Local-Pickup-Plus
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2020, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * The Local Pickup Plus shipping method class.
 *
 * Uses WooCommerce Shipping Method API to add a new shipping method, which in turn extends the WooCommerce Settings API.
 *
 * The core API requires to use the same class for both admin and frontend, hence there are settings and frontend functionality in the same class.
 *
 * This class tries to limit its responsibility to handle shipping method settings in both back end (along with settings UI) and front end, where it also instantiates additional classes where it delegates actual checkout and shipping logic.
 *
 * @since 1.4
 */
class WC_Shipping_Local_Pickup_Plus extends \WC_Shipping_Method {


	/**
	 * Initialize the Local Pickup Plus shipping method class.
	 *
	 * @since 1.4
	 */
	public function __construct() {

		parent::__construct();

		$this->load_textdomain();

		$this->id                 = \WC_Local_Pickup_Plus::SHIPPING_METHOD_ID;
		$this->method_title       = __( 'Local Pickup Plus', 'woocommerce-shipping-local-pickup-plus' );
		$this->method_description = __( 'Local Pickup Plus is a shipping method which allows customers to pick up their orders at a specified pickup location.', 'woocommerce-shipping-local-pickup-plus' );

		// load and init shipping method settings
		$this->handle_settings();

		// set Local Pickup Plus as the default shipping method
		add_filter( 'woocommerce_shipping_chosen_method', array( $this, 'set_default_shipping_method' ), 1, 3 );

		/**
		 * Local Pickup Plus shipping method init.
		 *
		 * @since 1.4
		 *
		 * @param \WC_Shipping_Local_Pickup_Plus $shipping_method instance of this class
		 */
		do_action( 'wc_shipping_local_pickup_plus_init', $this );
	}


	/**
	 * Ensures that the text domain is loaded for gettext strings included in this class.
	 *
	 * Unfortunately WooCommerce seems to insist loading the shipping handler too early before the translations have loaded.
	 * This introduces some code duplication from the framework but at least ensures the textdomain is registered and translations are loaded at all times.
	 * Otherwise, on some installations the shipping method settings page may not be translated on some installs, depending on load order.
	 *
	 * @see \WC_Local_Pickup_Plus::load_textdomain() framework method
	 *
	 * @since 2.4.0
	 */
	private function load_textdomain() {

		$textdomain  = 'woocommerce-shipping-local-pickup-plus';
		$plugin_path = dirname( plugin_basename( wc_local_pickup_plus()->get_file() ) );

		// user's locale if in the admin for WP 4.7+, or the site locale otherwise
		$locale = is_admin() && is_callable( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$locale = apply_filters( 'plugin_locale', $locale, $textdomain );

		load_textdomain( $textdomain, WP_LANG_DIR . '/' . $textdomain . '/' . $textdomain . '-' . $locale . '.mo' );

		load_plugin_textdomain( $textdomain, false, untrailingslashit( $plugin_path ) . '/i18n/languages' );
	}


	/**
	 * Get the shipping method ID.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_method_id() {
		return $this->id;
	}


	/**
	 * Get the shipping method name.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_method_title() {
		// looks for a user entered title first, defaults to parent method title which is filtered
		return $this->get_option( 'title', parent::get_method_title() );
	}


	/**
	 * Check whether the shipping method is available at checkout.
	 *
	 * @since 1.4
	 *
	 * @param array $package optional, a package as an array
	 * @return bool
	 */
	public function is_available( $package = array() ) {

		// WC shipping must be enabled, the shipping method must be enabled and there must be at least one pickup location published
		$is_available = wc_shipping_enabled() && $this->is_enabled() && wc_local_pickup_plus()->get_pickup_locations_instance()->get_pickup_locations_count( [ 'post_status' => 'publish' ] ) > 0;

		/* @see woocommerce/includes/abstracts/abstract-wc-shipping-method.php; only use $this if using WC 3.2+ */
		return (bool) apply_filters( "woocommerce_shipping_{$this->id}_is_available", $is_available, $package, $this );
	}


	/**
	 * Handle shipping method settings.
	 *
	 * @since 2.0.0
	 */
	private function handle_settings() {

		// load the form fields
		$this->form_fields = $this->get_settings_fields();

		// load the settings
		$this->init_settings();

		// init user settings
		foreach ( $this->settings as $setting_key => $setting ) {
			$this->$setting_key = $setting;
		}

		// save settings in admin when updated
		add_action( "woocommerce_update_options_shipping_{$this->id}", array( $this, 'process_admin_options' ) );
	}


	/**
	 * Get shipping method settings form fields
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function get_settings_fields() {

		$form_fields = [

			'enabled' => [
				'title'   => __( 'Enable', 'woocommerce-shipping-local-pickup-plus' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Local Pickup Plus', 'woocommerce-shipping-local-pickup-plus' ),
				'default' => 'no',
			],

			'title' => [
				'id'          => 'title',
				'title'       => __( 'Title', 'woocommerce-shipping-local-pickup-plus' ),
				'description' => __( 'The shipping method title that customers see during checkout.', 'woocommerce-shipping-local-pickup-plus' ),
				'type'        => 'text',
				'default'     => __( 'Local Pickup', 'woocommerce-shipping-local-pickup-plus' ),
			],

			'google_maps_api_key' => [
				'id'          => 'google_maps_api_key',
				'title'       => __( 'Google Maps Geocoding API Key', 'woocommerce-shipping-local-pickup-plus' ),
				'desc_tip'    => __( 'Use Google Maps Geocoding API to geocode your pickup locations and enable customers to search pickup locations by distance.', 'woocommerce-shipping-local-pickup-plus' ),
				'placeholder' => __( '(optional)', 'woocommerce-shipping-local-pickup-plus' ),
				'type'        => 'password',
				'default'     => '',
			],

			'enable_logging' => [
				'id'          => 'enable_logging',
				'title'    => __( 'Enable logging', 'woocommerce-shipping-local-pickup-plus' ),
				'desc_tip' => __( 'Log Google Maps Geocoding API responses and errors.', 'woocommerce-shipping-local-pickup-plus' ),
				'type'     => 'checkbox',
				'default'  => 'no',
			],

			'checkout_display_start' => [
				'name' => __( 'Checkout Display', 'woocommerce-shipping-local-pickup-plus' ),
				'desc' => __( 'Determine how pickup locations are shown to the customer at checkout.', 'woocommerce-shipping-local-pickup-plus' ),
				'type' => 'section_start',
			],

			'enable_per_item_selection' => [
				'title'    => __( 'Choosing Locations', 'woocommerce-shipping-local-pickup-plus' ),
				'type'     => 'select',
				'class'       => 'wc-local-pickup-plus-dropdown',
				'options'  => [
					'per-order' => __( 'Allow customers to select only one location per order', 'woocommerce-shipping-local-pickup-plus' ),
					'per-item'  => __( 'Allow customers to choose a location per product', 'woocommerce-shipping-local-pickup-plus' ),
				],
				'default'  => 'per-item',
			],

			'item_handling' => [
				'title'    => __( 'Cart Item Handling', 'woocommerce-shipping-local-pickup-plus' ),
				'type'     => 'select',
				'class'       => 'wc-local-pickup-plus-dropdown',
				'options'  => [
					'automatic' => __( 'Automatically group cart items into as few packages as possible', 'woocommerce-shipping-local-pickup-plus' ),
					'customer'  => __( 'Allow customers to toggle pickup or shipping for each item in the cart', 'woocommerce-shipping-local-pickup-plus' ),
				],
				'default'  => 'customer',
			],

			'default_handling' => [
				'title'    => __( 'Default Handling', 'woocommerce-shipping-local-pickup-plus' ),
				// TODO: not sure I like this: {CW 2017-06-14}
				'desc_tip' => __( 'Choose whether cart items will be set as to be shipped or for pickup when customers first arrive at the cart or checkout page.', 'woocommerce-shipping-local-pickup-plus' ),
				'type'     => 'select',
				'class'       => 'wc-local-pickup-plus-dropdown',
				'options'  => [
					'pickup' => __( 'Pick up items', 'woocommerce-shipping-local-pickup-plus' ),
					'ship'   => __( 'Ship items', 'woocommerce-shipping-local-pickup-plus' ),
				],
				'default' => 'ship',
			],

			'pickup_locations_sort_order' => [
				'title'       => __( 'Location Sort Order', 'woocommerce-shipping-local-pickup-plus' ),
				'desc_tip'    => __( 'Choose how the pickup location will be listed to the customer at checkout. Default is the default sort order determined by WordPress.', 'woocommerce-shipping-local-pickup-plus' ),
				'description' => __( 'Sorting by distance is only available with a Google Maps Geocoding API key to enable geocoding.', 'woocommerce-shipping-local-pickup-plus' ),
				'type'        => 'select',
				'class'       => 'wc-local-pickup-plus-dropdown',
				'options'     => [
					'default'               => __( 'Default', 'woocommerce-shipping-local-pickup-plus' ),
					'distance_customer'     => __( 'Distance from customer', 'woocommerce-shipping-local-pickup-plus' ),
					'location_alphabetical' => __( 'Alphabetical by location name', 'woocommerce-shipping-local-pickup-plus' ),
					'location_date_added'   => __( 'Most recently added location', 'woocommerce-shipping-local-pickup-plus' ),
				],
				'default'     => 'default',
			],

			'checkout_display_end' => [
				'type' => 'section_end',
			],

			'pickup_appointments_start' => [
				'name' => __( 'Pickup Appointments', 'woocommerce-shipping-local-pickup-plus' ),
				'desc' => __( 'Pickup scheduled appointments allow the customer to schedule an appointment for pickup at a selected pickup location on checkout.', 'woocommerce-shipping-local-pickup-plus' ),
				'type' => 'section_start',
			],

			'pickup_appointments_mode' => [
				'title'       => __( 'Pickup Appointments Mode', 'woocommerce-shipping-local-pickup-plus' ),
				'type'        => 'select',
				'class'       => 'wc-local-pickup-plus-dropdown',
				'options'     => [
					'disabled' => __( 'Do not offer appointments', 'woocommerce-shipping-local-pickup-plus' ),
					'enabled'  => __( 'Allow scheduled appointments', 'woocommerce-shipping-local-pickup-plus' ),
					'required' => __( 'Require scheduled appointments', 'woocommerce-shipping-local-pickup-plus' ),
				],
				'default'     => 'disabled',
			],

			'default_appointment_duration' => [
				'title'       => __( 'Appointment duration', 'woocommerce-shipping-local-pickup-plus' ),
				'description' => __( 'Set the duration for your pickup appointments. This will determine when users can schedule their appointments (e.g. hourly intervals).', 'woocommerce-shipping-local-pickup-plus' ),
				'type'        => 'select',
				'class'       => 'wc-local-pickup-plus-dropdown wc-local-pickup-plus-appointment-duration-field',
				'options'     => [
					DAY_IN_SECONDS         => __( 'Anytime during open hours', 'woocommerce-shipping-local-pickup-plus' ),
					15 * MINUTE_IN_SECONDS => __( '15 minutes', 'woocommerce-shipping-local-pickup-plus' ),
					30 * MINUTE_IN_SECONDS => __( '30 minutes', 'woocommerce-shipping-local-pickup-plus' ),
					60 * MINUTE_IN_SECONDS => __( '60 minutes', 'woocommerce-shipping-local-pickup-plus' ),
				],
				'default'     => DAY_IN_SECONDS,
			],

			'default_appointment_limits' => [
				/** @see \WC_Shipping_Local_Pickup_Plus::generate_appointment_limits_html() */
				'title'    => __( 'Appointment limits', 'woocommerce-shipping-local-pickup-plus' ),
				'desc_tip' => __( 'Determine how many customers can select the same appointment time before it is unavailable for a location.', 'woocommerce-shipping-local-pickup-plus' ),
				'type'     => 'appointment_limits',
			],

			'default_business_hours' => [
				'title'       => __( 'Default Business Hours', 'woocommerce-shipping-local-pickup-plus' ),
				'description' => __( 'If using scheduled appointments and no business hours are defined, customers may not be able to select a location. The default schedule can be overridden by individual pickup locations.', 'woocommerce-shipping-local-pickup-plus' ),
				'type'        => 'business_hours',
				// default business hours: Monday to Friday from 9:00 to 17:00
				'default'     => array_fill( 1, 5, [
					9 * HOUR_IN_SECONDS => 17 * HOUR_IN_SECONDS
				] ),
			],

			'default_public_holidays' => [
				'title'       => __( 'Common Public Holidays', 'woocommerce-shipping-local-pickup-plus' ),
				'description' => __( 'Manually exclude specific days of the calendar to have a pickup appointment scheduled. The selected dates will be excluded for all years. You can override default dates from each pickup location.', 'woocommerce-shipping-local-pickup-plus' ),
				'type'        => 'public_holidays',
				'default'     => '',
			],

			'default_lead_time' => [
				'title'       => __( 'Default Lead Time', 'woocommerce-shipping-local-pickup-plus' ),
				'description' => __( 'Set a default lead time to determine the earliest pickup date. Set to zero for no lead time. The lead time can be overridden by individual pickup locations.', 'woocommerce-shipping-local-pickup-plus' ),
				'type'        => 'lead_time',
				'default'     => '2 days',
			],

			'woocommerce_local_pickup_plus_lead_time_calculation' => [
				'title'           => __( 'Lead Time Calculation', 'woocommerce-shipping-local-pickup-plus' ),
				'type'            => 'radio',
				'id'              => 'woocommerce_local_pickup_plus_lead_time_calculation',
				'default'         => 'calendar_days',
				'options'         => [
					'open_hours'    => __( 'Count only open hours. Pauses outside business hours and on holidays.', 'woocommerce-shipping-local-pickup-plus' ),
					'calendar_days' => __( 'Count calendar days. Ignores business hours and holidays.', 'woocommerce-shipping-local-pickup-plus' ),
				],
				'show_if_checked' => 'option',
			],

			'default_deadline' => [
				'title'       => __( 'Default Deadline', 'woocommerce-shipping-local-pickup-plus' ),
				'description' => __( 'Set a default deadline to determine the latest pickup date. The deadline is based on calendar days and does not consider business hours and holidays. Set to zero for no deadline. The deadline can be overridden by individual pickup locations.', 'woocommerce-shipping-local-pickup-plus' ),
				'type'        => 'deadline',
				'default'     => '1 months',
			],

			'pickup_appointments_end' => [
				'type' => 'section_end',
			],

			'pickup_costs_discounts_start' => [
				'name' => __( 'Price &amp; Tax', 'woocommerce-shipping-local-pickup-plus' ),
				'desc' => __( 'Set a default cost or discount when a customer chooses to pickup up an order and how taxation should be handled.', 'woocommerce-shipping-local-pickup-plus' ),
				'type' => 'section_start',
			],

			'default_price_adjustment' => [
				'title'       => __( 'Default Price Adjustment', 'woocommerce-shipping-local-pickup-plus' ),
				'desc_tip'    => __( 'A cost or a discount applied when choosing Local Pickup Plus as the shipping method. You can set a fixed or a percentage amount. When using percentage, the value will be calculated based on cart contents value.', 'woocommerce-shipping-local-pickup-plus' ),
				'description' => __( 'Set to zero for no default adjustment. The default amount can be overridden by setting an adjustment in individual pickup locations.', 'woocommerce-shipping-local-pickup-plus' ),
				'type'        => 'price_adjustment',
				'default'     => '',
			],

			'apply_pickup_location_tax' => [
				'title'   => __( 'Pickup Location Tax', 'woocommerce-shipping-local-pickup-plus' ),
				'type'    => 'checkbox',
				'label'   => __( 'When this shipping method is chosen, apply the tax rate based on the pickup location than for the customer\'s given address.', 'woocommerce-shipping-local-pickup-plus' ),
				'default' => 'no',
			],

			'pickup_costs_discounts_end' => [
				'type' => 'section_end',
			],

		];

		/**
		 * Filter Local Pickup Plus shipping method settings fields.
		 *
		 * @since 1.14.0
		 *
		 * @param array $form_fields settings fields
		 */
		return (array) apply_filters( 'wc_local_pickup_plus_settings', $form_fields );
	}


	/**
	 * Generate HTML for a custom input field.
	 *
	 * @since 2.0.0
	 *
	 * @param string $field the type of field to output
	 * @param string $field_key the field key to identify the field ID and name values
	 * @param array $data input field data to build the markup
	 * @return string HTML
	 */
	private function get_custom_settings_field( $field, $field_key, array $data ) {

		ob_start();

		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<?php echo $this->get_tooltip_html( $data ); if ( ! empty( $data['desc_tip'] ) ) { $data['desc_tip'] = false; } ?>
			</th>
			<td class="forminp">
				<fieldset
					class="<?php echo esc_attr( $data['class'] ); ?>"
					style="<?php echo esc_attr( $data['css'] ); ?>">
					<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
					<?php

					$field_object = null;
					$default_data = get_option( $field_key, '' );
					$default_data = empty( $default_data ) ? $data['default'] : $default_data;

					switch ( $field ) {

						case 'deadline' :
						case 'lead_time' :
							$field_object = new \WC_Local_Pickup_Plus_Schedule_Adjustment( str_replace( '_', '-', $field ), $default_data );
						break;

						case 'business_hours' :
							$field_object = new \WC_Local_Pickup_Plus_Business_Hours( (array) $default_data );
						break;

						case 'price_adjustment' :
							$field_object = new \WC_Local_Pickup_Plus_Price_Adjustment( $default_data );
						break;

						case 'public_holidays' :
							$field_object = new \WC_Local_Pickup_Plus_Public_Holidays( (array) $default_data );
						break;

						case 'appointment_limits' :

							ob_start();

							?>
							<input type="number"
								   name="<?php echo esc_attr( $field_key ); ?>_max_customers"
								   id="<?php echo esc_attr( $field_key ); ?>_max_customers"
								   min="1"
								   step="1"
								   <?php disabled( 'unlimited' === $data['value'] ); ?>
								   value="<?php echo esc_attr( $this->get_default_appointment_limits_max_customers() ); ?>" />
							<?php $max_num_appointments_input = ob_get_clean(); ?>
							<div class="wc-local-pickup-plus-appointment-limits-field">
								<fieldset>
									<ul>
										<li>
											<input type="radio"
												   name="<?php echo esc_attr( $field_key ); ?>"
												   <?php checked( 'unlimited' === $data['value'] ); ?>
												   value="unlimited" /><?php esc_html_e( 'Unlimited', 'woocommerce-shipping-local-pickup-plus' ); ?>
										</li>
										<li>
											<input type="radio"
												   name="<?php echo esc_attr( $field_key ); ?>"
												   <?php checked( 'limited' === $data['value'] ); ?>
												   value="limited" /><?php printf(
															/* translators: Placeholder: %s numerical input field HTML to limit the number of appointments per slot */
															esc_html__( 'Limited to %s appointments per appointment time', 'woocommerce-shipping-local-pickup-plus' ),
															$max_num_appointments_input ); ?>
											<p class="description" style="<?php echo 'unlimited' === $data['value'] ? 'display:none;' : 'display:block;'; ?>"><?php esc_html_e( 'Limit the number of appointments available for each appointment time. Once the limit has been reached, the appointment will be removed from the calendar.', 'woocommerce-shipping-local-pickup-plus' ); ?></p>
										</li>
									</ul>
								</fieldset>
							</div>
							<?php

						break;
					}

					if ( null !== $field_object ) {
						echo $field_object->get_field_html( $data );
						echo $this->get_description_html( $data );
					}

					?>
				</fieldset>
			</td>
		</tr>
		<?php

		return ob_get_clean();
	}


	/**
	 * Parse custom fields default arguments
	 *
	 * @since 2.0.0
	 *
	 * @param array $args field args
	 * @param array $defaults field default values
	 * @return array
	 */
	private function parse_custom_fields_default_args( array $args, array $defaults ) {
		return wp_parse_args( $args, wp_parse_args( $defaults, array(
			'title'       => '',
			'disabled'    => false,
			'class'       => '',
			'css'         => '',
			'placeholder' => '',
			'desc_tip'    => false,
			'description' => '',
		) ) );
	}


	/**
	 * Generate a price adjustment field HTML.
	 *
	 * @since 2.0.0
	 *
	 * @param string $key field key
	 * @param array $data field data
	 * @return string HTML
	 */
	protected function generate_price_adjustment_html( $key, array $data ) {

		$field_key = $this->get_field_key( $key );
		$data      = $this->parse_custom_fields_default_args( $data, array(
			'name'    => $field_key,
			'default' => get_option( $field_key, 0 ),
		) );

		return $this->get_custom_settings_field( 'price_adjustment', $field_key, $data );
	}


	/**
	 * Gets a limit appointments field HTML.
	 *
	 * @since 2.8.0
	 *
	 * @param string $key field key
	 * @param array $data field data
	 * @return string HTML
	 */
	protected function generate_appointment_limits_html( $key, array $data ) {

		$field_key = $this->get_field_key( $key );
		$data      = $this->parse_custom_fields_default_args( $data, [
			'value' => $this->get_default_appointment_limits(),
		] );

		return $this->get_custom_settings_field( 'appointment_limits', $field_key, $data );
	}


	/**
	 * Get a business hours field HTML.
	 *
	 * @since 2.0.0
	 *
	 * @param string $key field key
	 * @param array $data field data
	 * @return string HTML
	 */
	protected function generate_business_hours_html( $key, array $data ) {

		$field_key = $this->get_field_key( $key );
		$data      = $this->parse_custom_fields_default_args( $data, array(
			'name'    => $field_key,
			'default' => get_option( $field_key, array() ),
		) );

		return $this->get_custom_settings_field( 'business_hours', $field_key, $data );
	}


	/**
	 * Returns public holidays calendar field HTML.
	 *
	 * @since 2.0.0
	 *
	 * @param string $key field key
	 * @param array $data field data
	 * @return string HTML
	 */
	protected function generate_public_holidays_html( $key, array $data ) {

		$field_key = $this->get_field_key( $key );
		$data      = $this->parse_custom_fields_default_args( $data, array(
			'name'    => $field_key,
			'default' => get_option( $field_key, array() ),
		) );

		return $this->get_custom_settings_field( 'public_holidays', $field_key, $data );
	}


	/**
	 * Validates public holidays form data.
	 *
	 * @since 2.3.14
	 *
	 * @param string $key the field key
	 * @param string|string[] $value expects an array of dates
	 * @return array
	 */
	protected function validate_public_holidays_field( $key, $value ) {

		$validated = array();

		if ( is_array( $value ) ) {
			$calendar  = new \WC_Local_Pickup_Plus_Public_Holidays( $value );
			$validated = $calendar->get_calendar();
		}

		return $validated;
	}


	/**
	 * Get a lead time field HTML.
	 *
	 * @since 2.0.0
	 *
	 * @param string $key field key
	 * @param array $data field data
	 * @return string HTML
	 */
	protected function generate_lead_time_html( $key, array $data ) {

		$field_key = $this->get_field_key( $key );
		$data      = $this->parse_custom_fields_default_args( $data, array(
			'name'    => $field_key,
			'default' => get_option( $field_key, '2 days' ),
		) );

		return $this->get_custom_settings_field( 'lead_time', $field_key, $data );
	}


	/**
	 * Generate a radio field HTML.
	 *
	 * @since 2.5.2
	 *
	 * @param string $key field key
	 * @param array $data field data
	 * @return string HTML
	 */
	protected function generate_radio_html( $key, array $data ) {

		ob_start();

		WC_Admin_Settings::output_fields( [ $key => $data ] );

		return ob_get_clean();
	}


	/**
	 * Get a deadline field HTML.
	 *
	 * @since 2.0.0
	 *
	 * @param string $key field key
	 * @param array $data field data
	 * @return string HTML
	 */
	protected function generate_deadline_html( $key, array $data ) {

		$field_key = $this->get_field_key( $key );
		$data      = $this->parse_custom_fields_default_args( $data, array(
			'name'    => $field_key,
			'default' => $this->get_default_pickup_deadline(),
		) );

		return $this->get_custom_settings_field( 'deadline', $field_key, $data );
	}


	/**
	 * Generate fields section start HTML.
	 *
	 * @since 2.0.0
	 *
	 * @param string $key field key
	 * @param array $data field data
	 * @return string HTML
	 */
	protected function generate_section_start_html( $key, array $data ) {
		return $this->get_fields_section_html( 'start', $key, $data );
	}


	/**
	 * Generate fields section end HTML.
	 *
	 * @since 2.0.0
	 *
	 * @param string $key field key
	 * @param array $data field data
	 * @return string HTML
	 */
	protected function generate_section_end_html( $key, array $data ) {
		return $this->get_fields_section_html( 'end', $key, $data );
	}


	/**
	 * Get fields section HTML.
	 *
	 * @since 2.0.0
	 *
	 * @param string $section which section to generate ('start' or 'end')
	 * @param string $key field key
	 * @param array $data field data
	 * @return string HTML
	 */
	private function get_fields_section_html( $section, $key, array $data ) {

		if ( 'end' === $section || ! $key ) {
			return '';
		}

		ob_start();

		?>
		<tr valign="top">
			<th scope="row" class="titledesc" colspan="2">
				<?php if ( isset( $data['name'] ) ) : ?>
					<h2><?php echo esc_html( $data['name'] ); ?></h2>
				<?php endif; ?>
				<?php if ( isset( $data['desc'] ) ) : ?>
					<p style="font-weight: normal;"><?php echo wp_kses_post( $data['desc'] ); ?></p>
				<?php endif; ?>
			</th>
		</tr>
		<?php

		return ob_get_clean();
	}


	/**
	 * Process admin options for the shipping method settings.
	 *
	 * @internal
	 *
	 * @since 1.4
	 *
	 * @return bool whether settings were saved
	 */
	public function process_admin_options() {

		// save the default price adjustment setting
		if ( isset( $_POST['woocommerce_local_pickup_plus_default_price_adjustment'], $_POST['woocommerce_local_pickup_plus_default_price_adjustment_amount'], $_POST['woocommerce_local_pickup_plus_default_price_adjustment_type'] ) ) {

			$adjustment = $_POST['woocommerce_local_pickup_plus_default_price_adjustment'];
			$amount     = $_POST['woocommerce_local_pickup_plus_default_price_adjustment_amount'];
			$type       = $_POST['woocommerce_local_pickup_plus_default_price_adjustment_type'];

			// validate and sanitize a valid price adjustment string
			$default_price_adjustment = new \WC_Local_Pickup_Plus_Price_Adjustment();
			$default_price_adjustment->set_value( $adjustment, (float) $amount, $type );

			update_option( 'woocommerce_local_pickup_plus_default_price_adjustment', $default_price_adjustment->get_value() );
		}

		$appointments_mode    = Framework\SV_WC_Helper::get_posted_value( 'woocommerce_local_pickup_plus_pickup_appointments_mode', 'disabled' );
		$appointment_duration = Framework\SV_WC_Helper::get_posted_value( 'woocommerce_local_pickup_plus_default_appointment_duration', DAY_IN_SECONDS );

		if ( 'disabled' !== $appointments_mode ) {

			// save the default appointment limits
			$appointment_limits = Framework\SV_WC_Helper::get_posted_value( 'woocommerce_local_pickup_plus_default_appointment_limits', 'unlimited' );

			update_option( 'woocommerce_local_pickup_plus_default_appointment_limits', $appointment_limits );

			// save the maximum number of customers per appointment limit, or retain previous value when disabled
			if ( 'limited' === $appointment_limits ) {

				$appointment_max_customers = max( 1, (int) Framework\SV_WC_Helper::get_posted_value( 'woocommerce_local_pickup_plus_default_appointment_limits_max_customers', 1 ) );

				update_option( 'woocommerce_local_pickup_plus_default_appointment_limits_max_customers', $appointment_max_customers );

				// if appointments are required and can be scheduled any time during open hours, the user was warned so we set the meta here so they aren't warned again
				if ( 'required' === $appointments_mode && DAY_IN_SECONDS === (int) $appointment_duration && $appointment_max_customers < 5 ) {
					update_user_meta( get_current_user_id(), '_wc_local_pickup_plus_appointments_max_customers_dismissed', 'yes' );
				}
			}

			// save the default business hours to schedule a pickup
			$business_hours = new \WC_Local_Pickup_Plus_Business_Hours();

			update_option( 'woocommerce_local_pickup_plus_default_business_hours', $business_hours->get_field_value( 'woocommerce_local_pickup_plus_default_business_hours', $_POST ) );

			// save the default public holidays for pickup appointment scheduling
			if ( ! empty( $_POST['woocommerce_local_pickup_plus_default_public_holidays'] ) ) {

				$public_holidays = (array) $_POST['woocommerce_local_pickup_plus_default_public_holidays'];
				$calendar        = new \WC_Local_Pickup_Plus_Public_Holidays( $public_holidays );

				update_option( 'woocommerce_local_pickup_plus_default_public_holidays', $calendar->get_calendar() );

				if ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_lt( '3.4.0' ) ) {

					// prevents a PHP error when the Shipping Settings Page tries to save an array
					unset( $_POST['woocommerce_local_pickup_plus_default_public_holidays'] );
				}

			} else {

				delete_option( 'woocommerce_local_pickup_plus_default_public_holidays' );
			}

			// save the default lead time affecting pickup scheduling
			if ( isset( $_POST['woocommerce_local_pickup_plus_default_lead_time_amount'], $_POST['woocommerce_local_pickup_plus_default_lead_time_interval'] ) ) {

				$amount   = max( 0, (int) $_POST['woocommerce_local_pickup_plus_default_lead_time_amount'] );
				$interval = $_POST['woocommerce_local_pickup_plus_default_lead_time_interval'];

				$default_lead_time = new \WC_Local_Pickup_Plus_Schedule_Adjustment( 'lead-time' );
				$default_lead_time->set_value( $amount, $interval );

				update_option( 'woocommerce_local_pickup_plus_default_lead_time', $default_lead_time->get_value() );
			}

			// save the lead calculation preference affecting pickup scheduling
			if ( isset( $_POST['woocommerce_local_pickup_plus_lead_time_calculation'] ) ) {
				update_option( 'woocommerce_local_pickup_plus_lead_time_calculation', $_POST['woocommerce_local_pickup_plus_lead_time_calculation'] );
			}

			// save the default deadline affecting pickup scheduling
			if ( isset( $_POST['woocommerce_local_pickup_plus_default_deadline_amount'], $_POST['woocommerce_local_pickup_plus_default_deadline_interval'] ) ) {

				$amount   = max( 0, (int) $_POST['woocommerce_local_pickup_plus_default_deadline_amount'] );
				$interval = $_POST['woocommerce_local_pickup_plus_default_deadline_interval'];

				$default_lead_time = new \WC_Local_Pickup_Plus_Schedule_Adjustment( 'deadline' );
				$default_lead_time->set_value( $amount, $interval );

				update_option( 'woocommerce_local_pickup_plus_default_deadline', $default_lead_time->get_value() );
			}

		} else {

			// if we have disabled pickup appointments, destroy related data:
			delete_option( 'woocommerce_local_pickup_plus_default_business_hours'  );
			delete_option( 'woocommerce_local_pickup_plus_default_public_holidays' );
			delete_option( 'woocommerce_local_pickup_plus_default_appointment_duration' );
			delete_option( 'woocommerce_local_pickup_plus_default_lead_time' );
			delete_option( 'woocommerce_local_pickup_plus_lead_time_calculation' );
			delete_option( 'woocommerce_local_pickup_plus_default_deadline' );

			// remove selected value for appointment duration option
			unset( $_POST[ 'woocommerce_local_pickup_plus_default_appointment_duration' ] );
		}

		// process other standard options
		return parent::process_admin_options();
	}


	/**
	 * Determines if per-item pickup selection is enabled.
	 *
	 * @since 2.1.0
	 *
	 * @return bool
	 */
	public function is_per_item_selection_enabled() {

		/**
		 * Filters whether per-item pickup selection is enabled.
		 *
		 * @since 2.1.0
		 *
		 * @param bool $is_enabled
		 */
		return (bool) apply_filters( "woocommerce_shipping_{$this->id}_per_item_selection_enabled", 'per-item' === $this->get_option( 'enable_per_item_selection' ) );
	}


	/**
	 * Determines if per-order pickup selection is enabled.
	 *
	 * @since 2.2.0
	 *
	 * @return bool
	 */
	public function is_per_order_selection_enabled() {
		return ! $this->is_per_item_selection_enabled();
	}


	/**
	 * Returns the pickup selection mode.
	 *
	 * @since 2.2.0
	 *
	 * @return string
	 */
	public function pickup_selection_mode() {
		return $this->is_per_item_selection_enabled() ? 'per-item' : 'per-order';
	}


	/**
	 * Gets the threshold of pickup locations for enabling enhanced search .
	 *
	 * @since 2.4.0
	 *
	 * @return int
	 */
	public function get_enhanced_search_pickup_locations_threshold() {

		/**
		 * Filters the threshold of pickup locations after which the enhanced search is normally enabled by default.
		 *
		 * @since 2.4.0
		 *
		 * @param int $threshold default 80
		 */
		$threshold = apply_filters( 'wc_local_pickup_plus_enhanced_pickup_location_search_threshold', 80 );

		return max( 1, (int) $threshold );
	}


	/**
	 * Determines if enhanced location searching is enabled.
	 *
	 * @since 2.1.0
	 *
	 * @return bool
	 */
	public function is_enhanced_search_enabled() {

		$enabled = wc_local_pickup_plus()->get_pickup_locations_instance()->get_pickup_locations_count() > $this->get_enhanced_search_pickup_locations_threshold();

		/**
		 * Whether to use an enhanced AJAX search in front end or a simpler dropdown.
		 *
		 * @since 2.1.0
		 *
		 * @param bool $use_enhanced_search by default this is true if there are at least 80 public locations
		 */
		return (bool) apply_filters( 'wc_local_pickup_plus_enhanced_pickup_location_search_enabled', $enabled );
	}


	/**
	 * Returns the handling mode for cart items.
	 *
	 * @since 2.2.0
	 *
	 * @return string either 'automatic' or 'customer'
	 */
	public function item_handling_mode() {

		/**
		 * Filters the item handling mode.
		 *
		 * @since 2.2.0
		 *
		 * @param string $item_handling the type of item handling mode
		 */
		return (string) apply_filters( "woocommerce_shipping_{$this->id}_item_handling", $this->get_option( 'item_handling' ) );
	}


	/**
	 * Checks whether an handling mode (automatic, customer) is the current handling mode for cart items.
	 *
	 * @since 2.2.0
	 *
	 * @param string $handling
	 * @return bool
	 */
	public function is_item_handling_mode( $handling ) {
		return $handling === $this->item_handling_mode();
	}


	/**
	 * Gets the default pickup/shipping handling.
	 *
	 * @since 2.1.0
	 *
	 * @return string either 'pickup' or 'ship'
	 */
	public function get_default_handling() {

		$session           = wc_local_pickup_plus()->get_session_instance();
		$valid_handling    = array( 'pickup', 'ship' );
		$default_handling  = $this->get_option( 'default_handling', 'ship' );
		$handling_override = $session ? $session->get_default_handling() : null;

		// perhaps use customer option override
		if (    $handling_override
		     && $this->is_per_order_selection_enabled()
		     && $this->is_item_handling_mode( 'automatic' )
		     && in_array( $handling_override, $valid_handling, true ) ) {

			$default_handling = $handling_override;
		}

		/**
		 * Filters the default pickup/shipping handling.
		 *
		 * @since 2.1.0
		 *
		 * @param string $default_handling either 'pickup' or 'ship'
		 */
		$default_handling = (string) apply_filters( "woocommerce_shipping_{$this->id}_default_handling", $default_handling );

		return in_array( $default_handling, $valid_handling, true ) ? $default_handling : 'ship';
	}


	/**
	 * Checks if the default handling is the specified one.
	 *
	 * @since 2.3.17
	 *
	 * @param string $handling default handling to check
	 * @return bool
	 */
	public function is_default_handling( $handling ) {

		return $handling === $this->get_default_handling();
	}


	/**
	 * Get the default pickup locations sort order.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function pickup_locations_sort_order() {

		$default_option  = 'default';
		$sort_order      = $this->get_option( 'pickup_locations_sort_order',  $default_option );
		$sorting_options = array(
			'default',
			'distance_customer',
			'location_alphabetical',
			'location_date_added',
		);

		return in_array( $sort_order, $sorting_options, true ) ? $sort_order : $default_option;
	}


	/**
	 * Whether applying the tax rate for the pickup location rather than the customer's given address.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function apply_pickup_location_tax() {

		return 'yes' === $this->get_option( 'apply_pickup_location_tax', 'no' );
	}


	/**
	 * Returns the pickup appointments mode from user's settings.
	 *
	 * @since 2.0.0
	 *
	 * @return string Either 'disabled', 'enabled' or 'required'
	 */
	public function pickup_appointments_mode() {

		$default = 'disabled';
		$option  = $this->get_option( 'pickup_appointments_mode', $default );

		return in_array( $option, array( 'disabled', 'enabled', 'required' ), true ) ? $option : $default;
	}


	/**
	 * Get the Google Maps API Key, if set.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_google_maps_api_key() {

		$default = '';
		$api_key = $this->get_option( 'google_maps_api_key', $default );

		return is_string( $api_key ) ? $api_key : $default;
	}


	/**
	 * Get the global pickup lead time.
	 *
	 * This might be overridden by individual pickup locations.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_default_pickup_lead_time() {

		$default = '2 days';
		// we don't use $this->get_option() as this is a composite option handled differently
		$value   = get_option( 'woocommerce_local_pickup_plus_default_lead_time', $default );

		return is_string( $value ) ? $value : $default;
	}


	/**
	 * Get the lead time calculation.
	 *
	 * @since 2.5.2
	 *
	 * @return string
	 */
	public function get_lead_time_calculation() {

		$default = 'open_hours';

		return get_option( 'woocommerce_local_pickup_plus_lead_time_calculation', $default );
	}


	/**
	 * Get the global pickup deadline.
	 *
	 * This might be overridden by individual pickup locations.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_default_pickup_deadline() {

		$default = '1 months';
		// we don't use $this->get_option() as this is a composite option handled differently
		$value   = get_option( 'woocommerce_local_pickup_plus_default_deadline', $default );

		return is_string( $value ) ? $value : $default;
	}


	/**
	 * Evaluates whether appointments could have any duration for pickup.
	 *
	 * @since 2.7.0
	 *
	 * @return bool
	 */
	public function is_anytime_appointments_enabled() {

		$duration = $this->get_option( 'default_appointment_duration', DAY_IN_SECONDS );

		return ! is_numeric( $duration ) || $duration <= 0 || $duration >= DAY_IN_SECONDS;
	}


	/**
	 * Gets the expected duration in seconds for pickup appointments.
	 *
	 * @since 2.7.0
	 *
	 * @return int a partial timestamp representing hours/minutes in seconds for a given appointment
	 */
	public function get_default_appointment_duration() {

		$duration = $this->get_option( 'default_appointment_duration', DAY_IN_SECONDS );

		return is_numeric( $duration ) ? (int) $duration : DAY_IN_SECONDS;
	}


	/**
	 * Gets the default appointment limits.
	 *
	 * @since 2.8.0
	 *
	 * @return string
	 */
	public function get_default_appointment_limits() {

		$default = 'unlimited';
		$values  = [ 'unlimited', 'limited' ];
		$saved   = get_option( 'woocommerce_local_pickup_plus_default_appointment_limits', $default );

		return in_array( $saved, $values, true ) ? $saved : $default;
	}


	/**
	 * Determines if the default appointment limits is the specified type.
	 *
	 * @since 2.8.0
	 *
	 * @param string $which one of 'limited' or 'unlimited'
	 * @return bool
	 */
	public function is_default_appointment_limits( $which ) {

		return $which === $this->get_default_appointment_limits();
	}


	/**
	 * Gets the max number of appointments per slot.
	 *
	 * @since 2.8.0
	 *
	 * @return int
	 */
	public function get_default_appointment_limits_max_customers() {

		return max( 1, (int) get_option( 'woocommerce_local_pickup_plus_default_appointment_limits_max_customers', 10 ) );
	}


	/**
	 * Get the default pickup location business hours.
	 *
	 * This might be overridden by individual pickup locations.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_default_business_hours() {

		$default = array_fill( 1, 5, array( 9 * HOUR_IN_SECONDS => 17 * HOUR_IN_SECONDS ) );

		// we don't use $this->get_option() as this is a composite option handled differently
		return (array) get_option( 'woocommerce_local_pickup_plus_default_business_hours', $default );
	}


	/**
	 * Get the global public holidays.
	 *
	 * This might be overridden by individual pickup locations.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_default_public_holidays() {

		// we don't use $this->get_option() as this is a composite option handled differently
		return (array) get_option( 'woocommerce_local_pickup_plus_default_public_holidays', array() );
	}


	/**
	 * Get the default price adjustment when completing a purchase with pickup.
	 *
	 * This might be overridden by individual pickup locations.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_default_price_adjustment() {

		$default = '';
		// we don't use $this->get_option() as this is a composite option handled differently
		$value   = get_option( 'woocommerce_local_pickup_plus_default_price_adjustment', $default );

		return is_string( $value ) || is_numeric( $value ) ? $value : $default;
	}


	/**
	 * Sets the default shipping method for a package.
	 *
	 * The filter we're hooking to runs in WooCommerce multiple times, but initially when a package has no shipping method set.
	 * However, to determine the default method, WooCommerce may simply choose the first shipping method in the array of shipping rates available to a package.
	 * If the default handling in Local Pickup Plus is to ship a package, then we should prevent WooCommerce from selecting Local Pickup Plus, and choose the next available shipping method instead.
	 *
	 * @see \wc_get_default_shipping_method_for_package() for WooCommerce versions >= 3.2.0
	 * @see \WC_Shipping::get_default_method() for WooCommerce versions <= 3.4.6
	 * @see \WC_Shipping::calculate_shipping() for WooCommerce versions <= 3.1.2
	 *
	 * Please note that since Local Pickup Plus does not use shipping zones, it may still end up being used as the default method as WooCommerce may still try to set the first available method.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string $chosen_shipping_method the default shipping method, normally with an instance suffix
	 * @param array $package_shipping_rates shipping rates available for a package
	 * @param string $default_shipping_method the raw shipping method before it was filtered
	 * @return string default shipping method for a package, when not user set
	 */
	public function set_default_shipping_method( $chosen_shipping_method, $package_shipping_rates = array(), $default_shipping_method = '' ) {

		if (    empty( $default_shipping_method )
		     && $this->is_available()
		     && $this->is_per_order_selection_enabled() ) {

			switch ( $this->get_default_handling() ) {

				// set to pickup if available
				case 'pickup' :
					$chosen_shipping_method = array_key_exists( $this->id, $package_shipping_rates ) ? $this->id : $chosen_shipping_method;
				break;

				// try to prevent from having pickup chosen by WooCommerce later
				case 'ship' :

					if ( ! empty( $package_shipping_rates ) ) {

						foreach ( array_keys( $package_shipping_rates ) as $shipping_method_id ) {

							if ( $this->id === $shipping_method_id ) {
								continue;
							}

							$chosen_shipping_method = $shipping_method_id;
							break;
						}
					}

					if ( $this->id === $chosen_shipping_method ) {
						$chosen_shipping_method = '';
					}

				break;
			}
		}

		return $chosen_shipping_method;
	}


	/**
	 * Determines whether shipping address fields should not be hidden regardless of pickup status.
	 *
	 * @since 2.3.16
	 *
	 * @return bool
	 */
	public function display_shipping_address_fields() {

		/**
		 * Toggles whether to show shipping address fields even when all packages are for pickup.
		 *
		 * @since 2.3.16
		 *
		 * @param bool $display_shipping_address_fields default false
		 */
		return (bool) apply_filters( 'wc_local_pickup_plus_display_shipping_address_fields', false );
	}


	/**
	 * Calculate shipping costs for local pickup of packages at chosen location.
	 *
	 * Extends parent method:
	 * @see \WC_Shipping_Method::calculate_shipping()
	 * @uses \WC_Shipping_Method::add_rate()
	 *
	 * @since 1.4
	 *
	 * @param array $package package data as associative array
	 */
	public function calculate_shipping( $package = [] ) {
		global $wp_query;

		$cost  = 0;
		$label = $this->get_method_title();

		if ( 'POST' === $_SERVER['REQUEST_METHOD'] || is_cart() || is_checkout() || ( $wp_query && defined( 'WC_DOING_AJAX' ) && 'update_order_review' === $wp_query->get( 'wc-ajax' ) ) ) {

			$pickup_location = wc_local_pickup_plus()->get_packages_instance()->get_package_pickup_location( $package );

			// address a situation where the user has a saved preferred location but this is not recorded yet to session for price adjustment calculation purposes
			if ( ! $pickup_location ) {

				// verify first that pickup is possible among the listed methods
				$chosen_methods       = WC()->session->get( 'chosen_shipping_methods' );
				$local_pickup_plus_id = $this->get_method_id();

				if ( ! empty( $chosen_methods ) && in_array( $local_pickup_plus_id, $chosen_methods, false ) ) {

					// look for a preferred pickup location
					$pickup_location = wc_local_pickup_plus_get_user_default_pickup_location();

					// then, verify that the items in package can be picked up at the default location
					if ( $pickup_location && isset( $package['contents'] ) && is_array( $package['contents'] ) ) {

						foreach ( $package['contents'] as $item ) {

							$product = isset( $item['data'] ) ? $item['data'] : null;

							if ( ! $product || ! wc_local_pickup_plus()->get_products_instance()->product_can_be_picked_up( $product, $pickup_location ) ) {
								$pickup_location = null;
								break;
							}
						}
					}
				}
			}

			$price_adjustment = $pickup_location ? $pickup_location->get_price_adjustment() : null;

			if ( $price_adjustment ) {
				$base = ! empty( $package['contents_cost'] ) ? $package['contents_cost'] : 0;
				$cost = $price_adjustment->get_relative_amount( $base );
			}

			$cost        = ! empty( $cost ) ? $cost : 0;
			$is_lpp_only = empty( $package['rates'] );

			// for packages that have other methods available as well (radio buttons will be displayed),
			// we need to display the discount in the label as WooCommerce does not handle negative values in the 'cost' property of a shipping rate;
			if ( ! $is_lpp_only ) {

				/* translators: Placeholder: %s - local pickup discount amount */
				$discount = $cost < 0 ? sprintf( __( '%s (discount!)', 'woocommerce-shipping-local-pickup-plus' ), wc_price( $cost ) ) : '';

				if ( ! $is_lpp_only && ! empty( $discount ) ) {
					if ( ! is_rtl() ) {
						$label = trim( $this->get_method_title() ) . ': ' . $discount;
					} else {
						$label = $discount . ' :' . trim( $this->get_method_title() );
					}
				}

			} else {

				// we don't add the discount for lpp-only packages because their shipping method labels will be grouped
				$label = $this->get_method_title();
			}
		}

		// register the rate for this package
		$this->add_rate( array(
			'id'       => $this->get_method_id(),      // default value (the method ID)
			'label'    => wp_strip_all_tags( $label ), // this might include a discount notice the customer will understand
			'cost'     => $cost > 0 ? $cost : 0,       // if there's a discount, dot not set a negative fee, later we will register a separate fee item as discount
			'taxes'    => $cost > 0 ? ''    : false,   // default values (taxes will be automatically calculated)
 			'calc_tax' => 'per_order',                 // applies to pickup package as a whole, regardless of items to be picked up
		) );
	}


}
