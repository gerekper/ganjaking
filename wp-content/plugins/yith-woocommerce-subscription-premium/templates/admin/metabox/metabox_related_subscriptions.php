<?php //phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Metabox for Related Subscriptions
 *
 * @package YITH WooCommerce Subscription
 * @since   2.0.0
 * @author  YITH
 *
 * @var array $subscriptions Related Subscriptions.
 */


if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}
?>

<div class="related_subscription">
	<?php if ( ! $subscriptions ) : ?>
		<p><?php esc_html_e( 'No subscriptions found for this order.', 'yith-woocommerce-subscription' ); ?></p>
	<?php else : ?>
	<table class="ywsbs-history-table widefat striped">
		<thead>
		<tr>
			<th></th>
			<th><?php esc_html_e( 'Started on:', 'yith-woocommerce-subscription' ); ?></th>
			<th><?php esc_html_e( 'Recurring:', 'yith-woocommerce-subscription' ); ?></th>
			<th><?php esc_html_e( 'Payment due:', 'yith-woocommerce-subscription' ); ?></th>
			<th><?php esc_html_e( 'Ended on:', 'yith-woocommerce-subscription' ); ?></th>
			<th><?php esc_html_e( 'Expiry date:', 'yith-woocommerce-subscription' ); ?></th>
			<th><?php esc_html_e( 'Renewals:', 'yith-woocommerce-subscription' ); ?></th>
			<th><?php esc_html_e( 'Payment method:', 'yith-woocommerce-subscription' ); ?></th>
			<th><?php esc_html_e( 'Failed attempts:', 'yith-woocommerce-subscription' ); ?></th>
			<th><?php esc_html_e( 'Status:', 'yith-woocommerce-subscription' ); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach ( $subscriptions as $subscription ) :
			$subscription_id = $subscription->get_id();

			?>
			<tr>
				<td>
					<a href="
						<?php
						echo esc_url(
							add_query_arg(
								array(
									'post'   => $subscription_id,
									'action' => 'edit',
								),
								admin_url( 'post.php' )
							)
						);
						?>
						"><?php printf( '%s - %s', esc_html( $subscription->get_number() ), wp_kses_post( $subscription->product_name ) ); ?></a>
				</td>
				<td>
					<?php
					$start_date = $subscription->get( 'start_date' );
					echo esc_html( ( $start_date ) ? date_i18n( wc_date_format(), $start_date ) : '-' );
					?>
				</td>
				<td>
					<?php
					echo wp_kses_post( YWSBS_Subscription_Helper()->get_formatted_recurring( $subscription ) );
					?>
				</td>
				<td>
					<?php
					$payment_due_date = $subscription->get( 'payment_due_date' );
					echo esc_html( ( $payment_due_date ) ? date_i18n( wc_date_format(), $payment_due_date ) : '-' );
					?>
				</td>
				<td>
					<?php
					$end_date = $subscription->get( 'end_date' );
					echo esc_html( ( $end_date ) ? date_i18n( wc_date_format(), $end_date ) : '-' );
					?>
				</td>
				<td>
					<?php
					$expired_date = $subscription->get( 'expired_date' );
					echo esc_html( ( $expired_date ) ? date_i18n( wc_date_format(), $expired_date ) : '-' );
					?>
				</td>
				<td>
					<?php
					$rates_payed = $subscription->get_paid_rates();
					echo esc_html( empty( $rates_payed ) ? '-' : $rates_payed );
					?>
				</td>
				<td>
					<?php
					echo esc_html( $subscription->get( 'payment_method_title' ) );
					?>
				</td>
				<td>
					<?php
					$renew_order     = $subscription->get_renew_order();
					$failed_attempts = $renew_order ? $renew_order->get_meta( 'failed_attemps' ) : false;
					$failed_attempts = $failed_attempts ? $failed_attempts : 0;
					$payment_method  = $subscription->get( 'payment_method' );
					$attempts_list   = ywsbs_get_max_failed_attempts_list();

					$failed_attempts .= isset( $attempts_list[ $payment_method ] ) ? '/' . $attempts_list[ $payment_method ] : '';
					echo esc_html( $failed_attempts );
					?>
				</td>
				<td>
					<?php

					$subscription_status_list = ywsbs_get_status();
					$status                   = $subscription->get_status(); //phpcs:ignore
					$subscription_status      = $subscription_status_list[ $status ];
					printf( '<span class="status %1$s">%2$s</span>', esc_attr( $subscription->get_status() ), esc_html( $subscription_status ) );

					?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php endif; ?>
</div>
