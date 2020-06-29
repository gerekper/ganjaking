<div class="wrap">
	<h2><?php esc_html_e( 'Reports', 'yith-woocommerce-recover-abandoned-cart' ); ?> </h2>

	<div id="poststuff">
		<div id="post-body" class="metabox-holder">
			<div id="post-body-content">
				<table class="ywrac-reports" cellpadding="10" cellspacing="0">
					<tbody>
					<tr>
						<th width="20%"><?php esc_html_e( 'Abandoned Cart and Pending Orders', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
						<td><?php echo esc_html( $abandoned_carts_counter ); ?></td>
					</tr>

					<tr>
						<th width="20%"><?php esc_html_e( 'Abandoned Carts', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
						<td><?php echo esc_html( $total_abandoned_carts ); ?></td>
					</tr>

					<tr>
						<th width="20%"><?php esc_html_e( 'Order Pending', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
						<td><?php echo esc_html( $total_pending_orders ); ?> </td>
					</tr>
					</tbody>
				</table>
				<table class="ywrac-reports" cellpadding="10" cellspacing="0">
					<tbody>
						<tr>
							<th width="20%"><?php esc_html_e( 'Emails Sent', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
							<td><?php printf( esc_html( __( '%1$d (%2$d Clicks)', 'yith-woocommerce-recover-abandoned-cart' ) ), esc_html( $email_sent_counter ), esc_html( $email_clicks_counter ) ); ?></td>
						</tr>
						<tr>
							<th width="20%"><?php esc_html_e( 'Emails for Abandoned Carts Sent', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
							<td><?php printf( esc_html( __( '%1$d (%2$d Clicks)', 'yith-woocommerce-recover-abandoned-cart' ) ), esc_html( $email_sent_cart_counter ), esc_html( $email_cart_clicks_counter ) ); ?></td>
						</tr>
						<tr>
							<th width="20%"><?php esc_html_e( 'Emails for Pending Orders Sent', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
							<td><?php printf( esc_html( __( '%1$d (%2$d Clicks)', 'yith-woocommerce-recover-abandoned-cart' ) ), esc_html( $email_sent_order_counter ), esc_html( $email_order_clicks_counter ) ); ?></td>
						</tr>

					</tbody>
				</table>
				<table class="ywrac-reports" cellpadding="10" cellspacing="0">
					<tbody>
						<tr>
							<th width="20%"><?php esc_html_e( 'Recovered Carts & Pending Orders', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
							<td><?php echo esc_html( $recovered_carts ); ?></td>
						</tr>

						<tr>
							<th><?php esc_html_e( 'Recovered Carts', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
							<td><?php echo esc_html( $total_recovered_carts ); ?></td>
						</tr>

						<tr>
							<th><?php esc_html_e( 'Pending Orders Recovered', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
							<td><?php echo esc_html( $total_recovered_pending_orders ); ?></td>
						</tr>
					</tbody>
				</table>
				<table class="ywrac-reports" cellpadding="10" cellspacing="0">
					<tbody>
						<tr>
							<th width="20%"><?php esc_html_e( 'Total Amount Recovered Cart and Pending Orders', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
							<td><?php echo wp_kses_post( wc_price( $total_amount ) ); ?></td>
						</tr>

						<tr>
							<th><?php esc_html_e( 'Total Amount Recovered Cart', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
							<td><?php echo wp_kses_post( wc_price( $total_cart_amount ) ); ?></td>
						</tr>

						<tr>
							<th><?php esc_html_e( 'Total Amount Recovered Pending Orders', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
							<td><?php echo wp_kses_post( wc_price( $total_order_amount ) ); ?></td>
						</tr>
					</tbody>
				</table>
				<table class="ywrac-reports" cellpadding="10" cellspacing="0">
					<tbody>
					<tr>
						<th width="20%"><?php esc_html_e( 'Rate Conversion', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
						<td><?php echo esc_html( $rate_conversion ); ?> %</td>
					</tr>

					<tr>
						<th><?php esc_html_e( 'Rate Cart Conversion', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
						<td><?php echo esc_html( $rate_cart_conversion ); ?> %</td>
					</tr>

					<tr>
						<th><?php esc_html_e( 'Rate Pending Order Conversion', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
						<td><?php echo esc_html( $rate_order_conversion ); ?> %</td>
					</tr>

					</tbody>
				</table>
			</div>
		</div>
		<br class="clear">
	</div>
</div>
