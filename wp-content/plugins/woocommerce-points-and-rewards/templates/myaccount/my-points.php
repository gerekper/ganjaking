<?php
/**
 * WooCommerce Points and Rewards
 *
 * @package WC-Points-Rewards/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * My Account - My Points
 */
?>
<?php /* translators: %s - Points Label */ ?>
<h2><?php echo esc_html( sprintf( esc_html__( 'My %s', 'woocommerce-points-and-rewards' ), $points_label ) ); ?></h2>

<?php /* translators: %1$d - Points Balance, %2$s - Points Label */ ?>
<p><?php echo esc_html( sprintf( esc_html__( 'You have %1$d %2$s', 'woocommerce-points-and-rewards' ), $points_balance, $points_label ) ); ?></p>

<?php if ( $events ) : ?>
	<table class="shop_table my_account_points_rewards my_account_orders">
		<thead>
			<tr>
				<th class="points-rewards-event-description"><span class="nobr"><?php esc_html_e( 'Event', 'woocommerce-points-and-rewards' ); ?></span></th>
				<th class="points-rewards-event-date"><span class="nobr"><?php esc_html_e( 'Date', 'woocommerce-points-and-rewards' ); ?></span></th>
				<th class="points-rewards-event-points"><span class="nobr"><?php echo esc_html( $points_label ); ?></span></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ( $events as $event ) : ?>
			<tr class="points-event">
				<td class="points-rewards-event-description">
					<?php echo wp_kses_post( $event->description ); ?>
				</td>
				<td class="points-rewards-event-date">
					<?php echo '<abbr title="' . esc_attr( $event->date_display ) . '">' . esc_html( $event->date_display_human ) . '</abbr>'; ?>
				</td>
				<td class="points-rewards-event-points" width="1%">
					<?php echo esc_html( ( $event->points > 0 ? '+' : '' ) . $event->points ); ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination">
	<?php if ( 1 !== intval( $current_page ) ) : ?>
		<a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button" href="<?php echo esc_url( wc_get_endpoint_url( 'points-and-rewards', $current_page - 1 ) ); ?>"><?php esc_html_e( 'Previous', 'woocommerce-points-and-rewards' ); ?></a>
	<?php endif; ?>

	<?php if ( $current_page * $count < $total_rows ) : ?>
		<a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button" href="<?php echo esc_url( wc_get_endpoint_url( 'points-and-rewards', $current_page + 1 ) ); ?>"><?php esc_html_e( 'Next', 'woocommerce-points-and-rewards' ); ?></a>
	<?php endif; ?>
	</div>

	<?php
endif;
