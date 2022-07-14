<?php
/**
 * The template for displaying the product element variation table for the builder mode
 *
 * @author  ThemeComplete
 * @package WooCommerce Extra Product Options/Templates/Products
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;
$attribute_keys = array_keys( $attributes );

if ( count( $attributes ) > 0 ) :

	$this_name = $name;
	if ( isset( $layout_mode ) ) {
		if ( 'checkbox' === $layout_mode || 'thumbnailmultiple' === $layout_mode ) {
			$this_name = $name . '_' . $option['_default_value_counter'];
		}
	}

	?>
	<div class="tc-epo-element-variable-product">
		<table class="tc-epo-element-variations" cellspacing="0">
			<tbody>
			<?php
			foreach ( $attributes as $attribute_name => $attribute_options ) :
				$uniqid = uniqid( sanitize_title( $this_name . '_' . $attribute_name ) . '_' );
				?>
				<tr class="tc-epo-element-variable-product-attribute">
					<td class="label">
					<label class="tc-epo-element-variable-product-attribute-label" for="<?php echo esc_attr( $uniqid ); ?>"><?php echo wc_attribute_label( $attribute_name ); // phpcs:ignore WordPress.Security.EscapeOutput ?></label>
					</td>
					<td class="value">
						<?php

						$_REQUEST['asscociated_name']                      = $this_name;
						$_REQUEST[ 'asscociated_cart_data_' . $this_name ] = isset( $cart_data ) ? $cart_data : [];

						$attribute_selected =
							isset( $cart_data ) && isset( $cart_data[ $this_name . '_attribute_' . sanitize_title( $attribute_name ) ] )
								? $cart_data[ $this_name . '_attribute_' . sanitize_title( $attribute_name ) ]
								: ( isset( $_REQUEST[ $this_name . '_attribute_' . sanitize_title( $attribute_name ) ] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
								? wp_unslash( $_REQUEST[ $this_name . '_attribute_' . sanitize_title( $attribute_name ) ] ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
								: false );
						wc_dropdown_variation_attribute_options(
							[
								'class'     => 'tc-epo-variable-product-selector tc-epo-element-variable-product-attribute-dropdown',
								'options'   => $attribute_options,
								'attribute' => $attribute_name,
								'product'   => $current_product,
								'selected'  => $attribute_selected,
								'name'      => $this_name . '_attribute_' . sanitize_title( $attribute_name ),
								'id'        => $uniqid,
							]
						);
						echo end( $attribute_keys ) === $attribute_name ? wp_kses_post( '<a class="tc-epo-element-variable-reset-variations" href="#">' . esc_html__( 'Clear', 'woocommerce-tm-extra-product-options' ) . '</a>' ) : '';
						?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<div class="tc-epo-element-single-variation-wrap">
			<div class="woocommerce-variation tc-epo-element-single-variation"></div>
		</div>
	</div>
	<?php
endif;
