<?php
/**
 * My Points
 *
 * Shows total of user's points account page
 *
 * @since   1.0.0
 * @author  YITH
 * @package YITH WooCommerce Points and Rewards
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! is_user_logged_in() ) { ?>

	<p><?php esc_html_e( 'You must to be logged in to view your points.', 'yith-woocommerce-points-and-rewards' ); ?></p>
	<?php
	return;
}


$points   = get_user_meta( get_current_user_id(), '_ywpar_user_total_points', true );
$points   = ( '' === $points ) ? 0 : $points;
$singular = YITH_WC_Points_Rewards()->get_option( 'points_label_singular' );
$plural   = YITH_WC_Points_Rewards()->get_option( 'points_label_plural' );

$history = YITH_WC_Points_Rewards()->get_history( get_current_user_id() );

if ( 'yes' === get_option( 'ywpar_show_point_worth_my_account', 'yes' ) ) {
	$toredeem          = '';
	$conversion_method = YITH_WC_Points_Rewards()->get_option( 'conversion_rate_method' );

	if ( 'fixed' === $conversion_method ) {
		$rates    = YITH_WC_Points_Rewards()->get_option( 'rewards_conversion_rate' );
		$money    = $rates[ get_woocommerce_currency() ]['money'];
		$toredeem = wc_price( abs( ( $points / $rates[ get_woocommerce_currency() ]['points'] ) * $money ) );
	} else {

		$rates    = YITH_WC_Points_Rewards()->get_option( 'rewards_percentual_conversion_rate' );
		$discount = $rates[ get_woocommerce_currency() ]['discount'];
		$toredeem = ( ( $points / $rates[ get_woocommerce_currency() ]['points'] ) * $discount );

		$toredeem = sprintf( '%s %s', $toredeem . '%', _x( 'on order total', '20% on order total', 'yith-woocommerce-points-and-rewards' ) );
	}
}

?>
<div class="ywpar-wrapper">
	<h2><?php echo wp_kses_post( apply_filters( 'ywpar_my_account_my_points_title', sprintf( _x( 'My %s','Placeholder: label of points;','yith-woocommerce-points-and-rewards' ), $plural ) ) ); ?></h2>

	<p>
		<?php
		$points  = sprintf( _nx( '<strong>%1$s</strong> %2$s', '<strong>%3$s</strong> %4$s', $points, 'First placeholder: number of points; second placeholder: label of points;','yith-woocommerce-points-and-rewards' ), $points, $singular, $points, $plural );
		$worth   = get_option( 'ywpar_show_point_worth_my_account', 'yes' ) === 'yes' ? ' <span>(' . __( 'worth', 'yith-woocommerce-points-and-rewards' ) . ' ' . $toredeem . ')</span>' : '';
		$message = _x( 'You have %1$s%2$s.','First placeholder: number of points; second placeholder: label of points;', 'yith-woocommerce-points-and-rewards' );
		echo sprintf( wp_kses_post( $message ), wp_kses_post( $points ), wp_kses_post( $worth ) );
		?>
	</p>

	<h3><?php echo wp_kses_post( apply_filters( 'ywpar_my_account_my_points_history_title', sprintf( _x( 'My %s History', 'Points label', 'yith-woocommerce-points-and-rewards' ), $plural ) ) ); ?></h3>

	<?php if ( $history ) : ?>
		<table class="shop_table ywpar_points_rewards my_account_orders">
			<thead>
			<tr>
				<th class="ywpar_points_rewards-date"><?php esc_html_e( 'Date', 'yith-woocommerce-points-and-rewards' ); ?></th>
				<th class="ywpar_points_rewards-action"><?php esc_html_e( 'Action', 'yith-woocommerce-points-and-rewards' ); ?></th>
				<th class="ywpar_points_rewards-order"><?php esc_html_e( 'Order No.', 'yith-woocommerce-points-and-rewards' ); ?></th>
				<th class="ywpar_points_rewards-points"><?php echo esc_html( $plural ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ( $history as $item ) : ?>
				<tr class="ywpar-item">
					<td class="ywpar_points_rewards-date">
						<?php echo wp_kses_post( date_i18n( wc_date_format(), strtotime( $item->date_earning ) ) ); ?>
					</td>
					<td class="ywpar_points_rewards-action">
						<?php echo esc_html( ( $item->description ) ? stripslashes( $item->description ) : YITH_WC_Points_Rewards()->get_action_label( $item->action ) ); ?>
					</td>
					<td class="ywpar_points_rewards-order">
						<?php
						if ( 0 !== $item->order_id ) :
							$order = wc_get_order( $item->order_id );
							if ( $order ) {
								echo wp_kses_post( '<a href="' . esc_url( $order->get_view_order_url() ) . '">#' . esc_html( $order->get_order_number() ) . '</a>' );
							} else {
								echo esc_html( '#' . $item->order_id );
							}
						endif
						?>
					</td>
					<td class="ywpar_points_rewards-points" width="1%">
						<?php echo wp_kses_post( $item->amount ); ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
</div>
