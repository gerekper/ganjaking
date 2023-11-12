<?php
/**
 * UAEL Button Module Template.
 *
 * @package UAEL
 */

use UltimateElementor\Classes\UAEL_Helper;
?>
<?php
$classname = '';
if ( 'yes' === $settings['gf_radio_check_custom'] ) {
	$classname = '';
}

?>
<div class="uael-gf-style <?php echo 'uael-gf-check-style'; ?> elementor-clickable">
	<?php
		$form_title  = '';
		$description = '';
		$form_desc   = 'false';

	if ( 'yes' === $settings['form_title_option'] ) {
		if ( class_exists( 'GFAPI' ) ) {
			$form       = array();
			$form       = GFAPI::get_form( absint( $settings['form_id'] ) );
			$form_title = isset( $form['title'] ) ? $form['title'] : '';
			$form_desc  = 'true';
		}
	} elseif ( 'no' === $settings['form_title_option'] ) {
		$form_title  = $this->get_settings_for_display( 'form_title' );
		$description = $this->get_settings_for_display( 'form_desc' );
		$form_desc   = 'false';
	} else {
		$form_title  = '';
		$description = '';
		$form_desc   = 'false';
	}
	if ( '' !== $form_title ) {
		$form_title_tag = UAEL_Helper::validate_html_tag( $settings['form_title_tag'] );
		?>
	<<?php echo esc_attr( $form_title_tag ); ?> class="uael-gf-form-title"><?php echo wp_kses_post( $form_title ); ?></<?php echo esc_attr( $form_title_tag ); ?>>
		<?php
	}
	if ( '' !== $description ) {
		?>
	<p class="uael-gf-form-desc"><?php echo wp_kses_post( $description ); ?></p>
		<?php
	}
	if ( '0' === $settings['form_id'] ) {
		esc_attr_e( 'Please select a Gravity Form', 'uael' );
	} elseif ( $settings['form_id'] ) {
		$ajax = ( 'yes' === $settings['form_ajax_option'] ) ? 'true' : 'false';

		$shortcode_extra = '';
		$shortcode_extra = apply_filters( 'uael_gf_shortcode_extra_param', '', absint( $settings['form_id'] ) );

		echo do_shortcode( '[gravityform id=' . absint( $settings['form_id'] ) . ' ajax="' . $ajax . '" title="false" description="' . $form_desc . '" tabindex=' . $settings['form_tab_index_option'] . ' ' . $shortcode_extra . ']' );
	}

	?>

</div>
