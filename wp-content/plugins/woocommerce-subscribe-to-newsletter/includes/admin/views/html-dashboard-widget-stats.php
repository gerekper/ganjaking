<?php
/**
 * Admin View: Dashboard widget - Stats.
 *
 * @package WC_Newsletter_Subscription/Admin/Views
 * @since   3.0.0
 */

if ( empty( $stats ) || empty( $last_sync ) ) :
	echo '<p>' . esc_html__( 'There are not subscriber stats yet.', 'woocommerce-subscribe-to-newsletter' ) . '</p>';
	return;
else :
	echo '<ul class="wc-newsletter-subscription-stats-list woocommerce_stats">';
	foreach ( $stats as $key => $stat ) {
		printf(
			'<li class="wc_newsletter_subscription_stats_%1$s"><strong>%2$s</strong> %3$s</li>',
			esc_attr( $key ),
			esc_html( $stat['value'] ),
			esc_html( $stat['label'] )
		);
	}
	echo '</ul>';
	echo '<div class="wc-newsletter-subscription-stats-footer">
			<span class="sync-label">' . esc_html__( 'Last sync', 'woocommerce-subscribe-to-newsletter' ) . ':</span>
			<span class="sync-date">' . esc_html( $last_sync ) . '</span>
			<button class="refresh-stats button">' . esc_html__( 'Refresh', 'woocommerce-subscribe-to-newsletter' ) . '</button>
		  </div>';
endif;
