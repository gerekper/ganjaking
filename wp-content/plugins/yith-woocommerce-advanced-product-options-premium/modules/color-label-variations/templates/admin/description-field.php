<?php
/**
 * Add description field to add/edit products attribute
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 * @var boolean $edit True if is edit section, false otherwise.
 * @var string $att_description The attribute description.
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

?>

<?php if ( $edit ) : ?>

	<tr class="form-field form-required">
		<th scope="row" valign="top">
			<label for="attribute_public"><?php echo esc_html__( 'Description', 'yith-woocommerce-product-add-ons' ); ?></label>
		</th>
		<td>
			<textarea name="attribute_description" id="attribute_description">
			<?php
			if ( $att_description ) {
				echo wp_kses_post( $att_description );
			}
			?>
			</textarea>
			<p class="description"><?php echo esc_html__( 'Description for product attributes.', 'yith-woocommerce-product-add-ons' ); ?></p>
		</td>
	</tr>

<?php else : ?>

	<div class="form-field">
		<label for="attribute_description"><?php echo esc_html__( 'Description', 'yith-woocommerce-product-add-ons' ); ?></label>
		<textarea name="attribute_description" id="attribute_description">
		<?php
		if ( $att_description ) {
			echo wp_kses_post( $att_description );
		}
		?>
		</textarea>
		<p class="description"><?php echo esc_html__( 'Description for product attributes.', 'yith-woocommerce-product-add-ons' ); ?></p>
	</div>

<?php endif; ?>
