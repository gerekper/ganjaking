<?php
/**
 * Add service in Product
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit;

$name_prefix = 'yith_booking_service_data';

yith_wcbk_form_field(
	array(
		'title'  => __( 'Service name', 'yith-booking-for-woocommerce' ),
		'fields' => array(
			array(
				'type'  => 'text',
				'value' => '',
				'class' => 'yith-wcbk-fake-form-field',
				'data'  => array(
					'name'     => 'title',
					'required' => 'yes',
				),
			),
		),
	)
);

yith_wcbk_form_field(
	array(
		'title'  => __( 'Description', 'yith-booking-for-woocommerce' ),
		'fields' => array(
			array(
				'type'  => 'textarea',
				'value' => '',
				'class' => 'yith-wcbk-fake-form-field',
				'data'  => array(
					'name' => 'description',
				),
			),
		),
	)
);

$service_taxonomy_info = YITH_WCBK_Service_Tax_Admin::get_service_taxonomy_fields();
$name_prefix           = 'yith_booking_service_data';
foreach ( $service_taxonomy_info as $key => $args ) {
	$field_name = '';
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

	$extra_class    = '';
	$container_data = array();
	if ( isset( $args['field_deps'] ) && isset( $args['field_deps']['id'] ) ) {
		$extra_class .= 'yith-wcbk-show-conditional';

		$container_data['field-id'] = 'yith_booking_service_' . $args['field_deps']['id'];
		if ( isset( $args['field_deps']['value'] ) ) {
			$container_data['value'] = $args['field_deps']['value'];
		}
	}

	$field_title = $args['short_title'] ?? $args['title'] ?? '';
	$desc        = $args['desc'] ?? '';

	unset( $args['title'] );
	unset( $args['desc'] );

	$args['yith-field']                     = true;
	$args['yith-wcbk-field-show-container'] = false;
	$args['value']                          = $args['default'] ?? '';
	$args['id']                             = 'yith_booking_service_' . $key;
	$args['class']                          = 'yith-wcbk-fake-form-field ' . ( $args['class'] ?? '' );
	$args['data']                           = array( 'name' => $field_name );

	$section_fields = array( $args );

	yith_wcbk_form_field(
		array(
			'title'  => $field_title,
			'class'  => $extra_class,
			'data'   => $container_data,
			'desc'   => $desc,
			'fields' => $section_fields,
		)
	);
}
?>
<input type="hidden" class="yith-wcbk-fake-form-field" data-name="security" value="<?php echo esc_attr( wp_create_nonce( 'create-service' ) ); ?>">
