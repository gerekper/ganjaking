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
 * @author  themeComplete
 * @package WooCommerce Extra Product Options/Templates
 * @version 5.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! in_array( $element, array( 'header', 'divider' ) ) && isset( $tm_element_settings ) && empty( THEMECOMPLETE_EPO()->tm_builder_elements[ $tm_element_settings['type'] ]["no_frontend_display"] ) ) {
	?>
    </ul>
	<?php do_action( 'tm_after_builder_ul', isset( $tm_element_settings ) ? $tm_element_settings : array() ); ?>
    </div>
	<?php
	if ( ! empty( $description ) && ! empty( $description_position ) && $description_position == "below" ) {
		$descriptionclass = "";
		if ( ! empty( $description_color ) ) {
			$descriptionclass = " color-" . sanitize_hex_color_no_hash( $description_color );
		}
		// $description contains HTML code
		?>
        <div class="tc-cell tc-width100 tm-description<?php echo esc_attr( $descriptionclass ); ?>"><?php echo apply_filters( 'wc_epo_kses', wp_kses_post( $description ), $description ); ?></div>
		<?php
	}
}
?></div></div></div>
<?php do_action( 'tm_after_builder_element', isset( $tm_element_settings ) ? $tm_element_settings : array() );
