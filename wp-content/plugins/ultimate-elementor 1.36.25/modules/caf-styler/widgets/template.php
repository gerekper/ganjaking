<?php
/**
 * UAEL Caldera Forms Styler Template.
 *
 * @package UAEL
 */

$classname = '';
if ( 'yes' === $settings['caf_radio_check_custom'] ) {
	$classname = 'uael-caf-check-style';
}

if ( '-1' === $settings['caf_select_caldera_form'] ) { ?>

	<div class="uael-form-editor-message">Please select a Caldera Form.</div>

<?php } else { ?>
	<div class="uael-caldera-form-wrapper">
		<div class="uael-caf-form elementor-clickable <?php echo esc_attr( $classname ); ?>">
			<?php echo do_shortcode( '[caldera_form id="' . $settings['caf_select_caldera_form'] . '" ]' ); ?>
		</div>
	</div>	
<?php } ?>
