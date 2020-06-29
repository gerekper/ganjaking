<form method="post" action="" id="address_form">
    <div id="address_wrapper">

        <div id="cart_items">
            <h2><?php _e('Cart Items', 'wc_shipping_multiple_address'); ?></h2>
            <ul class="cart-items">
                <?php

                foreach ($contents as $key => $value):
                    $_product   = $value['data'];
                    $pid        = $value['product_id'];

                    if (! $_product->needs_shipping() ) continue;

                    if ( isset($placed[$key]) ) {
                        if ( $placed[$key] >= $value['quantity'] ) {
                            continue;
                        } else {
                            $value['quantity'] -= $placed[$key];
                        }
                    }
                ?>
                <li data-product-id="<?php echo $value['product_id']; ?>" data-quantity="<?php echo $value['quantity']; ?>" class="cart-item cart-item-<?php echo $value['product_id']; ?>" id="<?php echo $key; ?>">
                    <span class="qty"><?php echo $value['quantity']; ?></span>

                    <h3 class="title">
                        <?php
                        echo get_the_title($value['product_id']);
                        ?>
                    </h3>

                    <?php echo WC_MS_Compatibility::get_item_data( $value );

                    $data_min = apply_filters( 'woocommerce_cart_item_data_min', '', $_product );
                    $data_max = ( $_product->backorders_allowed() ) ? '' : $_product->get_stock_quantity();
                    $data_max = apply_filters( 'woocommerce_cart_item_data_max', $data_max, $_product );
                    //printf( '<div class="quantity"><input name="cart[%s][qty]" data-min="%s" data-max="%s" value="%s" size="4" title="Qty" class="input-text qty text" maxlength="12" /></div>', $key, $data_min, $data_max, esc_attr( $value['quantity'] ) );
                    ?>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php
            $settings = get_option( 'woocommerce_multiple_shipping_settings', array() );

            if ( isset($settings['cart_duplication'])  && $settings['cart_duplication'] != 'no' ):
            ?>
            <a class="duplicate-cart-button user-duplicate-cart" href="#"><?php _e('Duplicate Cart', 'wc_shipping_multiple_address'); ?></a>
            <img class="help_tip" title="<?php _e('Duplicating your cart will allow you to ship the exact same cart contents to multiple locations. This will also increase the price of your purchase.', 'wc_shipping_multiple_address'); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16">
            <?php endif; ?>
        </div>

        <div id="cart_addresses">
            <?php
            if ($user->ID != 0):
                $addresses = $this->wcms->address_book->get_user_addresses( $user );

                if (! is_array($addresses) )
                    $addresses = array();

            $add_url = add_query_arg( 'address-form', 1 );
            ?>
            <h2>
                <?php _e('Saved Addresses', 'wc_shipping_multiple_address'); ?>
                <a class="h2-link" href="<?php echo $add_url; ?>"><?php _e('Add New', 'wc_shipping_multiple_address'); ?></a>
            </h2>

            <div id="addresses_container">
                <?php
                    $addresses_url = get_permalink( wc_get_page_id( 'account_addresses' ) );
                    if ($addresses) foreach ($addresses as $x => $addr) {

		                if ( empty( $addr ) )
		                	continue;

                        $address_fields = WC()->countries->get_address_fields( $addr['shipping_country'], 'shipping_' );

                        $address = array();
                        $formatted_address = false;

                        foreach ( $address_fields as $field_name => $field ) {
                            $addr_key = str_replace('shipping_', '', $field_name);
                            $address[$addr_key] = ( isset($addr[$field_name]) ) ? $addr[$field_name] : '';
                        }

                        if (! empty($address) ) {
                            $formatted_address = wcms_get_formatted_address( $address );
                            $json_address      = wp_json_encode( $address );
                            $json_address      = function_exists( 'wc_esc_json' ) ? wc_esc_json( $json_address ) : _wp_specialchars( $json_address, ENT_QUOTES, 'UTF-8', true );
                        }

                        if ( ! $formatted_address )
                        	continue;
		                ?>
		                <div class="account-address">
                            <?php
                            if ( isset($addr['default_address']) && $addr['default_address'] ) {
                                if (function_exists('wc_get_endpoint_url'))
                                    echo '<a href="'. wc_get_endpoint_url( 'edit-address', 'shipping', get_permalink(wc_get_page_id( 'myaccount' )) ) .'" class="edit">'. __( 'edit', 'wc_shipping_multiple_address' ) .'</a>';
                                else
                                    echo '<a href="'. esc_url( add_query_arg('address', 'shipping', get_permalink(wc_get_page_id( 'edit_address' ) ) ) ) .'" class="edit">'. __( 'edit', 'wc_shipping_multiple_address' ) .'</a>';
                            } else {
                                echo '<a class="edit" href="'. $addresses_url .'#shipping_address_'. $x .'">'. __('edit', 'wc_shipping_multiple_address').'</a>';
                            }
                            ?>

		                    <address><?php echo $formatted_address; ?></address>

		                    <div style="display: none;">
		                    <?php
		                    foreach ($shipFields as $key => $field) :
		                        $val = (isset($addr[$key])) ? $addr[$key] : '';
		                        $key .= '_'. $x;

		                        woocommerce_form_field( $key, $field, $val );
		                    endforeach;

		                    do_action('woocommerce_after_checkout_shipping_form', $checkout);
		                    ?>
		                        <input type="hidden" name="addresses[]" value="<?php echo $x; ?>" />
		                        <textarea style="display:none;"><?php echo $json_address; ?></textarea>
		                    </div>

		                    <ul class="items-column" id="items_column_<?php echo $x; ?>">
		                        <?php
		                        if ( isset($relations[$x]) && !empty($relations[$x]) ):
		                            $qty = array_count_values($relations[$x]);
		                            foreach ( $contents as $key => $value ) {
		                                if ( in_array($key, $relations[$x]) ) {
		                                    if ( isset($placed[$key]) ) {
		                                        $placed[$key] += $qty[$key];
		                                    } else {
		                                        $placed[$key] = $qty[$key];
		                                    }
		                        ?>
		                        <li data-product-id="<?php echo $value['product_id']; ?>" data-key="<?php echo $key; ?>" class="address-item address-item-<?php echo $value['product_id']; ?> address-item-key-<?php echo $key; ?>">
		                            <span class="qty"><?php echo $qty[$key]; ?></span>
		                            <h3 class="title"><?php echo get_the_title($value['product_id']); ?></h3>
		                            <?php echo WC_MS_Compatibility::get_item_data( $value ); ?>

		                            <?php for ($item_qty = 0; $item_qty < $qty[$key]; $item_qty++): ?>
		                            <input type="hidden" name="items_<?php echo $x; ?>[]" value="<?php echo $key; ?>">
		                            <?php endfor; ?>
		                            <a class="remove" href="#"><img style="width: 16px; height: 16px;" src="<?php echo plugins_url('images/delete.png', self::FILE); ?>" class="remove" title="<?php _e('Remove', 'wc_shipping_multiple_address'); ?>"></a>
		                        </li>
		                        <?php
		                                }
		                            }
		                        ?>

		                        <?php else: ?>
		                        <li class="placeholder"><?php _e('Drag items here', 'wc_shipping_multiple_address'); ?></li>
		                        <?php endif; ?>
		                    </ul>
		                </div>
		                <?php
                    }
                echo '</div>';
            else:
                ?>
            <h2>
                <?php _e('Shipping Addresses', 'wc_shipping_multiple_address'); ?>
            <a class="button" href="?address-form"><?php _e('Add New', 'wc_shipping_multiple_address'); ?></a>
            </h2>
            <div id="addresses_container" style="overflow: hidden; width:100%">
                <?php
                    $sigs = array();
                    $displayed_addresses = array();
                    if ( isset($addresses) && !empty($addresses) ) {
                        $sigs = wcms_session_get('cart_address_sigs');
                    }

                    foreach ( $addresses as $addr_id => $address ) {

                        if (! isset( $address['shipping_first_name']) )
                            continue;

                        foreach ( $address as $key => $value ) {
                            $new_key = str_replace( 'shipping_', '', $key );
                            $address[ $new_key ] = $value;
                        }

                        $formatted_address = wcms_get_formatted_address( $address );
                        $json_address      = wp_json_encode( $address );
                        $json_address      = function_exists( 'wc_esc_json' ) ? wc_esc_json( $json_address ) : _wp_specialchars( $json_address, ENT_QUOTES, 'UTF-8', true );

                        if (!$formatted_address) continue;
                        if ( in_array($json_address, $displayed_addresses) ) continue;
                        $displayed_addresses[] = $json_address;

                        ?>
                        <div class="account-address">
                            <address><?php echo $formatted_address; ?></address>

                            <div style="display: none;">
                                <?php
                                foreach ($shipFields as $key => $field) :
                                    $val = (isset($address[$key])) ? $address[$key] : '';
                                    $key .= '_'.$addr_id;
                                    //$key .= '_'. $x;

                                    woocommerce_form_field( $key, $field, $val );
                                endforeach;

                                do_action('woocommerce_after_checkout_shipping_form', $checkout);
                                ?>
                                <input type="hidden" name="addresses[]" value="<?php echo $addr_id; ?>" />
                                <textarea style="display:none;"><?php echo $json_address; ?></textarea>
                            </div>

                            <ul class="items-column" id="items_column_<?php echo $addr_id; ?>">
                                <?php
                                if ( isset($relations[$addr_id]) && !empty($relations[$addr_id]) ):
                                    $qty = array_count_values($relations[$addr_id]);

                                    foreach ( $contents as $key => $value ) {
                                        if ( in_array($key, $relations[$addr_id]) ) {
                                            if ( isset($placed[$key]) ) {
                                                $placed[$key] += $qty[$key];
                                            } else {
                                                $placed[$key] = $qty[$key];
                                            }
                                            ?>
                                            <li data-product-id="<?php echo $value['product_id']; ?>" data-key="<?php echo $key; ?>" class="address-item address-item-<?php echo $value['product_id']; ?> address-item-key-<?php echo $key; ?>">
                                                <span class="qty"><?php echo $qty[$key]; ?></span>
                                                <h3 class="title"><?php echo get_the_title($value['product_id']); ?></h3>
                                                <?php echo WC_MS_Compatibility::get_item_data( $value ); ?>
                                                <input type="hidden" name="items_<?php echo $addr_id; ?>[]" value="<?php echo $key; ?>">
                                                <a class="remove" href="#"><img style="width: 16px; height: 16px;" src="<?php echo plugins_url('images/delete.png', self::FILE); ?>" class="remove" title="<?php _e('Remove', 'wc_shipping_multiple_address'); ?>"></a>
                                            </li>
                                        <?php
                                        }
                                    }
                                    ?>

                                <?php else: ?>
                                    <li class="placeholder"><?php _e('Drag items here', 'wc_shipping_multiple_address'); ?></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    <?php

                    } // endforeach
                    ?>
            </div>
                <?php
            endif;
                ?>
        </div>

    </div>

    <br clear="both"/>
    <div class="form-row">
        <input type="hidden" name="shipping_type" value="item" />
        <input type="hidden" name="shipping_address_action" value="save" />
        <input type="submit" name="set_addresses" value="<?php echo __('Save Addresses and Continue', 'wc_shipping_multiple_address'); ?>" class="button alt" />
    </div>
