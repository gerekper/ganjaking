<?php
if ( $request['request_type'] == 'refund' ):
?>
    <div id="warranty-refund-modal-<?php echo $request['ID']; ?>" style="display:none;">
        <table class="form-table">
            <tr>
                <th><span class="label"><?php _e('Amount refunded:', 'wc_warranty'); ?></span></th>
                <td><span class="value"><?php echo wc_price( $refunded ); ?></span></td>
            </tr>
            <tr>
                <th><span class="label"><?php _e('Item cost:', 'wc_warranty'); ?></span></th>
                <td><span class="value"><?php echo wc_price( $item_amount ); ?></span></td>
            </tr>
            <tr>
                <th><span class="label"><?php _e('Refund amount:', 'wc_warranty'); ?></span></th>
                <td>
                    <?php echo get_woocommerce_currency_symbol(); ?>
                    <input type="text" class="input-short amount" value="<?php echo esc_attr( $available ); ?>" size="5" />
                </td>
            </tr>
        </table>

        <p class="submit alignright">
            <input
                type="button"
                class="warranty-process-refund button-primary"
                value="<?php _e('Process Refund', 'wc_warranty'); ?>"
                data-id="<?php echo $request['ID']; ?>"
                data-security="<?php echo $update_nonce; ?>"
                />
        </p>
    </div>
    <?php elseif ( $request['request_type'] == 'coupon' ): ?>
    <div id="warranty-coupon-modal-<?php echo $request['ID']; ?>" style="display:none;">
        <table class="form-table">
            <tr>
                <th><span class="label"><?php _e('Amount refunded:', 'wc_warranty'); ?></span></th>
                <td><span class="value"><?php echo wc_price( $refunded ); ?></span></td>
            </tr>
            <tr>
                <th><span class="label"><?php _e('Item cost:', 'wc_warranty'); ?></span></th>
                <td><span class="value"><?php echo wc_price( $item_amount ); ?></span></td>
            </tr>
            <tr>
                <th><span class="label"><?php _e('Coupon amount:', 'wc_warranty'); ?></span></th>
                <td>
                    <?php echo get_woocommerce_currency_symbol(); ?>
                    <input type="text" class="input-short amount" value="<?php echo esc_attr( $available ); ?>" size="5" />
                </td>
            </tr>
        </table>

        <p class="submit alignright">
            <input
                type="button"
                class="warranty-process-coupon button-primary"
                value="<?php _e('Send Coupon', 'wc_warranty'); ?>"
                data-id="<?php echo $request['ID']; ?>"
                data-security="<?php echo wp_create_nonce( 'warranty_send_coupon' ); ?>"
                />
        </p>
    </div>
<?php endif;