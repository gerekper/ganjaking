<?php
if( !defined('ABSPATH' ) ){
    exit;
}

$button_text = get_option( 'ywsfl_button_text_single_product' );
$remove_text = get_option( 'ywsfl_button_text_remove_in_list' );

/**
 * @var YITH_WC_Save_For_Later_Premium $YIT_Save_For_Later
 */
global $product,  $YIT_Save_For_Later;
$product_id = 'simple' == $product->get_type() ? $product->get_id() : false ;
$item_id = false;


?>
<div class="ywsfl_button_container">
    <?php
        if( $product_id ){
	        $item_id = $YIT_Save_For_Later->get_save_for_later_item_id( $product_id ) ;
        }


        $hide_add_button_class = $product_id && !$item_id;
        $disable_add_button = !$product_id;
    ?>
    <button type="submit" class="ywsfl_single_add button alt <?php echo $hide_add_button_class ? '' :'ywsfl_hide'?>" <?php echo $disable_add_button ? 'disabled' : '' ?>><?php echo esc_html( $button_text ); ?></button>
    <button type="submit" class="ywsfl_single_remove button alt <?php echo $hide_add_button_class ? 'ywsfl_hide' :''?>"><?php echo esc_html( $remove_text ); ?></button>
    <input type="hidden" name="save_item_id" value = "<?php echo $item_id ?  $item_id : '';?>">
    <span class="ywsfl_single_message"></span>
</div>
<?php
