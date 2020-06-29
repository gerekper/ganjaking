<?php
/**
 * Order Referral MetaBox
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly

if( ! empty( $referral ) ):
	$total = 0;
?>
	<div class="referral-user">
		<a href="<?php echo esc_url( add_query_arg( array( 'action' => 'yith_wcaf_delete_order_affiliate', 'order_id' => yit_get_prop( $order, 'id' ) ), admin_url( 'admin.php' ) ) ) ?>" class="delete-affiliate tips" data-tip="<?php _e( 'Delete Affiliates', 'yith-woocommerce-affiliates' ) ?>"></a>
		<div class="referral-avatar">
			<?php echo get_avatar( $referral, 64 ); ?>
		</div>
		<div class="referral-info">
			<h3><a href="<?php echo get_edit_user_link( $referral ) ?>"><?php echo $username ?></a></h3>
			<a href="mailto:<?php echo $user_email?>"><?php echo $user_email?></a>
		</div>
	</div>

	<?php if( ! empty( $commissions ) ): ?>
	<div class="referral-commissions woocommerce_order_items_wrapper">
		<table class="woocommerce_order_items wp-list-table">
			<thead>
			<tr>
				<th scope="col" class="column-id"><?php _e( 'ID', 'yith-wcaf' ) ?></th>
				<th scope="col" class="column-status"><span class="status_head tips" data-tip="<?php _e( 'Status', 'yith-wcaf' ) ?>"><?php _e( 'Status', 'yith-wcaf' ) ?></span></th>
				<th scope="col" class="column-rate"><?php _e( 'Rate', 'yith-wcaf' ) ?></th>
				<th scope="col" class="column-amount"><?php _e( 'Amount', 'yith-wcaf' ) ?></th>
			</tr>
			</thead>
			<?php
			foreach( $commissions as $commission ):
				$commission_url = esc_url( add_query_arg( array( 'page' => 'yith_wcaf_panel', 'commission_id' => $commission['ID'] ), admin_url( 'admin.php' ) ) );
				$human_friendly_status = YITH_WCAF_Commission_Handler()->get_readable_status( $commission['status'] );
				$total += floatval( $commission['amount'] );
				?>
				<tr>
					<td class="column-id"><a href="<?php echo $commission_url ?>"><strong>#<?php echo esc_attr( $commission['ID'] )?></strong></a></td>
					<td class="column-status"><mark class="<?php echo $commission['status']?> tips" data-tip="<?php echo $human_friendly_status ?>"><?php echo $human_friendly_status ?></mark></td>
					<td class="column-rate"><?php echo number_format( $commission['rate'], 2 ) ?>%</td>
					<td class="column-amount"><?php echo wc_price( $commission['amount'] ) ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
	</div>
	<div class="referral-totals">
		<table>
			<tbody>
			<tr>
				<td class="label"><?php _e( 'Order Total:', 'yith-wcaf' ) ?></td>
				<td class="total"><?php echo wc_price( $order->get_total() ) ?></td>
			</tr>
			<tr>
				<td class="label"><?php printf( '%s <span class="tips" data-tip="%s">[?]</span>:', __( 'Commissions', 'yith-wcaf' ), __( 'This is the total of commissions credited to referral', 'yith-wcaf' ) ) ?></td>
				<td class="total"><?php echo wc_price( $total ) ?></td>
			</tr>
			<tr>
				<td class="label"><?php _e( 'Store Earnings:', 'yith-wcaf' ) ?></td>
				<td class="total"><?php echo wc_price( $order->get_total() - $total ) ?></td>
			</tr>

			<?php do_action('yith_wcaf_refeal_totals_table', $order ); ?>
			</tbody>
		</table>
	</div>
	<?php endif; ?>
<?php else: ?>
	<div class="referral-user">
		<div class="no-referral-message">
			<?php _e( 'No referral set yet', 'yith-wcaf' ) ?>
		</div>
		<div class="set-referral">
            <?php
            yit_add_select2_fields( array(
                'name' => 'persistent_token',
                'id' => 'persistent_token',
                'style' => "width: 220px; vertical-align: middle; display: inline-block!important; margin-right: 2px;",
                'class' => 'wc-product-search',
                'data-action' => 'json_search_affiliates',
                'data-placeholder' => __( 'Select an affiliate', 'yith-wcaf' )
            ) );
            ?>
			<button class="button button-secondary calculate-commission-button wc-reload"><?php _e( 'Calculate commissions', 'yith-wcaf' ) ?></button>
		</div>
	</div>
<?php endif; ?>