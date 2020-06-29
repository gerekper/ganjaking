<?php
/**
 * General array options
 *
 * @author  YITH
 * @package YITH WooCommerce Checkout Manager
 * @version 1.0.0
 */

if ( ! defined( 'YWCCP' ) ) {
	exit;
} // Exit if accessed directly.

$general = array(

	'general' => array(

		array(
			'title' => __( 'General options', 'yith-woocommerce-checkout-manager' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'ywccp-general-options',
		),

		array(
			'title'     => __( 'Enable JS validation', 'yith-woocommerce-checkout-manager' ),
			'desc'      => __( 'Enable JavaScript field validation.', 'yith-woocommerce-checkout-manager' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'no',
			'id'        => 'ywccp-enable-js-error-check',
		),

		array(
			'title'     => __( 'Enable VAT JS Validation (EU)', 'yith-woocommerce-checkout-manager' ),
			'desc'      => __( 'Enable JavaScript VAT field validation. "Country" field is also required. This option is available only for European countries', 'yith-woocommerce-checkout-manager' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'no',
			'id'        => 'ywccp-enable-js-vat-check',
		),

		array(
			'title'     => __( 'Enable tooltip', 'yith-woocommerce-checkout-manager' ),
			'desc'      => __( 'Enable tooltip on checkout fields. Don\'t forget to set the tooltip text in single field edit window, otherwise the tooltip will not appear', 'yith-woocommerce-checkout-manager' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'no',
			'id'        => 'ywccp-enable-tooltip-check',
		),

		array(
			'title'     => __( 'Date format', 'yith-woocommerce-checkout-manager' ),
			'desc'      => __( 'Choose the date format for date fields.', 'yith-woocommerce-checkout-manager' ),
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'mm/dd/yy'                                                       => 'Default - mm/dd/yy',
				'yy-mm-dd'                                                       => 'ISO 8601 - yy-mm-dd',
				'd M, y'                                                         => 'Short - d M, y',
				'd MM, y'                                                        => 'Medium - d MM, y',
				'DD, d MM, yy'                                                   => 'Full - DD, d MM, yy',
				'&apos;day&apos; d &apos;of&apos; MM &apos;in the year&apos; yy' => 'With text - \'day\' d \'of\' MM \'in the year\' yy',
			),
			'default'   => 'mm/dd/yy',
			'id'        => 'ywccp-date-format-datepicker',
		),

		array(
			'title'     => __( 'Time format', 'yith-woocommerce-checkout-manager' ),
			'desc'      => __( 'Choose the time format for time fields.', 'yith-woocommerce-checkout-manager' ),
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'12' => __( 'Meridian (AM/PM)', 'yith-woocommerce-checkout-manager' ),
				'24' => __( '24 hour clock', 'yith-woocommerce-checkout-manager' ),
			),
			'default'   => '12',
			'id'        => 'ywccp-time-format-datepicker',
		),

		array(
			'title'     => __( 'Overwrite formatted addresses', 'yith-woocommerce-checkout-manager' ),
			'desc'      => __( 'Choose to overwrite formatted addresses with the plugin ordering or with the one applied by WooCommerce.', 'yith-woocommerce-checkout-manager' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'no',
			'id'        => 'ywccp-override-formatted-addresses',
		),

		array(
			'title'     => __( 'Show field label', 'yith-woocommerce-checkout-manager' ),
			'desc'      => __( 'Choose to show or hide the field label for the formatted addresses.', 'yith-woocommerce-checkout-manager' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
			'id'        => 'ywccp-show-label-formatted-addresses',
		),

		array(
			'type' => 'sectionend',
			'id'   => 'ywccp-end-general-options',
		),

		array(
			'title' => __( 'Style options', 'yith-woocommerce-checkout-manager' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'ywccp-style-options',
		),

		array(
			'title'     => __( 'Checkout style', 'yith-woocommerce-checkout-manager' ),
			'desc'      => __( 'Show checkout form in one column instead of using two-column default layout.', 'yith-woocommerce-checkout-manager' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'no',
			'id'        => 'ywccp-field-checkout-columns',
		),

		array(
			'title'     => __( 'Input field height', 'yith-woocommerce-checkout-manager' ),
			'desc'      => __( 'Set input field height (px)', 'yith-woocommerce-checkout-manager' ),
			'type'      => 'yith-field',
			'yith-type' => 'number',
			'default'   => '40',
			'min'       => '1',
			'id'        => 'ywccp-field-input-height',
		),

		array(
			'title'     => __( 'Input field border', 'yith-woocommerce-checkout-manager' ),
			'desc'      => __( 'Set a color for input field border.', 'yith-woocommerce-checkout-manager' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#d1d1d1',
			'id'        => 'ywccp-field-border-color',
		),

		array(
			'title'     => __( 'Input field border on focus', 'yith-woocommerce-checkout-manager' ),
			'desc'      => __( 'Set a color for input field border on focus.', 'yith-woocommerce-checkout-manager' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#d1d1d1',
			'id'        => 'ywccp-field-border-color-focus',
		),

		array(
			'title'     => __( 'Input field border (correct info)', 'yith-woocommerce-checkout-manager' ),
			'desc'      => __( 'Set a color for input field border when info entered is correct.', 'yith-woocommerce-checkout-manager' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#69bf29',
			'id'        => 'ywccp-field-border-color-success',
		),

		array(
			'title'     => __( 'Input field border (wrong info)', 'yith-woocommerce-checkout-manager' ),
			'desc'      => __( 'Set a color for input field border when info entered is wrong.', 'yith-woocommerce-checkout-manager' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#a00a00',
			'id'        => 'ywccp-field-border-color-error',
		),

		array(
			'title'     => __( 'Error message color', 'yith-woocommerce-checkout-manager' ),
			'desc'      => __( 'Set a color for error messages.', 'yith-woocommerce-checkout-manager' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#a00a00',
			'id'        => 'ywccp-field-error-color',
		),

		array(
			'type' => 'sectionend',
			'id'   => 'ywccp-end-style-options',
		),
	),
);

return apply_filters( 'ywccp_panel_general_options', $general );