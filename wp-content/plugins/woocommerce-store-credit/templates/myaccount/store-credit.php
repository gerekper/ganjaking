<?php
/**
 * My account: store credit.
 *
 * @package WC_Store_Credit/Templates/My Account
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;

$coupons = wc_store_credit_get_customer_coupons( get_current_user_id() );
$columns = array(
	'code'    => _x( 'Code', 'my account: column name', 'woocommerce-store-credit' ),
	'credit'  => _x( 'Credit', 'my account: column name', 'woocommerce-store-credit' ),
	'expiry'  => _x( 'Expiry date', 'my account: column name', 'woocommerce-store-credit' ),
	'actions' => _x( 'Actions', 'my account: column name', 'woocommerce-store-credit' ),
);
?>

<?php if ( empty( $coupons ) ) : ?>
	<p class="woocommerce-Message woocommerce-Message--info woocommerce-info"><?php esc_html_e( 'No store credit coupons found.', 'woocommerce-store-credit' ); ?></p>
<?php else : ?>
	<table class="woocommerce-MyAccount-store-credit shop_table shop_table_responsive">
		<thead>
			<tr>
				<th class="woocommerce-store-credit-code"><span class="nobr"><?php echo esc_html( $columns['code'] ); ?></span></th>
				<th class="woocommerce-store-credit-credit"><span class="nobr"><?php echo esc_html( $columns['credit'] ); ?></span></th>
				<th class="woocommerce-store-credit-expiry"><span class="nobr"><?php echo esc_html( $columns['expiry'] ); ?></span></th>
				<th class="woocommerce-store-credit-actions"><span class="nobr"><?php echo esc_html( $columns['actions'] ); ?></span></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ( $coupons as $coupon ) : ?>
			<tr class="woocommerce-store-credit-row">
				<td class="woocommerce-store-credit-code" data-title="<?php echo esc_attr( $columns['code'] ); ?>">
					<?php echo esc_html( $coupon->get_code() ); ?>
				</td>

				<td class="woocommerce-store-credit-credit" data-title="<?php echo esc_attr( $columns['credit'] ); ?>">
					<?php echo wp_kses_post( wc_price( $coupon->get_amount() ) ); ?>
				</td>

				<td class="woocommerce-store-credit-expiry" data-title="<?php echo esc_attr( $columns['expiry'] ); ?>">
					<?php
						$expiration_date = $coupon->get_date_expires();

						echo esc_html( $expiration_date ? wc_format_datetime( $expiration_date ) : '&ndash;' );
					?>
				</td>

				<td class="woocommerce-store-credit-actions" data-title="<?php echo esc_attr( $columns['actions'] ); ?>">
					<a href="<?php echo esc_url( wc_store_credit_get_redeem_url( $coupon ) ); ?>" class="woocommerce-button button apply">
						<?php echo esc_html__( 'Use now', 'woocommerce-store-credit' ); ?>
					</a>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>
