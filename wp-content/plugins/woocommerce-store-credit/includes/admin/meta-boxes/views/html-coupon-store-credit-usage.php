<?php
/**
 * Store Credit Usage
 *
 * @package WC_Store_Credit/Admin/Meta_Boxes
 * @since   3.3.0
 */

defined( 'ABSPATH' ) || exit;

$orders = WC_Store_Credit_Meta_Box_Coupon_Usage::get_orders( $coupon );
?>
<div class="store-credit-coupon-usage">
	<?php if ( empty( $orders ) ) : ?>
		<p class="empty-message"><?php esc_html_e( 'This coupon has not been used yet.', 'woocommerce-store-credit' ); ?></p>
	<?php else : ?>
		<table>
			<thead>
				<tr>
					<?php
					$columns = WC_Store_Credit_Meta_Box_Coupon_Usage::get_columns();

					foreach ( $columns as $key => $label ) :
						printf(
							'<th class="%1$s">%2$s</th>',
							esc_attr( $key ),
							esc_html( $label )
						);
					endforeach;
					?>
				</tr>
			</thead>
			<tbody>
			<?php foreach ( $orders as $related_order ) : ?>
				<tr>
				<?php
				foreach ( $columns as $key => $label ) :
					$value = WC_Store_Credit_Meta_Box_Coupon_Usage::get_column_value( $key, $coupon, $related_order );

					printf(
						'<td class="%1$s" data-colname="%2$s">%3$s</td>',
						esc_attr( $key ),
						esc_attr( $label ),
						$value // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					);
				endforeach;
				?>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
</div>
