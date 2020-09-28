<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}
?>
<div class="msrp_product_addon_column">
	<input type="number" name="product_addon_option_msrp[<?php esc_attr_e( $loop ); ?>][]"
		   value="<?php esc_attr_e( $msrp ) ?>" placeholder="N/A" min="0" step="any"/>
</div>
