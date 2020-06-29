<?php
/**
 * View template to display custom information.
 *
 * @package WC_Stamps_Integration/View
 */

ob_start();
include( 'html-customs-item.php' );
$line_html = ob_get_clean();
?>
<h4><?php _e( 'Customs', 'woocommerce-shipping-stamps' ); ?></h4>
<table class="form-table">
	<tr>
		<th><label><?php _e( 'Content type', 'woocommerce-shipping-stamps' ); ?></label></th>
		<td>
			<select name="stamps_customs_content_type">
				<option value="Merchandise"><?php _e( 'Merchandise', 'woocommerce-shipping-stamps' ); ?></option>
				<option value="Commercial Sample"><?php _e( 'Commercial Sample', 'woocommerce-shipping-stamps' ); ?></option>
				<option value="Gift"><?php _e( 'Gift', 'woocommerce-shipping-stamps' ); ?></option>
				<option value="Document"><?php _e( 'Document', 'woocommerce-shipping-stamps' ); ?></option>
				<option value="Returned Goods"><?php _e( 'Returned Goods', 'woocommerce-shipping-stamps' ); ?></option>
				<option value="Humanitarian Donation"><?php _e( 'Humanitarian Donation', 'woocommerce-shipping-stamps' ); ?></option>
				<option value="Dangerous Goods"><?php _e( 'Dangerous Goods', 'woocommerce-shipping-stamps' ); ?></option>
				<option value="Other"><?php _e( 'Other', 'woocommerce-shipping-stamps' ); ?></option>
			</select>

			<label class="other_describe">
				<input type="text" name="stamps_customs_other" maxlength="20" placeholder="<?php _e( 'Other description', 'woocommerce-shipping-stamps' ); ?>" >
			</label>
		</td>
	</tr>
	<tr>
		<th><label><?php _e( 'Comments', 'woocommerce-shipping-stamps' ); ?></label></th>
		<td>
			<input type="text" name="stamps_customs_comments" maxlength="76" placeholder="<?php _e( 'optional', 'woocommerce-shipping-stamps' ); ?>" />
		</td>
	</tr>
	<tr>
		<th><label><?php _e( 'License Number', 'woocommerce-shipping-stamps' ); ?></label></th>
		<td>
			<input type="text" name="stamps_customs_licence" maxlength="6" placeholder="<?php _e( 'optional', 'woocommerce-shipping-stamps' ); ?>" />
		</td>
	</tr>
	<tr>
		<th><label><?php _e( 'Certificate Number', 'woocommerce-shipping-stamps' ); ?></label></th>
		<td>
			<input type="text" name="stamps_customs_certificate" maxlength="8" placeholder="<?php _e( 'optional', 'woocommerce-shipping-stamps' ); ?>" />
		</td>
	</tr>
</table>

<h4><?php _e( 'Customs line items', 'woocommerce-shipping-stamps' ); ?> <a class="wc-stamps-customs-add-line" href="#" data-line_html="<?php echo esc_attr( $line_html ); ?>">(<?php _e( 'Add line', 'woocommerce-shipping-stamps' ); ?>)</a></h4>
<p class="wc-stamps-customs-line-intro"><?php _e( 'Add line items for the customs form here.', 'woocommerce-shipping-stamps' ); ?></p>
<div class="wc-stamps-customs-items">
	<?php
	foreach ( $order->get_items() as $item_id => $item ) {
		$product = $order->get_product_from_item( $item );

		if ( ! $product->needs_shipping() ) {
			continue;
		}

		$description = $product->get_title();
		$qty         = $item['qty'];
		$value       = $product->get_price() * $item['qty'];
		$weight      = wc_get_weight( $product->get_weight() * $item['qty'], 'lbs' );

		include( 'html-customs-item.php' );
	}
	?>
</div>
<p>
<?php
$stamps_rate = wp_json_encode( $stamps_rate );
$stamps_rate = function_exists( 'wc_esc_json' ) ? wc_esc_json( $stamps_rate ) : _wp_specialchars( $stamps_rate, ENT_QUOTES, 'UTF-8', true );
?>
	<input type="hidden" name="parsed_rate" value="<?php echo $stamps_rate; ?>" />
	<button type="submit" class="button button-primary stamps-action" data-stamps_action="request_label"><?php esc_html_e( 'Request label', 'woocommerce-shipping-stamps' ); ?></button>
	<button type="submit" class="button stamps-action" data-stamps_action="define_package"><?php esc_html_e( 'Cancel', 'woocommerce-shipping-stamps' ); ?></button>
</p>
