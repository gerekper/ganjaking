<?php
/**
 * Subscription details
 *
 * @package YITH WooCommerce Subscription
 * @since   2.0.0
 * @author  YITH
 *
 * @var YWSBS_Subscription $subscriptions Current Subscription.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$subscription_status_list = ywsbs_get_status();
?>
</div>
<!-- SUBSCRIPTION REVIEW BOX -->
<div class="ywsbs-box ywsbs-thank-you-page-box">
	<h3><?php esc_html_e( 'Related subscriptions', 'yith-woocommerce-subscription' ); ?></h3>
	<?php
	foreach ( $subscriptions

	as $subscription_id ) :
			$subscription          = ywsbs_get_subscription( $subscription_id );
			$subscription_status   = $subscription_status_list[ $subscription->get_status() ];
			$start_date            = ( $subscription->get( 'start_date' ) ) ? date_i18n( wc_date_format(), $subscription->get( 'start_date' ) ) : '<div class="empty-date">-</div>';
			$next_payment_due_date = ( ! in_array( $subscription_status, array( 'paused', 'cancelled' ), true ) && $subscription->get( 'payment_due_date' ) ) ? date_i18n( wc_date_format(), $subscription->get( 'payment_due_date' ) ) : '<span class="empty-date">-</span>';
			$end_date              = ( $subscription->get( 'end_date' ) ) ? date_i18n( wc_date_format(), $subscription->get( 'end_date' ) ) : '<div class="empty-date">-</div>';
		?>
	<table class="subscription-review-table">
		<tbody>
		<tr class="item-info">
			<th class="product-name" colspan="2">
				<h4><?php esc_html_e( 'Subscription', 'yith-woocommerce-subscription' ); ?> <?php echo esc_html( $subscription->get_number() ); ?></h4>
				<?php echo esc_html( $subscription->get_product_name() ); ?><?php echo esc_html( ' x ' . $subscription->get_quantity() ); ?>
				<?php
				if ( $subscription->variation_id ) {
					yith_ywsbs_get_product_meta( $subscription, $subscription->get_variation() );
				}
				?>
			</th>

		</tr>


		<tr>
			<th><?php echo esc_html_x( 'Status', 'Subscription status', 'yith-woocommerce-subscription' ); ?></th>
			<td><?php printf( '<span class="status %1$s">%1$s</span>', esc_attr( $subscription_status ) ); ?></td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Recurring amount', 'yith-woocommerce-subscription' ); ?></th>
			<td> <span class="recurring-price-wrapper"><?php echo wp_kses_post( YWSBS_Subscription_Helper()->get_formatted_recurring( $subscription, '', true, true ) ); ?>
					</span>
					<?php
					if ( $subscription->get_max_length() > 1 && 'yes' === get_option( 'ywsbs_subscription_total_amount', 'no' ) ) {
						$subscription_total = YWSBS_Subscription_Helper::get_total_subscription_amount( $subscription );
						echo wp_kses_post( $subscription_total );
					}
					?>
				</td>
		</tr>
		<tr>
			<th><?php echo esc_html_x( 'Start date:', 'Subscription started date', 'yith-woocommerce-subscription' ); ?></th>
			<td><?php echo wp_kses_post( $start_date ); ?></td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Next billing:', 'yith-woocommerce-subscription' ); ?></th>
			<td><?php echo wp_kses_post( $next_payment_due_date ); ?></td>
		</tr>
			<?php if ( ! empty( $subscription->get_end_date() ) ) : ?>
		<tr>
			<th><?php esc_html_e( 'Subscription ended on:', 'yith-woocommerce-subscription' ); ?></th>
			<td><?php echo wp_kses_post( $end_date ); ?></td>
		</tr>
		<?php endif; ?>
		</tbody>
	</table>



<?php endforeach; ?>
</div>
