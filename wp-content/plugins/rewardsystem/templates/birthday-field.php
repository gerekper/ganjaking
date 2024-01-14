<?php
/* Admin HTML Birthday Reward Points Settings */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<p class="<?php esc_html_e( $class_name ); ?> rs-birthday-field">
	<label for="rs_birthday_date_label"><?php echo esc_html( get_option( 'rs_bday_field_label' ) ); ?>
		<?php if ( 'yes' == get_option( 'rs_enable_bday_field_mandatory' ) ) : ?>
			<span class="required">*</span>
		<?php endif; ?>
	</label>
	<input type="date" id="<?php echo esc_attr( $args[ 'id' ] ); ?>" name="<?php echo esc_attr( $args[ 'id' ] ); ?>" value="<?php echo esc_attr( $args[ 'value' ] ); ?>">
	<span><em><?php echo esc_html( $label ); ?></em></span>
</p>
<?php
