<?php
/**
 * The template for displaying the end of an element in the builder mode options
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tm-builder-element-end.php
 *
 * NOTE that we may need to update template files and you
 * (the plugin or theme developer) will need to copy the new files
 * to your theme or plugin to maintain compatibility.
 *
 * @author  ThemeComplete
 * @package WooCommerce Extra Product Options/Templates
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

$do_end      = true;
$do_repeater = true;
if ( $repeater_quantity ) {
	$do_repeater = false;
}
if ( isset( $get_posted_key_count ) && isset( $get_posted_key ) && $get_posted_key_count > 1 ) {
	if ( $get_posted_key_count !== $get_posted_key + 1 ) {
		$do_end = false;
	}
	if ( $get_posted_key_count !== $get_posted_key + 1 ) {
		$do_repeater = false;
	}
}
?>
<?php if ( ! in_array( $element, [ 'header', 'divider' ], true ) && isset( $tm_element_settings ) && empty( THEMECOMPLETE_EPO()->tm_builder_elements[ $tm_element_settings['type'] ]->no_frontend_display ) ) : ?>
	</ul>
	<?php
	do_action( 'tm_after_builder_ul', isset( $tm_element_settings ) ? $tm_element_settings : [] );
	?>
	<?php if ( $repeater && ! $repeater_quantity ) : ?>
	<div class="tc-repeater-delete tc-hidden"><button type="button" class="tmicon tcfa tcfa-times delete"></button></div>
	<?php endif; ?>
	<?php if ( $repeater ) : ?>
	</div>
	<?php endif; ?>
	<?php if ( $do_end ) : ?>
	</div>
	<?php endif; ?>
	<?php if ( $do_repeater && isset( $tm_element_settings['repeater'] ) && ! empty( $tm_element_settings['repeater'] ) ) : ?>
	<div class="tc-cell tc-width100 tc-repeater-wrap">
		<button type="button" class="tc-repeater-add button">
		<?php
		echo ( '' !== $repeater_button_label ) ? wp_kses_post( $repeater_button_label ) : ( ! empty( THEMECOMPLETE_EPO()->tm_epo_add_button_text_repeater ) ? wp_kses_post( THEMECOMPLETE_EPO()->tm_epo_add_button_text_repeater ) : esc_html__( 'Add', 'woocommerce-tm-extra-product-options' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
		?>
		</button>
	</div>
	<?php endif; ?>
	<?php
	if ( ! empty( $description ) && ! empty( $description_position ) && 'below' === $description_position ) {
		$descriptionclass = '';
		if ( ! empty( $description_color ) ) {
			$descriptionclass = ' color-' . sanitize_hex_color_no_hash( $description_color );
		}
		// $description contains HTML code
		?>
		<div class="tc-cell tc-width100 tm-element-description tm-description<?php echo esc_attr( $descriptionclass ); ?>"><?php echo apply_filters( 'wc_epo_kses', wp_kses_post( $description ), $description ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
		<?php
	}
	?>
<?php endif; ?>
<?php if ( $do_end ) : ?>
	</div>
	</div>
</div>
<?php endif; ?>
<?php
do_action( 'tm_after_builder_element', isset( $tm_element_settings ) ? $tm_element_settings : [] );

