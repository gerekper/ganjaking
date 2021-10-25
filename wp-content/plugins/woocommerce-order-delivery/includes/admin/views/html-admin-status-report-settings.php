<?php
/**
 * Admin View: Setting Status Report.
 *
 * @package WC_OD/Admin/Views
 * @since   1.9.4
 */

/**
 * Template vars.
 *
 * @var array $data The template data.
 */

$has_outdated = false;
?>
<table class="wc_status_table widefat" cellspacing="0">
	<thead>
		<tr>
			<th colspan="5" data-export-label="Order Delivery">
				<h2><?php esc_html_e( 'Order Delivery', 'woocommerce-order-delivery' ); ?><?php echo wc_help_tip( esc_html__( 'This section shows information about WooCommerce Order Delivery.', 'woocommerce-order-delivery' ) ); ?></h2>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td data-export-label="Minimum working days"><?php esc_html_e( 'Minimum working days', 'woocommerce-order-delivery' ); ?>:</td>
			<td class="help"></td>
			<td>
				<?php echo esc_html( $data['settings']['min_working_days'] ); ?>
			</td>
		</tr>
		<tr>
			<td data-export-label="Shipping days"><?php esc_html_e( 'Shipping days', 'woocommerce-order-delivery' ); ?>:</td>
			<td class="help"></td>
			<td>
				<?php
				foreach ( $data['settings']['shipping_days'] as $index => $shipping_day ) :
					echo '<strong>' . esc_html( $data['days'][ $index ] ) . '</strong>';
					echo "<br/>\n\t\t Enabled: ";
					WC_OD_Admin_System_Status::output_bool_html( wc_string_to_bool( $shipping_day['enabled'] ) );

					echo "<br/>\n\t\t Cut-off time: " . ( ! empty( $shipping_day['time'] ) ? esc_html( $shipping_day['time'] ) : '-' );
					echo "<br/>\n\t       ";
				endforeach;
				?>
			</td>
		</tr>
		<tr>
			<td data-export-label="Checkout location"><?php esc_html_e( 'Checkout location', 'woocommerce-order-delivery' ); ?>:</td>
			<td class="help"></td>
			<td>
				<?php echo esc_html( $data['settings']['checkout_location'] ); ?>
			</td>
		</tr>
		<tr>
			<td data-export-label="Checkout options"><?php esc_html_e( 'Checkout options', 'woocommerce-order-delivery' ); ?>:</td>
			<td class="help"></td>
			<td>
				<?php echo esc_html( $data['settings']['checkout_delivery_option'] ); ?>
			</td>
		</tr>
		<tr>
			<td data-export-label="Delivery ranges"><?php esc_html_e( 'Delivery ranges', 'woocommerce-order-delivery' ); ?>:</td>
			<td class="help"></td>
			<td>
				<?php
				foreach ( $data['settings']['delivery_ranges'] as $delivery_range ) :
					printf(
						"<strong>%1\$s</strong><br/>\n\t\t   Range: %2\$s - %3\$s<br/>\n\t\t   Shipping methods: %4\$s<br/>\n\t\t ",
						esc_html( $delivery_range->get_title() ),
						esc_html( $delivery_range->get_from() ),
						esc_html( $delivery_range->get_to() ),
						( $delivery_range->get_id() > 0 ? esc_html( $delivery_range->get_shipping_methods_option() ) : '-' )
					);
				endforeach;
				?>
			</td>
		</tr>
		<tr>
			<td data-export-label="Delivery days"><?php esc_html_e( 'Delivery days', 'woocommerce-order-delivery' ); ?>:</td>
			<td class="help"></td>
			<td>
				<?php
				foreach ( $data['settings']['delivery_days'] as $delivery_day ) :
					echo '<strong>' . esc_html( $data['days'][ $delivery_day->get_weekday() ] ) . '</strong>';
					echo "<br/>\n\t\t Enabled: ";
					WC_OD_Admin_System_Status::output_bool_html( $delivery_day->is_enabled() );
					echo "<br/>\n\t\t Nº of orders: " . esc_html( $delivery_day->get_number_of_orders() );
					echo "<br/>\n\t\t Shipping methods: " . ( $delivery_day->get_shipping_methods_option() ? esc_html( $delivery_day->get_shipping_methods_option() ) : 'all' );
					echo "<br/>\n\t\t Time frames: ";
					WC_OD_Admin_System_Status::output_bool_html( $delivery_day->has_time_frames() );
					echo "<br/>\n\t       ";
				endforeach;
				?>
			</td>
		</tr>
		<tr>
			<td data-export-label="Max delivery days"><?php esc_html_e( 'Max delivery days', 'woocommerce-order-delivery' ); ?>:</td>
			<td class="help"></td>
			<td>
				<?php echo esc_html( $data['settings']['max_delivery_days'] ); ?>
			</td>
		</tr>
		<tr>
			<td data-export-label="Delivery fields"><?php esc_html_e( 'Delivery fields', 'woocommerce-order-delivery' ); ?>:</td>
			<td class="help"></td>
			<td>
				<?php echo esc_html( $data['settings']['delivery_fields_option'] ); ?>
			</td>
		</tr>
		<tr>
			<td data-export-label="Enable local pickup"><?php esc_html_e( 'Enable local pickup', 'woocommerce-order-delivery' ); ?>:</td>
			<td class="help"></td>
			<td>
				<?php WC_OD_Admin_System_Status::output_bool_html( wc_string_to_bool( $data['settings']['enable_local_pickup'] ) ); ?>
			</td>
		</tr>
		<?php if ( ! is_null( $data['settings']['limit_order_renewals'] ) ) : ?>
		<tr>
			<td data-export-label="Limit delivery of order renewals"><?php esc_html_e( 'Limit subscription orders to the billing interval', 'woocommerce-order-delivery' ); ?>:</td>
			<td class="help"></td>
			<td>
				<?php WC_OD_Admin_System_Status::output_bool_html( wc_string_to_bool( $data['settings']['limit_order_renewals'] ) ); ?>
			</td>
		</tr>
		<?php endif; ?>
		<tr>
			<td data-export-label="Overrides"><?php esc_html_e( 'Overrides', 'woocommerce' ); // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch ?>:</td>
			<td class="help"></td>
			<td>
				<?php
				if ( empty( $data['overrides'] ) ) :
					esc_html_e( '-', 'woocommerce-order-delivery' );
				else :
					$overrides_html = array();

					foreach ( $data['overrides'] as $override ) :
						if ( $override['core_version'] && ( empty( $override['version'] ) || version_compare( $override['version'], $override['core_version'], '<' ) ) ) :
							$has_outdated    = true;
							$current_version = ( $override['version'] ? $override['version'] : '-' );

							$overrides_html[] = sprintf(
								/* translators: %1$s: Template name, %2$s: Template version, %3$s: Core version. */
								esc_html__( '%1$s version %2$s is out of date. The core version is %3$s', 'woocommerce' ), // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch
								'<code>' . esc_html( $override['file'] ) . '</code>',
								'<strong style="color: #f00;">' . esc_html( $current_version ) . '</strong>',
								esc_html( $override['core_version'] )
							);
						else :
							$overrides_html[] = esc_html( $override['file'] );
						endif;
					endforeach;

					echo join( ',<br/>', $overrides_html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				endif;
				?>
			</td>
		</tr>

		<?php if ( $has_outdated ) : ?>
		<tr>
			<td data-export-label="Outdated Templates"><?php esc_html_e( 'Outdated templates', 'woocommerce' ); // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch ?>:</td>
			<td class="help"></td>
			<td>
				<mark class="error"><span class="dashicons dashicons-warning"></span></mark> <a href="https://docs.woocommerce.com/document/fix-outdated-templates-woocommerce/" target="_blank"><?php esc_html_e( 'Learn how to update', 'woocommerce' ); // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch ?></a>
			</td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>
<?php
