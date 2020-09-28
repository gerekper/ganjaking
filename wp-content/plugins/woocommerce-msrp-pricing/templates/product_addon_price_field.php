<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}
?>
<div class="msrp-product-addon-option-msrp-label">
	<label><?php _e( 'MSRP: ', 'woocommerce-msrp' ); ?></label>
</div>
<div class="msrp-product-addon-option-msrp-input">
	<input type="number" name="product_addon_msrp[<?php esc_attr_e( $loop ); ?>]"
		   value="<?php esc_attr_e( $msrp ) ?>" placeholder="N/A" min="0" step="any"/>
</div>

