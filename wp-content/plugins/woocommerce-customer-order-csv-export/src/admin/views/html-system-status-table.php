<?php
/**
 * WooCommerce Customer/Order/Coupon Export
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order/Coupon Export to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order/Coupon Export for your
 * needs please refer to http://docs.woocommerce.com/document/ordercustomer-csv-exporter/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2023, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * System Status table
 *
 * @since 3.11.0
 * @version 5.0.0
 */
?>

<table class="wc_status_table widefat" cellspacing="0" id="wc-customer-order-export-status">
	<thead>
		<tr>
			<?php $plugin_name = wc_customer_order_csv_export()->get_plugin_name(); ?>
			<th colspan="3" data-export-label="<?php echo esc_html( $plugin_name ); ?>"><?php echo esc_html( $plugin_name ); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td data-export-label="<?php esc_html_e( 'Automated exports', 'woocommerce-customer-order-csv-export' ); ?>"><?php esc_html_e( 'Automated exports', 'woocommerce-customer-order-csv-export' ); ?>:</td>
			<td class="help"><?php echo wc_help_tip( __( 'The number of configured automated exports.', 'woocommerce-customer-order-csv-export' ) ); ?></td>
			<td><?php echo esc_html( count( $automations ) ); ?></td>
		</tr>
		<tr>
			<td data-export-label="<?php esc_html_e( 'Custom formats', 'woocommerce-customer-order-csv-export' ); ?>"><?php esc_html_e( 'Custom formats', 'woocommerce-customer-order-csv-export' ); ?>:</td>
			<td class="help"><?php echo wc_help_tip( __( 'The number of configured custom formats.', 'woocommerce-customer-order-csv-export' ) ); ?></td>
			<td><?php echo esc_html( $custom_formats ); ?></td>
		</tr>

		<tr>
			<td data-export-label="Next export"><?php _e( 'Next export', 'woocommerce-customer-order-csv-export' ); ?>:</td>
			<td class="help"><?php echo wc_help_tip( __( 'The next scheduled automated export.', 'woocommerce-customer-order-csv-export' ) ); ?></td>
			<td>
				<?php
				if ( $scheduled_timestamp = as_next_scheduled_action( 'wc_customer_order_export_do_scheduled_automation' ) ) {
					/* translators: Placeholders: %s - date & time */
					$scheduled_desc = sprintf( __( 'Scheduled on %s', 'woocommerce-customer-order-csv-export' ), get_date_from_gmt( date( 'Y-m-d H:i:s', $scheduled_timestamp ), wc_date_format() . ' ' . wc_time_format() ) );
				} else {
					$scheduled_desc = '<mark class="error">' . esc_html__( 'Not scheduled', 'woocommerce-customer-order-csv-export' ) . '</mark>';
				}
				echo $scheduled_desc;
				?>
			</td>
		</tr>
	</tbody>
</table>
