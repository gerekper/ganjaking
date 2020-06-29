<?php
if ( function_exists('wc_print_notices') )
    wc_print_notices();
else
    WC()->show_messages();
?>

<p><?php _e('Select the addresses where you want to ship this order to.', 'wc_shipping_multiple_address'); ?></p>

<form method="post">
<?php

if ( empty($addresses) ) {
    echo '<p>'. __('No address on file. Please add one below.', 'wc_shipping_multiple_address') .'</p>';
} else {
    /* @var $woocommerce Woocommerce */

    echo '<div class="address-container">';
    foreach ( $addresses as $idx => $address ) {

        include 'address-duplicate-block.php';

    }
    echo '<div class="clear"></div>';
    echo '</div>';

}

?>

    <div>
        <input type="submit" class="button-primary button alt" name="duplicate_submit" value="<?php _e('Duplicate Cart', 'wc_shipping_multiple_address'); ?>" />
    </div>

</form>

<hr />

<?php
$address_id = '-1';
$address    = array();
?>

<h2><?php _e('Add a new address', 'wc_shipping_multiple_address'); ?></h2>


<form id="add_address_form">
    <div class="shipping_address address_block" id="shipping_address">
        <?php do_action('woocommerce_before_checkout_shipping_form', $checkout); ?>

        <div class="address-column">
            <?php
            foreach ($shipFields as $key => $field) :
                $val    = (isset($address[$key])) ? $address[$key] : '';
                $key    = 'address['. $key .']';
                $id     = rtrim( str_replace( '[', '_', $key ), ']' );
                $field['return'] = true;

                echo str_replace( 'id="'. $key .'"', 'id="'. $id .'"', woocommerce_form_field( $key, $field, $val ) );
            endforeach;

            do_action('woocommerce_after_checkout_shipping_form', $checkout);
            ?>
            <input type="hidden" name="id" id="address_id" value="<?php echo $address_id; ?>" />
            <input type="hidden" name="return" value="list" />
            <input type="hidden" name="next" value="<?php echo add_query_arg( 'duplicate-form', '1' ); ?>" />
        </div>

    </div>

    <input type="submit" class="button alt" id="use_address" value="<?php _e('Save Address', 'wc_shipping_multiple_address'); ?>" />

</form>