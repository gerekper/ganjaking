<?php
if( !defined('ABSPATH' ) ){
    exit;
}

global $post;

$product_receivers = get_post_meta( $post->ID, '_yit_paypal_adp_product_receivers', true );
$desc_tip =  sprintf('<span class="description"><b>%s:</b> %s.<br/><b>%s:</b> %s.<br/><b>%s:</b> %s.</span>',
                        _x( 'Email','As in the sentence "Email: enter a valid email address associated to PayPal"',
                            'yith-paypal-adaptive-payments-for-woocommerce' ),
                        _x('enter a valid email address associated
                         to PayPal','As in the sentence "Email: enter a valid email address associated
                         to PayPal"','yith-paypal-adaptive-payments-for-woocommerce'),
                        _x( 'Commission', 'As in the sentence "Commission: enter a percent value"','yith-paypal-adaptive-payments-for-woocommerce' ),
                        _x('enter a percent value', 'As in the sentence "Commission: enter a percent value"',
                            'yith-paypal-adaptive-payments-for-woocommerce'),
                        _x( 'Split After','Split payments after x sales','yith-paypal-adaptive-payments-for-woocommerce' ),
                        _x('split the payments after x product sales', 'As in the sentence: "Split After: split the payments after n product sales"',
                            'yith-paypal-adaptive-payments-for-woocommerce' ) );


$email_placeholder = __('Email','yith-paypal-adaptive-payments-for-woocommerce' );
$commission_placeholder = __('Commission', 'yith-paypal-adaptive-payments-for-woocommerce' );
?>

<div id="yith_adaptive_payments" class="panel woocommerce_options_panel">
    <div class="options_group" >
        <p class="form-field">
            <label id="yith_add_receivers"><?php _e('Add Receivers', 'yith-paypal-adaptive-payments-for-woocommerce' );?></label>
            <button type="button" id="yith_add_receivers" class="button"><?php _e( 'Add', 'yith-paypal-adaptive-payments-for-woocommerce');?></button>
        </p>
    </div>
    <div id="yith_product_receiver_list" class="options_group">
        <?php
            $i = 0;
            $receiver_label  = __( 'Receiver', 'yith-paypal-adaptive-payments-for-woocommerce' );

            if( !empty( $product_receivers ) ){
                
                foreach( $product_receivers as $receiver ) {
                    $user_id     = absint( $receiver['receiver_id'] );
                    $user        = get_user_by( 'id', $user_id );
                    $user_string = '#'.$user->ID.'-'.esc_html( $user->display_name ) ;
                    $user_email = $user->yith_paypal_email;

                    $args = array(
                        'id' => 'yith_receiver_'.$i,
                        'class' => 'wc-customer-search yith_receiver_user_id' ,
                        'name' => 'yith_product_receiver['.$i.'][receiver_id]',
                        'data-multiple' => false,
                        'data-action' => 'paypal_adptive_payments_search_paypal_email' ,
                        'data-placeholder' => __( 'Search for users', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                        'data-selected' => array( $user_id => $user_string ),
                        'data-allow_clear'=>true,
                        'value' =>  $user_id
                    );
                    ?>
                    <p class="form-field">
                        <label id="yith_receiver_<?php esc_attr_e( $i );?>"><?php echo sprintf('%s %s', $receiver_label, ( $i+1 ) );?></label>
                      <?php yit_add_select2_fields( $args );?>
                        <input type="email" required name="yith_product_receiver[<?php esc_attr_e( $i );?>][email]" class="yith_receiver_email"
                               placeholder="<?php esc_attr_e( 'Email address', 'yith-paypal-adaptive-payments-for-woocommerce' ); ?>"
                               value="<?php esc_attr_e( $user_email );?>"/>
                        <input type="number" required name="yith_product_receiver[<?php esc_attr_e( $i );?>][commission]" class="yith_receiver_commission" min="0" max="100" placeholder="<?php esc_attr_e( $commission_placeholder );?>" value="<?php esc_attr_e( $receiver['commission'] );?>">
                        <input type="number" name="yith_product_receiver[<?php esc_attr_e( $i );?>][split_after]" class="yith_receiver_split_after" min="0" placeholder="<?php _e('Split after', 'yith-paypal-adaptive-payments-for-woocommerce' );?>" value="<?php esc_attr_e( $receiver['split_after'] );?>">
                        <a href="#" class="delete_receiver"><?php _e( 'Delete', 'yith-paypal-adaptive-payments-for-woocommerce' );?></a>
                        <?php echo $desc_tip;?>
                    </p>
             <?php
                    $i++;
                }
            }
        ?>

    </div>
    <div id="yith_padp_commission_error" style="display:none;" title="<?php _e('Attention!', 'yith-paypal-adaptive-payments-for-woocommerce');?>">
    	<?php _e('The sum of commission percent values should not exceed 100%', 'yith-paypal-adaptive-payments-for-woocommerce');?>
    </div>
    
    <div id="field_hidden" style="visibility: hidden;">
        <p class="form-field" data-name_1="yith_product_receiver[%i][receiver_id]" data-name_2="yith_product_receiver[%i][commission]" data-name_3="yith_product_receiver[%i][email]" data-name_4="yith_product_receiver[%i][split_after]">
            <label id="yith_receiver_%i"><?php echo sprintf('%s %s', $receiver_label, '%#' );?></label>
            <?php
            $args = array(
                'id' => 'yith_receiver_%',
                'class' => 'wc-customer-search yith_receiver_user_id enhanced hidden' ,
                'data-multiple' => false,
                'data-action' => 'paypal_adptive_payments_search_paypal_email' ,
                'data-placeholder' => __( 'Search for users', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                'data-allow_clear'=>true,

            );
            yit_add_select2_fields( $args );
            ?>
            <input type="email" class="yith_receiver_email" placeholder="<?php esc_attr_e( 'Email address','yith-paypal-adaptive-payments-for-woocommerce' ); ?>"/>
            <input type="number"  class="yith_receiver_commission" min="0" max="100" placeholder="<?php esc_attr_e( $commission_placeholder );?>">
            <input type="number"  class="yith_receiver_split_after" min="0" placeholder="<?php _e('Split after', 'yith-paypal-adaptive-payments-for-woocommerce' );?>">
            <a href="#" class="delete_receiver"><?php _e( 'Delete', 'yith-paypal-adaptive-payments-for-woocommerce' );?></a>
            <?php echo  $desc_tip;?>
        </p>
    </div>
</div>