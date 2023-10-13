<?php
/**
 * Add service.
 *
 * @var array $languages
 *
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit;

$fields = array();

foreach ( $languages as $language_code => $language ) {
	$language_name = $language['display_name'] ?? $language_code;
	$fields[]      = array(
		'type'             => 'section',
		'section_html_tag' => 'div',
		'class'            => 'form-field',
		'fields'           => array(
			array(
				'type'  => 'html',
				'value' => '<h4>' . $language_name . '</h4>',
			),
			array(
				'type'  => 'text',
				'title' => __( 'Name', 'yith-booking-for-woocommerce' ),
				'value' => '',
				'id'    => 'yith_booking_service_wpml_translated_name_' . $language_code,
				'name'  => "yith_booking_service_data[wpml_translated_name][{$language_code}]",
			),
			array(
				'type'  => 'textarea',
				'title' => __( 'Description', 'yith-booking-for-woocommerce' ),
				'value' => '',
				'id'    => 'yith_booking_service_wpml_translated_description_' . $language_code,
				'name'  => "yith_booking_service_data[wpml_translated_description][{$language_code}]",
			),
		),
	);
}

echo '<h3>' . esc_html__( 'WPML translations', 'yith-booking-for-woocommerce' ) . '</h3>';

yith_wcbk_printer()->print_fields( $fields );
