<div class="address-block">
<?php
$page_id    = wc_get_page_id( 'multiple_addresses' );
$form_link  = get_permalink( $page_id );
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

$formatted_address = wcms_get_formatted_address( $address );
if ( $formatted_address ) {
    echo '<address>'.$formatted_address.'</address>';
    $ref = '';
    if ( is_account_page() ) {
        $ref = 'account';
    }
    $edit_link = add_query_arg( array('edit' =>  $idx, 'address-form' => 1, 'ref' => $ref), $form_link );
?>
    <div class="buttons">
        <a class="button" href="<?php echo $edit_link; ?>#shipping_address"><?php _e('Edit', 'wc_shipping_multiple_address'); ?></a>
        <a class="button ms_delete_address" data-idx="<?php echo $idx; ?>" href="#"><?php _e('Delete', 'wc_shipping_multiple_address'); ?></a>
    </div>
<?php
}
?>
</div>