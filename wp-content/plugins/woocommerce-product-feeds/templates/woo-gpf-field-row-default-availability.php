<select name="_woocommerce_gpf_data[{key}]" class="woocommerce-gpf-store-default">
	<option value="">{emptytext}</option>
	<option value="in stock" {in stock-selected}><?php _e( 'In Stock', 'woocommerce_gpf' ); ?></option>
	<option value="available for order" {available for order-selected}><?php _e( 'Available for order', 'woocommerce_gpf' ); ?></option>
	<option value="preorder" {preorder-selected}><?php _e( 'Pre-Order', 'woocommerce_gpf' ); ?></option>
	<option value="out of stock" {out of stock-selected}><?php _e( 'Out of stock', 'woocommerce_gpf' ); ?></option>
</select>