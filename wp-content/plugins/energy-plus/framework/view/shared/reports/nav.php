<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<div class="energyplus-title--menu __A__Reports_Mode_1">
  <div class="row energyplus-gp">
    <ul>
      <li><a class="__A__Button1<?php EnergyPlus_Helpers::selected('action')?>" href="<?php echo EnergyPlus_Helpers::admin_page('reports', array( ));  ?>"><?php esc_html_e('Overview', 'energyplus'); ?></a></li>
      <?php if (class_exists('\Automattic\WooCommerce\Admin\FeaturePlugin')){ // if Woocomerce Admin active ?>
        <li class="font-weight-normal"><i class="fas fa-bookmark"></i>&nbsp;&nbsp;&nbsp;<i class="fas fa-chevron-right"></i></li>
        <li><a class="__A__Button1<?php EnergyPlus_Helpers::selected('report', 'revenue')?>" href="<?php echo EnergyPlus_Helpers::admin_page('reports', array( 'action' => 'woocommerce', 'report'=>'revenue' ));  ?>"><?php esc_html_e('Revenue', 'wc-admin'); ?></a></li>
        <li><a class="__A__Button1<?php EnergyPlus_Helpers::selected('report', 'orders')?>" href="<?php echo EnergyPlus_Helpers::admin_page('reports', array( 'action' => 'woocommerce', 'report'=>'orders' ));  ?>"><?php esc_html_e('Orders', 'wc-admin'); ?></a></li>
        <li><a class="__A__Button1<?php EnergyPlus_Helpers::selected('report', 'products')?>" href="<?php echo EnergyPlus_Helpers::admin_page('reports', array( 'action' => 'woocommerce', 'report'=>'products' ));  ?>"><?php esc_html_e('products', 'wc-admin'); ?></a></li>
        <li><a class="__A__Button1<?php EnergyPlus_Helpers::selected('report', 'categories')?>" href="<?php echo EnergyPlus_Helpers::admin_page('reports', array( 'action' => 'woocommerce', 'report'=>'categories' ));  ?>"><?php esc_html_e('categories', 'wc-admin'); ?></a></li>
        <li><a class="__A__Button1<?php EnergyPlus_Helpers::selected('report', 'coupons')?>" href="<?php echo EnergyPlus_Helpers::admin_page('reports', array( 'action' => 'woocommerce', 'report'=>'coupons' ));  ?>"><?php esc_html_e('coupons', 'wc-admin'); ?></a></li>
        <li><a class="__A__Button1<?php EnergyPlus_Helpers::selected('report', 'taxes')?>" href="<?php echo EnergyPlus_Helpers::admin_page('reports', array( 'action' => 'woocommerce', 'report'=>'taxes' ));  ?>"><?php esc_html_e('taxes', 'wc-admin'); ?></a></li>
        <li><a class="__A__Button1<?php EnergyPlus_Helpers::selected('report', 'downloads')?>" href="<?php echo EnergyPlus_Helpers::admin_page('reports', array( 'action' => 'woocommerce', 'report'=>'downloads' ));  ?>"><?php esc_html_e('downloads', 'wc-admin'); ?></a></li>
        <li><a class="__A__Button1<?php EnergyPlus_Helpers::selected('report', 'stock')?>" href="<?php echo EnergyPlus_Helpers::admin_page('reports', array( 'action' => 'woocommerce', 'report'=>'stock' ));  ?>"><?php esc_html_e('stock', 'wc-admin'); ?></a></li>
        <li><a class="__A__Button1<?php EnergyPlus_Helpers::selected('report', 'customers')?>" href="<?php echo EnergyPlus_Helpers::admin_page('reports', array( 'action' => 'woocommerce', 'report'=>'customers' ));  ?>"><?php esc_html_e('customers', 'wc-admin'); ?></a></li>
      <?php } else { ?>
        <li><a class="__A__Button1<?php EnergyPlus_Helpers::selected('action', 'woocommerce')?>" href="<?php echo EnergyPlus_Helpers::admin_page('reports', array( 'action' => 'woocommerce' ));  ?>"><?php esc_html_e('Woocommerce Reports', 'energyplus'); ?></a></li>
      <?php } ?>
      <?php if (EnergyPlus_Helpers::get('action', '') !== 'woocommerce') { ?>
        <li class="__A__Li_Search">
        <a href="<?php echo EnergyPlus_Helpers::admin_page('reports', array('graph'=>1));  ?>" class="__A__Button1 __A__Graph_Button __A__Search_Button<?php if ( "1" === EnergyPlus::option('reports-graph', "2")) echo ' __A__Selected';?>"><span class="dashicons dashicons-chart-bar"></span></a>
        <a href="<?php echo EnergyPlus_Helpers::admin_page('reports', array('graph'=>2));  ?>" class="__A__Button1 __A__Graph_Button __A__Search_Button<?php if ( "2" === EnergyPlus::option('reports-graph', "2")) echo ' __A__Selected';?>"><span class="dashicons dashicons-chart-area"></span></span></a>
      </li>
    <?php } ?>
    </ul>
  </div>
</div>
