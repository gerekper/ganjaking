<?php
if (count($countries) > 0){

    if(a2w_check_defined('A2W_USE_RAW_SELECTBOX')){
        $select='<p class="form-row chzn-drop validate-required" id="a2w_to_country_field_field" data-priority=""><label for="a2w_to_country_field" class="">'.__('Ship my order(s) to: ', 'ali2woo').'</label><span class="woocommerce-input-wrapper">';
        $select.='<select name="a2w_to_country_field" id="a2w_to_country_field" class="select " data-allow_clear="true">';
        foreach($countries as $key=>$val){
            $select.='<option value="'.$key.'" '.($key==$default_country?'selected="selected"':'').'>'.$val.'</option>';
        }
        $select.='</select></span></p>';
    }else{
        $select = woocommerce_form_field('a2w_to_country_field', array(
            'type'       => 'select',
            'class'      => array( 'chzn-drop' ),
            
            'label'      => __('Ship my orders to: ', 'ali2woo'),
            'placeholder'    => __('Select a Country', 'ali2woo'),
            'options'    => $countries,
            'default' => $default_country,
            'required'=>true,
            'return' => true
        ));
    }

} else {
    //use external country selector if not countries are provided
    $select = '';
}

$a2w_shipping_country_html = '<div class="a2w_to_country">' . $select . '</div>';
$a2w_shipping_country_html = str_replace(array("\r", "\n"), '', $a2w_shipping_country_html);

    $input_class = array( 'select' );
    $shipping_div_class = array('a2w_shipping');
    //$shipping_to_country_allowed = true;

    /**
     * force generation of the shipping drop-down select 
     * by adding first empty element for woocommerce_form_field()
     * 
    */ 
    if (!$shipping_to_country_allowed) {
       // $input_class = array( 'select', 'hidden' );
        $shipping_div_class = array('a2w_shipping', 'hidden');
       // $shipping_to_country_allowed = false;
    }



    $shipping_field_label = "";

    if (is_product()){
        $shipping_field_label = __('Ship my orders via:', 'ali2woo');     
    }


   // $fid = 'a2w_shipping_method_field'. isset($cart_item_key) ? $cart_item_key : $product_id;
    $fid = 'a2w_shipping_method_field';
    if(a2w_check_defined('A2W_USE_RAW_SELECTBOX')){
        $select='<p class="form-row chzn-drop validate-required" id="'.$fid.'_field" data-priority=""><label for="'.$fid.'" class="">'.$shipping_field_label.'</label><span class="woocommerce-input-wrapper">';
        $select.='<select name="'.$fid.'" id="'.$fid.'" class="' . implode(' ', $input_class) . '" data-allow_clear="true">';
        foreach($shipping_methods as $key=>$val){
            $select.='<option value="'.$key.'" '.($key==$default_shipping_method?'selected="selected"':'').'>'.$val.'</option>';
        }
        $select.='</select></span></p>';
    }else{

        $select = woocommerce_form_field($fid, array(
                'type'       => 'select',
                'class'      => array( 'chzn-drop' ),
                'input_class' => $input_class,
                'label'      => $shipping_field_label,
                'placeholder'    => __('Select a shipping method', 'ali2woo'),
                'options'    => $shipping_methods ,
                'default' => $default_shipping_method,
                'required'=>true,
                'return' => true
                )
            );
    }



$a2w_shipping_html = '<div class="'. implode(' ', $shipping_div_class) .'">' . $select . '</div>';
$a2w_shipping_html = str_replace(array("\r", "\n"), '', $a2w_shipping_html);

?>

<div class="a2w_shipping_wrap" id="a2w_shipping_wrap_<?php echo isset($cart_item_key) ? $cart_item_key : $product_id; ?>">
    
    <input type="hidden" class="product_id" value="<?php echo $product_id; ?>">
    <input type="hidden" class="item_id" value="<?php echo isset($cart_item_key) ? $cart_item_key : $product_id; ?>">
    <?php  if ( is_product() ):  ?>
    <input type="hidden" class="a2w_remove_cart_item" name="a2w_remove_cart_item" value="<?php echo a2w_get_setting( 'aliship_not_available_remove' ) ? 1 : 0; ?>">
    <input type="hidden" class="a2w_fake_method" name="a2w_fake_method" value="<?php echo A2W_Shipping::get_fake_method_id(); ?>">
    <?php  endif; ?>
    <?php echo $a2w_shipping_country_html; ?>
    <?php echo $a2w_shipping_html; ?>
    <div class="info<?php if ($shipping_to_country_allowed): ?> hidden<?php endif; ?> ">
        <?php echo $shipping_info; ?>
    </div>

</div>
