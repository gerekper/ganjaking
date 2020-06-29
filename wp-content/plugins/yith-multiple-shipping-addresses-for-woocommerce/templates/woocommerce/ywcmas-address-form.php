<?php
?>
<div class="woocommerce">
    <form method="post">

        <?php
        $title = '';

        if ( $current_address_id ) {
            $address_name = '';

            switch ( $current_address_id ) {
                case YITH_WCMAS_BILLING_ADDRESS_ID :
                    $address_name = esc_html__( 'Billing address', 'yith-multiple-shipping-addresses-for-woocommerce' );
                    break;
                case YITH_WCMAS_DEFAULT_SHIPPING_ADDRESS_ID :
                    $address_name = esc_html__( 'Default shipping address', 'yith-multiple-shipping-addresses-for-woocommerce' );
                    break;
                default :
                    $address_name = $current_address_id;
            }

            $title = sprintf( _x( 'Edit "%s"', 'Edit (shipping address identifier)', 'yith-multiple-shipping-addresses-for-woocommerce' ), $address_name );
        } else {
	        $title = esc_html__( 'New shipping address', 'yith-multiple-shipping-addresses-for-woocommerce' );
        }
        ?>
        <h3><?php echo $title; ?></h3>

        <div class="woocommerce-address-fields">
		    <?php do_action( "woocommerce_before_edit_address_form_shipping" ); ?>
            <div class="woocommerce-address-fields__field-wrapper">
	            <?php if ( YITH_WCMAS_BILLING_ADDRESS_ID != $current_address_id && YITH_WCMAS_DEFAULT_SHIPPING_ADDRESS_ID != $current_address_id ) : ?>
                    <p class="form-row form-row-wide validate-required" id="address_id_field" data-priority="">
                        <label for="address_id" class=""><?php esc_html_e( 'Shipping Identifier', 'yith-multiple-shipping-addresses-for-woocommerce' ); ?> <abbr class="required" title="required">*</abbr></label>
                        <input type="text" class="input-text" id="address_id" autofocus="autofocus" value="<?php echo $current_address_id ? $current_address_id : '' ?>" >
                    </p>
	            <?php else : ?>
                    <input type="hidden" id="address_id" value="<?php echo $current_address_id ? $current_address_id : '' ?>" >
	            <?php endif; ?>

				<?php foreach ( $address as $key => $field ) : ?>
					<?php
					if ( isset( $field['country_field'], $address[ $field['country_field'] ] ) ) {
						$field['country'] = wc_get_post_data_by_key( $field['country_field'], $address[ $field['country_field'] ]['value'] );
					}
//					woocommerce_form_field( $key, $field, ! empty( $current_address[ $key ] ) ? wc_clean( $current_address[ $key ] ) : $field['value'] );
					woocommerce_form_field( $key, $field, wc_get_post_data_by_key( $key, $field['value'] ) );
					?>
				<?php endforeach; ?>
			</div>

			<?php do_action( "woocommerce_after_edit_address_form_shipping" ); ?>

			<p>
				<input type="submit" class="button" id="ywcmas_save_address" value="<?php esc_attr_e( 'Save address', 'yith-multiple-shipping-addresses-for-woocommerce' ); ?>" />
				<?php wp_nonce_field( 'ywcmas_form_address' ); ?>
				<?php if ( $current_address ) : ?>
					<input type="hidden" id="ywcmas_current_address_id" value="<?php echo $current_address_id; ?>" />
				<?php endif; ?>
			</p>
		</div>

	</form>
</div>