<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<div class="energyplus-title--menu __A__Coupons_Mode_2 __A__Scroll">
  <div class="row energyplus-gp">
    <ul>
      <li><a class="__A__Button1<?php EnergyPlus_Helpers::selected('panel')?>" href="<?php echo EnergyPlus_Helpers::admin_page('settings', array( ));  ?>"><?php esc_html_e('General', 'energyplus'); ?></a> </li>
      <?php if (EnergyPlus_Admin::is_admin(null)) { ?><li><a class="__A__Button1<?php EnergyPlus_Helpers::selected('panel', 'panels')?>" href="<?php echo EnergyPlus_Helpers::admin_page('settings', array( 'panel' => 'panels' ));  ?>"><?php esc_html_e('Menu', 'energyplus'); ?></a></li><?php } ?>
      <?php if (EnergyPlus_Admin::is_admin(null) || (!EnergyPlus_Admin::is_admin(null) && '1' === EnergyPlus::option('reactors-tweaks-settings-woocommerce',0))) { ?><li><a class="__A__Button1<?php EnergyPlus_Helpers::selected('panel', 'woocommerce')?>" href="<?php echo EnergyPlus_Helpers::admin_page('settings', array( 'panel' => 'woocommerce' ));  ?>"><?php esc_html_e('WooCommerce', 'energyplus'); ?></a></li><?php } ?>
    </ul>
  </div>
</div>
