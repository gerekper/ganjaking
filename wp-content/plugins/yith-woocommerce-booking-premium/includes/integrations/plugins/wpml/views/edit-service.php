<?php
/**
 * Edit service.
 *
 * @var YITH_WCBK_Service $service the booking service
 * @var array             $languages
 *
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit;

?>

<tr class="form-field yith-wcbk-edit-service-title">
	<th scope="row" valign="top" colspan="2">
		<h3><?php echo esc_html__( 'WPML translations', 'yith-booking-for-woocommerce' ); ?></h3>
	</th>
</tr>

<?php
$fields                  = array();
$translated_names        = $service->get_meta( 'wpml_translated_name' );
$translated_names        = ! ! $translated_names ? $translated_names : array();
$translated_descriptions = $service->get_meta( 'wpml_translated_description' );
$translated_descriptions = ! ! $translated_descriptions ? $translated_descriptions : array();

foreach ( $languages as $language_code => $language ) {
	$language_name     = $language['display_name'] ?? $language_code;
	$name_name         = "yith_booking_service_data[wpml_translated_name][{$language_code}]";
	$description_name  = "yith_booking_service_data[wpml_translated_description][{$language_code}]";
	$value_name        = $translated_names[ $language_code ] ?? '';
	$value_description = $translated_descriptions[ $language_code ] ?? '';
	?>

	<tr class="form-field yith-wcbk-edit-service-subtitle">
		<th scope="row" valign="top" colspan="2">
			<h4><?php echo esc_html( $language_name ); ?></h4>
		</th>
	</tr>

	<tr class="form-field">
		<th scope="row" valign="top">
			<label for="yith_booking_service_wpml_translated_name_<?php echo esc_attr( $language_code ); ?>"><?php esc_html_e( 'Name', 'yith-booking-for-woocommerce' ); ?></label>
		</th>
		<td>
			<input type="text" name="<?php echo esc_attr( $name_name ); ?>"
					id="yith_booking_service_wpml_translated_name_<?php echo esc_attr( $language_code ); ?>"
					value="<?php echo esc_attr( $value_name ); ?>"/>
		</td>
	</tr>

	<tr class="form-field">
		<th scope="row" valign="top">
			<label for="yith_booking_service_wpml_translated_description_<?php echo esc_attr( $language_code ); ?>"><?php esc_html_e( 'Description', 'yith-booking-for-woocommerce' ); ?></label>
		</th>
		<td>
			<textarea type="text" name="<?php echo esc_attr( $description_name ); ?>"
					id="yith_booking_service_wpml_translated_description_<?php echo esc_attr( $language_code ); ?>"
			><?php echo user_can_richedit() ? wp_kses_post( $value_description ) : esc_textarea( $value_description ); ?></textarea>
		</td>
	</tr>

	<?php
}
?>
