<?php
/**
 * Edit service view.
 *
 * @var YITH_WCBK_Service $service the booking service
 *
 * @package YITH\Booking\Views
 */

$service_taxonomy_info = YITH_WCBK_Service_Tax_Admin::get_service_taxonomy_fields();
$name_prefix           = 'yith_booking_service_data';
?>

<?php foreach ( $service_taxonomy_info as $key => $args ) : ?>
	<?php
	$field_type        = $args['type'];
	$field_class       = $args['class'] ?? '';
	$field_name        = '';
	$custom_attributes = $args['custom_attributes'] ?? '';

	$args['title'] = $args['short_title'] ?? $args['title'] ?? '';

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

	$container_class = 'form-field yith-wcbk-booking-service-form-section yith-wcbk-booking-service-form-section-' . $field_type;
	$container_data  = array();
	if ( isset( $args['field_deps'] ) && isset( $args['field_deps']['id'] ) ) {
		$container_class .= ' yith-wcbk-show-conditional';

		$container_data['field-id'] = 'yith_booking_service_' . $args['field_deps']['id'];
		if ( isset( $args['field_deps']['value'] ) ) {
			$container_data['value'] = $args['field_deps']['value'];
		}
	}

	$args['id'] = "yith_booking_service_{$key}";

	if ( isset( $args['person_type_id'] ) ) {
		$args['value'] = $service->get_price_for_person_type( $args['person_type_id'] );
	} else {
		$getter = 'get_' . $key;
		if ( $service->is_internal_prop( $key ) && is_callable( array( $service, $getter ) ) ) {
			$args['value'] = $service->$getter( 'edit' );

			if ( in_array( $key, array( 'min_quantity', 'max_quantity' ), true ) && ! $args['value'] ) {
				$args['value'] = '';
			}
		} else {
			$args['value'] = $service->get_meta( $key );
		}
	}

	$args['yith-field']                     = true;
	$args['yith-wcbk-field-show-container'] = false;

	?>

	<tr
			class="<?php echo esc_attr( $container_class ); ?>"
		<?php yith_plugin_fw_html_data_to_string( $container_data, true ); ?>
	>
		<th scope="row" valign="top">
			<label for="yith_booking_service_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $args['title'] ); ?></label>
		</th>
		<td>
			<?php
			if ( isset( $args['title'] ) ) {
				unset( $args['title'] );
			}
			yith_wcbk_print_field( $args );
			?>
		</td>
	</tr>

<?php endforeach; ?>
