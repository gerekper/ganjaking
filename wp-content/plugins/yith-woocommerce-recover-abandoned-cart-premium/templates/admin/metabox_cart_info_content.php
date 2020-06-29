<?php
/**
 * YITH WooCommerce Recover Abandoned Cart Content metabox template
 *
 * @package YITH WooCommerce Recover Abandoned Cart
 * @since   1.0.0
 * @author YITH
 */

?>
<table class="yith-ywrac-info-cart" cellspacing="20">
	<tbody>
		<tr>
			<th><?php esc_html_e( 'Cart Status:', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
			<td><span class="<?php echo esc_attr( $status ); ?>"><?php echo esc_html( $status ); ?></span></td>
		</tr>

		<tr>
			<th><?php esc_html_e( 'Cart Last Update:', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
			<td><?php echo esc_html( $last_update ); ?></td>
		</tr>


		<tr>
			<th><?php esc_html_e( 'User:', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
			<td><?php echo esc_html( $user_first_name . ' ' . $user_last_name ); ?></td>
		</tr>

		<tr>
			<th><?php esc_html_e( 'User email:', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
			<td><?php echo '<a href="mailto:' . esc_attr( $user_email ) . '">' . esc_html( $user_email ) . '</a>'; ?></td>
		</tr>

		<tr>
			<th><?php esc_html_e( 'User phone:', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
			<td><?php echo esc_html( $user_phone ); ?></td>
		</tr>


		<tr>
			<th><?php esc_html_e( 'Language:', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
			<td><?php echo esc_html( $language ); ?></td>
		</tr>


		<tr>
			<th><?php esc_html_e( 'Currency:', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
			<td><?php echo esc_html( $currency ); ?></td>
		</tr>


		<?php if ( ! empty( $history ) ) : ?>
		<tr>
			<th><?php esc_html_e( 'History:', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
			<td>
				<table class="ywrac-history-table" cellpadding="5">
					<tr>
						<th><?php esc_html_e( 'Sending Date', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
						<th><?php esc_html_e( 'Email Template', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
						<th><?php esc_html_e( 'Link Clicked', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
					</tr>
				<?php foreach ( $history as $h ) : ?>
					<tr>
						<td><?php echo esc_html( $h['data_sent'] ); ?></td>
						<td><?php echo esc_html( $h['email_name'] ); ?></td>
						<td><?php echo esc_html( ( $h['clicked'] == 0 ) ? 'no' : 'yes' ); ?></td>
					</tr>
				<?php endforeach ?>
				</table>

			</td>
		</tr>
	<?php endif ?>
	</tbody>
</table>
