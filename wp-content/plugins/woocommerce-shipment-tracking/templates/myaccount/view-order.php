<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * View Order: Tracking information
 *
 * Shows tracking numbers view order page
 *
 * @author  WooThemes
 * @package WooCommerce Shipment Tracking/templates/myaccount
 * @version 1.6.4
 */

if ( $tracking_items ) : ?>

	<h2><?php echo apply_filters( 'woocommerce_shipment_tracking_my_orders_title', __( 'Tracking Information', 'woocommerce-shipment-tracking' ) ); ?></h2>

	<table class="shop_table shop_table_responsive my_account_tracking">
		<thead>
			<tr>
				<th class="tracking-provider"><span class="nobr"><?php _e( 'Provider', 'woocommerce-shipment-tracking' ); ?></span></th>
				<th class="tracking-number"><span class="nobr"><?php _e( 'Tracking Number', 'woocommerce-shipment-tracking' ); ?></span></th>
				<th class="date-shipped"><span class="nobr"><?php _e( 'Date', 'woocommerce-shipment-tracking' ); ?></span></th>
				<th class="order-actions">&nbsp;</th>
			</tr>
		</thead>
		<tbody><?php
		foreach ( $tracking_items as $tracking_item ) {
				?><tr class="tracking">
					<td class="tracking-provider" data-title="<?php _e( 'Provider', 'woocommerce-shipment-tracking' ); ?>">
						<?php echo esc_html( $tracking_item['formatted_tracking_provider'] ); ?>
					</td>
					<td class="tracking-number" data-title="<?php _e( 'Tracking Number', 'woocommerce-shipment-tracking' ); ?>">
						<?php echo esc_html( $tracking_item['tracking_number'] ); ?>
					</td>
					<td class="date-shipped" data-title="<?php _e( 'Date', 'woocommerce-shipment-tracking' ); ?>" style="text-align:left; white-space:nowrap;">
						<time datetime="<?php echo date( 'Y-m-d', $tracking_item['date_shipped'] ); ?>" title="<?php echo date( 'Y-m-d', $tracking_item['date_shipped'] ); ?>"><?php echo date_i18n( get_option( 'date_format' ), $tracking_item['date_shipped'] ); ?></time>
					</td>
					<td class="order-actions" style="text-align: center;">
							<?php if ( '' !== $tracking_item['formatted_tracking_link'] ) { ?>
							<a href="<?php echo esc_url( $tracking_item['formatted_tracking_link'] ); ?>" target="_blank" class="button"><?php _e( 'Track', 'woocommerce-shipment-tracking' ); ?></a>
							<?php } ?>
					</td>
				</tr><?php
		}
		?></tbody>
	</table>

<?php
endif;
