<?php
/**
 * Subscription form template website input (used in shortcode and widget)
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Mailchimp
 * @version 1.0.0
 */
?>

<input type="text" name="<?php echo esc_attr( $mailchimp_data['tag'] ) ?>[addr1]" id="<?php echo esc_attr( $mailchimp_data['tag'] ) ?>_<?php echo esc_attr( $id ) ?>_addr1" value="<?php echo isset( $_REQUEST[ $mailchimp_data['tag'] ]['addr1'] ) ? $_REQUEST[ $mailchimp_data['tag'] ]['addr1'] : '' ?>"<?php echo ( $mailchimp_data['required'] ) ? 'required="required"' : '' ?> placeholder="<?php echo apply_filters( 'yith_wcmc_address_addr1_placeholder', __( 'Street Address', 'yith-woocommerce-mailchimp' ) ) ?>" />

<input type="text" name="<?php echo esc_attr( $mailchimp_data['tag'] ) ?>[addr2]" id="<?php echo esc_attr( $mailchimp_data['tag'] ) ?>_<?php echo esc_attr( $id ) ?>_addr2" value="<?php echo isset( $_REQUEST[ $mailchimp_data['tag'] ]['addr2'] ) ? $_REQUEST[ $mailchimp_data['tag'] ]['addr2'] : '' ?>" placeholder="<?php echo apply_filters( 'yith_wcmc_address_addr2_placeholder', __( 'Address Line 2', 'yith-woocommerce-mailchimp' ) ) ?>" />

<input type="text" name="<?php echo esc_attr( $mailchimp_data['tag'] ) ?>[city]" id="<?php echo esc_attr( $mailchimp_data['tag'] ) ?>_<?php echo esc_attr( $id ) ?>_city" value="<?php echo isset( $_REQUEST[ $mailchimp_data['tag'] ]['city'] ) ? $_REQUEST[ $mailchimp_data['tag'] ]['city'] : '' ?>"<?php echo ( $mailchimp_data['required'] ) ? 'required="required"' : '' ?> placeholder="<?php echo apply_filters( 'yith_wcmc_address_city_placeholder', __( 'City', 'yith-woocommerce-mailchimp' ) ) ?>" />

<input type="text" name="<?php echo esc_attr( $mailchimp_data['tag'] ) ?>[state]" id="<?php echo esc_attr( $mailchimp_data['tag'] ) ?>_<?php echo esc_attr( $id ) ?>_state" value="<?php echo isset( $_REQUEST[ $mailchimp_data['tag'] ]['state'] ) ? $_REQUEST[ $mailchimp_data['tag'] ]['state'] : '' ?>"<?php echo ( $mailchimp_data['required'] ) ? 'required="required"' : '' ?> placeholder="<?php echo apply_filters( 'yith_wcmc_address_state_placeholder', __( 'State/Province', 'yith-woocommerce-mailchimp' ) ) ?>" />

<input type="text" name="<?php echo esc_attr( $mailchimp_data['tag'] ) ?>[zip]" id="<?php echo esc_attr( $mailchimp_data['tag'] ) ?>_<?php echo esc_attr( $id ) ?>_zip" value="<?php echo isset( $_REQUEST[ $mailchimp_data['tag'] ]['zip'] ) ? $_REQUEST[ $mailchimp_data['tag'] ]['zip'] : '' ?>"<?php echo ( $mailchimp_data['required'] ) ? 'required="required"' : '' ?> placeholder="<?php echo apply_filters( 'yith_wcmc_address_state_placeholder', __( 'Zip', 'yith-woocommerce-mailchimp' ) ) ?>" />

<?php
$countries = WC()->countries->get_allowed_countries();

if( ! empty( $countries ) ):
?>
	<select name="<?php echo esc_attr( $mailchimp_data['tag'] ) ?>[country]" id="<?php echo esc_attr( $mailchimp_data['tag'] ) ?>_<?php echo esc_attr( $id ) ?>_country"<?php echo ( $mailchimp_data['required'] ) ? 'required="required"' : '' ?>>
		<?php foreach( $countries as $key => $value ): ?>
			<option value="<?php echo esc_attr( $key )?>" <?php echo isset( $_REQUEST[ $mailchimp_data['tag'] ]['country'] ) ? selected( $key, $_REQUEST[ $mailchimp_data['tag'] ]['country'], false ) : ''?> ><?php echo esc_html( $value )?></option>
		<?php endforeach; ?>
	</select>
<?php
endif;
?>