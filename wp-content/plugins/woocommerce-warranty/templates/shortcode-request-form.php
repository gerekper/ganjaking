<form name="warranty_form" id="warranty_form" method="POST" action="" enctype="multipart/form-data" >

    <?php
    if ( isset( $_REQUEST['error'] ) ): ?>
    <ul class="woocommerce_error">
        <li><?php echo $_REQUEST['error']; ?></li>
    </ul>
    <?php
    endif;

    $errors = array();
    if ( !empty( $_REQUEST['errors'] ) ) {
        $errors = json_decode( stripslashes( $_REQUEST['errors'] ), true );
    }

    if ( ! empty( $errors ) ) {
        echo '<div class="woocommerce-error">';
        _e( 'The following errors were found while processing your request:', 'wc_warranty' );
        echo '<ul>';

        foreach ( $errors as $error ) {
            echo '<li>' . esc_html( $error ) . '</li>';
        }

        echo '</ul>';
        echo '</div>';
    }

    foreach ( $idxs as $idx ) {
        $item      = ( isset( $items[ $idx ] ) ) ? $items[ $idx ] : false;
        $variation = warranty_get_variation_string( $order, $item );

        if ( $item && $item['qty'] >= 1 ):
            $max = warranty_get_quantity_remaining( $order_id, $item['product_id'], $idx );
            ?>
            <div class="wfb-field-div wfb-field-div-select">
                <label>
                    <?php
                    echo $item['name'];

                    if ( $variation ) {
                        echo '<div class="item-variations">' . $variation . '</div>';
                    }
                    ?>
                </label>
                <select name="warranty_qty[<?php echo $idx; ?>]" class="wfb-field">
                    <?php for ( $x = 1; $x <= $max; $x++ ): ?>
                        <option value="<?php echo $x; ?>"><?php echo $x; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <?php
        endif;

        echo '<input type="hidden" name="idx[]" value="'. $idx .'" />';
    }

    echo '<hr/>';

    $refunds_allowed = warranty_refund_requests_enabled();
    $coupons_allowed = warranty_coupon_requests_enabled();

    if ( $refunds_allowed || $coupons_allowed ):
        ?>
        <div class="wfb-field-div wfb-field-div-select">
            <label><?php _e('I want to request for a', 'wc_warranty'); ?></label>

            <select name="warranty_request_type" class="wfb-field">
                <option value="replacement"><?php _e('Replacement item', 'wc_warranty'); ?></option>
                <?php if ( $refunds_allowed ): ?>
                    <option value="refund"><?php _e('Refund', 'wc_warranty'); ?></option>
                <?php endif; ?>
                <?php if ( $coupons_allowed ): ?>
                    <option value="coupon"><?php _e('Refund as store credit', 'wc_warranty'); ?></option>
                <?php endif; ?>
            </select>
        </div>
    <?php
    else:
        echo '<input type="hidden" name="warranty_request_type" value="replacement" />';
    endif;

    $request_tracking_code = get_option( 'warranty_show_tracking_field', 'no' );

    if ( 'yes' == $request_tracking_code ) :
    ?>
	    <div class="wfb-field-div wfb-field-div-select">
		    <label><?php _e('Return Shipping Tracking', 'wc_warranty'); ?></label>

		    <select class="tracking_provider wfb-field" name="tracking_provider">
			    <?php
			    foreach ( WooCommerce_Warranty::get_providers() as $provider_group => $providers ) {
				    echo '<optgroup label="' . $provider_group . '">';
				    foreach ( $providers as $provider => $url ) {
					    $selected = isset( $request['tracking_provider'] ) && (sanitize_title($provider) == $request['tracking_provider']) ? 'selected' : '';
					    echo '<option value="' . sanitize_title( $provider ) . '" '. $selected .'>' . $provider . '</option>';
				    }
				    echo '</optgroup>';
			    }
			    ?>
		    </select>
		    <input type="text" class="tracking_code" name="tracking_code" value="" placeholder="<?php _e( 'Tracking code', 'wc_warranty' ); ?>" />
	    </div>


	<?php
    endif;

    WooCommerce_Warranty::render_warranty_form();
    ?>
    <p>
        <input type="hidden" name="req" value="new_warranty" />
        <input type="hidden" name="order" value="<?php echo WC_Warranty_Compatibility::get_order_prop( $order, 'id' ); ?>" />
        <input type="submit" name="submit" value="<?php _e('Submit', 'wc_warranty'); ?>" class="button">
    </p>

</form>
