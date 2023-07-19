<?php
/**
 * Product attribute settings.
 *
 * @package WC_Instagram/Admin/Meta_Boxes
 * @since   4.4.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template vars.
 *
 * @var WC_Product_Attribute $attribute Attribute object.
 * @var int                  $index     Attribute index.
 * @var string               $google_pa Google product attribute key.
 */
?>
<tr>
	<td>
		<label for="attribute_google_pa"><?php echo esc_html__( 'Google attribute', 'woocommerce-instagram' ); ?>:</label>
		<?php
		if ( $attribute->get_id() ) :
			printf( '<strong>%s</strong>', esc_html( WC_Instagram_Google_Product_Attributes::get_label( $google_pa ) ) );
		else :
			$options = array( '' => __( 'Select an attribute&hellip;', 'woocommerce-instagram' ) ) + wp_list_pluck( WC_Instagram_Google_Product_Attributes::get_attributes(), 'label' );

			printf( '<select name="attribute_google_pa[%d]">', esc_attr( $index ) );
			foreach ( $options as $value => $label ) :
				// Use a product attribute instead for Google product attributes with pre-defined options.
				if ( WC_Instagram_Google_Product_Attributes::has_options( $value ) ) :
					continue;
				endif;

				printf(
					'<option value="%1$s"%2$s>%3$s</option>',
					esc_attr( $value ),
					selected( $value, $google_pa, false ),
					esc_html( $label )
				);
			endforeach;
			echo '</select>';
		endif;
		?>
	</td>
</tr>
