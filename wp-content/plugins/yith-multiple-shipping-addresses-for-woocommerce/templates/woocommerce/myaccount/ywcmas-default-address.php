<div id="ywcmas_default_address_viewer">
	<?php $default_address = get_user_meta( get_current_user_id(), 'yith_wcmas_default_address', true ); ?>
	<?php $addresses = yith_wcmas_get_user_custom_addresses( get_current_user_id() ); ?>
    <span><strong><?php esc_html_e( 'Default address:', 'yith-multiple-shipping-addresses-for-woocommerce' ); ?></strong></span>
    <span id="ywcmas_default_address_field">
        <?php
        if ( $default_address ) {
	        if ( YITH_WCMAS_BILLING_ADDRESS_ID == $default_address ) {
		        esc_html_e( 'Billing address', 'yith-multiple-shipping-addresses-for-woocommerce' );
	        } else if ( YITH_WCMAS_DEFAULT_SHIPPING_ADDRESS_ID == $default_address ) {
		        esc_html_e( 'Default shipping address', 'yith-multiple-shipping-addresses-for-woocommerce' );
	        } else {
		        echo $default_address;
	        }
        } else if ( $addresses ) {
	        $address_name = key( yith_wcmas_get_user_default_and_custom_addresses( get_current_user_id() ) );
	        if ( YITH_WCMAS_BILLING_ADDRESS_ID == $address_name ) {
		        esc_html_e( 'Billing Address', 'yith-multiple-shipping-addresses-for-woocommerce' );
	        } else if ( YITH_WCMAS_DEFAULT_SHIPPING_ADDRESS_ID == $address_name ) {
		        esc_html_e( 'Default Shipping Address', 'yith-multiple-shipping-addresses-for-woocommerce' );
	        } else {
		        echo $address_name;
	        }
        } else {
	        esc_html_e( 'There is no default address set', 'yith-multiple-shipping-addresses-for-woocommerce' );
        }
        ?>
    </span>
    <a id="ywcmas_default_address_change_button"><?php esc_html_e( 'Change', 'yith-multiple-shipping-addresses-for-woocommerce' ); ?></a>
</div>
<div id="ywcmas_default_address_selector_container" style="display: none;">
    <input id="ywcmas_user_id" type="hidden" value="<?php echo get_current_user_id(); ?>">
    <select id="ywcmas_default_address_selector"><?php yith_wcmas_print_addresses_select_options( $default_address, get_current_user_id() ) ?></select>
    <button id="ywcmas_default_address_update_button"><?php esc_html_e( 'Update', 'yith-multiple-shipping-addresses-for-woocommerce' ); ?></button>
</div>