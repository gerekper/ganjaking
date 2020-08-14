<?php
$multi_shipping_enabled = WC()->session->get( 'ywcmas_multi_shipping_enabled' );
?>
<div class="ywcmas_manage_addresses_cb_container" style="margin: 40px 0;">
    <label>
        <input id="ywcmas_manage_addresses_cb" type="checkbox" <?php checked( $multi_shipping_enabled ); ?> name="ywcmas_manage_addresses_cb">
        <span><?php esc_html_e( 'Do you want to ship to multiple addresses?', 'yith-multiple-shipping-addresses-for-woocommerce' ); ?></span>
    </label>
</div>