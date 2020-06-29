<form method="post" action="admin-post.php" enctype="multipart/form-data">
    <?php
    $order_id = WC_Warranty_Compatibility::get_order_prop( $order, 'id' );

    foreach ( $_GET['idx'] as $idx ):
        $item = (isset($items[ $idx ])) ? $items[ $idx ] : false;
        $variation = warranty_get_variation_string( $order, $item );

        if ( !$item ) {
            continue;
        }

        if ( $has_warranty && $item['qty'] > 1 ) {
            $max = warranty_get_quantity_remaining( $order_id, $item['product_id'], $idx );
        } else {
            $max = $item['qty'] - warranty_count_quantity_used( $order_id, $item['product_id'], $idx );
        }

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
    endforeach;

    $refunds_allowed = warranty_refund_requests_enabled();
    $coupons_allowed = warranty_coupon_requests_enabled();

    if ( $refunds_allowed || $coupons_allowed ):
        ?>
        <div class="wfb-field-div wfb-field-div-select">
            <label><?php _e('Request type', 'wc_warranty'); ?></label>

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

    WooCommerce_Warranty::render_warranty_form();

    ?>
    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />
    <input type="hidden" name="action" value="warranty_create" />
    <input type="submit" name="submit" value="<?php _e('Submit', 'wc_warranty'); ?>" class="button">
</form>
