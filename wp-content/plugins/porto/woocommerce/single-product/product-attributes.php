<?php
/**
 * Product attributes
 *
 * @version     3.6.0
 */

defined( 'ABSPATH' ) || exit;

$porto_woo_version = porto_get_woo_version_number();
if ( version_compare( $porto_woo_version, '3.6', '<' ) ) :
?>
	<table class="table table-striped shop_attributes">
		<?php if ( $display_dimensions && $product->has_weight() ) : ?>
			<tr>
				<th><?php esc_html_e( 'Weight', 'woocommerce' ); ?></th>
				<td class="product_weight"><?php echo esc_html( wc_format_weight( $product->get_weight() ) ); ?></td>
			</tr>
		<?php endif; ?>

		<?php if ( $display_dimensions && $product->has_dimensions() ) : ?>
			<tr>
				<th><?php esc_html_e( 'Dimensions', 'woocommerce' ); ?></th>
				<td class="product_dimensions"><?php echo esc_html( wc_format_dimensions( $product->get_dimensions( false ) ) ); ?></td>
			</tr>
		<?php endif; ?>

		<?php foreach ( $attributes as $attribute ) : ?>
			<tr>
				<th><?php echo wc_attribute_label( $attribute->get_name() ); ?></th>
				<td>
				<?php
					$values = array();

				if ( $attribute->is_taxonomy() ) {
					$attribute_taxonomy = $attribute->get_taxonomy_object();
					$attribute_values   = wc_get_product_terms( $product->get_id(), $attribute->get_name(), array( 'fields' => 'all' ) );

					foreach ( $attribute_values as $attribute_value ) {
						$value_name = esc_html( $attribute_value->name );

						if ( $attribute_taxonomy->attribute_public ) {
							$values[] = '<a href="' . esc_url( get_term_link( $attribute_value->term_id, $attribute->get_name() ) ) . '" rel="tag">' . $value_name . '</a>';
						} else {
							$values[] = $value_name;
						}
					}
				} else {
					$values = $attribute->get_options();

					foreach ( $values as &$value ) {
						$value = make_clickable( esc_html( $value ) );
					}
				}

					echo apply_filters( 'woocommerce_attribute', function_exists( 'porto_shortcode_format_content' ) ? porto_shortcode_format_content( implode( ', ', $values ) ) : implode( ', ', $values ), $attribute, $values );
				?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>

<?php
else :
	if ( ! $product_attributes ) {
		return;
	}
	?>
	<table class="woocommerce-product-attributes shop_attributes table table-striped">
		<?php foreach ( $product_attributes as $product_attribute_key => $product_attribute ) : ?>
			<tr class="woocommerce-product-attributes-item woocommerce-product-attributes-item--<?php echo esc_attr( $product_attribute_key ); ?>">
				<th class="woocommerce-product-attributes-item__label"><?php echo wp_kses_post( $product_attribute['label'] ); ?></th>
				<td class="woocommerce-product-attributes-item__value"><?php echo wp_kses_post( $product_attribute['value'] ); ?></td>
			</tr>
		<?php endforeach; ?>
	</table>

<?php
endif;
