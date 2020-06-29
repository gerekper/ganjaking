<?php
if(!defined('ABSPATH')){
    exit;
}
?>
<div class="ywcrbp_new_option_content">
   <h4 style="text-align: center;"><?php _e('With YITH Role-based Prices v. 1.0.4 you can:','yith-woocommerce-role-based-prices');?></h4>
    <ul class="yit-panel-sidebar-links-list">
        <li><?php _e('Show prices with or without tax for user role!','yith-woocommerce-role-based-prices');?></li>
        <li><?php _e('Change all prices label!','yith-woocommerce-role-based-prices');?></li>
        <li><?php _e('Add prices suffix! for example','yith-woocommerce-role-based-prices');?>
            <ul class="yit-panel-sidebar-links-list" style="margin-left: 20px;margin-top: 10px;">
                <li><?php echo wc_price( 20 ).' inc.TAX';?></li>
                <li><?php echo wc_price( 20 ).' excl.TAX';?></li>
            </ul>
        </li>
    </ul>
</div>
