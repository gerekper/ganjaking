<?php
/**
 * The Template for list saved cards on checkout
 */

do_action( 'woocommerce_before_saved_cards' );
?>

<?php if ( ! empty( $payment_methods ) ) : ?>

	<table class="shop_table shop_table_responsive my_account_orders my_account_authorize_payment_methods">

		<thead>
		<tr>
			<th class="payment-method-type">
				<span class="nobr"><?php esc_html_e( 'Card', 'yith-woocommerce-authorizenet-payment-gateway' ); ?></span></th>
			<th class="payment-method-expire">
				<span class="nobr"><?php esc_html_e( 'Expire', 'yith-woocommerce-authorizenet-payment-gateway' ); ?></span></th>
			<th class="payment-method-actions">&nbsp;</th>
		</tr>
		</thead>

		<tbody>

		<?php foreach ( $payment_methods as $payment_method ) : ?>
			<tr class="order">
				<td class="payment-method-type" data-title="<?php esc_html_e( 'Account Number', 'yith-woocommerce-authorizenet-payment-gateway' ); ?>">
					<?php
					printf(
						'<span class="payment-method-number"><small>%s</small></span>',
						esc_html( $payment_method['account_num'] )
					);
					?>
					<?php if ( $payment_method['default'] ) : ?>
						<span class="tag-label default"><?php esc_html_e( 'default', 'yith-woocommerce-authorizenet-payment-gateway' ); ?></span>
					<?php else : ?>
						<?php
						$set_default_url = wp_nonce_url(
							add_query_arg(
								array(
									'wcauthnet-action' => 'set-default-card',
									'id'               => $payment_method['profile_id'],
								)
							),
							'wcauthnet-set-default-card'
						);
						?>
						<a class="tag-label default show-on-hover" href="<?php echo esc_url( $set_default_url ); ?>" data-table-action="default"><?php esc_html_e( 'set default', 'yith-woocommerce-authorizenet-payment-gateway' ); ?></a>
					<?php endif; ?>
				</td>
				<td class="payment-method-expire" data-title="<?php esc_html_e( 'Expire', 'yith-woocommerce-authorizenet-payment-gateway' ); ?>">
					<?php echo esc_html( implode( '/', str_split( $payment_method['expiration_date'], 2 ) ) ); ?>
				</td>
				<td class="payment-method-actions">
					<?php
					$delete_url = wp_nonce_url(
						add_query_arg(
							array(
								'wcauthnet-action' => 'delete-card',
								'id'               => $payment_method['profile_id'],
							)
						),
						'wcauthnet-delete-card'
					);
					?>
					<a href="<?php echo esc_url( $delete_url ); ?>" class="button delete" data-table-action="delete"><?php esc_html_e( 'Delete', 'yith-woocommerce-authorizenet-payment-gateway' ); ?></a>
				</td>
			</tr>
		<?php endforeach; ?>

		</tbody>

	</table>

<?php else : ?>

	<p><?php _e( 'No cards saved', 'yith-woocommerce-authorizenet-payment-gateway' ) ?></p>

<?php endif; ?>