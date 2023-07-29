<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}
?>
<optgroup label="<?php esc_html_e( 'MSRP Prices', 'woocommerce-msrp' ); ?>">
    <option value="msrp_set_prices"><?php esc_html_e( 'Set prices', 'woocommerce-msrp' ); ?></option>
    <option value="msrp_clear_prices"><?php esc_html_e( 'Clear MSRP prices', 'woocommerce-msrp' ); ?></option>
</optgroup>
