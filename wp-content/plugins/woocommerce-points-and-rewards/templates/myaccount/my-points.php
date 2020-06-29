<?php
/**
 * WooCommerce Points and Rewards
 *
 * @package     WC-Points-Rewards/Templates
 * @author      WooThemes
 * @copyright   Copyright (c) 2013, WooThemes
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * My Account - My Points
 */
?>
<h2><?php printf( __( 'My %s', 'woocommerce-points-and-rewards' ), $points_label  ); ?></h2>

<p><?php printf( __( "You have %d %s", 'woocommerce-points-and-rewards' ), $points_balance, $points_label ); ?></p>

<?php if ( $events ) : ?>
	<table class="shop_table my_account_points_rewards my_account_orders">
		<thead>
			<tr>
				<th class="points-rewards-event-description"><span class="nobr"><?php _e( 'Event', 'woocommerce-points-and-rewards' ); ?></span></th>
				<th class="points-rewards-event-date"><span class="nobr"><?php _e( 'Date', 'woocommerce-points-and-rewards' ); ?></span></th>
				<th class="points-rewards-event-points"><span class="nobr"><?php echo esc_html( $points_label ); ?></span></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ( $events as $event ) : ?>
			<tr class="points-event">
				<td class="points-rewards-event-description">
					<?php echo $event->description; ?>
				</td>
				<td class="points-rewards-event-date">
					<?php echo '<abbr title="' . esc_attr( $event->date_display ) . '">' . esc_html( $event->date_display_human ) . '</abbr>'; ?>
				</td>
				<td class="points-rewards-event-points" width="1%">
					<?php echo ( $event->points > 0 ? '+' : '' ) . $event->points; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination">
	<?php if ( $current_page != 1 ) : ?>
		<a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button" href="<?php echo esc_url( wc_get_endpoint_url( 'points-and-rewards', $current_page - 1 ) ); ?>"><?php _e( 'Previous', 'woocommerce-points-and-rewards' ); ?></a>
	<?php endif; ?>

	<?php if ( $current_page * $count < $total_rows ) : ?>
		<a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button" href="<?php echo esc_url( wc_get_endpoint_url( 'points-and-rewards', $current_page + 1 ) ); ?>"><?php _e( 'Next', 'woocommerce-points-and-rewards' ); ?></a>
	<?php endif; ?>
	</div>

<?php endif;
