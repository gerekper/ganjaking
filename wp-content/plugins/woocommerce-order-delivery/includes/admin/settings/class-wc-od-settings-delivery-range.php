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
	 * The delivery range ID.
	 *
	 * @var string
	 */
	public $range_id;

	/**
	 * Constructor.
	 *
	 * @since 1.7.0
	 *
	 * @param string $range_id Optional. The delivery range ID.
	 */
	public function __construct( $range_id = 'new' ) {
		$this->id       = 'delivery_ranges';
		$this->range_id = $range_id;

		parent::__construct();
	}

	/**
	 * Gets if it's a new delivery range.
	 *
	 * @since 1.7.0
	 *
	 * @return bool
	 */
	public function is_new() {
		return ( 'new' === $this->range_id );
	}

	/**
	 * Gets if it's the default delivery range.
	 *
	 * @since 1.7.0
	 *
	 * @return bool
	 */
	public function is_default() {
		return ( '0' === $this->range_id || 0 === $this->range_id );
	}

	/**
	 * Initialise form fields.
	 *
	 * @since 1.7.0
	 */
	public function init_form_fields() {
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

			$this->form_fields['shipping_methods_option']['description'] = __( 'Choose the available shipping methods for this delivery range.', 'woocommerce-order-delivery' );
		}
	}

	/**
	 * Initialise Settings.
	 *
	 * @since 1.7.0
	 */
	public function init_settings() {
		$settings = $this->get_form_fields_defaults();

		if ( ! $this->is_new() ) {
			$delivery_range = WC_OD_Delivery_Ranges::get_range( $this->range_id );

			if ( $delivery_range ) {
				$data = $delivery_range->get_data();

				unset( $data['id'], $data['meta_data'] );

				$settings = array_merge( $settings, $data );
			}
		}

		if ( $settings['shipping_methods_option'] ) {
			$setting_key = "{$settings['shipping_methods_option']}_shipping_methods";

			$settings[ $setting_key ] = $settings['shipping_methods'];
		}

		unset( $settings['shipping_methods'] );

		$this->settings = $settings;
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
		if ( ! $this->is_new() || $this->has_errors() ) {
			return;
		}

		wp_safe_redirect( wc_od_get_settings_url() );
		exit;
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

		if ( $this->is_new() ) {
			$delivery_range = new WC_OD_Delivery_Range();
		} else {
			$delivery_range = WC_OD_Delivery_Ranges::get_range( $this->range_id );
		}

		if ( ! $delivery_range ) {
			return false;
		}

		$delivery_range->set_props( $settings );
		$delivery_range->save();

		return ( null !== $delivery_range->get_id() );
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

			// Don't update these settings.
			unset( $settings['from'], $settings['to'] );
		}

		return $settings;
	}
}
