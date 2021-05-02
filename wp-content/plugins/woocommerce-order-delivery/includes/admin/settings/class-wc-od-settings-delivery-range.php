<?php
/**
 * Settings: Delivery Range.
 *
 * @package WC_OD/Admin/Settings
 * @since   1.7.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_OD_Settings_API', false ) ) {
	include_once WC_OD_PATH . 'includes/abstracts/abstract-class-wc-od-settings-api.php';
}

if ( class_exists( 'WC_OD_Settings_Delivery_Range', false ) ) {
	return;
}

/**
 * WC_OD_Settings_Delivery_Range class.
 */
class WC_OD_Settings_Delivery_Range extends WC_OD_Settings_API {

	/**
	 * The delivery range object.
	 *
	 * @var WC_OD_Delivery_Range
	 */
	protected $delivery_range;

	/**
	 * Constructor.
	 *
	 * @since 1.7.0
	 * @since 1.8.7 The parameter must be a delivery range object.
	 *
	 * @param WC_OD_Delivery_Range $delivery_range Delivery range object.
	 */
	public function __construct( $delivery_range ) {
		$this->id = 'delivery_ranges';

		if ( ! $delivery_range instanceof WC_OD_Delivery_Range ) {
			wc_doing_it_wrong( __FUNCTION__, 'You must provide a WC_OD_Delivery_Range object.', '1.8.7' );

			$range_id = ( 'new' === $delivery_range ? null : (int) $delivery_range );

			$this->delivery_range = WC_OD_Delivery_Ranges::get_range( $range_id );
		} else {
			$this->delivery_range = $delivery_range;
		}

		parent::__construct();
	}

	/**
	 * Auto-load in-accessible properties on demand.
	 *
	 * NOTE: Keep backward compatibility with some deprecated properties on this class.
	 *
	 * @since 1.8.7
	 *
	 * @param mixed $key The property name.
	 * @return mixed The property value.
	 */
	public function __get( $key ) {
		if ( 'range_id' === $key ) {
			wc_deprecated_argument( 'WC_OD_Settings_Delivery_Range->range_id', '1.8.7', 'This property is deprecated and will be removed in future releases.' );

			return $this->delivery_range->get_id();
		}
	}

	/**
	 * Gets if it's a new delivery range.
	 *
	 * @since 1.7.0
	 *
	 * @return bool
	 */
	public function is_new() {
		return ( null === $this->delivery_range->get_id() );
	}

	/**
	 * Gets if it's the default delivery range.
	 *
	 * @since 1.7.0
	 *
	 * @return bool
	 */
	public function is_default() {
		return ( 0 === $this->delivery_range->get_id() );
	}

	/**
	 * Initialise form fields.
	 *
	 * @since 1.7.0
	 */
	public function init_form_fields() {
		// Form fields have already been initialized in the method process_admin_options().
		if ( ! empty( $this->form_fields ) ) {
			return;
		}

		$this->form_fields = array(
			'title' => array(
				'title'             => __( 'Title', 'woocommerce-order-delivery' ),
				'type'              => 'text',
				'description'       => __( 'This controls the title which the user sees during checkout.', 'woocommerce-order-delivery' ),
				'desc_tip'          => true,
				'disabled'          => $this->is_default(),
				'custom_attributes' => array(
					'required' => 'required',
				),
			),
			'from'  => array(
				'title'             => __( 'From (days)', 'woocommerce-order-delivery' ),
				'type'              => 'number',
				'description'       => __( 'The minimum days to deliver the order.', 'woocommerce-order-delivery' ),
				'desc_tip'          => true,
				'custom_attributes' => array(
					'required' => 'required',
					'min'      => 0,
				),
			),
			'to'    => array(
				'title'             => __( 'To (days)', 'woocommerce-order-delivery' ),
				'type'              => 'number',
				'description'       => __( 'The maximum days to deliver the order.', 'woocommerce-order-delivery' ),
				'desc_tip'          => true,
				'custom_attributes' => array(
					'required' => 'required',
					'min'      => 0,
				),
			),
		);

		if ( ! $this->is_default() ) {
			$this->form_fields = array_merge( $this->form_fields, $this->get_shipping_methods_fields() );

			$this->form_fields['shipping_methods_option']['default'] = 'specific';

			if ( $this->is_new() || '' !== $this->delivery_range->get_shipping_methods_option() ) {
				unset( $this->form_fields['shipping_methods_option']['options'][''] );
			}

			$this->form_fields['shipping_methods_option']['description'] = __( 'Choose the available shipping methods for this delivery range.', 'woocommerce-order-delivery' );
		}
	}

