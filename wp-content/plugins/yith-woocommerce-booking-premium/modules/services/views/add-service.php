<?php
/**
 * Add service view.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Views
 */

$fields = array();

$service_taxonomy_info = YITH_WCBK_Service_Tax_Admin::get_service_taxonomy_fields();
$name_prefix           = 'yith_booking_service_data';

foreach ( $service_taxonomy_info as $key => $args ) {
	$field_type        = $args['type'];
	$field_name        = '';
	$custom_attributes = $args['custom_attributes'] ?? '';

	if ( isset( $args['name'] ) ) {
		$_name     = $args['name'];
		$_position = strpos( $_name, '[' );
		if ( $_position > 0 ) {
			$_first_key = substr( $_name, 0, $_position );
			$_other_key = substr( $_name, $_position );
			$field_name = sprintf( '%s[%s]%s', $name_prefix, $_first_key, $_other_key );
		}
	} else {
		$field_name = sprintf( '%s[%s]', $name_prefix, $key );
	}

	$args['name'] = $field_name;

	$extra_class    = '';
	$container_data = array();
	if ( isset( $args['field_deps'] ) && isset( $args['field_deps']['id'] ) ) {
		$extra_class .= 'yith-wcbk-show-conditional';

		$container_data['field-id'] = 'yith_booking_service_' . $args['field_deps']['id'];
		if ( isset( $args['field_deps']['value'] ) ) {
			$container_data['value'] = $args['field_deps']['value'];
		}
	}

	$args['yith-field']                     = true;
	$args['yith-wcbk-field-show-container'] = false;
	$args['value']                          = $args['default'] ?? '';
	$args['id']                             = 'yith_booking_service_' . $key;
	$args['desc']                           = '';

	$section_fields = array( $args );

	$fields[] = array(
		'type'             => 'section',
		'section_html_tag' => 'div',
		'class'            => 'form-field yith-wcbk-booking-service-form-section yith-wcbk-booking-service-form-section-' . $field_type . ' ' . $extra_class,
		'data'             => $container_data,
		'fields'           => $section_fields,
	);
}

yith_wcbk_print_fields( $fields );
