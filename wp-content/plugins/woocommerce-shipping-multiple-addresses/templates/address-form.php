<?php

if ( empty($addresses) ) {
    echo '<p>'. __('No address on file. Please add one below.', 'wc_shipping_multiple_address') .'</p>';
} else {
    /* @var $woocommerce Woocommerce */

    echo '<div class="address-container">';
    foreach ( $addresses as $idx => $address ) {

        wc_get_template(
            'address-block.php',
            array(
                'idx'           => $idx,
                'address'       => $address,
                'checkout'      => $checkout,
                'shipFields'    => $shipFields
            ),
            'multi-shipping',
            dirname( WC_Ship_Multiple::FILE ) .'/templates/'
        );

    }
        echo '<div class="clear"></div>';
    echo '</div>';

}

?>

<hr />

<?php
$address_id = '-1';
$address    = array();

if ( isset($_GET['edit']) ):
    $address_id = intval($_GET['edit']);
    $address    = $addresses[ $address_id ];

?>
    <h2><?php _e('Edit address', 'wc_shipping_multiple_address'); ?></h2>
<?php else: ?>
    <h2><?php _e('Add a new address', 'wc_shipping_multiple_address'); ?></h2>
    <?php if ( !isset( $_GET['ref'] ) ): ?>
    <p>
        <a href="#" class="button btn-import-billing" style="display: none;"><?php _e('Import billing address', 'wc_shipping_multiple_address'); ?></a>
        <a href="#" class="button btn-import-shipping" style="display: none;"><?php _e('Import shipping address', 'wc_shipping_multiple_address'); ?></a>
    </p>
    <?php endif; ?>
<?php endif; ?>

<?php ( function_exists('wc_print_notices') ) ? wc_print_notices() : WC()->show_messages(); ?>

<form action="" method="post" class="wcms-address-form">
    <div class="shipping_address address_block" id="shipping_address">
        <?php do_action('woocommerce_before_checkout_shipping_form', $checkout); ?>

        <div class="address-column">
            <?php
            if ( function_exists('wc_get_customer_default_location') ) {
                $location = wc_get_customer_default_location();

                if ( empty( $address ) && !empty( $location ) ) {
                    foreach ( $location as $key => $value ) {
                        $address['shipping_'. $key] = $value;
                    }
                }
            }

            foreach ($shipFields as $key => $field) :
                $val    = (isset($address[$key])) ? $address[$key] : '';
                $id     = rtrim( str_replace( '[', '_', $key ), ']' );
                $field['return'] = true;

                if ( empty( $val ) && !empty( $_GET[ $key ] ) ) {
                    $val = $_GET[ $key ];
                }

                echo str_replace( 'name="'. $key .'"', 'name="address['. $id .']"', woocommerce_form_field( $key, $field, $val ) );
            endforeach;

            do_action('woocommerce_after_checkout_shipping_form', $checkout);
            ?>

            <input type="hidden" name="action" value="save_to_address_book" />
            <input type="hidden" name="id" id="address_id" value="<?php echo $address_id; ?>" />

            <input type="hidden" name="return" value="list" />

            <?php if ( !empty( $_GET['ref'] ) ): ?>
            <input type="hidden" name="next" value="<?php echo get_permalink( wc_get_page_id( 'myaccount' ) ); ?>" />
            <?php endif; ?>
        </div>

    </div>

    <?php if ( $address_id > -1 ): ?>
        <input type="submit" class="button alt" id="use_address" value="<?php _e('Update Address', 'wc_shipping_multiple_address'); ?>" />
    <?php else: ?>
        <input type="submit" class="button alt" id="use_address" value="<?php _e('Save Address', 'wc_shipping_multiple_address'); ?>" />
    <?php endif; ?>

</form>
<script type="text/javascript">
    var billing_address = null,
        shipping_address = null;

    jQuery(document).ready(function($) {
        if ( supports_html5_storage() ) {
            billing_address = localStorage.getItem( 'wcms_billing_fields' );
            shipping_address = localStorage.getItem( 'wcms_shipping_fields' );

            if ( billing_address ) {
                billing_address = JSON.parse( billing_address );
                $(".btn-import-billing").show();
            }

            if ( shipping_address ) {
                shipping_address = JSON.parse( shipping_address );
                $(".btn-import-shipping").show();
            }

            $(".btn-import-billing").click(function(e) {
                e.preventDefault();
                $( '#ms_addresses' ).val(''); // Reset dropdown on add screen

                for ( field in billing_address ) {
                    var shipping_field = field.replace('billing_', 'shipping_');
                    $("#"+ shipping_field)
                        .val( billing_address[field] )
                        .change();
                }
            });

            $(".btn-import-shipping").click(function(e) {
                e.preventDefault();
                $( '#ms_addresses' ).val(''); // Reset dropdown on add screen

                for ( field in shipping_address ) {
                    $("#"+ field)
                        .val( shipping_address[field] )
                        .change();
                }
            });
        }
    });
</script>
