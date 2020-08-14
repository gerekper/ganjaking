<?php
/**
 * Cart discount field.
 *
 * @package YITH WooCommerce Dynamic Pricing and Discounts Premium
 * @since   1.0.0
 * @version 1.6.0
 * @author  YITH
 *
 * @var array $args
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;
extract( $args );
$db_value = get_post_meta( $post->ID, $id, true );

$cart_rules_options = YITH_WC_Dynamic_Pricing()->cart_rules_options;
$discount_type      = isset( $db_value['discount_type'] ) ? $db_value['discount_type'] : '';
?>


<?php if ( function_exists( 'yith_field_deps_data' ) ) : ?>
<div id="<?php echo esc_attr( $id ); ?>-container" <?php echo yith_field_deps_data( $args );  //phpcs:ignore?>
	class="yith-plugin-fw-metabox-field-row">
	<?php else : ?>
	<div id="<?php echo esc_attr( $id ); ?>-container"
		<?php
		if ( isset( $deps ) ) :
			?>
			data-field="<?php echo esc_attr( $id ); ?>" data-dep="<?php echo esc_attr( $deps['ids'] ); ?>" data-value="<?php echo esc_attr( $deps['values'] ); ?>" <?php endif ?>>
		<?php endif; ?>
		<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>

		<div class="discount-table-rules-wrapper">
			<table class="cart-discount-amount">
				<tr>
					<th width="75%"><?php esc_html_e( 'Discount Type', 'ywdpd' ); ?></th>
					<th with="25%"><?php esc_html_e( 'Amount', 'ywdpd' ); ?></th>
				</tr>
				<tr>
					<td>
						<select class="wc-enhanced-select" name="<?php echo esc_attr( $name ); ?>[discount_type]"
							id="<?php echo esc_attr( $id ) . '[discount_type]'; ?>">
							<?php foreach ( $cart_rules_options['discount_type'] as $key_type => $rule_type ) : ?>
								<option
									value="<?php echo esc_attr( $key_type ); ?>" <?php selected( $discount_type, $key_type ); ?>><?php echo esc_html( $rule_type ); ?></option>
							<?php endforeach ?>
						</select>
					</td>

					<td>
						<input type="text" name="<?php echo esc_attr( $name ); ?>[discount_amount]"
							id="<?php echo esc_attr( $id ) . '[discount_amount]'; ?>"
							value="<?php echo ( isset( $db_value['discount_amount'] ) ) ? esc_attr( $db_value['discount_amount'] ) : ''; ?>"/>
					</td>
				</tr>
			</table>

		</div>

	</div>
