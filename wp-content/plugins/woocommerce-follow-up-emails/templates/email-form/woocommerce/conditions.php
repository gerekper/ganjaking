<?php
	$value                     = ! empty( $conditions[ $idx ]['value'] ) ? $conditions[ $idx ]['value'] : '';
	$products                  = ! empty( $conditions[ $idx ]['products'] ) ? $conditions[ $idx ]['products'] : '';
	$condition_categories      = ! empty( $conditions[ $idx ]['categories'] ) ? $conditions[ $idx ]['categories'] : array();
	$condition_payment_gateway = ! empty( $conditions[ $idx ]['payment_method'] ) ? $conditions[ $idx ]['payment_method'] : '';
	$condition_shipping_method = ! empty( $conditions[ $idx ]['shipping_method'] ) ? $conditions[ $idx ]['shipping_method'] : '';
?>
<span class="value" style="display: none;">
	<span class="value-currency" style="display: none;"><?php echo esc_html( get_woocommerce_currency_symbol() ); ?></span>
	<input type="text" name="conditions[<?php echo esc_attr( $idx ); ?>][value]" class="condition-value" disabled value="<?php echo esc_attr( $value ); ?>" >
</span>
<div class="value-products" style="display: none; margin: 5px 0 0 45px;">
	<select
		class="ajax-select2-init"
		name="conditions[<?php echo esc_attr( $idx ); ?>][products][]"
		id="conditions_<?php echo esc_attr( $idx ); ?>_products"
		multiple
		data-placeholder="<?php esc_attr_e( 'Search for products&hellip;', 'follow_up_emails' ); ?>"
	>
	<?php
		if ( ! is_array( $products ) ) {
			$products = explode( ',', $products );
		}
		$product_ids = array_filter( array_map( 'absint', $products ) );

		foreach ( $product_ids as $product_id ) {
			$product      = WC_FUE_Compatibility::wc_get_product( $product_id );
			$product_name = $product ? htmlspecialchars( wp_kses_post( $product->get_formatted_name() ) ) : '';
	?>
		<option value="<?php echo esc_attr( $product_id ); ?>" selected><?php echo esc_html( $product_name ); ?></option>
	<?php
		}
	?>
	</select>
</div>
<div class="value-categories" style="display: none; margin: 5px 0 0 45px;">
	<select id="conditions_<?php echo esc_attr( $idx ); ?>_categories" name="conditions[<?php echo esc_attr( $idx ); ?>][categories][]" class="select2-init" multiple="multiple" data-placeholder="No categories" style="width:500px;">
		<?php
		foreach ( $categories as $category ) :
			$selected = ( ! in_array( $category->term_id, $condition_categories ) ) ? '' : 'selected';
		?>
			<option value="<?php echo esc_attr( $category->term_id ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo esc_html( $category->name ); ?></option>
		<?php endforeach; ?>
	</select>
</div>
<div class="value-payment-method" style="display: none; margin: 5px 0 0 45px;">
	<?php $payment_gateways = WC_Payment_Gateways::instance()->payment_gateways(); ?>
	<select id="conditions_<?php echo esc_attr( $idx ); ?>_payment_method" name="conditions[<?php echo esc_attr( $idx ); ?>][payment_method]" class="select2-init">
		<?php foreach ( $payment_gateways as $gateway ): ?>
			<option value="<?php echo esc_attr( $gateway->id ); ?>" <?php selected( $condition_payment_gateway, $gateway->id ); ?>><?php echo esc_attr( $gateway->title ); ?></option>
		<?php endforeach; ?>
		<option value="other"><?php esc_html_e('Other', 'follow_up_emails'); ?></option>
	</select>
</div>
<div class="value-shipping-method" style="display: none; margin: 5px 0 0 45px;">
	<?php $shipping_methods = WC_Shipping::instance()->get_shipping_methods(); ?>
	<select id="conditions_<?php echo esc_attr( $idx ); ?>_shipping_method" name="conditions[<?php echo esc_attr( $idx ); ?>][shipping_method]" class="select2-init">
		<?php foreach ( $shipping_methods as $method ): ?>
			<option value="<?php echo esc_attr( $method->id ); ?>" <?php selected( $condition_shipping_method, $method->id ); ?>><?php echo esc_attr( $method->method_title ); ?></option>
		<?php endforeach; ?>
	</select>
</div>
