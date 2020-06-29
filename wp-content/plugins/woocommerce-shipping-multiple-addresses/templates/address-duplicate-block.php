<div class="address-block address-duplicate-block">
    <?php

    $addr = array(
        'first_name'    => $address['shipping_first_name'],
        'last_name'     => $address['shipping_last_name'],
        'company'       => $address['shipping_company'],
        'address_1'     => $address['shipping_address_1'],
        'address_2'     => $address['shipping_address_2'],
        'city'          => $address['shipping_city'],
        'state'         => $address['shipping_state'],
        'postcode'      => $address['shipping_postcode'],
        'country'       => $address['shipping_country']
    );
    $formatted_address = wcms_get_formatted_address( $addr );

    if (!$formatted_address)
        _e( 'You have not set up a shipping address yet.', 'wc_shipping_multiple_address' );
    else
        echo '<address>'.$formatted_address.'</address>';

    ?>
    <div class="buttons">
        <button type="button" class="button selectable" data-index="<?php echo $idx; ?>"><?php _e('Ship to this address', 'wc_shipping_multiple_address'); ?></button>
        <input type="checkbox" name="address_ids[]" value="<?php echo $idx; ?>" id="address_input_<?php echo $idx; ?>" style="display: none;" />
    </div>

</div>
