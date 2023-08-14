<?php
/**
 * View template for defining packages from AJAX response.
 *
 * @package WC_Stamps_Integration/View
 */

?>

<p><?php esc_html_e( 'Enter the weight and dimensions for the package being shipped. Dimensions are optional, but may be required for more accurate rating.', 'woocommerce-shipping-stamps' ); ?></p>

<table class="form-table">
	<tr>
		<th><label><?php esc_html_e( 'Package type', 'woocommerce-shipping-stamps' ); ?></label></th>
		<td>
			<select name="stamps_package_type">
				<option value=""><?php esc_html_e( 'Any (return all options)', 'woocommerce-shipping-stamps' ); ?></option>
				<?php
				foreach ( $this->package_types as $package_type => $package_info ) {
					echo '<option value="' . esc_attr( $package_type ) . '">' . esc_html( $package_info['name'] ) . '</option>';
				}
				?>
			</select> <span class="description"></span>
		</td>
	</tr>
	<tr>
		<th><label><?php esc_html_e( 'Content Type', 'woocommerce-shipping-stamps' ); ?></label></th>
		<td>
			<select name="stamps_content_type">
				<option value="Commercial Sample"><?php esc_html_e( 'Commercial Sample', 'woocommerce-shipping-stamps' ); ?></option>
				<option value="Dangerous Goods"><?php esc_html_e( 'Dangerous Goods', 'woocommerce-shipping-stamps' ); ?></option>
				<option value="Document"><?php esc_html_e( 'Document', 'woocommerce-shipping-stamps' ); ?></option>
				<option value="Gift"><?php esc_html_e( 'Gift', 'woocommerce-shipping-stamps' ); ?></option>
				<option value="Humanitarian Donation"><?php esc_html_e( 'Humanitarian Donation', 'woocommerce-shipping-stamps' ); ?></option>
				<option value="Merchandise"><?php esc_html_e( 'Merchandise', 'woocommerce-shipping-stamps' ); ?></option>
				<option value="Returned Goods"><?php esc_html_e( 'Returned Goods', 'woocommerce-shipping-stamps' ); ?></option>
				<option value="Other"><?php esc_html_e( 'Other', 'woocommerce-shipping-stamps' ); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<th><label><?php esc_html_e( 'Ship date', 'woocommerce-shipping-stamps' ); ?></label></th>
		<td><input type="text" value="<?php echo esc_attr( $ship_date ); ?>" name="stamps_package_date" class="stamps-date-picker" /></td>
	</tr>
	<tr>
		<th><label><?php echo esc_html__( 'Weight', 'woocommerce-shipping-stamps' ) . ' (' . esc_html( get_option( 'woocommerce_weight_unit' ) ) . ')'; ?></label></th>
		<td><input type="text" value="<?php echo esc_attr( $total_weight ); ?>" name="stamps_package_weight" /></td>
	</tr>
	<tr>
		<th><label><?php esc_html_e( 'Value', 'woocommerce-shipping-stamps' ); ?></label></th>
		<td><input type="text" value="<?php echo esc_attr( $total_cost ); ?>" name="stamps_package_value" /></td>
	</tr>
	<tr>
		<th><label><?php echo esc_html__( 'Length', 'woocommerce-shipping-stamps' ) . ' (' . esc_html( get_option( 'woocommerce_dimension_unit' ) ) . ')'; ?></label></th>
		<td>
			<input type="text" name="stamps_package_length" />
		</td>
	</tr>
	<tr>
		<th><label><?php echo esc_html__( 'Width', 'woocommerce-shipping-stamps' ) . ' (' . esc_html( get_option( 'woocommerce_dimension_unit' ) ) . ')'; ?></label></th>
		<td>
			<input type="text" name="stamps_package_width" />
		</td>
	</tr>
	<tr>
		<th><label><?php echo esc_html__( 'Height', 'woocommerce-shipping-stamps' ) . ' (' . esc_html( get_option( 'woocommerce_dimension_unit' ) ) . ')'; ?></label></th>
		<td>
			<input type="text" name="stamps_package_height" />
		</td>
	</tr>
</table>

<p><button type="submit" class="button button-primary stamps-action" data-stamps_action="get_rates"><?php esc_html_e( 'Get rates', 'woocommerce-shipping-stamps' ); ?></button></p>
