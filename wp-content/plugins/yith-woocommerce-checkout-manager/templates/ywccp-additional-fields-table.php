<?php
/**
 * Additional Fields Table for view order
 *
 * @since   1.0.0
 * @author  YITH
 * @package YITH WooCommerce Checkout Manager
 * @context frontend
 */

defined( 'YWCCP' ) || exit; // Exit if accessed directly.
?>

<header><h2><?php esc_html_e( 'Additional info', 'yith-woocommerce-checkout-manager' ); ?></h2></header>

<table class="shop_table additional_fields">
	<?php foreach ( $fields as $field ) : ?>
		<tr>
			<th><?php echo wp_kses_post( $field['label'] ? $field['label'] . ':' : '' ); ?></th>
			<td><?php echo wp_kses_post( $field['value'] ); ?></td>
		</tr>
	<?php endforeach; ?>
</table>
