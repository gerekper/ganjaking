<?php
/**
 * This template is used for Display Multiple Anniversary Date.
 *
 * This template can be overridden by copying it to yourtheme/rewardsystem/multiple-anniversary-date.php
 *
 * To maintain compatibility, Reward System will update the template files and you have to copy the updated files to your theme.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<p class="<?php echo esc_attr( $classname ); ?> rs-multiple-anniversary-field">
	<label>
	<?php
		echo esc_html( $field_name );
	if ( 'on' == $mandatory_field ) :
		?>
			<span class="required">*</span>
		<?php endif; ?> 
	</label>
	<?php srp_get_datepicker_html( $args ); ?>
	<span><em><?php echo esc_html( $field_desc ); ?></em></span>
</p>
<?php
