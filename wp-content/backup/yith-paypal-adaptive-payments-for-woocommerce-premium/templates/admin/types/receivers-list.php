<?php
$default = isset( $option['default'] ) ? $option['default'] : array();

$id = $option['id'];
$name = $option['name'];
$desc =  sprintf('<span class="description"><b>%s:</b> %s. <b>%s:</b> %s.</span>',
                        _x( 'Email','As in the sentence "Email: a valid email address associated to PayPal"',
                            'yith-paypal-adaptive-payments-for-woocommerce' ),
                        _x('enter a valid email address associated to PayPal','As in the sentence "Email: enter a valid email address associated to
                        PayPal','yith-paypal-adaptive-payments-for-woocommerce'),
                        _x( 'Commission', 'As in the sentence "Commission: enter a percent value"','yith-paypal-adaptive-payments-for-woocommerce' ),
                        _x('enter a percent value', 'As in the sentence "Commission: enter a percent value"','yith-paypal-adaptive-payments-for-woocommerce') );


$email_placeholder = __('Email','yith-paypal-adaptive-payments-for-woocommerce' );
$commission_placeholder = __('Commission', 'yith-paypal-adaptive-payments-for-woocommerce' );
$receivers = get_option( $option['id'], $default );
?>
<tr valign="top">
    <th scope="row"><?php echo $name;?></th>
    <td class="forminp">
    <button type="button" id="yith_add_receivers" class="button"><?php _e( 'Add', 'yith-paypal-adaptive-payments-for-woocommerce');?></button>  
    </td>
</tr>
<tr valign="top">
<td class="forminp yith_list" colspan=2>
	<div id="yith_receiver_list">
		<?php 
			$i = 0;
			$receiver_label  = __( 'Receiver', 'yith-paypal-adaptive-payments-for-woocommerce' );
			if( !empty( $receivers) ){
				foreach( $receivers as $receiver ){
					$user_id     = absint( $receiver['receiver_id'] );
					$user        = get_user_by( 'id', $user_id );
					$user_string = '#'.$user->ID.'-'.esc_html( $user->display_name ) ;
                    $user_email = $user->yith_paypal_email;

                    $args = array(
                        'id' => 'customer_user_'.$i,
                        'class' => 'wc-customer-search yith_receiver_user_id' ,
                        'name' => $id.'['.$i.'][receiver_id]',
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
                              <input type="email" name="<?php esc_attr_e( $id );?>[<?php esc_attr_e( $i );?>][email]" class="yith_receiver_email"
                                     required placeholder="<?php esc_attr_e( 'Enter a valid email address',
                                  'yith-paypal-adaptive-payments-for-woocommerce' );
                              ?>" value="<?php esc_attr_e( $user_email );?>"/>
					           <input type="number" required name="<?php esc_attr_e( $id );?>[<?php esc_attr_e( $i );?>][commission]" class="yith_receiver_commission" min="0" max="100" placeholder="<?php esc_attr_e( $commission_placeholder );?>" value="<?php esc_attr_e( $receiver['commission'] );?>">
					          <a href="#" class="delete_receiver"><?php _e( 'Delete', 'yith-paypal-adaptive-payments-for-woocommerce' );?></a>
					          <?php echo $desc;?>
					   </p>
					<?php
					    $i++;
					    }
				
				}
		
		?>
	</div>
	<div id="field_hidden" style="visibility: hidden;">
        <p class="form-field" data-name_1="<?php esc_attr_e( $id );?>[%i][receiver_id]" data-name_2="<?php esc_attr_e( $id );?>[%i][commission]" data-name_3="<?php esc_attr_e( $id );?>[%i][email]">
            <label id="yith_receiver_%i"><?php echo sprintf('%s %s', $receiver_label, '%#' );?></label>
            <?php
            $args = array(
                'id' => 'customer_user_%',
                'class' => 'wc-customer-search yith_receiver_user_id enhanced hidden' ,
                'data-multiple' => false,
                'data-action' => 'paypal_adptive_payments_search_paypal_email' ,
                'data-placeholder' => __( 'Search for users', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                'data-allow_clear'=>true,

            );
            yit_add_select2_fields( $args );
            ?>

            <input type="email" class="yith_receiver_email" placeholder="<?php esc_attr_e( 'Enter a valid email address',
                'yith-paypal-adaptive-payments-for-woocommerce' ); ?>"/>
            <input type="number" class="yith_receiver_commission" min="0" max="100" placeholder="<?php esc_attr_e( $commission_placeholder );?>">
            <a href="#" class="delete_receiver"><?php _e( 'Delete', 'yith-paypal-adaptive-payments-for-woocommerce' );?></a>
            <?php echo  $desc;?>
        </p>
    </div>
    <div id="yith_padp_commission_error" style="display:none;" title="<?php _e('Attention!', 'yith-paypal-adaptive-payments-for-woocommerce');?>">
    	<?php _e('The sum of commission percent values should not exceed 100%', 'yith-paypal-adaptive-payments-for-woocommerce');?>
    </div>
</td>
    <input type="hidden" name="ywcpadp_hidden_field" value="check_empty" />
</tr>

