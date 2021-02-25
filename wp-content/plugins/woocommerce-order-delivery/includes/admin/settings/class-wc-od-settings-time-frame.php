<?php
/**
 * Settings: Time Frame.
 *
 * @package WC_OD/Admin/Settings
 * @since   1.5.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_OD_Settings_API', false ) ) {
	include_once WC_OD_PATH . 'includes/abstracts/abstract-class-wc-od-settings-api.php';
}

if ( class_exists( 'WC_OD_Settings_Time_Frame', false ) ) {
	return;
}

/**
 * WC_OD_Settings_Time_Frame class.
 */
class WC_OD_Settings_Time_Frame extends WC_OD_Settings_API {

	/**
	 * Settings Form ID.
	 *
	 * @var String
	 */
	public $id = 'time_frames';

	/**
	 * The time frame ID.
	 *
	 * @var string
	 */
	public $frame_id;

	/**
	 * Constructor.
	 *
	 * @since 1.5.0
	 * @since 1.6.0 Added default value to `new` for `$frame_id`.
	 *
	 * @param string $frame_id Optional. The time frame ID.
	 */
	public function __construct( $frame_id = 'new' ) {
		$this->frame_id = $frame_id;

		parent::__construct();
	}

	/**
	 * Gets if it's a new time frame or not.
	 *
	 * @since 1.5.0
	 *
	 * @return bool
	 */
	public function is_new() {
		return ( 'new' === $this->frame_id );
	}

	/**
	 * Initialise form fields.
	 *
	 * @since 1.5.0
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'title'     => array(
				'title'             => __( 'Title', 'woocommerce-order-delivery' ),
				'type'              => 'text',
				'description'       => __( 'This controls the title which the user sees during checkout.', 'woocommerce-order-delivery' ),
				'desc_tip'          => true,
				'custom_attributes' => array(
					'required' => 'required',
				),
			),
			'time_from' => array(
				'title'             => __( 'Time from', 'woocommerce-order-delivery' ),
				'type'              => 'text',
				'description'       => __( 'The starting time of this time frame.', 'woocommerce-order-delivery' ),
				'desc_tip'          => true,
				'class'             => 'timepicker',
				'custom_attributes' => array(
					'required' => 'required',
				),
			),
			'time_to'   => array(
				'title'             => __( 'Time to', 'woocommerce-order-delivery' ),
				'type'              => 'text',
				'description'       => __( 'The ending time of this time frame.', 'woocommerce-order-delivery' ),
				'desc_tip'          => true,
				'class'             => 'timepicker',
				'custom_attributes' => array(
					'required' => 'required',
				),
			),
		);

		$this->form_fields = array_merge(
			$this->form_fields,
			$this->get_number_of_orders_field(),
			$this->get_shipping_methods_fields()
		);

		$this->form_fields['number_of_orders']['default']            = 0;
		$this->form_fields['shipping_methods_option']['description'] = __( 'Choose the available shipping methods for this time frame.', 'woocommerce-order-delivery' );
	}

	/**
	 * Enqueue the settings scripts.
	 *
	 * @since 1.5.0
	 */
	public function enqueue_scripts() {
		$suffix = wc_od_get_scripts_suffix();

		wp_enqueue_style( 'jquery-timepicker', WC_OD_URL . 'assets/css/lib/jquery.timepicker.css', array(), '1.13.18' );
		wp_enqueue_script( 'jquery-timepicker', WC_OD_URL . 'assets/js/lib/jquery.timepicker.min.js', array( 'jquery' ), '1.13.18', true );
		wp_enqueue_script( 'wc-od-admin-settings-time-frame', WC_OD_URL . "assets/js/admin/settings-time-frame{$suffix}.js", array( 'jquery', 'jquery-timepicker' ), WC_OD_VERSION, true );
	}
}
