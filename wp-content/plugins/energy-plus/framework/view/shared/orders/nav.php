<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<div class="energyplus-title--menu __A__Coupons_Mode_2">
  <div class="__A__Scroll">
  <div class="row energyplus-gp">
    <ul>
      <li><a class="__A__Button1<?php EnergyPlus_Helpers::selected('status')?>" href="<?php echo EnergyPlus_Helpers::admin_page('orders', array());  ?>"><?php esc_html_e('All Orders', 'energyplus'); ?> <?php echo esc_html($list['statuses_count']['count']) ?></a></li>
      <?php  foreach ( $list['statuses'] AS $status_k => $status) {
        if ($list['statuses_count'][$status_k] > 0) { ?>
        <li><a class="__A__Button1<?php EnergyPlus_Helpers::selected('status', $status_k)?>" href="<?php echo EnergyPlus_Helpers::admin_page('orders', array('status' => $status_k));  ?>"><?php echo esc_attr($status) ?> <span class="__A__Count"><?php echo esc_html($list['statuses_count'][$status_k]) ?></span></a></li>
      <?php }
    }  ?>

    <?php if (0<$list['statuses_count']['trash']) { ?>
      <li><a class="__A__Button1<?php EnergyPlus_Helpers::selected('status', 'trash')?>" href="<?php echo EnergyPlus_Helpers::admin_page('orders', array('status' => 'trash'));  ?>"><?php esc_html_e('Trash', 'energyplus'); ?>  <span class="__A__Count"><?php echo esc_html($list['statuses_count']['trash']) ?></span></a></li>
    <?php }  ?>

    <?php do_action('energyplus_submenu', 'orders'); ?>

    <li class="__A__Li_Search">
      <a href="javascript:;" class="__A__Button1 __A__Search_Button"><?php esc_html_e('Search', 'energyplus'); ?></a>
    </li>
  </ul>
</div>
</div>
</div>
