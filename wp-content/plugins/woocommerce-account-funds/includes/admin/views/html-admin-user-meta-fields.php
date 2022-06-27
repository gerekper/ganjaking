<?php
/**
 * Admin View: User meta fields.
 *
 * @package WC_Account_Funds/Admin/Views
 * @since   2.7.0
 */

/**
 * Template vars.
 *
 * @var float $funds Account funds.
 */
?>
<h2><?php esc_html_e( 'Account Funds', 'woocommerce-account-funds' ); ?></h2>

<table class="form-table" id="fieldset-account-funds">
	<tr>
		<th>
			<label for="account_funds"><?php esc_html_e( 'Account Funds Amount', 'woocommerce-account-funds' ); ?></label>
		</th>
		<td>
			<input type="text" name="account_funds" id="account_funds" value="<?php echo esc_attr( wc_format_localized_price( $funds ) ); ?>" class="wc_input_price" style="position:relative;" />
			<p class="description"><?php esc_html_e( 'Funds this user can use to purchase items', 'woocommerce-account-funds' ); ?></p>
		</td>
	</tr>
</table>
