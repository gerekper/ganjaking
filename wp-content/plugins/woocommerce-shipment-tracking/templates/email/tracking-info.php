<?php
/**
 * Tracking info email template.
 * Shows tracking information in the HTML order email
 *
 * @package WC_Shipment_Tracking
 * @version 1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $tracking_items ) : ?>

	<h2>
		<?php
		/**
		 * Filter to change the content title of the email.
		 *
		 * @since 1.3.7
		 * @param String Content title.
		 */
		echo esc_html( apply_filters( 'woocommerce_shipment_tracking_my_orders_title', __( 'Tracking Information', 'woocommerce-shipment-tracking' ) ) );
		?>
	</h2>

	<table class="td" cellspacing="0" cellpadding="6" style="width: 100%;" border="1">

		<thead>
			<tr>
				<th class="tracking-provider" scope="col" class="td" style="text-align: left; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #737373; border: 1px solid #e4e4e4; padding: 12px;"><?php esc_html_e( 'Provider', 'woocommerce-shipment-tracking' ); ?></th>
				<th class="tracking-number" scope="col" class="td" style="text-align: left; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #737373; border: 1px solid #e4e4e4; padding: 12px;"><?php esc_html_e( 'Tracking Number', 'woocommerce-shipment-tracking' ); ?></th>
				<th class="date-shipped" scope="col" class="td" style="text-align: left; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #737373; border: 1px solid #e4e4e4; padding: 12px;"><?php esc_html_e( 'Date', 'woocommerce-shipment-tracking' ); ?></th>
				<th class="order-actions" scope="col" class="td" style="text-align: left; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #737373; border: 1px solid #e4e4e4; padding: 12px;">&nbsp;</th>
			</tr>
		</thead>

		<tbody>
		<?php
		foreach ( $tracking_items as $tracking_item ) {
			?>
				<tr class="tracking">
					<td class="tracking-provider" data-title="<?php esc_html_e( 'Provider', 'woocommerce-shipment-tracking' ); ?>" style="text-align: left; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #737373; border: 1px solid #e4e4e4; padding: 12px;">
						<?php echo esc_html( $tracking_item['formatted_tracking_provider'] ); ?>
					</td>
					<td class="tracking-number" data-title="<?php esc_html_e( 'Tracking Number', 'woocommerce-shipment-tracking' ); ?>" style="text-align: left; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #737373; border: 1px solid #e4e4e4; padding: 12px;">
						<?php echo esc_html( $tracking_item['tracking_number'] ); ?>
					</td>
					<td class="date-shipped" data-title="<?php esc_html_e( 'Status', 'woocommerce-shipment-tracking' ); ?>" style="text-align: left; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #737373; border: 1px solid #e4e4e4; padding: 12px;">
						<time datetime="<?php echo esc_html( gmdate( wc_date_format(), $tracking_item['date_shipped'] ) ); ?>" title="<?php echo esc_html( gmdate( wc_date_format(), $tracking_item['date_shipped'] ) ); ?>"><?php echo esc_html( date_i18n( get_option( 'date_format' ), $tracking_item['date_shipped'] ) ); ?></time>
					</td>
					<td class="order-actions" style="text-align: center; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #737373; border: 1px solid #e4e4e4; padding: 12px;">
							<a href="<?php echo esc_url( $tracking_item['formatted_tracking_link'] ); ?>" target="_blank"><?php esc_html_e( 'Track', 'woocommerce-shipment-tracking' ); ?></a>
					</td>
				</tr>
				<?php
		}
		?>
		</tbody>
	</table><br /><br />

	<?php
endif;
