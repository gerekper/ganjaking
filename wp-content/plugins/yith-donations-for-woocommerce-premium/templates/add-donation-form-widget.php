<?php
if( !defined( 'ABSPATH' ) )
    exit;
?>
<div class="ywcds_form_container">
    <form id="ywcds_add_donation_form" method="post">
        <div class="ywcds_amount_field">
            <label for="ywcds_amount"><?php echo $message_for_donation;?></label>
            <input type="text" class="ywcds_amount" name="ywcds_amount"/>
            <?php do_action('ywcds_after_widget_amount_field' );?>
        </div>
        <div class="ywcds_select_amounts_content">
        <?php if( !empty( $donation_amount ) ){

            $values = explode("|", $donation_amount );

            foreach ($values as $value ) {
                $value = apply_filters('ywcds_get_donation_amount', $value );
                $formatted_value = wc_price( $value );
	            if ( 'label' == $donation_amount_style ) :?>
	                <span class="ywcdp_single_amount button">
                        <?php echo $formatted_value;?>
                        <input type="hidden" value="<?php echo $value;?>">
                    </span>
            <?php else:?>
                <label for="single_donation_<?php echo $value;?>">
                    <?php echo $formatted_value;?>
                    <input type="radio" class="ywcdp_single_amount" value="<?php echo $value;?>" name="single_donation[]">
                </label>
            <?php
                endif;
            }

        }?>
        </div>
        <?php
            if( isset( $show_extra_desc ) && ( 'on' === $show_extra_desc ) ):

            ?>
            <div class="ywcds_show_extra_info_content">
                <label for="ywcds_show_extra_info"><?php echo ( $extra_desc_label );?></label>
                <input type="text" name="ywcds_show_extra_info" class="ywcds_show_extra_info" value="">
                <input type="hidden" name="ywcds_show_extra_info_label" value="<?php echo  $extra_desc_label ;?>">
            </div>
        <?php endif;?>
        <div class="ywcds_button_field">
            <input type="hidden" class="ywcds_product_id" name="add_donation_to_cart" value="<?php echo $product_id;?>" />
            <input type="submit" name="ywcds_submit_widget" class="ywcds_submit_widget <?php echo $button_class;?>" value="<?php echo $button_text;?>" />
        </div>
    </form>
    <img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ) ?>" class="ajax-loading" alt="loading" width="16" height="16" style="visibility:hidden" />
    <div class="ywcds_message woocommerce-message" style="display: none;"></div>
</div>
