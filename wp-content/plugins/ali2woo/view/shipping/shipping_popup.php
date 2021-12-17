<?php
if (count($countries) > 0){
    if(a2w_check_defined('A2W_USE_RAW_SELECTBOX')){
        $select='<p class="form-row chzn-drop" data-priority=""><label for="a2w_to_country_popup_field" class="">'.__('Ship my order(s) to: ', 'ali2woo').'</label><span class="woocommerce-input-wrapper">';
        $select.='<select name="a2w_to_country_popup_field" id="a2w_to_country_popup_field" class="select " data-allow_clear="true">';
        foreach($countries as $key=>$val){
            $select.='<option value="'.$key.'" '.($key==$default_country?'selected="selected"':'').'>'.$val.'</option>';
        }
        $select.='</select></span></p>';
    }else{
        $select = woocommerce_form_field('a2w_to_country_popup_field', array(
            'type'       => 'select',
            'class'      => array( 'chzn-drop' ),
        
            'placeholder'    => __('Select a Country', 'ali2woo'),
            'options'    => $countries,
            'default' => $default_country,
            'return' => true
        ));
    }
} else {
    //use external country selector if not countries are provided
    $select = '';
}

$a2w_shipping_html = '<div class="a2w_to_country">' . $select . '</div>';
$a2w_shipping_html = str_replace(array("\r", "\n"), '', $a2w_shipping_html);

?>

<div class="a2w_shipping_wrap" id="a2w_shipping_wrap_<?php echo isset($cart_item_key) ? $cart_item_key : $product_id; ?>" data-initial-shipping-info="<?php echo htmlspecialchars(json_encode($shipping_info_data), ENT_QUOTES, 'UTF-8'); ?>"> 
    <div>
        <input type="hidden" class="a2w_to_country_field" name="a2w_to_country_field" value="<?php echo isset($default_country) ? $default_country : ''; ?>"><input type="hidden" class="a2w_shipping_method_field" name="a2w_shipping_method_field" value="<?php if ($default_shipping_method) echo $default_shipping_method; ?>"><?php  if ( is_product() ): ?><input type="hidden" class="a2w_remove_cart_item" name="a2w_remove_cart_item" value="<?php echo a2w_get_setting( 'aliship_not_available_remove' ) ? 1 : 0; ?>"><input type="hidden" class="a2w_fake_method" name="a2w_fake_method" value="<?php echo A2W_Shipping::get_fake_method_id(); ?>"><?php  endif; ?><input type="hidden" class="product_id" value="<?php echo $product_id; ?>"><input type="hidden" class="item_id" value="<?php echo isset($cart_item_key) ? $cart_item_key : $product_id; ?>">
    </div>
    <?php if ($show_label) : ?><span class="label"><?php _e('Shipping', 'ali2woo'); ?>:</span><?php endif; ?>
    <div class="shipping_info"><?php echo $shipping_info; ?></div><div class="product-shipping-date"></div>
</div>
<div class="a2w_shipping_modal" id="a2w_shipping_modal_<?php echo isset($cart_item_key) ? $cart_item_key : $product_id; ?>">
  <div class="logistics">

    <?php if (count($countries) > 0): ?>
    <div class="ship-to"><?php _e('Ship to: ', 'ali2woo'); ?></div>
    <div class="address"><?php echo $a2w_shipping_html; ?></div>
    <?php endif; ?>
 
    <div class="shipping-result">
        <div class="choose-delivery"><?php _e('Shipping Method: ', 'ali2woo'); ?></div>
        <div class="a2w-div-table shipping-table <?php echo empty($shipping_methods) ? 'hidden' : ''; ?>" >
            <div class="a2w-div-table-row first-row">
            <div class="a2w-div-table-col delivery-col"><?php _e('Estimated Delivery', 'ali2woo'); ?></div>
            <div class="a2w-div-table-col"><?php _e('Cost', 'ali2woo'); ?></div>
            <div class="a2w-div-table-col"><?php _e('Tracking', 'ali2woo'); ?></div>
            <div class="a2w-div-table-col"><?php _e('Carrier', 'ali2woo'); ?></div></div>
     
            <?php $fid = 'a2w_shipping_method_popup_field_'. (isset($cart_item_key) ? $cart_item_key : $product_id); ?>
            <?php foreach($shipping_methods as $key=>$method) : ?>
            <div class="a2w-div-table-row">
                <div class="a2w-div-table-col small-col">
                    <input type="radio" class="select_method" value="<?php echo $method['serviceName']; ?>" name="<?php echo $fid; ?>" id="<?php echo $fid . '_' . $method['serviceName']; ?>" <?php echo  $method['serviceName'] == $default_shipping_method  ? 'checked': ''; ?>>
                </div>
                <div class="a2w-div-table-col"><?php echo A2W_Shipping::process_delivery_time($method['time']); ?></div>
                <div class="a2w-div-table-col"><?php echo ($method['price'] ? $method['formated_price'] : esc_html__('free', 'ali2woo')); ?></div>
                <div class="a2w-div-table-col"><?php echo ($method['tracking'] ? 'yes' : 'no'); ?></div>
                <div class="a2w-div-table-col"><?php echo $method['company']; ?></div></div>
            <?php endforeach; ?>
        </div>  
     
        <div class="shipping_info<?php if ($shipping_to_country_allowed): ?> hidden<?php endif; ?>"><?php echo $shipping_info; ?></div></div>
    
  </div>
</div>
