<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<div class="energyplus-title--menu __A__Coupons_Mode_2">
  <div class="row energyplus-gp">
    <ul>
      <li><a class="__A__Button1<?php EnergyPlus_Helpers::selected('status')?>" href="<?php echo EnergyPlus_Helpers::admin_page('reactors', array( ));  ?>"><?php echo sprintf(esc_html__('All %d', 'energyplus'), $counts['active']+$counts['inactive']); ?></a> </li>
      <?php if (0 < $counts['active']) { ?><li><a class="__A__Button1<?php EnergyPlus_Helpers::selected('status', 'active')?>" href="<?php echo EnergyPlus_Helpers::admin_page('reactors', array( 'status' => 'active' ));  ?>"><?php echo sprintf(esc_html__('Active %d', 'energyplus'), $counts['active']); ?></a> </li><?php } ?>
      <?php if (0 < $counts['inactive']) { ?><li><a class="__A__Button1<?php EnergyPlus_Helpers::selected('status', 'inactive')?>" href="<?php echo EnergyPlus_Helpers::admin_page('reactors', array( 'status' => 'inactive' ));  ?>"><?php echo sprintf(esc_html__('Inactive %d', 'energyplus'), $counts['inactive']); ?></a> </li><?php } ?>
      </ul>
  </div>
</div>
