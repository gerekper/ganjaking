<?php
/**
 * Admin View: Setting Status Report.
 *
 * @package WC_Account_Funds/Admin/Views
 * @since   2.5.4
 */

/**
 * Template vars.
 *
 * @var array $data The template data.
 */
?>
<table class="wc_status_table widefat" cellspacing="0">
	<thead>
		<tr>
			<th colspan="5" data-export-label="Account Funds">
				<h2><?php esc_html_e( 'Account Funds', 'woocommerce-account-funds' ); ?><?php echo wc_help_tip( esc_html__( 'This section shows information about Account Funds.', 'woocommerce-account-funds' ) ); ?></h2>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td data-export-label="Funds name"><?php esc_html_e( 'Funds name', 'woocommerce-account-funds' ); ?>:</td>
			<td class="help"></td>
			<td><?php echo esc_html( $data['name'] ); ?></td>
		</tr>
		<tr>
			<td data-export-label="Partial payment"><?php esc_html_e( 'Partial payment', 'woocommerce-account-funds' ); ?>:</td>
			<td class="help"></td>
			<td><?php WC_Account_Funds_Admin_System_Status::output_bool_html( wc_string_to_bool( $data['partial_funds_payment'] ) ); ?></td>
		</tr>
		<tr>
			<td data-export-label="Enabled My Account Top-up"><?php esc_html_e( 'Enabled My Account Top-up', 'woocommerce-account-funds' ); ?>:</td>
			<td class="help"></td>
			<td><?php WC_Account_Funds_Admin_System_Status::output_bool_html( wc_string_to_bool( $data['enable_topup'] ) ); ?></td>
		</tr>
		<tr>
			<td data-export-label="Minimum Top-up"><?php esc_html_e( 'Minimum Top-up', 'woocommerce-account-funds' ); ?>:</td>
			<td class="help"></td>
			<td><?php echo esc_html( $data['min_topup'] ); ?></td>
		</tr>
		<tr>
			<td data-export-label="Maximum Top-up"><?php esc_html_e( 'Maximum Top-up', 'woocommerce-account-funds' ); ?>:</td>
			<td class="help"></td>
			<td><?php echo esc_html( $data['max_topup'] ); ?></td>
		</tr>
		<tr>
			<td data-export-label="Give discount"><?php esc_html_e( 'Give discount', 'woocommerce-account-funds' ); ?>:</td>
			<td class="help"></td>
			<td><?php WC_Account_Funds_Admin_System_Status::output_bool_html( wc_string_to_bool( $data['give_discount'] ) ); ?></td>
		</tr>
		<tr>
			<td data-export-label="Discount type"><?php esc_html_e( 'Discount type', 'woocommerce-account-funds' ); ?>:</td>
			<td class="help"></td>
			<td><?php echo esc_html( $data['discount_type'] ); ?></td>
		</tr>
		<tr>
			<td data-export-label="Discount amount"><?php esc_html_e( 'Discount amount', 'woocommerce-account-funds' ); ?>:</td>
			<td class="help"></td>
			<td><?php echo esc_html( $data['discount_amount'] ); ?></td>
		</tr>
	</tbody>
</table>
<?php