	/**
	 * Initialise Settings.
	 *
	 * @since 1.7.0
	 */
	public function init_settings() {
		// Settings have already been initialized in the method process_admin_options().
		if ( ! empty( $this->settings ) ) {
			return;
		}

		$settings = $this->get_form_fields_defaults();

		if ( ! $this->is_new() ) {
			$data = $this->delivery_range->get_data();

			unset( $data['id'], $data['meta_data'] );

			$settings = array_merge( $settings, $data );
		}

		if ( $settings['shipping_methods_option'] && isset( $settings['shipping_methods'] ) ) {
			$setting_key = "{$settings['shipping_methods_option']}_shipping_methods";

			$settings[ $setting_key ] = $settings['shipping_methods'];
		}

		unset( $settings['shipping_methods'] );

		$this->settings = $settings;
	}

	/**
	 * Outputs the settings notices.
	 *
	 * @since 1.8.7
	 */
	public function output_notices() {
		if ( ! $this->is_new() && ! $this->is_default() && '' === $this->delivery_range->get_shipping_methods_option() ) {
			$message = __( 'The “All shipping methods” option is reserved for the default delivery range. Consider moving these values to the default delivery range and delete this one.', 'woocommerce-order-delivery' );

			echo '<div id="message" class="notice notice-warning inline"><p>' . esc_html( $message ) . '</p></div>';
		}
	}

	/**
	 * Gets the form title.
	 *
	 * @since 1.7.0
	 *
	 * @return string
	 */
	public function get_form_title() {
		return sprintf(
			/* translators: %s: delivery range title */
			_x( 'Delivery ranges > %s', 'settings page title', 'woocommerce-order-delivery' ),
			( $this->is_new() ? __( 'Add Delivery Range', 'woocommerce-order-delivery' ) : $this->settings['title'] )
		);
	}

	/**
	 * Gets the form description.
	 *
	 * @since 1.7.0
	 *
	 * @return string
	 */
	public function get_form_description() {
		return _x( 'Edit the delivery range settings.', 'settings page description', 'woocommerce-order-delivery' );
	}

	/**
	 * Outputs the backlink in the heading.
	 *
	 * @since 1.7.0
	 */
	public function output_heading_backlink() {
		wc_back_link( _x( 'Return to the shipping options', 'settings back link label', 'woocommerce-order-delivery' ), wc_od_get_settings_url() );
	}

	/**
	 * Redirects to a different page after saving the settings if necessary.
	 *
	 * @since 1.5.0
	 */
	public function maybe_redirect() {
		$range_id = ( isset( $_GET['range_id'] ) ? wc_clean( wp_unslash( $_GET['range_id'] ) ) : 'new' ); // phpcs:ignore WordPress.Security.NonceVerification

		if ( 'new' === $range_id && ! $this->has_errors() ) {
			wp_safe_redirect( wc_od_get_settings_url( 'delivery_range', array( 'range_id' => $this->delivery_range->get_id() ) ) );
			exit;
		}
	}

	/**
	 * Saves the settings.
	 *
	 * @since 1.7.0
	 *
	 * @return bool was anything saved?
	 */
	public function save() {
		if ( $this->has_errors() ) {
			return false;
		}

		$settings = $this->sanitized_fields( $this->settings );

		$this->delivery_range->set_props( $settings );
		$this->delivery_range->save();

		return ( null !== $this->delivery_range->get_id() );
	}

	/**
	 * Validates the settings.
	 *
	 * @since 1.7.0
	 *
	 * @param array $settings The settings to validate.
	 * @return array
	 */
	public function validate_fields( $settings ) {
		$settings = parent::validate_fields( $settings );

		if ( $settings['to'] < $settings['from'] ) {
			$this->add_error(
				sprintf(
					/* translators: 1: field title 2: field title */
					__( 'The field "%1$s" must be higher or equal than the field "%2$s".', 'woocommerce-order-delivery' ),
					__( 'To (days)', 'woocommerce-order-delivery' ),
					__( 'From (days)', 'woocommerce-order-delivery' )
				)
			);
		} elseif ( isset( $settings['shipping_methods_option'] ) && '' !== $settings['shipping_methods_option'] ) {
			$field_key = $settings['shipping_methods_option'] . '_shipping_methods';

			if ( empty( $settings[ $field_key ] ) ) {
				$this->add_error( __( 'No shipping methods provided.', 'woocommerce-order-delivery' ) );
			}
		}

		return $settings;
	}
}
