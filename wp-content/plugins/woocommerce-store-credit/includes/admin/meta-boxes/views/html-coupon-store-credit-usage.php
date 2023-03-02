<?php
/**
 * Store Credit Usage
 *
 * @package WC_Store_Credit/Admin/Meta_Boxes
 * @since   3.3.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template vars.
 *
 * @var WC_Coupon  $coupon Coupon object.
 * @var WC_Order[] $orders The orders in which the coupon has been used.
 */
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
			<?php
			$total_used = 0;

			foreach ( $orders as $related_order ) :
				$total_used += wc_get_coupon_store_credit_used_for_order( $related_order, $coupon );
				?>
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
			<tfoot>
				<?php
				$initial_amount = (float) $coupon->get_meta( 'store_credit_amount' );

				if ( $initial_amount <= 0 ) :
					$initial_amount = $total_used + $coupon->get_amount();
				endif;
				?>
				<tr>
					<th colspan="2"><?php esc_html_e( 'Initial amount:', 'woocommerce-store-credit' ); ?> <?php echo wp_kses_post( wc_price( $initial_amount ) ); ?></th>
					<th colspan="2"><?php esc_html_e( 'Total used:', 'woocommerce-store-credit' ); ?> <?php echo wp_kses_post( wc_price( $total_used ) ); ?></th>
				</tr>
			</tfoot>
		</table>
	<?php endif; ?>
</div>
