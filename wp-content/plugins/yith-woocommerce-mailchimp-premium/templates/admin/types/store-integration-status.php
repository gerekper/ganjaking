<tr valign="top">
    <th scope="row" class="titledesc">
        <label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
    </th>
    <td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?> <?php echo sanitize_title( $value['type'] ) ?>">
        <div class="store-banner">
            <div class="store-details">
                <p class="store-info">
                    <span class="label"><b><?php _e( 'Status:', 'yith-woocommerce-mailchimp' )?></b></span>

                    <mark class="completed tips" data-tip="<?php _e( 'Correctly synchronized', 'yith-woocommerce-mailchimp' )?>"><?php _e( 'OK', 'yith-woocommerce-mailchimp' )?></mark>
                </p>

                <p class="store-info">
                    <span class="label"><b><?php _e( 'Name:', 'yith-woocommerce-mailchimp' )?></b></span>

					<?php echo ! empty( $store_info['name'] ) ? $store_info['name'] : __( '&lt; Not Found &gt;', 'yith-woocommerce-mailchimp' ) ?>
                </p>

                <p class="store-info">
                    <span class="label"><b><?php _e( 'List:', 'yith-woocommerce-mailchimp' ) ?></b></span>

                    <?php echo ! empty( $lists[ $store_info['list_id'] ] ) ?  $lists[ $store_info['list_id'] ] : $store_info['list_id'] ?>
                </p>

                <p class="store-info">
                    <span class="label"><b><?php _e( 'Currency:', 'yith-woocommerce-mailchimp' )?></b></span>

					<?php echo ! empty( $store_info['currency_code'] ) ? $store_info['currency_code'] : __( '&lt; Not Found &gt;', 'yith-woocommerce-mailchimp' ) ?>
                </p>

                <p class="store-info">
                    <span class="label"><b><?php _e( 'Address:', 'yith-woocommerce-mailchimp' )?></b></span>

					<span class="address">
                        <?php
                        if( isset( $store_info['address'] ) ) {
                            $store_address = $store_info['address'];
	                        printf( '%s<br/>%s<br/>%s, %s<br/>%s (%s)', $store_address['address1'], $store_address['address2'], $store_address['city'], $store_address['postal_code'], $store_address['province'], $store_address['country_code'] );
                        }
                        ?>
                    </span>
                </p>
            </div>
            <div class="store-deactivate">
                <button id="yith_wcmc_store_delete_store" class="button"><?php _e( 'Disconnect store', 'yith-woocommerce-mailchimp' ) ?></button>
            </div>
        </div>
    </td>
</tr>