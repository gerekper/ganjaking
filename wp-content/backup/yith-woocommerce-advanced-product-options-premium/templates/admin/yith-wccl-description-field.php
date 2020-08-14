<?php
/**
 * Add description field to add/edit products attribute
 *
 * @author  Yithemes
 * @package YITH WooCommerce Color and Label Variations Premium
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php if( $edit ) : ?>

	<tr class="form-field form-required">
		<th scope="row" valign="top">
			<label for="attribute_public"><?php _e( 'Description', 'yith-woocommerce-product-add-ons' ); ?></label>
		</th>
		<td>
			<textarea name="attribute_description" id="attribute_description"><?php if( $value ) echo $value ?></textarea>
			<p class="description"><?php _e( 'Product attribute description.', 'yith-woocommerce-product-add-ons' ); ?></p>
		</td>
	</tr>

<?php else: ?>

	<div class="form-field">
		<label for="attribute_description"><?php _e( 'Description', 'yith-woocommerce-product-add-ons' ); ?></label>
		<textarea name="attribute_description" id="attribute_description"><?php if( $value ) echo $value ?></textarea>
		<p class="description"><?php _e( 'Product attribute description.', 'yith-woocommerce-product-add-ons' ); ?></p>
	</div>

<?php endif; ?>