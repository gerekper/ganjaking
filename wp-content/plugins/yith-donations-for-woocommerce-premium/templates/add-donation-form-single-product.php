<?php
if( !defined( 'ABSPATH' ) )
    exit;

$donation_is_obligatory =   isset( $is_obligatory ) ?   $is_obligatory : 'false';
$min_don                =   isset( $min_don )       ?   $min_don    :   '';
$max_don                =   isset( $max_don )       ?   $max_don    :   '';
$make_donation_placeholder = isset( $make_donation_placeholder ) ? $make_donation_placeholder : '';

?>
<div class="ywcds_form_container_single_product">
    <div id="ywcds_add_donation_form_single_product" data-donation_is_obligatory="<?php echo $donation_is_obligatory;?>" data-min_donation="<?php echo $min_don;?>" data-max_donation="<?php echo $max_don;?>">
        <div class="ywcds_amount_field">
            <label for="ywcds_amount"><?php echo $message_for_donation;?></label>
            <input type="text" class="ywcds_amount_single_product" name="amount_single_product" value=""  placeholder="<?php echo $make_donation_placeholder;?>"/>
            <input type="hidden" name="donation_product" value="<?php echo $product_id;?>" />
            <?php do_action('ywcds_after_donation_amount');?>
        </div>

    </div>
</div>