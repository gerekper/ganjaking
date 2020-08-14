<?php
/**
 * My Points
 *
 * Shows total of user's points account page
 *
 * @package YITH WooCommerce Points and Rewards
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$singular = YITH_WC_Points_Rewards()->get_option( 'points_label_singular' );
$plural   = YITH_WC_Points_Rewards()->get_option( 'points_label_plural' );

?>
	<h2 style="padding-top: 5px;margin: 10px 0;border-top: 1px solid #ececec; text-align: center;"><?php echo wp_kses_post( apply_filters( 'ywpar_my_account_my_points_title', $plural ) ); ?></h2>

<?php if ( $history ) : ?>
	<style>
		.shop_table.ywpar_points_rewards {
			border-top: 1px solid #ececec;
			border-bottom: 1px solid #ececec;
			margin-bottom: 10px;
			border-collapse: collapse;
		}

		.shop_table.ywpar_points_rewards thead th {
			font-size: 16px;
		}

		.shop_table.ywpar_points_rewards th, .shop_table.ywpar_points_rewards td {
			border: none;
			border-right: 1px solid #ececec;
		}

		.shop_table.ywpar_points_rewards th:last-child, .shop_table.ywpar_points_rewards td:last-child {
			border-right: none;
		}

		.shop_table.ywpar_points_rewards tr:nth-child(even), .shop_table.ywpar_points_rewards thead {
			background-color: #ececec;
		}

	</style>
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
					<?php echo wp_kses_post( ( $item->description ) ? stripslashes( $item->description ) : YITH_WC_Points_Rewards()->get_action_label( $item->action ) ); ?>
				</td>
				<td class="ywpar_points_rewards-order">
					<?php
					if ( 0 !== $item->order_id ) :
						$order = wc_get_order( $item->order_id );
						if ( $order ) :
							?>
						<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
							#<?php echo esc_html( $order->get_order_number() ); ?>
						</a>
							<?php
						 endif;
					 endif;
					?>
				</td>
				<td class="ywpar_points_rewards-points" width="1%">
					<?php
					$points_amount = apply_filters( 'ywpar_email_points_formatted', $item->amount );
					echo esc_html( $points_amount );
					?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php
endif;
