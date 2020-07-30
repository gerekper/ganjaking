<select name="_woocommerce_gpf_data[{key}]" class="woocommerce-gpf-store-default">
	<option value="">{emptytext}</option>
	<option value="buy" {buy-selected}><?php _e( 'Buy (the entire transaction occurs online)', 'woocommerce_gpf' ); ?></option>
	<option value="reserve" {reserve-selected}><?php _e( 'Reserve (reserved online, but transaction occurs in-store)', 'woocommerce_gpf' ); ?></option>
	<option value="ship to store" {ship to store-selected}><?php _e( 'Ship to store (purchased online, shipped to local store for customer pick up)', 'woocommerce_gpf' ); ?></option>
	<option value="not supported" {not supported-selected}><?php _e( 'Not supported (not available for store pickup', 'woocommerce_gpf' ); ?></option>
</select>
