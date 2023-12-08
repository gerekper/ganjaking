<?php
/**
 * The template for displaying the product element variation id
 *
 * @author  ThemeComplete
 * @package Extra Product Options/Templates/Products
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;
if ( isset( $name, $variation_id ) ) :
	$name         = (string) $name;
	$variation_id = (string) $variation_id;

	$input_name = $name;
	if ( isset( $option ) && isset( $option['_default_value_counter'] ) && '' !== $option['_default_value_counter'] ) {
		$input_name .= '_' . $option['_default_value_counter'];
	}
	$input_name .= '_variation_id';
	?>
<div class="tc-epo-element-product-container-variation-id tm-hidden">
	<input type="hidden" class="product-variation-id" name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $variation_id ); ?>">
</div>
	<?php
endif;