</form>
<?php if ( $user->ID == 0 ): ?>
<div id="address_form_template" style="display: none;">
    <form id="add_address_form">
    <div class="shipping_address address_block" id="shipping_address">
    <?php do_action('woocommerce_before_checkout_shipping_form', $checkout); ?>

        <div class="address-column">
    <?php
        foreach ($shipFields as $key => $field) :
            $key = 'address['. $key .']';
            $val = '';

            woocommerce_form_field( $key, $field, $val );
        endforeach;

        do_action('woocommerce_after_checkout_shipping_form', $checkout);
    ?>
            <input type="hidden" name="id" id="address_id" value="" />
        </div>

    </div>

    <input type="hidden" name="return" value="div" />
    <input type="submit" class="button" id="use_address" value="<?php _e('Use this address', 'wc_shipping_multiple_address'); ?>" />
    </form>
</div>
<?php else: ?>
<div id="address_form_template" style="display: none;">
    <form id="add_address_form">
    <div class="shipping_address address_block" id="shipping_address">
    <?php do_action('woocommerce_before_checkout_shipping_form', $checkout); ?>

        <div class="address-column">
    <?php
        foreach ($shipFields as $key => $field) :
            $val    = '';
            $key    = 'address['. $key .']';
            $id     = rtrim( str_replace( '[', '_', $key ), ']' );
            $field['return'] = true;

            echo str_replace( 'id="'. $key .'"', 'id="'. $id .'"', woocommerce_form_field( $key, $field, $val ) );
        endforeach;

        do_action('woocommerce_after_checkout_shipping_form', $checkout);
    ?>
        </div>
    </div>

    <input type="hidden" name="return" value="div" />
    <input type="submit" id="save_address" class="button" value="<?php _e('Save Address', 'wc_shipping_multiple_address'); ?>" />
    </form>
