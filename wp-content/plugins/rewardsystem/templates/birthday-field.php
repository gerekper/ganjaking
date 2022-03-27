<?php
/* Admin HTML Birthday Reward Points Settings */

if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
?>
<p class="<?php esc_html_e( $class_name ) ; ?> rs-birthday-field">
	<label for="rs_birthday_date_label"><?php echo wp_kses_post( get_option( 'rs_bday_field_label' ) ) ; ?></label>
	<?php srp_get_datepicker_html( $args ) ; ?>
	<span><em><?php echo wp_kses_post( $label ) ; ?></em></span>
</p>
<?php
