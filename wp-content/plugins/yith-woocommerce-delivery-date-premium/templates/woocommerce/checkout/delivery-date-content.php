<?php
if(!defined('ABSPATH')){
    exit;
}

//$shipping_settings = get_option('woocommerce_'.$shipping_id.'_settings');
//$processing_method = isset( $shipping_settings['select_process_method'] ) ? $shipping_settings['select_process_method']  : '';

?>
<div class="ywcdd_select_delivery_date_content">
    <input type="hidden" name="ywcdd_fields" value="1" />
    <?php if( $processing_method != ''):
       
        wc_get_template('woocommerce/checkout/delivery-date-select-date.php', array( 'processing_method'=>$processing_method, 'is_mandatory' => $is_mandatory ), YITH_DELIVERY_DATE_TEMPLATE_PATH, YITH_DELIVERY_DATE_TEMPLATE_PATH );
    ?>
    <?php endif;?>
</div>