</div>
<?php endif; ?>
<div id="duplicate_address_form_template" style="display:none;">
    <form id="duplicate_cart_form">

        <p>
            <select name="address_id">
                <option value=""><?php _e('Select an existing address', 'wc_shipping_multiple_address'); ?></option>
                <?php
                foreach ($addresses as $x => $addr) {
                    if ( empty($addr) ) continue;

                    $address = array(
                        'first_name'    => $addr['shipping_first_name'],
                        'last_name'     => $addr['shipping_last_name'],
                        'company'       => $addr['shipping_company'],
                        'address_1'     => $addr['shipping_address_1'],
                        'address_2'     => $addr['shipping_address_2'],
                        'city'          => $addr['shipping_city'],
                        'state'         => $addr['shipping_state'],
                        'postcode'      => $addr['shipping_postcode'],
                        'country'       => $addr['shipping_country']
                    );

                    $formatted_address = wcms_get_formatted_address( $address );
                    $json_address      = wp_json_encode( $address );
                    $json_address      = function_exists( 'wc_esc_json' ) ? wc_esc_json( $json_address ) : _wp_specialchars( $json_address, ENT_QUOTES, 'UTF-8', true );

                    if (!$formatted_address) continue;
                ?>
                    <option value="<?php echo $x; ?>">
                        <?php echo $address['first_name'] .' '. $address['last_name'] .' - '. $address['address_1'] .' '. $address['address_2'] .', '. $address['city'] .', '. $address['state'] .' '. $address['country'] .' '. $address['postcode']; ?>
                    </option>
                <?php
                }
                ?>
            </select>
        </p>

    <div class="shipping_address address_block" id="shipping_address">
    <?php do_action('woocommerce_before_checkout_shipping_form', $checkout); ?>

        <div class="address-column">
    <?php
        foreach ($shipFields as $key => $field) :
            $val = '';
            $key = 'address['. $key .']';

            woocommerce_form_field( $key, $field, $val );
        endforeach;

        do_action('woocommerce_after_checkout_shipping_form', $checkout);
    ?>
        </div>
    </div>

    <input type="submit" id="duplicate_cart" class="button" value="<?php _e('Duplicate Cart Items', 'wc_shipping_multiple_address'); ?>" />
    </form>
</div>
