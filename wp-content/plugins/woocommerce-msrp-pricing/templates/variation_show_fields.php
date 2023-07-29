<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}
?>
<p class="form-field form-row form-row-first">
    <label><?php esc_html_e( 'MSRP Price', 'woocommerce_msrp' ) . ' (' . get_woocommerce_currency_symbol() . ')'; ?></label><input
            type="text" size="5" name="variable_msrp[<?php echo $loop; ?>]"
            value="<?php echo esc_attr( wc_format_localized_price( $msrp ) ); ?>"/>
</p>
